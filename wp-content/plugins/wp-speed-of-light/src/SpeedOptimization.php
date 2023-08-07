<?php
namespace Joomunited\WPSOL;

if (!defined('ABSPATH')) {
    exit;
}

use Joomunited\WPSOL\Cache\CleanCacheTime;
use Joomunited\WPSOLADDON\CdnIntegration;
use Joomunited\WPSOLADDON\FlushThirdPartyCache;

/**
 * Class SpeedOptimization
 */
class SpeedOptimization
{

    /**
     * SpeedOptimization constructor.
     */
    public function __construct()
    {
    }

    /**
     *  Sett action for class
     *
     * @return void
     */
    public function setAction()
    {
        if (class_exists('\Joomunited\WPSOLADDON\SpeedOptimization')) {
            /**
             * Filter check clean up on save.
             *
             * @param boolean Default value
             *
             * @internal
             *
             * @return boolean
             */
            $check_on_save = apply_filters('wpsol_addon_check_cleanup_on_save', false);

            if ($check_on_save) {
                add_action('pre_post_update', array($this, 'purgePostOnUpdate'), 10, 1);
                add_action('save_post', array($this, 'purgePostOnUpdate'), 10, 1);
            }
        } else {
            add_action('pre_post_update', array($this, 'purgePostOnUpdate'), 10, 1);
            add_action('save_post', array($this, 'purgePostOnUpdate'), 10, 1);
        }

        add_action('wp_trash_post', array($this, 'purgePostOnUpdate'), 10, 1);
        add_action('comment_post', array($this, 'purgePostOnNewComment'), 10, 3);
        add_action('wp_set_comment_status', array($this, 'purgePostOnCommentStatusChange'), 10, 2);
        add_action('set_comment_cookies', array($this, 'setCommentCookieExceptions'), 10, 2);

        // Remove query strings
        add_filter('wpsol_query_strings_return', array($this, 'removeQueryStrings'));
    }



