<?php
/**
 * Plugin Name: WP Speed of Light
 * Plugin URI: https://www.joomunited.com/wordpress-products/wp-speed-of-light
 * Description: WP Speed of Light is used to speed up your WP site. It will approach the speed of light
 * Version: 3.3.2
 * Text Domain: wp-speed-of-light
 * Domain Path: /languages
 * Author: JoomUnited
 * Author URI: https://www.joomunited.com
 * License: GPL2
 */

/*
 * @copyright 2014  Joomunited  ( email : contact _at_ joomunited.com )
 *
 *  Original development of this plugin was kindly funded by Joomunited
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

use Joomunited\WPSOL\Admin;
use Joomunited\WPSOL\Cache\CleanCacheTime;
use Joomunited\WPSOL\Cache\Ecommerce;
use Joomunited\WPSOL\Cdn\Integration;
use Joomunited\WPSOL\Configuration;
use Joomunited\WPSOL\ImportExport;
use Joomunited\WPSOL\Install\Install;
use Joomunited\WPSOL\Minification\Cache;
use Joomunited\WPSOL\Rest;
use Joomunited\WPSOL\Rss;
use Joomunited\WPSOL\SpeedOptimization;

defined('ABSPATH') || die('No direct script access allowed!');
if (!defined('WPSOL_PLUGIN_NAME')) {
    define('WPSOL_PLUGIN_NAME', plugin_basename(__FILE__));
}
if (!defined('WPSOL_MINIMUM_WP_VERSION')) {
    define('WPSOL_MINIMUM_WP_VERSION', '4.0');
}
if (!defined('WPSOL_PLUGIN_URL')) {
    define('WPSOL_PLUGIN_URL', plugin_dir_url(__FILE__));
}
if (!defined('WPSOL_PLUGIN_DIR')) {
    define('WPSOL_PLUGIN_DIR', plugin_dir_path(__FILE__));
}
if (!defined('WPSOL_SITEURL')) {
    define('WPSOL_SITEURL', get_site_url());
}
if (!defined('WPSOL_VERSION')) {
    define('WPSOL_VERSION', '3.3.2');
}
if (!defined('WPSOL_FILE')) {
    define('WPSOL_FILE', __FILE__);
}
if (!defined('WPSOL_MINIFICATION_CACHE')) {
    define('WPSOL_MINIFICATION_CACHE', WP_CONTENT_DIR . '/cache/wpsol-minification/');
}
if (!defined('WPSOL_CACHE_CHILD_DIR')) {
    define('WPSOL_CACHE_CHILD_DIR', '/cache/wpsol-minification/');
}
if (!defined('WPSOL_CACHEFILE_PREFIX')) {
    define('WPSOL_CACHEFILE_PREFIX', 'wpsol_');
}
if (!defined('WPSOL_WP_CONTENT_NAME')) {
    define('WPSOL_WP_CONTENT_NAME', '/' . wp_basename(WP_CONTENT_DIR));
}
define('WPSOL_ROOT_DIR', str_replace(WPSOL_WP_CONTENT_NAME, '', WP_CONTENT_DIR));

include_once(WPSOL_PLUGIN_DIR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

// Check jurequirements
if (is_admin()) {
    //Check plugin requirements
    if (version_compare(PHP_VERSION, '5.6', '<')) {
        if (!function_exists('wpsol_disable_plugin')) {
            /**
             * Plugin disable
             *
             * @return void
             */
            function wpsol_disable_plugin()
            {
                if (current_user_can('activate_plugins') && is_plugin_active(plugin_basename(__FILE__))) {
                    deactivate_plugins(__FILE__);
                    //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- for disable plugins, no need NonceVerification
                    unset($_GET['activate']);
                }
            }
        }

        if (!function_exists('wpsol_show_error')) {
            /**
             * Show error when install
             *
             * @return void
             */
            function wpsol_show_error()
            {
                echo '<div class="error"><p><strong>WP Speed Of Light</strong>
                    need at least PHP 5.6 version, please update php before installing the plugin.</p>
                    </div>';
            }
        }

        //Add actions
        add_action('admin_init', 'wpsol_disable_plugin');
        add_action('admin_notices', 'wpsol_show_error');

        //Do not load anything more
        return;
    }

    // Plugins name for translate
    $args = array(
        'plugin_name' => esc_html__('WP Speed Of Light', 'wp-speed-of-light'),
        'plugin_path' => 'wp-speed-of-light/wp-speed-of-light.php',
        'plugin_textdomain' => 'wp-speed-of-light',
        'requirements' => array(
            'php_version' => '5.6',
            // Minimum addons version
            'addons_version' => array(
                'wpsolAddons' => '2.3.0'
            )
        ),
    );
    $wpsolCheck = call_user_func('\Joomunited\WPSOL\JUCheckRequirements::init', $args);

    if (!$wpsolCheck['success']) {
        //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Do not load anything more
        unset($_GET['activate']);
        return;
    }
}

