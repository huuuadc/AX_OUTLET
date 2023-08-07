<?php
if (!defined('ABSPATH')) {
    exit;
}
$optimization = get_option('wpsol_optimization_settings');
//disabled cache mobile and tablet when other mobile plugin installed
$disabled = '';
if (file_exists(WP_PLUGIN_DIR . '/wp-mobile-detect/wp-mobile-detect.php') ||
    file_exists(WP_PLUGIN_DIR . '/wp-mobile-edition/wp-mobile-edition.php') ||
    file_exists(WP_PLUGIN_DIR . '/wptouch/wptouch.php') ||
    file_exists(WP_PLUGIN_DIR . '/wiziapp-create-your-own-native-iphone-app/wiziapp.php') ||
    file_exists(WP_PLUGIN_DIR . '/wordpress-mobile-pack/wordpress-mobile-pack.php')
) {
    $disabled = 'disabled="disabled"';
}
$parameters = array('Days', 'Hours', 'Minutes');

$disabled_addon_class = 'addon-disabled';
$disabled_addon_attr = 'disabled="disabled"';
$disabled_panel = '';
if (class_exists('\Joomunited\WPSOLADDON\SpeedOptimization')) {
    $disabled_addon_class = '';
    $disabled_addon_attr = '';
    $disabled_panel = 'pannel-addon-enabled';
}

$advanced = get_option('wpsol_advanced_settings');

if (file_exists(ABSPATH . 'wp-config.php') && is_writable(ABSPATH . 'wp-config.php')) {
    /**
     * The config file resides in ABSPATH
     */
    $disable_cache = '';
} elseif (file_exists(dirname(ABSPATH) . '/wp-config.php') && is_writable(dirname(ABSPATH) . '/wp-config.php')) {
    /**
     * The config file resides one level above ABSPATH but is not part of another installation
     */
    $disable_cache = '';
} else {
    // A config file doesn't exist or isn't writeable
    $disable_cache = 'disabled="disabled"';
}
?>


