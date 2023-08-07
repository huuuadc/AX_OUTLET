<?php
namespace Joomunited\WPSOL;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main plugin functions here
 */
class Admin
{
    /**
     * Admin constructor.
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'registerMenuPage'));
        /**
         * Load admin js *
        */
        add_action('admin_enqueue_scripts', array($this, 'loadAdminScripts'));
        //** load languages **//
        add_action(
            'init',
            function () {
                load_plugin_textdomain(
                    'wp-speed-of-light',
                    false,
                    dirname(plugin_basename(WPSOL_FILE)) . '/languages/'
                );
            }
        );

        // Disable all admin notice for page belong to plugin
        add_action('admin_print_scripts', function () {
            global $wp_filter;
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- No action, nonce is not required
            if ((!empty($_GET['page']) && in_array($_GET['page'], array('wpsol_speed_optimization', 'wpsol_speed_analysis', 'wpsol_dashboard')))) {
                if (is_user_admin()) {
                    if (isset($wp_filter['user_admin_notices'])) {
                        unset($wp_filter['user_admin_notices']);
                    }
                } elseif (isset($wp_filter['admin_notices'])) {
                    unset($wp_filter['admin_notices']);
                }
                if (isset($wp_filter['all_admin_notices'])) {
                    unset($wp_filter['all_admin_notices']);
                }
            }
        });

