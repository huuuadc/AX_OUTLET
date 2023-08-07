<?php

namespace Joomunited\WPSOL\Install;

use Joomunited\WPSOL\Cache;
use Joomunited\WPSOL\SpeedOptimization;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Install
 */
class Install
{
    /**
     * Install constructor.
     */
    public function __construct()
    {
        add_action('init', array($this, 'installRedirects'));
        add_action('admin_init', array($this, 'wpsolFirstInstall'));
        //Update option when update plugin
        add_action('admin_init', array($this, 'wpsolUpdateVersion'));
    }

    /**
     * Redirect when active plugin
     *
     * @return void
     */
    public function installRedirects()
    {
        // Setup/welcome
        // phpcs:disable WordPress.Security.NonceVerification.Recommended -- View request, no action
        if (isset($_GET['page']) && !empty($_GET['page'])) {
            switch ($_GET['page']) {
                // phpcs:enable
                case 'wpsol-wizard':
                    new InstallWizard();
                    break;
            }
        }
    }

    /**
     * First install plugin
     *
     * @return void
     */
    public function wpsolFirstInstall()
    {
        if (self::checkNewInstall()) {
            update_option('wpsol_version', WPSOL_VERSION);
            wp_safe_redirect(admin_url('index.php?page=wpsol-wizard'));
        }
    }

    /**
     * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
     *
     * @static
     * @return void
     */
    public static function pluginActivation()
    {
        WP_Filesystem();
        $opts = get_option('wpsol_optimization_settings');

        if (empty($opts)) {
            $opts['speed_optimization'] = array();
            $opts['advanced_features'] = array();
        }
        $default_opts = array(
            'speed_optimization' => array(
                'act_cache' => 1,
                'add_expires' => 1,
                'clean_cache' => 40,
                'clean_cache_each_params' => 2,
                'devices' => array(
                    'cache_desktop' => 1,
                    'cache_tablet' => 1,
                    'cache_mobile' => 1,
                ),
                'query_strings' => 1,
                'remove_rest_api' => 0,
                'remove_rss_feed' => 0,
                'cache_external_script' => 0,
                'disable_page' => array(),
            ),
            'advanced_features' => array(
                'html_minification' => 0,
                'css_minification' => 0,
                'js_minification' => 0,
                'cssgroup_minification' => 0,
                'jsgroup_minification' => 0
            )
        );

        $opts['speed_optimization'] = array_merge($default_opts['speed_optimization'], $opts['speed_optimization']);
        $opts['advanced_features'] = array_merge($default_opts['advanced_features'], $opts['advanced_features']);

        update_option('wpsol_optimization_settings', $opts);

        //config by default
        $config = get_option('wpsol_configuration');
        if (empty($config)) {
            $config = array(
                'disable_user' => 0,
                'display_clean' => 1
            );
        }
        $config['display_clean'] = 1;
        update_option('wpsol_configuration', $config);

        // default cdn
        $cdn_integration = get_option('wpsol_cdn_integration');
        if (empty($cdn_integration)) {
            $cdn_integration = array(
                'cdn_active' => 0,
                'cdn_url' => '',
                'cdn_content' => array('wp-content', 'wp-includes'),
                'cdn_exclude_content' => array('.php'),
                'cdn_relative_path' => 1,
                'third_parts' => array(),
            );
        }
        $default_cdn = array(
            'cdn_active' => 0,
            'cdn_url' => '',
            'cdn_content' => array('wp-content', 'wp-includes'),
            'cdn_exclude_content' => array('.php'),
            'cdn_relative_path' => 1,
            'third_parts' => array(),
        );
        $cdn_integration = array_merge($default_cdn, $cdn_integration);
        update_option('wpsol_cdn_integration', $cdn_integration);

        //add header to htaccess by default
        SpeedOptimization::addExpiresHeader(true);
        SpeedOptimization::addGzipHtacess(true);
        //automatic config start cache
        Cache::write();
        Cache::writeConfigCache();

        if (!empty($opts) && !empty($opts['speed_optimization']['act_cache'])) {
            Cache::toggleCaching(true);
        }

        //display message plugin active
        if (version_compare($GLOBALS['wp_version'], WPSOL_MINIMUM_WP_VERSION, '<')) {
            deactivate_plugins(basename(__FILE__));
            wp_die(
                '<p>The <strong>WP Speed of Light</strong> plugin requires WordPress '.
                esc_html(WPSOL_MINIMUM_WP_VERSION).'or higher.</p>',
                'Plugin Activation Error',
                array('response' => 200, 'back_link' => true)
            );
        }
    }