<div class="content-speedup wpsol-optimization">
    <form method="post">
        <input type="hidden" name="action" value="wpsol_save_speedup" />
        <input type="hidden" name="page-redirect" value="speedup" />
        <?php wp_nonce_field('wpsol_speed_optimization', '_wpsol_nonce'); ?>
        <div class="title">
            <label><?php esc_html_e('Speed Optimization', 'wp-speed-of-light')?></label>
        </div>

        <?php //phpcs:ignore WordPress.Security.NonceVerification -- Check request, no action
        if (isset($_REQUEST['settings-updated']) && $_REQUEST['settings-updated'] && isset($_REQUEST['p']) && $_REQUEST['p'] === 'speedup') : ?>
            <div id="message-speedup" class="ju-notice-success message-optimize">
                <strong><?php esc_html_e('Setting saved successfully', 'wp-speed-of-light'); ?></strong></div>
        <?php endif; ?>

        <div class="content">
            <div class="left">
                <ul class="field">
                    <li class="ju-settings-option full-width">
                        <label for="active-cache" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Cache activation will speedup your website by pre-loading
                                common page elements and database queries', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Activate cache system', 'wp-speed-of-light') ?></label>

                        <div class="ju-switch-button">
                            <label class="switch">
                                <input type="checkbox" id="active-cache"
                                       name="active-cache"
                                       value="1"
                                    <?php echo esc_attr($disable_cache); ?>
                                    <?php
                                    if (!empty($optimization)) {
                                        if ($optimization['speed_optimization']['act_cache'] === 1) {
                                            echo 'checked="checked"';
                                        }
                                    }?> >
                                <div class="slider"></div>
                            </label>
                        </div>
                        <?php if (!empty($disable_cache)) : ?>
                            <label class="notice-not-writable">
                                <?php
                                printf(
                                    esc_html__('wp-config.php file is not writable, cache can\'t be activated or deactivated through the plugin see the documentation %1$s', 'wp-speed-of-light'),
                                    '<a href="'.esc_url('https://www.joomunited.com/wordpress-documentation/wp-speed-of-light/251-wp-speed-of-light-speed-optimization#toc-how-to-add-cache-manually-').'" target="_blank" style="text-decoration: none">' . esc_html__('how to activate cache', 'wp-speed-of-light') . '</a>'
                                )
                                ?>
                            </label>
                        <?php endif; ?>
                    </li>

                    <?php
                    $checked = '';
                    if (!empty($optimization['speed_optimization']['cleanup_on_save'])) {
                        $checked = 'checked = "checked"';
                    }
                    ?>
                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="cleanup-on-save" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Clean cache automatically
              when saving a WordPress content like post, page...', 'wp-speed-of-light') ?>"
                        > <?php esc_html_e('Cleanup on save', 'wp-speed-of-light') ?></label>
                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>
                        <div class="ju-switch-button" >
                            <label class="switch ">
                                <input type="checkbox" class="wpsol-optimization" id="cleanup-on-save" name="cleanup-on-save"
                                    <?php echo esc_attr($disabled_addon_attr) ?>
                                    <?php echo esc_attr($checked) ?> value="1" />
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>
                    <li class="ju-settings-option full-width">
                        <label for="clean-cache-frequency" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Automatically cleanup the cache stored each x minutes and
                                generate a new version instantly', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Clean each', 'wp-speed-of-light') ?></label>
                        <div class="ju-input-select">
                            <input type="text"  id="clean-cache-frequency" size="2" style="vertical-align: middle;"
                                   name="clean-cache-frequency" class="clean-frequency ju-input"
                                   value="<?php echo esc_attr($optimization['speed_optimization']['clean_cache']); ?>">
                            <select name="clean-cache-each-params" class="clean-frequency-params ju-select">
                                <?php
                                foreach ($parameters as $k => $v) {
                                    $selected = '';
                                    if ($k === $optimization['speed_optimization']['clean_cache_each_params']) {
                                        $selected = 'selected = "selected"';
                                    }
                                    echo '<option value="' . esc_html($k) . '" ' . esc_attr($selected) . '>' . esc_html($v) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </li>
                    <li class="ju-settings-option full-width field-block">
                        <label for="cache-desktop" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Serve the cache for desktop for all devices: Recommended,
                                unless wrong cache version is served', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Cache for desktop', 'wp-speed-of-light') ?></label>
                        <div class="custom-optimization">
                            <ul class="horizontal">
                                <li><input type="radio" name="cache-desktop" id="cache-desktop1"
                                           value="1"
                                        <?php
                                        if (isset($optimization['speed_optimization']['devices']['cache_desktop'])) {
                                            if ($optimization['speed_optimization']['devices']['cache_desktop'] === 1) {
                                                echo 'checked="checked"';
                                            }
                                        } ?>
                                           class="ju-radiobox"/>
                                    <label for="cache-desktop1" class="radio-label"><?php esc_html_e('Activated', 'wp-speed-of-light') ?></label></li>
                                <li><input type="radio" name="cache-desktop" id="cache-desktop2"
                                           value="2"
                                        <?php
                                        if (isset($optimization['speed_optimization']['devices']['cache_desktop'])) {
                                            if ($optimization['speed_optimization']['devices']['cache_desktop'] === 2) {
                                                echo 'checked="checked"';
                                            }
                                        } ?>
                                           class="ju-radiobox"/>
                                    <label for="cache-desktop2" class="radio-label"><?php esc_html_e('No cache for desktop', 'wp-speed-of-light') ?></label></li>
                            </ul>
                        </div>
                    </li>
                    <li class="ju-settings-option full-width field-block">
                        <label for="cache-tablet" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Serve the cache for tablet: Recommended ONLY if you’re 
                               experiencing wrong cache version served', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Cache for tablet', 'wp-speed-of-light') ?></label>
                        <div class="custom-optimization">
                            <ul>
                                <li><input type="radio" name="cache-tablet" id="cache-tablet1" <?php echo esc_attr($disabled); ?>
                                           value="1"
                                        <?php
                                        if (isset($optimization['speed_optimization']['devices']['cache_tablet'])) {
                                            if ($optimization['speed_optimization']['devices']['cache_tablet'] === 1) {
                                                echo 'checked="checked"';
                                            }
                                        } ?>
                                           class="ju-radiobox"/>
                                    <label for="cache-tablet1" class="radio-label"><?php esc_html_e('Automatic (same as desktop)', 'wp-speed-of-light') ?></label></li>
                                <li><input type="radio" name="cache-tablet" id="cache-tablet2" <?php echo esc_attr($disabled); ?>
                                           value="2"
                                        <?php
                                        if (isset($optimization['speed_optimization']['devices']['cache_tablet'])) {
                                            if ($optimization['speed_optimization']['devices']['cache_tablet'] === 2) {
                                                echo 'checked="checked"';
                                            }
                                        } ?>
                                           class="ju-radiobox"/>
                                    <label for="cache-tablet2" class="radio-label"><?php esc_html_e('Specific tablet cache', 'wp-speed-of-light') ?></label></li>
                                <li><input type="radio" name="cache-tablet" id="cache-tablet3" <?php echo esc_attr($disabled); ?>
                                           value="3"
                                        <?php
                                        if (isset($optimization['speed_optimization']['devices']['cache_tablet'])) {
                                            if ($optimization['speed_optimization']['devices']['cache_tablet'] === 3) {
                                                echo 'checked="checked"';
                                            }
                                        } ?>
                                           class="ju-radiobox"/>
                                    <label for="cache-tablet3" class="radio-label"><?php esc_html_e('No cache for tablet', 'wp-speed-of-light') ?></label></li>
                            </ul>
                        </div>
                    </li>
                    <li class="ju-settings-option full-width field-block">
                        <label for="cache-mobile" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Serve the cache for mobile: Recommended ONLY if you’re experiencing 
                               wrong cache version served', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Cache for mobile', 'wp-speed-of-light') ?></label>
                        <div class="custom-optimization">
                            <ul>
                                <li><input type="radio" name="cache-mobile" id="cache-mobile1" <?php echo esc_attr($disabled); ?>
                                           value="1"
                                        <?php
                                        if (isset($optimization['speed_optimization']['devices']['cache_mobile'])) {
                                            if ($optimization['speed_optimization']['devices']['cache_mobile'] === 1) {
                                                echo 'checked="checked"';
                                            }
                                        } ?>
                                           class="ju-radiobox"/>
                                    <label for="cache-mobile1" class="radio-label"><?php esc_html_e('Automatic (same as desktop)', 'wp-speed-of-light') ?></label></li>
                                <li><input type="radio" name="cache-mobile" id="cache-mobile2" <?php echo esc_attr($disabled); ?>
                                           value="2"
                                        <?php
                                        if (isset($optimization['speed_optimization']['devices']['cache_mobile'])) {
                                            if ($optimization['speed_optimization']['devices']['cache_mobile'] === 2) {
                                                echo 'checked="checked"';
                                            }
                                        } ?>
                                           class="ju-radiobox"/>
                                    <label for="cache-mobile2" class="radio-label"><?php esc_html_e('Specific mobile cache', 'wp-speed-of-light') ?></label></li>
                                <li><input type="radio" name="cache-mobile" id="cache-mobile3" <?php echo esc_attr($disabled); ?>
                                           value="3"
                                        <?php
                                        if (isset($optimization['speed_optimization']['devices']['cache_mobile'])) {
                                            if ($optimization['speed_optimization']['devices']['cache_mobile'] === 3) {
                                                echo 'checked="checked"';
                                            }
                                        } ?>
                                           class="ju-radiobox"/>
                                    <label for="cache-mobile3" class="radio-label"><?php esc_html_e('No cache for mobile', 'wp-speed-of-light') ?></label></li>
                            </ul>
                        </div>
                    </li>

                    <li class="ju-settings-option full-width">
                        <label for="disable-page" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Add the URL of the pages you want to exclude
                                from cache (one URL per line)', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Never cache the following pages : ', 'wp-speed-of-light'); ?></label>
                        <p><textarea placeholder="<?php esc_html_e('One file per line, or you can target several URLs with: /blog/*', 'wp-speed-of-light'); ?>" cols="100" rows="10" id="disable-page"
                                     name="disable_page"><?php
                                        if (!empty($optimization['speed_optimization']['disable_page'])) {
                                            $output = implode("\n", $optimization['speed_optimization']['disable_page']);
                                            echo esc_textarea($output);
                                        } ?></textarea></p>
                    </li>
                </ul>
            </div>
            <div class="right">
                <ul class="field">
                    <li class="ju-settings-option full-width">
                        <label for="add-expires" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e("Expires headers gives instruction 
                               to the browser whether it should request a specific file
                                from the server or whether they should grab it 
                                from the browser's cache (it’s faster).", 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Add expire headers', 'wp-speed-of-light') ?></label>
                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox"  id="add-expires"
                                       name="add-expires"
                                       value="1"
                                    <?php
                                    if (!empty($optimization)) {
                                        if ($optimization['speed_optimization']['add_expires'] === 1) {
                                            echo 'checked="checked"';
                                        }
                                    }
                                    ?>
                                >
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>
                    <li class="ju-settings-option full-width">
                        <label for="cache-external" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Cache external resources such as script served from Google. Warning:
                                make sure you monitor the performance before and after activation, in some case you may
                                 experience performance loss when activated!', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Cache external script', 'wp-speed-of-light'); ?>
                        </label>
                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox" id="cache-external"
                                       name="cache_external_script"
                                       value="1"
                                    <?php
                                    if (!empty($optimization)) {
                                        if (isset($optimization['speed_optimization']['cache_external_script']) && $optimization['speed_optimization']['cache_external_script'] === 1) {
                                            echo 'checked="checked"';
                                        }
                                    } ?>
                                />
                                <div class="slider round"></div>
                            </label>
                        </div>
                    </li>
                    <?php
                    $lazy_loading_checked =  '';
                    $output = '';
                    if (!empty($advanced) && isset($advanced['lazy_loading']) && $advanced['lazy_loading'] === 1) {
                        $lazy_loading_checked = 'checked="checked"';
                    }
                    if (!empty($advanced) && !empty($advanced['exclude_lazy_loading'])) {
                        $output = implode("\n", $advanced['exclude_lazy_loading']);
                    }
                    ?>
                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="lazy-loading" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Load only images when it’s visible in the by user (on scroll)', 'wp-speed-of-light') ?>"
                        ><?php esc_html_e('Image lazy loading', 'wp-speed-of-light') ?></label>
                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>
                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox" class="wpsol-minification" id="lazy-loading" name="lazy-loading"
                                    <?php echo esc_attr($disabled_addon_attr) ?>
                                    <?php echo esc_html($lazy_loading_checked) ?> value="1" />
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>
                    <li class="exclude-lazyloading-mtf ju-settings-option full-width field-block addon-field <?php echo esc_attr($disabled_addon_class); ?>" style="display: none">
                        <div class="custom-optimization" style="margin-top: 20px;">
                            <ul class="horizontal">
                                <li>
                                    <label for="exclude-width-lazyload" class="text speedoflight_tool ju-setting-label"
                                           alt="<?php esc_html_e('Exclude from image lazy loading all images that have less than this width in pixel value. Useful to exclude small icons or logos', 'wp-speed-of-light') ?>">
                                        <?php esc_html_e('Exclude image less than (width px)', 'wp-speed-of-light') ?>
                                    </label>
                                    <div class="ju-switch-button">
                                        <input type="text" name="exclude-width-lazyload" id="exclude-width-lazyload"
                                               value="<?php echo (!empty($advanced) &&
                                                   isset($advanced['exclude_width_lazyload']) &&
                                                   !empty($advanced['exclude_width_lazyload'])) ?
                                                   esc_html($advanced['exclude_width_lazyload']) : '' ?>"
                                               size="5" class="ju-input lazyload-input"/>
                                    </div>

                                </li>
                                <li><label for="exclude-height-lazyload" class="text speedoflight_tool ju-setting-label"
                                           alt="<?php esc_html_e('Exclude from image lazy loading all images that have less than this height in pixel value. Useful to exclude small icons or logos', 'wp-speed-of-light') ?>">
                                        <?php esc_html_e('Exclude image less than (height px)', 'wp-speed-of-light') ?>
                                    </label>
                                    <div class="ju-switch-button">
                                        <input type="text" name="exclude-height-lazyload" id="exclude-height-lazyload"
                                               value="<?php echo (!empty($advanced) &&
                                                   isset($advanced['exclude_height_lazyload']) &&
                                                   !empty($advanced['exclude_height_lazyload'])) ?
                                                   esc_html($advanced['exclude_height_lazyload']) : '' ?>"
                                               size="5" class="ju-input lazyload-input"/>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="exclude-lazyloading-mtf ju-settings-option full-width <?php echo esc_attr($disabled_addon_class); ?>" style="display: none">
                        <label for="exclude-lazyloading" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Exclude from the image lazy loading.
                       You can also exclude a set of URLs by using rule like: www.website.com/news* or a class, id, located of images', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Image lazy loading exclusion : ', 'wp-speed-of-light') ?></label>
                        <p><textarea placeholder="<?php esc_html_e('One file per line, example: /wp-content/uploads/image.png', 'wp-speed-of-light'); ?>" cols="100" rows="9"
                                     id="exclude-lazyloading" class="wpsol-minification"
                                     name="exclude-lazyloading-url"><?php echo esc_textarea($output) ?></textarea></p>
                    </li>

                    <!--Iframe and HTML5 lazy loading-->
                    <?php
                    $iframe_video_lazy_loading_checked =  '';
                    if (!empty($advanced) && isset($advanced['iframe_video_lazy_loading']) && $advanced['iframe_video_lazy_loading'] === 1) {
                        $iframe_video_lazy_loading_checked = 'checked="checked"';
                    }
                    ?>
                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="iframe-video-lazy-loading" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Lazy load all iframe and HTML5 videos', 'wp-speed-of-light') ?>"
                        ><?php esc_html_e('Iframe & video lazy loading', 'wp-speed-of-light') ?></label>
                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>
                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox" class="wpsol-minification" id="iframe-video-lazy-loading" name="iframe-video-lazy-loading"
                                    <?php echo esc_attr($disabled_addon_attr) ?>
                                    <?php echo esc_html($iframe_video_lazy_loading_checked) ?> value="1" />
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>
                    <!--//Iframe and HTML5 lazy loading-->

                    <!-- Image dimension -->
                    <?php
                    $image_dimensions_checked =  '';
                    if (!empty($advanced) && isset($advanced['image_dimensions']) && $advanced['image_dimensions'] === 1) {
                        $image_dimensions_checked = 'checked="checked"';
                    }
                    ?>
                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="image-dimensions" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Add missing width and height attributes to images. Helps prevent layout shifts and improve the reading experience for your visitors', 'wp-speed-of-light') ?>"
                        ><?php esc_html_e('Image Dimensions', 'wp-speed-of-light') ?></label>
                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>
                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox" class="wpsol-minification" id="image-dimensions" name="image_dimensions"
                                    <?php echo esc_attr($disabled_addon_attr) ?>
                                    <?php echo esc_html($image_dimensions_checked) ?> value="1" />
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>
                    <!-- End Image dimension -->
                </ul>
            </div>
        </div>
        <div class="clear"></div>
        <div class="footer">
            <button type="submit"
                    class="ju-button orange-button waves-effect waves-light" id="save-speedup-btn">
                <span><?php esc_html_e('Save', 'wp-speed-of-light'); ?></span>
            </button>
        </div>
    </form>
</div>

