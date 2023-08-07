<?php
if (!defined('ABSPATH')) {
    exit;
}
$woo = get_option('wpsol_woocommerce_speedup');
?>
<div class="content-woocommerce-speedup wpsol-optimization">
    <form method="post">
        <input type="hidden" name="action" value="wpsol_save_woocommerce">
        <input type="hidden" name="page-redirect" value="woocommerce"/>
        <?php wp_nonce_field('wpsol_speed_optimization', '_wpsol_nonce'); ?>
        <div class="title">
            <label><?php esc_html_e('WooCommerce SpeedUp', 'wp-speed-of-light') ?></label>
        </div>
        <div class="woocommerce-ajax-message">
            <div class="ajax-loader-icon">
                <img src="<?php echo esc_url(WPSOL_PLUGIN_URL.'assets/images/ajax-loader.gif'); ?>" title="Loading" />
            </div>
            <div class="notice woocommerce-ajax-result"></div>
        </div>
        <?php //phpcs:ignore WordPress.Security.NonceVerification -- Check request, no action
        if (isset($_REQUEST['settings-updated']) && $_REQUEST['settings-updated'] && isset($_REQUEST['p']) && $_REQUEST['p'] === 'woocommerce') : ?>
            <div id="message-woocommerce" class="ju-notice-success message-optimize">
                <strong><?php esc_html_e('Setting saved', 'wp-speed-of-light'); ?></strong></div>
        <?php endif; ?>
        <div class="content">
            <div class="left">
                <ul class="field">
                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="woo-disable-fragments" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('By default, WooCommerce uses cart fragments which is used to update the shopping cart without refreshing the page. Depending on your theme and on WooCommerce configuration it maybe necessary or you can be disable it for a better performance', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Disable Cart Fragments', 'wp-speed-of-light') ?>
                        </label>

                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?>
                        </div>

                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox" class="wpsol-minification" id="woo-disable-fragments" name="woo-disable-fragments" value="1"
                                    <?php echo esc_attr($disabled_addon_attr) ?>
                                    <?php (isset($woo['woo_disable_fragments']) ? checked($woo['woo_disable_fragments'], '1') : '') ?> />
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>
                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="woo-disable-styles" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Disable WooCommerce Styles for non woocommerce pages', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Disable Styles', 'wp-speed-of-light') ?>
                        </label>

                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>

                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox" class="wpsol-minification" id="woo-disable-styles" name="woo-disable-styles" value="1"
                                    <?php echo esc_attr($disabled_addon_attr) ?>
                                    <?php (isset($woo['woo_disable_styles']) ? checked($woo['woo_disable_styles'], '1') : '') ?> />
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>
                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="woo-disable-scripts" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Disable WooCommerce Scripts for non woocommerce pages', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Disable Scripts', 'wp-speed-of-light') ?>
                        </label>

                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>

                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox" class="wpsol-minification" id="woo-disable-scripts" name="woo-disable-scripts" value="1"
                                    <?php echo esc_attr($disabled_addon_attr) ?>
                                    <?php (isset($woo['woo_disable_scripts']) ? checked($woo['woo_disable_scripts'], '1') : '') ?> />
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="right">
                <ul class="field">
                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="woo-customer-sessions" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Clear all the WooCommerce customer sessions and cart that are temporary stored in your database. You can save a lot of performance and database storage.', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Clear customers session + cart', 'wp-speed-of-light') ?>
                        </label>

                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>

                        <button type="button" class="ju-button orange-button waves-effect waves-light woocommerce-speedup-button woocommerce-clearup"
                            <?php echo esc_attr($disabled_addon_attr) ?>
                                id="woo-customer-sessions" data-type="clear_woo_customer_sessions">
                            <span><?php esc_html_e('Clear', 'wp-speed-of-light'); ?></span>
                        </button>
                    </li>
                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="woo-clear-transients" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Clear all WooCommerce transients that are temporary stored in your database', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Clear WooCommerce Transients', 'wp-speed-of-light') ?>
                        </label>

                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>

                        <button type="button" class="ju-button orange-button waves-effect waves-light woocommerce-speedup-button woocommerce-clearup"
                            <?php echo esc_attr($disabled_addon_attr) ?>
                                id="woo-clear-transients" data-type="clear_woocommerce_transients">
                            <span><?php esc_html_e('Clear', 'wp-speed-of-light'); ?></span>
                        </button>
                    </li>
                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="woo-auto-cleanup" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Automatically cleanup sessions, carts and transients after a certain duration (in hours)', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Autocleanup session, cart transients', 'wp-speed-of-light') ?>
                        </label>

                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>

                        <div class="ju-input-select" style="margin: 0">
                            <select name="woo-auto-cleanup" class="frequency-params ju-select" style="width: 90px !important;padding-left: 10px;line-height: 1"
                                    id="woo-auto-cleanup" <?php echo esc_attr($disabled_addon_attr) ?> >
                                <option value="disable"
                                    <?php echo (isset($woo['woo_auto_cleanup']) && 'disable' === $woo['woo_auto_cleanup']) ? 'selected = "selected"' : ''?>>
                                    <?php esc_html_e('Disable', 'wp-speed-of-light') ?>
                                </option>
                                <?php
                                for ($i = 1; $i <= 24; $i ++) {
                                    if (is_int($i / 2)) {
                                        $selected = '';
                                        if (isset($woo['woo_auto_cleanup']) && $i === (int)$woo['woo_auto_cleanup']) {
                                            $selected = 'selected = "selected"';
                                        }
                                        echo '<option value="' . esc_html($i) . '" ' . esc_attr($selected) . '>' . esc_html($i) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </li>
                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>" >
                        <label for="heartbeat-frequency" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Reducing activity will change Heartbeat frequency. Disabling Heartbeat entirely may break other plugins and themes using this API', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Disable/Reduce Heartbeat API', 'wp-speed-of-light') ?>
                        </label>
                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>
                        <div class="ju-input-select" style="margin: 0">
                            <select name="heartbeat-frequency" class="frequency-params ju-select" style="width: 90px !important;padding-left: 10px;line-height: 1"
                                    id="heartbeat-frequency" <?php echo esc_attr($disabled_addon_attr) ?> >
                                <option value="disable"
                                    <?php echo (isset($woo['heartbeat_frequency']) && 'disable' === $woo['heartbeat_frequency']) ? 'selected = "selected"' : ''?>>
                                    <?php esc_html_e('Disable', 'wp-speed-of-light') ?>
                                </option>
                                <?php
                                for ($i = 60; $i <= 300; $i ++) {
                                    if (is_int($i / 30)) {
                                        $selected = '';
                                        if (isset($woo['heartbeat_frequency']) && $i === (int)$woo['heartbeat_frequency']) {
                                            $selected = 'selected = "selected"';
                                        }
                                        echo '<option value="' . esc_html($i) . '" ' . esc_attr($selected) . '>' . esc_html($i) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="clear"></div>
        <div class="footer">
            <button type="submit"
                    class="ju-button orange-button waves-effect waves-light" id="save-woocommercespeedup-btn">
                <span><?php esc_html_e('Save', 'wp-speed-of-light'); ?></span>
            </button>
        </div>
    </form>
</div>
