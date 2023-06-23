<?php
/*
 * Plugin Name: WooCommerce Alepay Gateway
 * Plugin URI: https://alepay.vn/
 * Description: Add a payment method to WooCommerce using Alepay Gateway.
 * Author: Cuongntk
 * Author URI: https://alepay.vn/
 * Version: 1.0
 * Text Domain: woocommerce-gateway-alepay
 * Copyright (c) 2017 NextTech
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Required minimums and constants
 */
define('WC_ALEPAY_VERSION', '1.0');
define('WC_ALEPAY_MIN_WC_VER', '2.5.0');
define('WC_ALEPAY_MAIN_FILE', __FILE__);
define('WC_ALEPAY_PLUGIN_URL', untrailingslashit(plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__))));
define('WC_ALEPAY_PLUGIN_PATH', untrailingslashit(plugin_dir_path(__FILE__)));

if (!class_exists('WC_Alepay')) {

    class WC_Alepay {

        private static $domain;

        /**
         * @var Singleton The reference the *Singleton* instance of this class
         */
        private static $instance;

        /**
         * @var Reference to logging class.
         */
        private static $log;

        /**
         * Returns the *Singleton* instance of this class.
         *
         * @return Singleton The *Singleton* instance.
         */
        public static function get_instance() {
            if (null === self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Notices (array)
         * @var array
         */
        public $notices = array();

        /**
         * Protected constructor to prevent creating a new instance of the
         * *Singleton* via the `new` operator from outside of this class.
         */
        protected function __construct() {
            self::$domain = 'woocommerce-gateway-alepay';

            add_action('admin_init', array($this, 'check_environment'));
            add_action('admin_notices', array($this, 'admin_notices'), 15);
            add_action('plugins_loaded', array($this, 'init'));
        }

        /**
         * Init the plugin after plugins_loaded so environment variables are set.
         */
        public function init() {
            // Don't hook anything else in the plugin if we're in an incompatible environment
            if (self::get_environment_warning()) { 
                return;
            }

            include_once( dirname(__FILE__) . '/includes/class-wc-alepay-api.php' );

            // Init the gateway itself
            $this->init_gateways();

            add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'plugin_action_links'));
        }

        /**
         * Allow this class and other classes to add slug keyed notices (to avoid duplication)
         */
        public function add_admin_notice($slug, $class, $message) {
            $this->notices[$slug] = array(
                'class' => $class,
                'message' => $message,
            );
        }

        /**
         * The backup sanity check, in case the plugin is activated in a weird way,
         * or the environment changes after activation. Also handles upgrade routines.
         */
        public function check_environment() {

            $environment_warning = self::get_environment_warning();

            if ($environment_warning && is_plugin_active(plugin_basename(__FILE__))) {
                $this->add_admin_notice('bad_environment', 'error', $environment_warning);
                deactivate_plugins( plugin_basename( __FILE__ ) );
            }

            // Check if secret key present. Otherwise prompt, via notice, to go to
            // setting.
            if (!class_exists('WC_Alepay_API')) {
                include_once( dirname(__FILE__) . '/includes/class-wc-alepay-api.php' );
            }
        }

        /**
         * Checks the environment for compatibility problems.  Returns a string with the first incompatibility
         * found or false if the environment has no problems.
         */
        static function get_environment_warning() {
            if (!defined('WC_VERSION')) {
                return __('WooCommerce Alepay requires WooCommerce to be activated to work.', self::$domain);
            }

            if (version_compare(WC_VERSION, WC_ALEPAY_MIN_WC_VER, '<')) {
                $message = __('WooCommerce Alepay - The minimum WooCommerce version required for this plugin is %1$s. You are running %2$s.', self::$domain);

                return sprintf($message, WC_ALEPAY_MIN_WC_VER, WC_VERSION);
            }

            if (!function_exists('curl_init')) {
                return __('WooCommerce Alepay - cURL is not installed.', self::$domain);
            }

            return false;
        }

        /**
         * Adds plugin action links
         *
         * @since 1.0.0
         */
        public function plugin_action_links($links) {
            $settings_link = '<a href="admin.php?page=wc-settings&tab=checkout&section=alepay">' . __('Settings') . '</a>';
            array_unshift($links, $settings_link);
            return $links;
        }

        /**
         * Display any notices we've collected thus far (e.g. for connection, disconnection)
         */
        public function admin_notices() {
            foreach ((array) $this->notices as $notice_key => $notice) {
                echo "<div class='" . esc_attr($notice['class']) . "'><p>";
                echo wp_kses($notice['message'], array('a' => array('href' => array())));
                echo '</p></div>';
            }
        }

        /**
         * Initialize the gateway. Called very early - in the context of the plugins_loaded action
         *
         * @since 1.0.0
         */
        public function init_gateways() {

            if (!class_exists('WC_Payment_Gateway')) { 
                return;
            }

            include_once( dirname(__FILE__) . '/includes/class-wc-gateway-alepay.php' );

            add_filter('woocommerce_payment_gateways', array($this, 'add_gateways'));
        }

        /**
         * Add the gateways to WooCommerce
         *
         * @since 1.0.0
         */
        public function add_gateways($methods) {
            $methods[] = 'WC_Gateway_Alepay';
            return $methods;
        }

        /**
         * What rolls down stairs
         * alone or in pairs,
         * and over your neighbor's dog?
         * What's great for a snack,
         * And fits on your back?
         * It's log, log, log
         */
        public static function log($message) {
            if (empty(self::$log)) {
                self::$log = new WC_Logger();
            }

            self::$log->add(self::$domain, $message);
        }

    }
}

$GLOBALS['wc_alepay'] = WC_Alepay::get_instance();

register_activation_hook(__FILE__, 'wc_alepay_create_db');
function wc_alepay_create_db() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . "alepay_token";

    $sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        token tinytext NOT NULL,
        user_id bigint(20) NOT NULL,
        time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);
}

register_deactivation_hook(__FILE__, 'wc_alepay_remove_database');
function wc_alepay_remove_database() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'alepay_token';
    $sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query($sql);
}