        $this->ajaxHandle();
    }

    /**
     * Register menu page
     *
     * @return void
     */
    public function registerMenuPage()
    {
        // add main menu
        $page_title = __('WP Speed of Light:', 'wp-speed-of-light') . ' ' . __('Dashboard', 'wp-speed-of-light');
        $menu_title = __('WP Speed of Light', 'wp-speed-of-light');
        $admin_page =add_menu_page(
            $page_title,
            $menu_title,
            'manage_options',
            'wpsol_dashboard',
            array($this, 'loadPage'),
            'dashicons-performance'
        );

        /**
         * Filter Capability and Role to display menu.
         *
         * @param string Capability name
         *
         * @return string
         */
        $manage_options_cap = apply_filters('wpsol_manage_options_capability', 'manage_options');

        // add submenu
        $submenu_pages = array(
            array(
                'wpsol_dashboard',
                '',
                __('Dashboard', 'wp-speed-of-light'),
                $manage_options_cap,
                'wpsol_dashboard',
                array($this, 'loadPage'),
                null,
            ),
            array(
                'wpsol_dashboard',
                '',
                sprintf(__('Speed optimization %s', 'wp-speed-of-light'), ''),
                $manage_options_cap,
                'wpsol_speed_optimization',
                array($this, 'loadPage'),
                null,
            ),
            array(
                'wpsol_dashboard',
                '',
                __('Speed analysis', 'wp-speed-of-light'),
                $manage_options_cap,
                'wpsol_speed_analysis',
                array($this, 'loadPage'),
                null,
            ),
        );

        if (!is_plugin_active('wp-speed-of-light-addon/wp-speed-of-light-addon.php')) {
            $more_speedup = array(
                'wpsol_dashboard',
                '',
                '<span style="color:orange">' . __('More SpeedUp', 'wp-speed-of-light') . '</span>',
                $manage_options_cap,
                'wpsol_more_speedup',
                array($this, 'loadPage'),
                null,
            );

            array_push($submenu_pages, $more_speedup);
        }

        if (count($submenu_pages)) {
            foreach ($submenu_pages as $submenu_page) {
                // Add submenu page
                $admin_page = add_submenu_page(
                    $submenu_page[0],
                    $submenu_page[2] . ' - ' . __('WP Speed of Light:', 'wp-speed-of-light'),
                    $submenu_page[2],
                    $submenu_page[3],
                    $submenu_page[4],
                    $submenu_page[5]
                );

                if (strpos($admin_page, 'wpsol_speed_optimization') !== false) {
                    add_action('load-'. $admin_page, array(new SpeedOptimization(), 'saveSettings'));
                    add_action('load-'. $admin_page, array(new ImportExport(), 'importConfiguration'));
                }
            }
        }
    }

    /**
     * Include display page
     *
     * @return void
     */
    public function loadPage()
    {
        // phpcs:disable WordPress.Security.NonceVerification.Recommended -- View request, no action
        if (isset($_GET['page'])) {
            switch ($_GET['page']) {
                // phpcs:enable
                case 'wpsol_speed_analysis':
                    include_once WPSOL_PLUGIN_DIR . 'src/views/speed-analysis.php';
                    break;
                case 'wpsol_speed_optimization':
                    include_once WPSOL_PLUGIN_DIR . 'src/views/speed-optimization.php';
                    break;
                case 'wpsol_more_speedup':
                    include_once WPSOL_PLUGIN_DIR . 'src/views/more_speedup.php';
                    break;
                default:
                    include_once WPSOL_PLUGIN_DIR . 'src/views/dashboard.php';
                    break;
            }
        }
    }

    /**
     * Load script for backend
     *
     * @return void
     */
    public function loadAdminScripts()
    {
        $current_screen = get_current_screen();
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-tabs');
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_script('jquery-ui-progressbar');
        // wp-speed-of-light_page_wpsol_speed_analysis
        if (strpos($current_screen->base, 'wpsol_dashboard') !== false ||
            strpos($current_screen->base, 'wpsol_speed_analysis') !== false ||
            strpos($current_screen->base, 'wpsol_speed_optimization') !== false
        ) {
            wp_enqueue_script(
                'wpsol-material_tabs',
                plugins_url('assets/js/material/tabs.js', dirname(__FILE__)),
                array('jquery'),
                WPSOL_VERSION,
                true
            );

            wp_enqueue_script(
                'wpsol-tooltip',
                plugins_url('assets/js/material/tooltip.js', dirname(__FILE__)),
                array('jquery'),
                '0.1',
                true
            );
            wp_enqueue_script(
                'wpsol-global',
                plugins_url('assets/js/material/global.js', dirname(__FILE__)),
                array('jquery'),
                '0.1',
                true
            );
            wp_enqueue_script(
                'wpsol-velocity',
                plugins_url('assets/js/material/velocity.min.js', dirname(__FILE__)),
                array('jquery'),
                '0.1',
                true
            );

            wp_enqueue_style(
                'style-light-speed-jquery-ui-fresh',
                plugins_url('assets/css/jquery-ui-fresh.css', dirname(__FILE__)),
                array(),
                WPSOL_VERSION
            );

            wp_enqueue_script(
                'wpsol-waves',
                plugins_url('assets/js/material/waves.js', dirname(__FILE__)),
                array('jquery'),
                '0.1',
                true
            );

            wp_enqueue_script(
                'wpsol-speed_cookie',
                plugins_url('assets/js/jquery.cookie.js', dirname(__FILE__)),
                array('jquery'),
                WPSOL_VERSION,
                true
            );

            wp_enqueue_style(
                'wpsol-css-framework',
                plugins_url('assets/css/wp-css-framework/style.css', dirname(__FILE__))
            );
        }

//
//
//        DASHBOARD
        if (strpos($current_screen->base, 'wpsol_dashboard') !== false) {
            wp_enqueue_style(
                'wpsol-dashboard',
                plugins_url('/assets/css/dashboard.css', dirname(__FILE__)),
                array(),
                WPSOL_VERSION
            );

            wp_enqueue_script(
                'wpsol-dashboard',
                plugins_url('assets/js/dashboard.js', dirname(__FILE__)),
                array('jquery'),
                WPSOL_VERSION,
                true
            );

            if (class_exists('\Joomunited\WPSOLADDON\SpeedOptimization')) {
                wp_enqueue_style(
                    'wpsoladdon-style-dashboard',
                    WPSOL_ADDON_PLUGIN_URL . 'assets/css/wpsol-addon-dashboard.css',
                    array(),
                    WPSOL_ADDON_VERSION
                );
            }
        }
//
//
//
//
//        ANALYSIS
        if (strpos($current_screen->base, 'wpsol_speed_analysis') !== false) {
            wp_enqueue_script(
                'wpsol-speed_analysis',
                plugins_url('assets/js/wpsol-speed-analysis.js', dirname(__FILE__)),
                array('jquery'),
                WPSOL_VERSION,
                true
            );
            $ajax_non = wp_create_nonce('wpsolAnalysisJS');
            wp_localize_script('wpsol-speed_analysis', 'wpsolAnalysisJS', array('ajaxnonce' => $ajax_non));

            wp_enqueue_script(
                'wpsol-speed_tablesorter',
                plugins_url('assets/js/jquery.tablesorter.js', dirname(__FILE__)),
                array('jquery'),
                WPSOL_VERSION,
                true
            );

            // LOAD STYLES
            wp_enqueue_style(
                'wpsol-analysis',
                plugins_url('assets/css/speed_analysis.css', dirname(__FILE__))
            );

            wp_enqueue_style(
                'style-light-speed-jquery-ui-fresh',
                plugins_url('assets/css/jquery-ui-fresh.css', dirname(__FILE__)),
                array(),
                WPSOL_VERSION
            );
        }

        if (strpos($current_screen->base, 'wpsol_speed_optimization') !== false) {
            // Load jquery ui first tab jquery
            wp_enqueue_script(
                'wpsol-jquery-ui',
                plugins_url('assets/js/jquery-ui.min.js', dirname(__FILE__)),
                array('jquery'),
                WPSOL_VERSION,
                true
            );

            wp_enqueue_script(
                'wpsol-speed-optimization',
                plugins_url('assets/js/wpsol-speed-optimization.js', dirname(__FILE__)),
                array('jquery'),
                WPSOL_VERSION,
                true
            );
            $nonce1 = wp_create_nonce('wpsolSpeedOptimizationSystem');
            wp_localize_script('wpsol-speed-optimization', 'speedoptimizeNonce', array('ajaxnonce' => $nonce1));

            wp_enqueue_script(
                'wpsol-import-export',
                plugins_url('assets/js/wpsol-import-export.js', dirname(__FILE__)),
                array('jquery'),
                WPSOL_VERSION,
                true
            );
            wp_localize_script('wpsol-import-export', 'myAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
            $ajax_non = wp_create_nonce('wpsolImportExportCheck');
            wp_localize_script('wpsol-import-export', 'ajaxNonce', array('ajaxnonce' => $ajax_non));


            wp_enqueue_script(
                'jquery-qtip',
                plugins_url('assets/js/jquery.qtip.min.js', dirname(__FILE__)),
                array('jquery'),
                '2.2.1',
                true
            );

            wp_enqueue_script(
                'wpsol-js-framework',
                plugins_url('assets/js/wp-js-framework/script.js', dirname(__FILE__)),
                array('jquery'),
                WPSOL_VERSION,
                true
            );

            // Load style
            //
            // LOAD STYLE
            wp_enqueue_style(
                'wpsol-speed-optimization',
                plugins_url('assets/css/speed-optimization.css', dirname(__FILE__)),
                array(),
                WPSOL_VERSION
            );

            wp_enqueue_style(
                'jquery-qtip',
                plugins_url('assets/css/jquery.qtip.css', dirname(__FILE__)),
                array(),
                WPSOL_VERSION
            );

            wp_enqueue_style('wpsol-quirk', plugins_url('/assets/css/quirk.css', dirname(__FILE__)));

            wp_enqueue_style('wpsol-import-export', plugins_url('/assets/css/import_export.css', dirname(__FILE__)));
        }


        if (strpos($current_screen->base, 'wpsol_more_speedup') !== false) {
            wp_enqueue_style(
                'wpsol-more-speedup',
                plugins_url('/assets/css/more-speedup.css', dirname(__FILE__)),
                array(),
                WPSOL_VERSION
            );
        }
    }

    /**
     * Add ajax handle action
     *
     * @return void
     */
    public function ajaxHandle()
    {
        add_action('wp_ajax_wpsol_load_page_time', array('Joomunited\WPSOL\SpeedAnalysis', 'loadPageTime'));
        add_action('wp_ajax_wpsol_start_scan_query', array('Joomunited\WPSOL\SpeedAnalysis', 'startScanQuery'));
        add_action('wp_ajax_wpsol_stop_scan_query', array('Joomunited\WPSOL\SpeedAnalysis', 'stopScanQuery'));
        add_action('wp_ajax_wpsol_ajax_clean_cache', array('Joomunited\WPSOL\Configuration', 'ajaxCleanCache'));
        add_action('wp_ajax_wpsol_ajax_single_purge_cf_cache', array('Joomunited\WPSOLADDON\FlushThirdPartyCache', 'singlePurgeCFCache'));
        add_action('wp_ajax_wpsol_ajax_preload_cache', array('Joomunited\WPSOL\Configuration', 'startPreloadCache'));
        add_action('wp_ajax_wpsol_more_details', array('Joomunited\WPSOL\SpeedAnalysis', 'moreDetails'));
        add_action('wp_ajax_wpsol_delete_details', array('Joomunited\WPSOL\SpeedAnalysis', 'deleteDetails'));
        add_action('wp_ajax_wpsol_check_response_dashboard', array('Joomunited\WPSOL\Dashboard', 'checkResponseDashboard'));
        add_action('wp_ajax_wpsol_export_configuration', array('Joomunited\WPSOL\ImportExport', 'exportConfiguration'));
        add_action('wp_ajax_wpsol_ajax_system_check', array('Joomunited\WPSOL\SpeedOptimization', 'modulesCheck'));
        add_action('wp_ajax_wpsol_ajax_clear_woo_customer_sessions', array('Joomunited\WPSOLADDON\WoocommerceSpeedup', 'ajaxClearWooCustomerSessions'));
        add_action('wp_ajax_wpsol_ajax_clear_woocommerce_transients', array('Joomunited\WPSOLADDON\WoocommerceSpeedup', 'ajaxClearWoocommerceTransients'));
        add_action('wp_ajax_wpsol_ajax_load_database_element', array('Joomunited\WPSOL\WpsolDatabaseCleanup', 'ajaxLoadDatabaseElement'));
    }
}