// analysis queries time
if (function_exists('get_option') &&
    !isset($GLOBALS['WpSoL_DB_Queries']) &&
    basename(__FILE__) !== basename($_SERVER['SCRIPT_FILENAME'])) {
    $opts = get_option('wpsol_profiles_option');
    if (!empty($opts['query_enabled'])) {
        $GLOBALS['WpSoL_DB_Queries'] = new Joomunited\WPSOL\DBQueries();
    }

    unset($opts);
}

$wpsolConfig = new Configuration();

new Integration();

new Rest();

new Rss();

if (is_admin()) {
    call_user_func(
        '\Joomunited\WPSOL\Jutranslation::init',
        __FILE__,
        'wpsol',
        'WP Speed Of Light',
        'wp-speed-of-light',
        'languages' . DIRECTORY_SEPARATOR . 'wp-speed-of-light-en_US.mo'
    );

    register_activation_hook(__FILE__, array('Joomunited\WPSOL\Install\Install', 'pluginActivation'));
    register_deactivation_hook(__FILE__, array('Joomunited\WPSOL\Install\Install', 'pluginDeactivation'));

    new Install();

    new Admin();

    //cache when ecommerce installed
    new Ecommerce();

    $im_exportconfig = new ImportExport();

    // Include jufeedback helpers
    require_once('jufeedback'. DIRECTORY_SEPARATOR . 'jufeedback.php');
    call_user_func(
        '\Joomunited\WPSOL\Jufeedback\Jufeedback::init',
        __FILE__,
        'wpsol',
        'wp-speed-of-light',
        'WP Speed Of Light',
        'wp-speed-of-light'
    );
}

add_action('init', 'wpsol_optimize_data');

if (!function_exists('wpsol_optimize_data')) {
    /**
     * Run minify
     *
     * @return void
     */
    function wpsol_optimize_data()
    {
        $wpsolConfig = new Configuration();
        $current_url = getCurrentUrl();

        $exclude_url = array();
        if (isset($GLOBALS['wpsol_config'])) {
            $opts_config = $GLOBALS['wpsol_config'];
            if (isset($opts_config['disable_page'])) {
                $exclude_url = $opts_config['disable_page'];
            }
        } else {
            $opts_config = get_option('wpsol_optimization_settings');
            if (isset($opts_config['speed_optimization']['disable_page'])) {
                $exclude_url = $opts_config['speed_optimization']['disable_page'];
            }
        }
        $check_exclude = check_exclude_url($exclude_url, $current_url);

        // Compare current url with rules and exclude urls
        if (!$check_exclude) {
            $check_admin = $wpsolConfig->checkAdminRole();

            /**
             * Check user roles to exclude
             *
             * @param boolean Default value
             *
             * @internal
             *
             * @return boolean
             */
            $check_user_roles = apply_filters('wpsol_addon_check_user_roles', false);

            if ($check_admin || $check_user_roles) {
                return ;
            }

            // Disable optimize and cache for admin user
            // Run minification
            if (Cache::createCacheMinificationFolder()) {
                add_action('template_redirect', 'wpsol_start_buffering', 2);
            }
        }
    }
}

SpeedOptimization::factory();
new CleanCacheTime();


// Call back ob start
ob_start('wpsol_ob_start_callback');
/**
 * Call back ob start - stack
 *
 * @param string $buffer Content of page
 *
 * @return mixed|void
 */
