<?php
if (!defined('ABSPATH')) {
    exit;
}
$cdn_integration = get_option('wpsol_cdn_integration');
$cdn_content_value = '';
$cdn_exclude_content_value = '';
if (!empty($cdn_integration['cdn_content'])) {
    $cdn_content_value = implode(',', $cdn_integration['cdn_content']);
}
if (!empty($cdn_integration['cdn_exclude_content'])) {
    $cdn_exclude_content_value = implode(',', $cdn_integration['cdn_exclude_content']);
}

if (!isset($cdn_integration['third_parts'])) {
    $cdn_integration['third_parts'] = array();
}

$disabled_addon_class = 'addon-disabled';
$disabled_addon_attr = 'disabled="disabled"';
$disabled_panel = '';
if (class_exists('\Joomunited\WPSOLADDON\SpeedOptimization')) {
    $disabled_addon_class = '';
    $disabled_addon_attr = '';
    $disabled_panel = 'pannel-addon-enabled';
}

$third_parts = array(
    'siteground-cache' => array(
        'name' => __('Siteground cache', 'wp-speed-of-light'),
        'shortname' => __('Siteground cache', 'wp-speed-of-light'),
        'description' => __('Siteground hosting cache system', 'wp-speed-of-light'),
        'linkto' => 'https://www.siteground.com/index.htm?afcode=cfe245cfa727bdb1c9875ea2854f97f4'),
    'maxcdn-cache' => array(
        'name' => __('MaxCDN cache', 'wp-speed-of-light'),
        'shortname' => __('MaxCDN', 'wp-speed-of-light'),
        'description' => __('Stackpath CDN dedicated service', 'wp-speed-of-light'),
        'linkto' => 'http://tracking.stackpath.com/e5BrX'),
    'keycdn-cache' => array(
        'name' => __('KeyCDN cache', 'wp-speed-of-light'),
        'shortname' => __('KeyCDN', 'wp-speed-of-light'),
        'description' => __('Dedicated CDN service provider', 'wp-speed-of-light'),
        'linkto' => ' https://www.keycdn.com/?a=37661'),
    'cloudflare-cache' => array(
        'name' => __('CloudFlare cache', 'wp-speed-of-light'),
        'shortname' => __('CloudFlare', 'wp-speed-of-light'),
        'description' => __('CDN dedicated service with a free version', 'wp-speed-of-light'),
        'linkto' => 'https://www.cloudflare.com/'),
    'varnish-cache' => array(
        'name' => __('Varnish cache', 'wp-speed-of-light'),
        'shortname' => __('Varnish', 'wp-speed-of-light'),
        'description' => __('Server side HTTP cache system', 'wp-speed-of-light'),
        'linkto' => '')
);
?>