    /**
     * Action when save settings optimization
     *
     * @return void
     */
    public function saveSettings()
    {
        if (current_user_can('manage_options')) {
            WP_Filesystem();

            $opts = get_option('wpsol_optimization_settings');
            //save setting speed optimization
            //phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification -- Check admin referer exist, because have 3 action in function
            if (isset($_REQUEST['action'])) {
                check_admin_referer('wpsol_speed_optimization', '_wpsol_nonce');
                if ('wpsol_save_speedup' === $_REQUEST['action']) {
                    $this->saveSpeedUp($opts);

                    Cache::write();
                    //write config for cache
                    Cache::writeConfigCache();
                    // Reschedule cron events
                    CleanCacheTime::unscheduleClearCache();
                    CleanCacheTime::scheduleClearCache();
                }

                //save settings on wordpress page
                if ('wpsol_save_wordpress' === $_REQUEST['action']) {
                    $this->saveWordpress();
                }

                //save settings on minify page
                if ('wpsol_save_minification' === $_REQUEST['action']) {
                    $this->saveMinification($opts);
                }

                //save settings on advanced optimization page
                if ('wpsol_save_advanced' === $_REQUEST['action']) {
                    if (class_exists('\Joomunited\WPSOLADDON\SpeedOptimization')) {
                        do_action('wpsol_addon_storage_advanced_optimization');
                    }
                }

                //save settings on advanced optimization page
                if ('wpsol_save_woocommerce' === $_REQUEST['action']) {
                    if (class_exists('\Joomunited\WPSOLADDON\SpeedOptimization')) {
                        do_action('wpsol_addon_storage_woocommerce_speedup');
                    }
                }

                // Save cdn settings
                if ('wpsol_save_cdn' === $_REQUEST['action']) {
                    $cdn_content         = array();
                    $cdn_exclude_content = array();
                    if (!empty($_REQUEST['cdn-content'])) {
                        $cdn_content = explode(',', $_REQUEST['cdn-content']);
                    }

                    if (!empty($_REQUEST['cdn-exclude-content'])) {
                        $cdn_exclude_content = explode(',', $_REQUEST['cdn-exclude-content']);
                    }

                    $cdn_settings = array(
                        'cdn_active'          => (isset($_REQUEST['cdn-active'])) ? 1 : 0,
                        'cdn_url'             => $_REQUEST['cdn-url'],
                        'cdn_content'         => $cdn_content,
                        'cdn_exclude_content' => $cdn_exclude_content,
                        'cdn_relative_path'   => (isset($_REQUEST['cdn-relative-path'])) ? 1 : 0
                    );

                    if (class_exists('\Joomunited\WPSOLADDON\CdnIntegration')) {
                        $cdn_settings = apply_filters('wpsol_addon_save_cdn_integration', $cdn_settings, $_REQUEST);
                        CdnIntegration::saveThirdPartyCacheSettings();
                    }

                    update_option('wpsol_cdn_integration', $cdn_settings);
                }

                // Save configuration
                if ('wpsol_save_configuration' === $_REQUEST['action']) {
                    $opts = get_option('wpsol_configuration');
                    if (isset($_POST['disable_user'])) {
                        $opts['disable_user'] = 1;
                    } else {
                        $opts['disable_user'] = 0;
                    }
                    if (isset($_POST['display_clean'])) {
                        $opts['display_clean'] = 1;
                    } else {
                        $opts['display_clean'] = 0;
                    }

                    if (class_exists('\Joomunited\WPSOLADDON\SpeedOptimization')) {
                        $opts = apply_filters('wpsol_addon_save_configuration', $opts, $_REQUEST);
                    }

                    update_option('wpsol_configuration', $opts);

                    //write config for cache
                    Cache::writeConfigCache();
                }

                if ('wpsol_save_database' === $_REQUEST['action']) {
                    if (isset($_POST['clean'])) {
                        update_option('wpsol_clean_database_config', $_POST['clean']);
                        foreach ($_POST['clean'] as $type) {
                            WpsolDatabaseCleanup::cleanSystem($type);
                        }
                    }
                    do_action('wpsol_addon_database_cleanup_save_settings');
                    update_option('wpsol_database_cleanup_count_time', date('Y-m-d H:i:s'));
                }
                //clear cache after save settings
                Cache::wpsolCacheFlush();
                \Joomunited\WPSOL\Minification\Cache::clearMinification();
                // Purge third party cache
                if (is_plugin_active('wp-speed-of-light-addon/wp-speed-of-light-addon.php')) {
                    if (class_exists('Joomunited\WPSOLADDON\FlushThirdPartyCache')) {
                        $third = new FlushThirdPartyCache();
                        $third->runPurgeThirdparty();
                    }
                }

                // update javascript localize variable to preload cache in background after all, DOM content loaded
                update_option('wpsol_preload_after_saved_setting', 'wpsolSavedSetting');

                /**
                 * Redirect back to the settings page that was submitted
                 */
                if (isset($_REQUEST['_wp_http_referer']) && isset($_REQUEST['page-redirect'])) {
                    wp_safe_redirect(admin_url('admin.php?page=wpsol_speed_optimization&p='.$_REQUEST['page-redirect'].'&settings-updated=success#'.$_REQUEST['page-redirect']));
                    exit;
                }
            }
        }
    }

    /**
     * Automatically purge all file based page cache on post changes
     *
     * @param integer $post_id ID of post
     *
     * @return void
     */
    public function purgePostOnUpdate($post_id)
    {
        $post_type = get_post_type($post_id);
        if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || 'revision' === $post_type) {
            return;
        } elseif (!current_user_can('edit_post', $post_id) && (!defined('DOING_CRON') || !DOING_CRON)) {
            return;
        }

        $config = get_option('wpsol_optimization_settings');

        /**
         * Delete cache after update post or page
         *
         * @param array Type action and attachment ID
         *
         * @ignore Hook already documented
         */
        do_action('wpsol_purge_cache', array('type' => 'post_update','post_id' => $post_id));

        // File based caching only
        if (!empty($config) && !empty($config['speed_optimization']['act_cache'])) {
            Cache::wpsolCacheFlush();
        }
        Minification\Cache::clearMinification();