function wpsol_ob_start_callback($buffer)
{
    if (is_admin()) {
        return $buffer;
    }

    $conf = get_option('wpsol_optimization_settings');
    $cdn_settings = get_option('wpsol_cdn_integration');

    /**
     * Filter get buffer from minify
     *
     * @param string Content page
     *
     * @internal
     *
     * @return string
     */
    $buffer = apply_filters('wpsol_minify_content_return', $buffer);


    if (!empty($cdn_settings) && !empty($cdn_settings['cdn_active'])) {
        /**
         * Filter get buffer after replace cdn content
         *
         * @param string Content page
         *
         * @internal
         *
         * @return string
         */
        $buffer = apply_filters('wpsol_cdn_content_return', $buffer);
    }

    if (!empty($conf['speed_optimization']['query_strings'])) {
        /**
         * Filter get buffer after remove query strings
         *
         * @param string Content page
         *
         * @internal
         *
         * @return string
         */
        $buffer = apply_filters('wpsol_query_strings_return', $buffer);
    }

    // Add lazy-loading for content
    $current_url = getCurrentUrl();
    $advanced_option = get_option('wpsol_advanced_settings');
    $exclude_url = array();

    if (isset($advanced_option['exclude_lazy_loading'])) {
        $exclude_url = $advanced_option['exclude_lazy_loading'];
    }
    $check_exclude = check_exclude_url($exclude_url, $current_url);

    if (!$check_exclude) {
        /**
         * Filter add lazy-loading for content
         *
         * @param string Content page
         *
         * @internal
         *
         * @return string
         */
        $buffer = apply_filters('wpsol_addon_image_lazy_loading', $buffer, $exclude_url);
    }

    /**
     * Filter add lazy-loading for content
     *
     * @param string Content page
     *
     * @internal
     *
     * @return string
     */
    $buffer = apply_filters('wpsol_addon_iframe_and_video_lazy_loading', $buffer);

    /**
     * Filter add image dimensions
     *
     * @param string Content page
     *
     * @internal
     *
     * @return string
     */
    $buffer = apply_filters('wpsol_addon_sol_specify_image_dimensions', $buffer);
    // Return content
    return $buffer;
}



/**
 *  Start loading
 *
 * @return void
 */
function wpsol_start_buffering()
{
    if (is_admin()) {
        return;
    }

    $pagebuilders = array('tve', 'elementor-preview', 'fl_builder', 'vc_action', 'et_fb', 'bt-beaverbuildertheme', 'ct_builder', 'fb-edit', 'siteorigin_panels_live_editor');
    $noptimize = false;
    foreach ($pagebuilders as $_pagebuilder) {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Check request, not action
        if (array_key_exists($_pagebuilder, $_GET)) {
            $noptimize = true;
            break;
        }
    }

    // Return if visual builder enable
    if ($noptimize) {
        return;
    }

    // Return if the current query is for a feed
    if (is_feed()) {
        return;
    }

    if (is_embed()) {
        return;
    }

    // Check for site being previewed in the Customizer
    if (function_exists('is_customize_preview') && is_customize_preview()) {
        return;
    }

    // Now, start the real thing!
    add_filter('wpsol_minify_content_return', 'wpsol_end_buffering');
}

/**
 * Cache css , js and optimize html when start
 *
 * @param string $content Content of page.
 *
 * @return mixed|void
 */