<div class="content-cdn wpsol-optimization">
    <form method="post">
        <input type="hidden" name="action" value="wpsol_save_cdn">
        <input type="hidden" name="page-redirect" value="cdn" />
        <?php wp_nonce_field('wpsol_speed_optimization', '_wpsol_nonce'); ?>
        <div class="title">
            <label><?php esc_html_e('CDN Integration', 'wp-speed-of-light')?></label>
        </div>
        <?php //phpcs:ignore WordPress.Security.NonceVerification -- Check request, no action
        if (isset($_REQUEST['settings-updated']) && $_REQUEST['settings-updated'] && isset($_REQUEST['p']) && $_REQUEST['p'] === 'cdn') :  ?>
            <div id="message-cdn" class="ju-notice-success message-optimize">
                <strong><?php esc_html_e('Setting saved', 'wp-speed-of-light'); ?></strong></div>
        <?php endif; ?>
        <div class="content">
            <div class="left">
                <ul class="field">
                    <li class="ju-settings-option full-width">
                        <label for="cdn-active" class="speedoflight_tool cdn_label ju-setting-label"
                               alt="<?php esc_html_e('Enable to make CDN effective on your website', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Activate CDN', 'wp-speed-of-light') ?></label>
                        <div class="ju-switch-button">
                            <label class="switch">
                                <input type="checkbox" class="cdn-active" id="cdn-active" name="cdn-active"
                                       value="1" <?php (isset($cdn_integration['cdn_active']) ? checked($cdn_integration['cdn_active'], '1') : '') ?>>
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>
                    <li class="ju-settings-option full-width">
                        <label for="cdn-url" class="speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Add your CDN URL, without 
                           the trailing slash (at the end)', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('CDN URL', 'wp-speed-of-light') ?></label>
                        <input type="text" class="wpsol-configuration ju-input" id="cdn-url" name="cdn-url" size="50"
                               placeholder="<?php esc_html_e('https://www.domain.com', 'wp-speed-of-light') ?>"
                               value="<?php
                                echo((isset($cdn_integration['cdn_url']) && $cdn_integration['cdn_url']) ? esc_html($cdn_integration['cdn_url']) : ''); ?>"/>
                        <div class="note">
                            <b>Note:&nbsp;</b>
                            <span>
                            <?php esc_html_e('Use double slash ‘//’ at the start or url, if you have some pages on  HTTP and some are on HTTPS.', 'wp-speed-of-light') ?>
                            </span>

                        </div>
                    </li>
                    <li class="ju-settings-option full-width">
                        <label for="cdn-exclude-content" class="speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Exclude file type or directories from CDN network', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Exclude Content', 'wp-speed-of-light') ?></label>
                        <input type="text" class="wpsol-configuration ju-input" id="cdn-exclude-content" name="cdn-exclude-content" size="50"
                               value="<?php
                                echo(($cdn_exclude_content_value) ? esc_html($cdn_exclude_content_value) : ''); ?>
                        "/>

                        <div class="note">
                            <b>Note:&nbsp;</b>
                            <span>
                                <?php esc_html_e('Exclude file types or directories from CDN. Example,
                     enter .css to exclude the CSS files.', 'wp-speed-of-light') ?>
                            </span>
                        </div>
                    </li>

                </ul>
            </div>
            <div class="right">
                <ul class="field">
                    <li class="ju-settings-option full-width">
                        <label for="cdn-relative-path" class="speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Enabled by default, Enable/Disable the CDN for relative paths resources.
                     Used for some compatibilities with specific Wordpress plugins', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Relative path', 'wp-speed-of-light') ?>
                        </label>
                        <div class="ju-switch-button">
                            <label class="switch">
                                <input type="checkbox" class="cdn-relative-path" id="cdn-relative-path"
                                       name="cdn-relative-path"
                                       value="1" <?php (isset($cdn_integration['cdn_relative_path']) ? checked($cdn_integration['cdn_relative_path'], '1') : '') ?>>
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>
                    <li class="ju-settings-option full-width">
                        <label for="cdn-content" class="speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Your WordPress content served through CDN resources, separated by comma.
                           By default wp-content,wp-includes', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('CDN Content', 'wp-speed-of-light') ?></label>
                        <input type="text" class="wpsol-configuration ju-input" id="cdn-content" name="cdn-content" size="50"
                               value="<?php echo(($cdn_content_value) ? esc_html($cdn_content_value) : ''); ?>"/>
                        <div class="note">
                            <b>Note:&nbsp;</b>
                            <span>
                                <?php esc_html_e('Enter the directories (comma separated) of which you want the CDN 
                    to serve the content.', 'wp-speed-of-light') ?>
                            </span>
                        </div>
                    </li>
                    <li class="ju-settings-option full-width">
                        <label for="main-cdn-content" class="speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Main CDN and Cache providers links', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Main CDN and Cache providers links', 'wp-speed-of-light') ?></label>
                        <ul class="list-main-cdn">
                            <?php foreach ($third_parts as $k => $v) : ?>
                                <li>
                                    <?php if (!empty($v['linkto'])) : ?>
                                    <a href="<?php echo esc_attr($v['linkto']) ?>" target="_blank" class="wpsol-cdn-refer-link"><?php echo esc_html($v['shortname']) ?></a>
                                    <?php else : ?>
                                    <span style="font-weight: bold;"><?php echo esc_html($v['shortname']) ?></span>
                                    <?php endif; ?>
                                    :
                                    <span class="information">
                                    <?php echo esc_html($v['description']) ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="clear"></div>

            <div class="third-part">
                <div class="title">
                    <label for="3rd-party-cache" class="text speedoflight_tool"
                                 alt="<?php esc_html_e('3rd party Cache cleanup ', 'wp-speed-of-light') ?>">
                        <?php esc_html_e('On cache cleanup, also clean:', 'wp-speed-of-light') ?>
                    </label>
                </div>
                <div class="cdn-ajax-message">
                    <div class="ajax-loader-icon">
                        <img src="<?php echo esc_url(WPSOL_PLUGIN_URL.'assets/images/ajax-loader.gif'); ?>" title="Loading" />
                    </div>
                    <div class="notice cdn-ajax-result"></div>
                </div>
                <div class="third-part-content">
                    <ul class="field">
                        <div class="left">
                        <?php
                        foreach ($third_parts as $k => $v) {
                            if ($k === 'maxcdn-cache'|| $k === 'cloudflare-cache') {
                                continue;
                            }
                            //phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Content already escaped
                            echo '<li class="field-cdn addon-field '. esc_attr($k) . ' ' . esc_attr($disabled_addon_class) .'">
                                <label alt="' . esc_html__('Also cleanup the ', 'wp-speed-of-light') . esc_html($v['name']) .
                                esc_html__(' when a cache clean is performed', 'wp-speed-of-light') . '"
                                   for="' . esc_html($k) . '" class="speedoflight_tool field-title">' . esc_html($v['name']) .
                                (!empty($v['linkto']) ? (', <a href="'.esc_attr($v['linkto']).'" target="_blank" class="wpsol-cdn-refer-link" title="">'.esc_html__('Get it >>', 'wp-speed-of-light').'</a>') : '')
                                .'</label>';
                            // phpcs:enable
                            echo '<div class="panel-disabled-addon speedoflight_tool '. esc_attr($disabled_panel) .'"
                            style="top: 9px;"
                             alt="'. esc_html__('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') .'">
                            '. esc_html__('Pro addon', 'wp-speed-of-light') .'</div>';

                            echo '<div class="ju-switch-button">
                                <label class="switch ">
                                    <input type="checkbox" class="thirds-party-select"
                                   '. esc_attr($disabled_addon_attr) .'
                                           id="' . esc_html($k) . '" name="' . esc_html($k) . '"
                                    ' . (in_array($k, $cdn_integration['third_parts']) ? 'checked="checked"' : '') . '
                                    value="' . (in_array($k, $cdn_integration['third_parts']) ? '1' : '0') . '">
                                    <div class="slider"></div>
                                </label>
                            </div>';
                            if ($k === 'siteground-cache') {
                                continue;
                            }
                            if (class_exists('\Joomunited\WPSOLADDON\Admin')) {
                                \Joomunited\WPSOLADDON\Admin::renderForm($k);
                            }
                            echo  '</li>';
                        } ?>
                        </div>
                        <div class="right">
                            <?php
                            foreach ($third_parts as $k => $v) {
                                if ($k === 'maxcdn-cache'|| $k === 'cloudflare-cache') {
                                    //phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Content already escaped
                                    echo '<li class="field-cdn addon-field ' . esc_attr($k) . ' ' . esc_attr($disabled_addon_class) . '">
                                <label alt="' . esc_html__('Also cleanup the ', 'wp-speed-of-light') . esc_html($v['name']) .
                                        esc_html__(' when a cache clean is performed', 'wp-speed-of-light') . '"
                                   for="' . esc_html($k) . '" class="speedoflight_tool field-title">' . esc_html($v['name']) .
                                        (!empty($v['linkto']) ? (', <a href="' . esc_attr($v['linkto']) . '" target="_blank" class="wpsol-cdn-refer-link" title="">' . esc_html__('Get it >>', 'wp-speed-of-light') . '</a>') : '')
                                        . '</label>';
                                    // phpcs:enable
                                    echo '<div class="panel-disabled-addon speedoflight_tool ' . esc_attr($disabled_panel) . '"
                            style="top: 9px;"
                             alt="' . esc_html__('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') . '">
                            ' . esc_html__('Pro addon', 'wp-speed-of-light') . '</div>';

                                    echo '<div class="ju-switch-button">
                                <label class="switch ">
                                    <input type="checkbox" class="thirds-party-select"
                                   ' . esc_attr($disabled_addon_attr) . '
                                           id="' . esc_html($k) . '" name="' . esc_html($k) . '"
                                    ' . (in_array($k, $cdn_integration['third_parts']) ? 'checked="checked"' : '') . '
                                    value="' . (in_array($k, $cdn_integration['third_parts']) ? '1' : '0') . '">
                                    <div class="slider"></div>
                                </label>
                            </div>';
                                    if (class_exists('\Joomunited\WPSOLADDON\Admin')) {
                                        \Joomunited\WPSOLADDON\Admin::renderForm($k);
                                    }
                                    echo '</li>';
                                }
                            } ?>
                        </div>
                    </ul>
                </div>
            </div>
        </div>
        <div class="clear"></div>
        <div class="footer">
            <button type="submit"
                   class="ju-button orange-button waves-effect waves-light" id="save-cdn-btn">
            <span><?php esc_html_e('Save', 'wp-speed-of-light'); ?></span>
            </button>
        </div>
    </form>
</div>