        // preload cache after cleared
        \Joomunited\WPSOL\Configuration::startPreloadCache();
    }

    /**
     * Purge cache con new comment
     *
     * @param integer $comment_ID  ID of comment
     * @param integer $approved    Approved
     * @param string  $commentdata Content of comment
     *
     * @return void
     */
    public function purgePostOnNewComment($comment_ID, $approved, $commentdata)
    {
        if (empty($approved)) {
            return;
        }
        $config = get_option('wpsol_optimization_settings');



        // File based caching only
        if (!empty($config) && !empty($config['speed_optimization']['act_cache'])) {
            $post_id = $commentdata['comment_post_ID'];

            /**
             * Delete cache after approve new comment
             *
             * @param array Type action, comment ID, comment data
             *
             * @ignore Hook already documented
             */
            do_action(
                'wpsol_purge_cache',
                array('type' => 'new_comment',
                      'comment_id' => $comment_ID,
                      'comment_data' => $commentdata)
            );

            global $wp_filesystem;

            if (empty($wp_filesystem)) {
                require_once(ABSPATH . '/wp-admin/includes/file.php');
                WP_Filesystem();
            }

            $url_path = get_permalink($post_id);
            if ($wp_filesystem->exists(untrailingslashit(WP_CONTENT_DIR) . '/cache/wpsol-cache/' . md5($url_path))) {
                $wp_filesystem->rmdir(untrailingslashit(WP_CONTENT_DIR) . '/cache/wpsol-cache/' . md5($url_path), true);
            }
        }
    }

    /**
     * If a comments status changes, purge it's parent posts cache
     *
     * @param integer $comment_ID     ID of comment
     * @param boolean $comment_status Status of commnet
     *
     * @return void
     */
    public function purgePostOnCommentStatusChange($comment_ID, $comment_status)
    {
        $config = get_option('wpsol_optimization_settings');

        // File based caching only
        if (!empty($config) && !empty($config['speed_optimization']['act_cache'])) {
            $comment = get_comment($comment_ID);
            $post_id = $comment->comment_post_ID;

            /**
             * Delete cache after changing comment status
             *
             * @param array Type action, comment ID, comment status
             *
             * @ignore Hook already documented
             */
            do_action(
                'wpsol_purge_cache',
                array('type'=> 'comment_update', 'comment_id' => $comment_ID, 'comment_status' => $comment_status)
            );

            global $wp_filesystem;

            if (empty($wp_filesystem)) {
                require_once(ABSPATH . '/wp-admin/includes/file.php');
                WP_Filesystem();
            }

            $url_path = get_permalink($post_id);

            if ($wp_filesystem->exists(untrailingslashit(WP_CONTENT_DIR) . '/cache/wpsol-cache/' . md5($url_path))) {
                $wp_filesystem->rmdir(untrailingslashit(WP_CONTENT_DIR) . '/cache/wpsol-cache/' . md5($url_path), true);
            }
        }
    }
    /**
     * Save settings physical
     *
     * @param array $opts Optionm speed optimization
     *
     * @return void
     */
    public function saveSpeedUp($opts)
    {
        check_admin_referer('wpsol_speed_optimization', '_wpsol_nonce');

        if (file_exists(ABSPATH . 'wp-config.php') && is_writable(ABSPATH . 'wp-config.php')) {
            /**
             * The config file resides in ABSPATH
             */
            $config_is_writeable = true;
        } elseif (file_exists(dirname(ABSPATH) . '/wp-config.php') && is_writable(dirname(ABSPATH) . '/wp-config.php')) {
            /**
             * The config file resides one level above ABSPATH but is not part of another installation
             */
            $config_is_writeable = true;
        } else {
            // A config file doesn't exist or isn't writeable
            $config_is_writeable = false;
        }

        if ($config_is_writeable) {
            if (isset($_REQUEST['active-cache'])) {
                $opts['speed_optimization']['act_cache'] = 1;
                Cache::toggleCaching(true);
            } else {
                $opts['speed_optimization']['act_cache'] = 0;
                Cache::toggleCaching(false);
            }
        } else {
            $opts['speed_optimization']['act_cache'] = 0;
        }
        if (isset($_REQUEST['cache-desktop'])) {
            $opts['speed_optimization']['devices']['cache_desktop'] = (int)$_REQUEST['cache-desktop'];
        }
        if (isset($_REQUEST['cache-tablet'])) {
            $opts['speed_optimization']['devices']['cache_tablet'] = (int)$_REQUEST['cache-tablet'];
        }
        if (isset($_REQUEST['cache-mobile'])) {
            $opts['speed_optimization']['devices']['cache_mobile'] = (int)$_REQUEST['cache-mobile'];
        }

        if (isset($_REQUEST['add-expires'])) {
            $opts['speed_optimization']['add_expires'] = 1;
            $this->addExpiresHeader(true);
        } else {
            $opts['speed_optimization']['add_expires'] = 0;
            $this->addExpiresHeader(false);
        }

        if (isset($_REQUEST['cache_external_script'])) {
            $opts['speed_optimization']['cache_external_script'] = 1;
        } else {
            $opts['speed_optimization']['cache_external_script'] = 0;
        }
        // Update advanced option
        $advanced = get_option('wpsol_advanced_settings');
        if (isset($_REQUEST['lazy-loading'])) {
            $advanced['lazy_loading'] = 1;
        } else {
            $advanced['lazy_loading'] = 0;
        }

        if (isset($_REQUEST['iframe-video-lazy-loading'])) {
            $advanced['iframe_video_lazy_loading'] = 1;
        } else {
            $advanced['iframe_video_lazy_loading'] = 0;
        }

        if (isset($_REQUEST['image_dimensions'])) {
            $advanced['image_dimensions'] = 1;
        } else {
            $advanced['image_dimensions'] = 0;
        }

        $exclude_lazy = array();
        if (isset($_REQUEST['exclude-lazyloading-url'])) {
            if (!empty($_REQUEST['exclude-lazyloading-url'])) {
                $input = sanitize_textarea_field($_REQUEST['exclude-lazyloading-url']);
                $input = rawurldecode($input);
                $input = trim($input);
                $input = str_replace(' ', '', $input);
                $input = explode("\n", $input);
                $exclude_lazy = array_map(array($this, 'trimList'), $input);
            }
        }
        $advanced['exclude_lazy_loading'] = $exclude_lazy;

        if (class_exists('\Joomunited\WPSOLADDON\SpeedOptimization')) {
            /**
             * Filter configuration to store database.
             *
             * @param array List of configuration
             * @param array Request server
             *
             * @internal
             *
             * @return array
             */
            $advanced = apply_filters('wpsol_addon_storage_settings_advanced', $advanced, $_REQUEST);
        }

        update_option('wpsol_advanced_settings', $advanced);
        //
        if (isset($_REQUEST['clean-cache-frequency'])) {
            $opts['speed_optimization']['clean_cache'] = (int)$_REQUEST['clean-cache-frequency'];
        } else {
            $opts['speed_optimization']['clean_cache'] = 0;
        }

        $opts['speed_optimization']['clean_cache_each_params'] = (int)$_REQUEST['clean-cache-each-params'];

        if (isset($_POST['disable_page'])) {
            $input = $_POST['disable_page'];
            //decode url when insert russian character to input text
            $input = rawurldecode($input);
            $input = trim($input);
            $input = str_replace(' ', '', $input);
            $input = explode("\n", $input);

            $opts['speed_optimization']['disable_page'] = $input;
        } else {
            $opts['speed_optimization']['disable_page'] = array();
        }

        if (class_exists('\Joomunited\WPSOLADDON\SpeedOptimization')) {
            /**
             * Filter configuration to store database.
             *
             * @param array List of configuration
             * @param array Request server
             *
             * @internal
             *
             * @return array
             */
            $opts = apply_filters('wpsol_addon_storage_settings_speedup', $opts, $_REQUEST);
        }

        //disabled cache mobile and tablet when other mobile plugin installed
        if (file_exists(WP_PLUGIN_DIR . '/wp-mobile-detect/wp-mobile-detect.php') ||
            file_exists(WP_PLUGIN_DIR . '/wp-mobile-edition/wp-mobile-edition.php') ||
            file_exists(WP_PLUGIN_DIR . '/wptouch/wptouch.php') ||
            file_exists(WP_PLUGIN_DIR . '/wiziapp-create-your-own-native-iphone-app/wiziapp.php') ||
            file_exists(WP_PLUGIN_DIR . '/wordpress-mobile-pack/wordpress-mobile-pack.php')
        ) {
            $opts['speed_optimization']['devices']['cache_tablet'] = 3;
            $opts['speed_optimization']['devices']['cache_mobile'] = 3;
        }

        update_option('wpsol_optimization_settings', $opts);
    }

    /**
     * Trim item
     *
     * @param string $item Item need to trim
     *
     * @return string
     */
    protected function trimList($item)
    {
        return rtrim($item, ";\n\t\r");
    }

    /**
     * Save setting of Wordpress tab
     *
     * @return void
     */
    public function saveWordpress()
    {
        check_admin_referer('wpsol_speed_optimization', '_wpsol_nonce');
        $opts = get_option('wpsol_optimization_settings');
        $advanced = get_option('wpsol_advanced_settings');

        if (isset($_REQUEST['query-strings'])) {
            $opts['speed_optimization']['query_strings'] = 1;
        } else {
            $opts['speed_optimization']['query_strings'] = 0;
        }

        if (isset($_REQUEST['remove_rest_api'])) {
            $opts['speed_optimization']['remove_rest_api'] = 1;
        } else {
            $opts['speed_optimization']['remove_rest_api'] = 0;
        }

        if (isset($_REQUEST['remove_rss_feed'])) {
            $opts['speed_optimization']['remove_rss_feed'] = 1;
        } else {
            $opts['speed_optimization']['remove_rss_feed'] = 0;
        }

        if (isset($_REQUEST['remove-emojis'])) {
            $advanced['remove_emojis'] = 1;
        } else {
            $advanced['remove_emojis'] = 0;
        }
        if (isset($_REQUEST['disable-gravatar'])) {
            $advanced['disable_gravatar'] = 1;
            if (!file_exists(WPSOL_UPLOAD_AVATAR)) {
                mkdir(WPSOL_UPLOAD_AVATAR, 0777, true);
            }
        } else {
            $advanced['disable_gravatar'] = 0;
        }

        update_option('wpsol_optimization_settings', $opts);
        update_option('wpsol_advanced_settings', $advanced);
    }

    /**
     * Save save_settings_advanced_features
     *
     * @param array $opts Option
     *
     * @return void
     */
    public function saveMinification($opts)
    {
        check_admin_referer('wpsol_speed_optimization', '_wpsol_nonce');

        if (isset($_REQUEST['html-minification'])) {
            $opts['advanced_features']['html_minification'] = 1;
        } else {
            $opts['advanced_features']['html_minification'] = 0;
        }
        if (isset($_REQUEST['css-minification'])) {
            $opts['advanced_features']['css_minification'] = 1;
        } else {
            $opts['advanced_features']['css_minification'] = 0;
        }
        if (isset($_REQUEST['js-minification'])) {
            $opts['advanced_features']['js_minification'] = 1;
        } else {
            $opts['advanced_features']['js_minification'] = 0;
        }
        if (isset($_REQUEST['cssgroup-minification'])) {
            $opts['advanced_features']['cssgroup_minification'] = 1;
        } else {
            $opts['advanced_features']['cssgroup_minification'] = 0;
        }
        if (isset($_REQUEST['jsgroup-minification'])) {
            $opts['advanced_features']['jsgroup_minification'] = 1;
        } else {
            $opts['advanced_features']['jsgroup_minification'] = 0;
        }

        if (class_exists('\Joomunited\WPSOLADDON\SpeedOptimization')) {
            /**
             * Handle exclude file from text area to store database.
             *
             * @internal
             */
            do_action('wpsol_addon_handle_textarea_exclude_file', $_REQUEST['wpsol-exclude-files-minification']);
            /**
             * Handle textarea input and store exclude from defer css file path in database
             *
             * @internal
             */
            do_action('wpsol_addon_store_file_exclude_from_defer_css', $_REQUEST['exclude-from-defer-css']);
            /**
             * Filter configuration to store database.
             *
             * @param array List of configuration
             * @param array Request server
             *
             * @internal
             *
             * @return array
             */
            $opts = apply_filters('wpsol_addon_storage_settings_minify', $opts, $_REQUEST);
        }

        if (isset($_REQUEST['cssgroup-minification']) || isset($_REQUEST['jsgroup-minification'])) {
            update_option('wpsol_file_group_activation_popup_settings', 1);
        }

        update_option('wpsol_optimization_settings', $opts);
    }

    /**
     * When user posts a comment, set a cookie so we don't show them page cache
     *
     * @param string  WP_Comment $comment Comment of usser
     * @param integer WP_User    $user    Id user
     *
     * @return void
     */
    public function setCommentCookieExceptions($comment, $user)
    {
        $config = get_option('wpsol_optimization_settings');
        // File based caching only
        if (!empty($config) && !empty($config['speed_optimization']['act_cache'])) {
            $post_id = $comment->comment_post_ID;

            setcookie(
                'wpsol_commented_posts[' . $post_id . ']',
                parse_url(get_permalink($post_id), PHP_URL_PATH),
                (time() + HOUR_IN_SECONDS * 24 * 30)
            );
        }
    }

    /**
     * Write gzip htaccess to .htaccess
     *
     * @param boolean $check Check to add gzip htaccess
     *
     * @return boolean
     */
    public static function addGzipHtacess($check)
    {
        $htaccessFile = ABSPATH . DIRECTORY_SEPARATOR . '.htaccess';
        $htaccessContent = '';
        $data = '#WP Speed of Light Gzip compression activation
<IfModule mod_deflate.c>
# Launch the compression
SetOutputFilter DEFLATE
# Force deflate for mangled headers
<IfModule mod_setenvif.c>
<IfModule mod_headers.c>
SetEnvIfNoCase ^(Accept-EncodXng|X-cept-Encoding|X{15}|~{15}|-{15})$';
        $data .= ' ^((gzip|deflate)\s*,?\s*)+|[X~-]{4,13}$ HAVE_Accept-Encoding
RequestHeader append Accept-Encoding "gzip,deflate" env=HAVE_Accept-Encoding
# Remove non-compressible file type
SetEnvIfNoCase Request_URI \
\.(?:gif|jpe?g|png|rar|zip|exe|flv|mov|wma|mp3|avi|swf|mp?g|mp4|webm|webp)$ no-gzip dont-vary
</IfModule>
</IfModule>

# Regroup compressed resource in MIME-types
<IfModule mod_filter.c>
AddOutputFilterByType DEFLATE application/atom+xml \
		                          application/javascript \
		                          application/json \
		                          application/rss+xml \
		                          application/vnd.ms-fontobject \
		                          application/x-font-ttf \
		                          application/xhtml+xml \
		                          application/xml \
		                          font/opentype \
		                          image/svg+xml \
		                          image/x-icon \
		                          text/css \
		                          text/html \
		                          text/plain \
		                          text/x-component \
		                          text/xml
</IfModule>
<IfModule mod_headers.c>
Header append Vary: Accept-Encoding
</IfModule>
</IfModule>

<IfModule mod_mime.c>
AddType text/html .html_gzip
AddEncoding gzip .html_gzip
</IfModule>
<IfModule mod_setenvif.c>
SetEnvIfNoCase Request_URI \.html_gzip$ no-gzip
</IfModule>
#End of WP Speed of Light Gzip compression activation' . PHP_EOL;
        if ($check) {
            if (!is_super_admin()) {
                return false;
            }
            //open htaccess file

            if (is_writable($htaccessFile)) {
                $htaccessContent = file_get_contents($htaccessFile);
            }

            if (empty($htaccessContent)) {
                return false;
            }
            //if isset Gzip access
            if (strpos($htaccessContent, 'mod_deflate') !== false ||
                strpos($htaccessContent, 'mod_setenvif') !== false ||
                strpos($htaccessContent, 'mod_headers') !== false ||
                strpos($htaccessContent, 'mod_mime') !== false ||
                strpos($htaccessContent, '#WP Speed of Light Gzip compression activation') !== false) {
                return false;
            }

            $htaccessContent = $data . $htaccessContent;
            file_put_contents($htaccessFile, $htaccessContent);
            return true;
        } else {
            if (!is_super_admin()) {
                return true;
            }
            //open htaccess file
            if (is_writable($htaccessFile)) {
                $htaccessContent = file_get_contents($htaccessFile);
            }
            if (empty($htaccessContent)) {
                return false;
            }

            $htaccessContent = str_replace($data, '', $htaccessContent);
            file_put_contents($htaccessFile, $htaccessContent);
            return true;
        }
    }

    /**
     * Write expires header to .htaccess
     *
     * @param boolean $check Check to add Expires header
     *
     * @return boolean
     */
    public static function addExpiresHeader($check)
    {
        $htaccessFile = ABSPATH . DIRECTORY_SEPARATOR . '.htaccess';
        $htaccessContent = '';
        $expires = '#Expires headers configuration added by Speed of Light plugin' . PHP_EOL .
            '<IfModule mod_expires.c>' . PHP_EOL .
            '   ExpiresActive On' . PHP_EOL .
            '   ExpiresDefault A2592000' . PHP_EOL .
            '   ExpiresByType application/javascript "access plus 30 days"' . PHP_EOL .
            '   ExpiresByType text/javascript "access plus 30 days"' . PHP_EOL .
            '   ExpiresByType text/css "access plus 30 days"' . PHP_EOL .
            '   ExpiresByType image/jpeg "access plus 30 days"' . PHP_EOL .
            '   ExpiresByType image/png "access plus 30 days"' . PHP_EOL .
            '   ExpiresByType image/gif "access plus 30 days"' . PHP_EOL .
            '   ExpiresByType image/ico "access plus 30 days"' . PHP_EOL .
            '   ExpiresByType image/x-icon "access plus 30 days"' . PHP_EOL .
            '   ExpiresByType image/svg+xml "access plus 30 days"' . PHP_EOL .
            '   ExpiresByType image/bmp "access plus 30 days"' . PHP_EOL .
            '</IfModule>' . PHP_EOL .
            '#End of expires headers configuration' . PHP_EOL;

        if ($check) {
            if (!is_super_admin()) {
                return false;
            }
            //open htaccess file
            if (is_writable($htaccessFile)) {
                $htaccessContent = file_get_contents($htaccessFile);
            }


            if (empty($htaccessContent)) {
                return false;
            }
            //if isset expires header in htacces
            if (strpos($htaccessContent, 'mod_expires') !== false ||
                strpos($htaccessContent, 'ExpiresActive') !== false ||
                strpos($htaccessContent, 'ExpiresDefault') !== false ||
                strpos($htaccessContent, 'ExpiresByType') !== false) {
                return false;
            }

            $htaccessContent = $expires . $htaccessContent;
            file_put_contents($htaccessFile, $htaccessContent);
            return true;
        } else {
            if (!is_super_admin()) {
                return false;
            }
            //open htaccess file
            if (is_writable($htaccessFile)) {
                $htaccessContent = file_get_contents($htaccessFile);
            }
            if (empty($htaccessContent)) {
                return false;
            }
            $htaccessContent = str_replace($expires, '', $htaccessContent);
            file_put_contents($htaccessFile, $htaccessContent);
            return true;
        }
    }
    /**
     * Remove query string from static resources
     *
     * @param string $content Content raw
     *
     * @return mixed
     */
    public function removeQueryStrings($content)
    {
        if ($content !== '') {
            $blog_regexp = self::blogDomainRootUrlRegexp();

            if (!$blog_regexp) {
                return $content;
            }
            $pattern = '~(href|src)=?([\'"])((' .
                $blog_regexp .
                ')?(/[^\'"/][^\'"]*\.([a-z-_]+)([\?#][^\'"]*)?))[\'"]~Ui';
            $content = preg_replace_callback(
                $pattern,
                array($this, 'queryStringsReplaceCallback'),
                $content
            );
        }

        return $content;
    }

    /**
     * Callback replace for js and css file
     *
     * @param string $matches Matches of query string
     *
     * @return string
     */
    public function queryStringsReplaceCallback($matches)
    {
        list ($match, $attr, $quote, $url, , , $extension) = $matches;

        if ($extension === 'js' || $extension === 'css') {
            $url = preg_replace('/[&\?]+(ver=([a-z0-9-_\.]+|[0-9-]+))+[&\?]*([a-z0-9-_=]*)*/i', '', $url);
        }
        return $attr . '=' . $quote . $url . $quote;
    }

    /**
     * Returns domain url regexp
     *
     * @return string
     */
    public static function blogDomainRootUrlRegexp()
    {
        $home_url = get_option('home');
        $parse_url = parse_url($home_url);

        if ($parse_url && isset($parse_url['scheme']) && isset($parse_url['host'])) {
            $scheme = $parse_url['scheme'];
            $host = $parse_url['host'];
            $port = (isset($parse_url['port']) && $parse_url['port'] !== 80 ? ':' . (int)$parse_url['port'] : '');
            $domain_url = sprintf('[%s:]*//%s%s', $scheme, $host, $port);

            return $domain_url;
        }

        return false;
    }

    /**
     * Parse module info.
     * Based on https://gist.github.com/sbmzhcn/6255314
     *
     * @return array
     */
    public static function parsePhpinfo()
    {
        ob_start();
        //phpcs:ignore WordPress.PHP.DevelopmentFunctions.prevent_path_disclosure_phpinfo -- Get info modules of phpinfo
        phpinfo(INFO_MODULES);
        $s = ob_get_contents();
        ob_end_clean();
        $s = strip_tags($s, '<h2><th><td>');
        $s = preg_replace('/<th[^>]*>([^<]+)<\/th>/', '<info>\1</info>', $s);
        $s = preg_replace('/<td[^>]*>([^<]+)<\/td>/', '<info>\1</info>', $s);
        $t = preg_split('/(<h2[^>]*>[^<]+<\/h2>)/', $s, -1, PREG_SPLIT_DELIM_CAPTURE);
        $r = array();
        $count = count($t);
        $p1 = '<info>([^<]+)<\/info>';
        $p2 = '/'.$p1.'\s*'.$p1.'\s*'.$p1.'/';
        $p3 = '/'.$p1.'\s*'.$p1.'/';
        for ($i = 1; $i < $count; $i++) {
            if (preg_match('/<h2[^>]*>([^<]+)<\/h2>/', $t[$i], $matchs)) {
                $name = trim($matchs[1]);
                $vals = explode("\n", $t[$i + 1]);
                foreach ($vals as $val) {
                    if (preg_match($p2, $val, $matchs)) { // 3cols
                        $r[$name][trim($matchs[1])] = array(trim($matchs[2]), trim($matchs[3]));
                    } elseif (preg_match($p3, $val, $matchs)) { // 2cols
                        $r[$name][trim($matchs[1])] = trim($matchs[2]);
                    }
                }
            }
        }
        return $r;
    }

    /**
     * Get header response
     *
     * @param string $url URL to get header
     *
     * @return array
     */
    public static function getHeadersResponse($url)
    {
        $args = array(
            'headers' => array(
                'timeout' => 30,
                'redirection' => 10,
            )
        );
        // Retrieve the raw response from the HTTP request
        $response = wp_remote_get($url, $args);
        if (is_wp_error($response)) {
            $headers = esc_html__('Some requirements can’t be checked :', 'wp-speed-of-light') . '<br/>' . $response->get_error_message();
        } else {
            $headers = wp_remote_retrieve_headers($response);
        }

        return $headers;
    }

    /**
     * Check modules enable in system
     *
     * @return void
     */
    public static function modulesCheck()
    {
        check_ajax_referer('wpsolSpeedOptimizationSystem', 'ajaxnonce');

        global $is_apache;

        $modules = array(
            'mod_expires' => false,
            'mod_headers' => false,
            'mod_deflate' => false,
            'mod_filter' => false,
            'gzip'=> false,
        );

        $check_success = 0;
        // Apache server
        if ($is_apache) {
            foreach ($modules as $mod => $val) {
                if ($mod === 'gzip') {
                    continue;
                }
                if (apache_mod_loaded($mod)) {
                    $check_success++;
                    $modules[$mod] = true;
                }
            }
        } else {
            // Other server is not support and remove warning
            $modules = array(
                'mod_expires' => true,
                'mod_headers' => true,
                'mod_deflate' => true,
                'mod_filter' => true,
                'gzip'=> true,
            );
            echo json_encode(array('list_modules' => $modules, 'error' => false));
            die();
        }

        $headers = self::getHeadersResponse(WPSOL_PLUGIN_URL . '/assets/file.php');

        if (is_string($headers)) {
            echo json_encode(array('list_modules' => $modules, 'error' => $headers));
            die();
        }

        //Get gzip status
        if (!empty($headers)) {
            foreach ($headers as $header => $value) {
                if (trim(strtolower($header)) === 'content-encoding') {
                    $modules['gzip'] = true;
                    break;
                }
            }
        }

        // If apache_get_modules disable and phpinfo can not detect
        if ($check_success < 4) {
            if (!empty($headers)) {
                foreach ($headers as $header => $value) {
                    if (trim(strtolower($header)) === 'expires') {
                        $modules['mod_expires'] = true;
                        continue;
                    }
                    if (trim(strtolower($header)) === 'wpsol-header-module') {
                        $modules['mod_headers'] = true;
                        continue;
                    }
                    if (trim(strtolower($header)) === 'wpsol-deflate-module' || trim(strtolower($header)) === 'accept-ranges') {
                        $modules['mod_filter'] = true;
                        $modules['mod_deflate'] = true;
                        continue;
                    }
                }
            }
        }

        echo json_encode(array('list_modules' => $modules, 'error' => false));
        die();
    }

    /**
     * Return an instance of the current class, create one if it doesn't exist
     *
     * @since  1.0
     * @return object
     */
    public static function factory()
    {

        static $instance;

        if (!$instance) {
            $instance = new self();
            $instance->setAction();
        }

        return $instance;
    }
}