function wpsol_end_buffering($content)
{
    if (stripos($content, '<html') === false ||
        stripos($content, '<html amp') !== false ||
        stripos($content, '<html ⚡') !== false ||
        stripos($content, '<xsl:stylesheet') !== false) {
        return $content;
    }
    // load URL constants as late as possible to allow domain mapper to kick in
    if (function_exists('domain_mapping_siteurl')) {
        define('WPSOL_WP_SITE_URL', domain_mapping_siteurl(get_current_blog_id()));
        define(
            'WPSOL_WP_CONTENT_URL',
            str_replace(get_original_url(WPSOL_WP_SITE_URL), WPSOL_WP_SITE_URL, content_url())
        );
    } else {
        define('WPSOL_WP_SITE_URL', site_url());
        define('WPSOL_WP_CONTENT_URL', content_url());
    }
    if (is_multisite()) {
        $blog_id = get_current_blog_id();
        define('WPSOL_CACHE_URL', WPSOL_WP_CONTENT_URL . WPSOL_CACHE_CHILD_DIR . $blog_id . '/');
    } else {
        define('WPSOL_CACHE_URL', WPSOL_WP_CONTENT_URL . WPSOL_CACHE_CHILD_DIR);
    }
    define('WPSOL_WP_ROOT_URL', str_replace(WPSOL_WP_CONTENT_NAME, '', WPSOL_WP_CONTENT_URL));

    define('WPSOL_HASH', wp_hash(WPSOL_CACHE_URL));
    // Config element
    $conf = get_option('wpsol_optimization_settings');

    // Check page exclusion
    if (class_exists('\Joomunited\WPSOLADDON\SpeedOptimization')) {
        if (!empty($conf['advanced_features']['wpsol_page_exclusion'])) {
            /**
             * Filter to check page exclusion
             *
             * @param array List of configuration
             *
             * @internal
             *
             * @return array
             */
            $checkPageExclusion = apply_filters('wpsol_addon_check_page_exclusion', getCurrentUrl(), $conf['advanced_features']['wpsol_page_exclusion'], false);
            if ($checkPageExclusion) {
                return $content; // do nothing;
            }
        }
    }

    // Choose the classes
    $classes = array();
    $groupcss = false;
    $groupjs = false;
    $minifyHtml = false;
    $minifyCss = false;
    $minifyJs = false;
    $groupfonts = false;
    $cache_external = false;
    $exclude_js = array();
    $exclude_css = array();
    $excludeInlineStyle = false;
    $moveToFooter = false;
    $excludeScriptMoveToFooter = array();
    $defer_css = false;
    $defer_js = false;
    if (!empty($conf['advanced_features']['html_minification'])) {
        $minifyHtml = true;
    }
    if (!empty($conf['advanced_features']['css_minification'])) {
        $minifyCss = true;
    }
    if (!empty($conf['advanced_features']['js_minification'])) {
        $minifyJs = true;
    }
    if (!empty($conf['advanced_features']['cssgroup_minification'])) {
        $groupcss = true;
    }
    if (!empty($conf['advanced_features']['jsgroup_minification'])) {
        $groupjs = true;
    }
    if (!empty($conf['speed_optimization']['cache_external_script'])) {
        $cache_external = true;
    }
    if (class_exists('\Joomunited\WPSOLADDON\SpeedOptimization')) {
        $exclude = get_option('wpsol_addon_exclude_file_lists');
        if (!empty($conf['advanced_features']['excludefiles_minification'])) {
            $exclude_js = $exclude['js-exclude'];
            $exclude_css = $exclude['css-font-exclude'];
        }
        /**
         * Filter to check google font configuration.
         *
         * @param array List of configuration
         *
         * @internal
         *
         * @return array
         */
        $groupfonts = apply_filters('wpsol_addon_check_group_google_fonts', $conf);
        /**
         * Filter inline script to exclude from minify.
         *
         * @param boolean Default value
         *
         * @internal
         *
         * @return boolean
         */
        $excludeInlineStyle = apply_filters('wpsol_addon_check_exclude_inline_style', false);
        /**
         * Filter parameter to check move minified file to footer.
         *
         * @param boolean Default value
         *
         * @internal
         *
         * @return boolean
         */
        $moveToFooter = apply_filters('wpsol_addon_check_move_script_to_footer', false);
        /**
         * Filter parameter to check exclude script move to footer.
         *
         * @param array Default value
         *
         * @internal
         *
         * @return array
         */
        $excludeScriptMoveToFooter = apply_filters('wpsol_addon_get_exclude_script_move_to_footer', array());

        /**
         * Filter to defer css
         *
         * @param array List of configuration
         *
         * @internal
         *
         * @return array
         */
        $defer_css = apply_filters('wpsol_addon_check_defer_css', $conf);

        /**
         * Filter to defer js
         *
         * @param array List of configuration
         *
         * @internal
         *
         * @return array
         */
        $defer_js = apply_filters('wpsol_addon_check_defer_js', $conf);
    }

    // Check for load class to optimization
    $doOptimizationJS = $minifyJs || $groupjs || $cache_external || $defer_js || $moveToFooter;
    if ($doOptimizationJS) {
        $classes[] = '\\Joomunited\\WPSOL\\Minification\\Scripts';
    }

    $doOptimizationCSS = $minifyCss || $groupcss || $defer_css || $groupfonts;
    if ($doOptimizationCSS) {
        $classes[] = '\\Joomunited\\WPSOL\\Minification\\Styles';
    }

    if ($minifyHtml) {
        $classes[] = '\\Joomunited\\WPSOL\\Minification\\Html';
    }

    if (!$doOptimizationJS && !$doOptimizationCSS && !$minifyHtml) {
        return $content; // do nothing, great!
    }

    // Set some options
    $classoptions = array(
        '\\Joomunited\\WPSOL\\Minification\\Scripts' => array(
            'minify_js' => $minifyJs,
            'group_js' => $groupjs,
            'exclude_js' => $exclude_js,
            'cache_external' => $cache_external,
            //'exclude_inline' => $excludeInlineScript,
            'move_to_script' => $moveToFooter,
            'exclude_move_to_script' => $excludeScriptMoveToFooter,
            'defer' => $defer_js
        ),
        '\\Joomunited\\WPSOL\\Minification\\Styles' => array(
            'minifyCSS' => $minifyCss,
            'css_exclude' => array(),
            'exclude_inline' => $excludeInlineStyle,
            'groupcss' => $groupcss,
            'groupfonts' => $groupfonts,
            'exclude_css' => $exclude_css,
            'defer' => $defer_css,
        ),
        '\\Joomunited\\WPSOL\\Minification\\Html' => array(
            'minifyHTML' => $minifyHtml
        )
    );

    // Run the classes
    foreach ($classes as $name) {
        $instance = new $name($content);

        if ($instance->read($classoptions[$name])) {
            // call for start group/minify file
            $instance->minify();
            $instance->cache();
            $content = $instance->getcontent();
        }
        unset($instance);
    }

    return $content;
}