    /**
     * Removes all connection options
     *
     * @static
     * @return void
     */
    public static function pluginDeactivation()
    {
        WP_Filesystem();
        $cache = new Cache();
        $cache->cleanUp();
        $cache->toggleCaching(false);
        $cache->cleanConfig();

        \Joomunited\WPSOL\Minification\Cache::clearMinification();
        //delete header in htacctess
        SpeedOptimization::addExpiresHeader(false);
        SpeedOptimization::addGzipHtacess(false);
    }
    /**
     * Update option when plugin updated
     *
     * @return void
     */
    public function wpsolUpdateVersion()
    {
        $db_installed    = get_option('wpsol_db_version', false);
        $opts            = get_option('wpsol_optimization_settings');
        $cdn_integration = get_option('wpsol_cdn_integration');
        $config          = get_option('wpsol_configuration');
        $default_opts    = array(
            'speed_optimization' => array(
                'act_cache'               => 1,
                'add_expires'             => 1,
                'clean_cache'             => 40,
                'clean_cache_each_params' => 2,
                'devices'                 => array(
                    'cache_desktop' => 1,
                    'cache_tablet'  => 1,
                    'cache_mobile'  => 1,
                ),
                'query_strings'           => 1,
                'remove_rest_api'         => 0,
                'remove_rss_feed'         => 0,
                'cache_external_script'   => 0,
                'disable_page'            => array(),
            ),
            'advanced_features'  => array(
                'html_minification'     => 0,
                'css_minification'      => 0,
                'js_minification'       => 0,
                'cssgroup_minification' => 0,
                'jsgroup_minification'  => 0
            )
        );
        $default_cdn     = array(
            'cdn_active'          => 0,
            'cdn_url'             => '',
            'cdn_content'         => array('wp-content', 'wp-includes'),
            'cdn_exclude_content' => array('.php'),
            'cdn_relative_path'   => 1,
            'third_parts'         => array(),
        );
        $default_config  = array(
            'disable_user'    => 0,
            'display_clean'   => 1
        );

        if (!$db_installed) {
            // update option wpsol_optimization_settings
            $this->wpsolUpdateOption($opts, $default_opts);
            // Update cdn settings
            if (empty($cdn_integration)) {
                $cdn_integration = array();
            }
            $cdn_integration = array_merge($default_cdn, $cdn_integration);
            update_option('wpsol_cdn_integration', $cdn_integration);
            // Update config
            if (empty($config)) {
                $config = array();
            }
            $config = array_merge($default_config, $config);
            update_option('wpsol_configuration', $config);
            // Update current version
            update_option('wpsol_db_version', WPSOL_VERSION);

            return;
        }
        if (!empty($db_installed) && strpos($db_installed, '{{version') !== false) {
            return;
        }
        if (version_compare($db_installed, '1.4.0', '<')) {
            $default_opts['speed_optimization']['query_strings'] = 1;
            $this->wpsolUpdateOption($opts, $default_opts);
        }

        if (version_compare($db_installed, '1.5.1', '<')) {
            // Move clean cache after from optimization to configuration tab.
            $disable_page = array();
            if (!empty($config['disable_page'])) {
                $disable_page = $config['disable_page'];
            }
            $opts['speed_optimization']['disable_page'] = $disable_page;
            unset($config['disable_page']);
            // Update configuration.
            $opts['speed_optimization']['clean_cache_each_params']  = 2;
            $opts['speed_optimization']['cleanup_on_save']          = 1;
            $opts['advanced_features']['fontgroup_minification']    = 0;
            $opts['advanced_features']['excludefiles_minification'] = 0;
            update_option('wpsol_optimization_settings', $opts);
            update_option('wpsol_configuration', $config);
        }
        if (version_compare($db_installed, '2.0.0', '<')) {
            // default cdn
            $cdn_integration = array(
                'cdn_active'          => 0,
                'cdn_url'             => '',
                'cdn_content'         => array('wp-content', 'wp-includes'),
                'cdn_exclude_content' => array('.php'),
                'cdn_relative_path'   => 1,
                'third_parts'         => array()
            );
            update_option('wpsol_cdn_integration', $cdn_integration);
        }

        if (version_compare($db_installed, '2.1.0', '<')) {
            // Update configuration
            $opts['speed_optimization']['cache_external_script'] = 0;
            $opts['speed_optimization']['remove_rest_api']       = 0;
            $opts['speed_optimization']['remove_rss_feed']       = 0;
            update_option('wpsol_optimization_settings', $opts);
        }

        if (version_compare($db_installed, '2.5.1', '<')) {
            // Force clean cache
            Cache::wpsolCacheFlush();
        }

        update_option('wpsol_db_version', WPSOL_VERSION);
    }

    /**
     * Update optimization when plugin updated
     *
     * @param array $opts         Current option
     * @param array $default_opts Default option
     *
     * @return boolean
     */
    public static function wpsolUpdateOption($opts, $default_opts)
    {
        if (!empty($opts['speed_optimization']) && !empty($opts['advanced_features'])) {
            $opts['speed_optimization'] = array_merge($default_opts['speed_optimization'], $opts['speed_optimization']);
            $opts['advanced_features'] = array_merge($default_opts['advanced_features'], $opts['advanced_features']);
            update_option('wpsol_optimization_settings', $opts);
            return true;
        }
        return false;
    }

    /**
     * UPdate configuration when update plugin
     *
     * @param array $opts         Current option
     * @param array $default_opts Default option
     *
     * @return boolean
     */
    public static function wpsolUpdateConfiguration($opts, $default_opts)
    {
        if (!empty($opts)) {
            $opts = array_merge($default_opts, $opts);
            update_option('wpsol_configuration', $opts);
            return true;
        }
        return false;
    }

    /**
     * Is this a brand new wpsol install?
     *
     * @return boolean
     */
    private static function checkNewInstall()
    {
        return is_null(get_option('wpsol_version', null)) && is_null(get_option('wpsol_db_version', null));
    }
}