/**
 * Get current loaded url
 *
 * @return string
 */
function getCurrentUrl()
{
    if (isset($_SERVER['SERVER_PORT']) && isset($_SERVER['HTTP_HOST'])) {
        $domain = (((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
                $_SERVER['SERVER_PORT'] === 443) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        $current_url = $domain . rawurldecode($_SERVER['REQUEST_URI']);
    } else {
        global $wp;
        // phpcs:disable WordPress.Security.NonceVerification.Recommended -- Get only, not check request
        $current_url = home_url(add_query_arg($_GET, $wp->request));
    }

    return $current_url;
}

/**
 * Check url to exclude minify
 *
 * @param array  $exclude_url Url to exclude
 * @param string $current_url Url to check exclude
 *
 * @return boolean
 */
function check_exclude_url($exclude_url, $current_url)
{
    //check disable for page
    if (!empty($exclude_url)) {
        foreach ($exclude_url as $v) {
            if (empty($v)) {
                continue;
            }

            if (preg_match('/(\/?\&?\(\.?\*\)|\(\.\*\)|\/\*|\*)$/', trim($v), $matches)) {
                // End of rules is /*, /(*) , /(.*)
                $pattent = substr($v, 0, strpos($v, $matches[0]));
                if ($v[0] === '/') {
                    // A path of exclude url with regex
                    if ((preg_match('@' . $pattent . '@', $current_url, $matches) > 0)) {
                        return true;
                    }
                } else {
                    // Full exclude url with regex
                    if (strpos($current_url, $pattent) !== false) {
                        return true;
                    }
                }
            } elseif (preg_match('/(\/?\(\.?\*\))/', trim($v), $matches)) {
                // In middle rules is /(*) or /(.*)
                $v = str_replace('(*)', '(.*)', $v);
                if ((preg_match('@' . $v . '@', $current_url, $matches) > 0)) {
                    return true;
                }
            } else {
                if ($v[0] === '/') {
                    // A path of exclude
                    // Fixme:: when we exclude /wordpress/cat_a page, it will exclude all page/post in cat_a, example: /wordpress/cat_a/page_1
                    if ((preg_match('@' . $v . '@', $current_url, $matches) > 0)) {
                        return true;
                    }
                } else {
                    // Whole path
                    if (rtrim($v, '/') === rtrim($current_url, '/')) {
                        return true;
                    }
                }
            }
        }
    }
    return false;
}


// Load Addons
if (isset($wpsolCheck) && !empty($wpsolCheck['load'])) {
    foreach ($wpsolCheck['load'] as $addonName) {
        if (function_exists($addonName . 'Init')) {
            call_user_func($addonName . 'Init');
        }
    }
}
