<?php
if (!defined('ABSPATH')) {
    exit;
}
$parameters = array('Days', 'Hours', 'Minutes');

$check_cache = '';
$disable_cache = 'disabled="disabled"';
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
    $check_cache = 'checked="checked"';
    $disable_cache = '';
}
?>
<form method="post">
    <?php wp_nonce_field('wpsol-setup-wizard', 'wizard_nonce'); ?>
    <div class="main-optimization-header">
        <div class="title"><?php esc_html_e('Main optimization', 'wp-speed-of-light'); ?></div>
    </div>
    <div class="main-optimization-content configuration-content">
        <div class="activate-container first-container">
            <div class="title"><?php esc_html_e('Activate cache system', 'wp-speed-of-light'); ?></div>
            <table>
                <tr>
                    <td width="85%">
                        <label for="active_cache"><?php esc_html_e('Activate cache system', 'wp-speed-of-light'); ?></label>
                        <?php if (!empty($disable_cache)) : ?>
                        <span class="notice-not-writable">
                            <?php
                            printf(
                                esc_html__('wp-config.php file is not writable, cache can\'t be activated or deactivated through the plugin see the documentation %1$s', 'wp-speed-of-light'),
                                '<a href="'.esc_url('https://www.joomunited.com/wordpress-documentation/wp-speed-of-light/251-wp-speed-of-light-speed-optimization#toc-how-to-add-cache-manually-').'" target="_blank" style="text-decoration: none">' . esc_html__('how to activate cache', 'wp-speed-of-light') . '</a>'
                            )
                            ?>
                        </span>
                        <?php endif; ?>
                    </td>
                    <td width="15%">
                        <label class="switch wizard-switch">
                            <input type="checkbox" class="wizard-switch" id="active_cache"
                                   name="active_cache"
                                   value="1"
                                    <?php echo esc_attr($check_cache); ?>
                                    <?php echo esc_attr($disable_cache); ?>
                            />
                            <div class="wizard-slider round"></div>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td width="85%"><label for="clean_each"><?php esc_html_e('Clean each', 'wp-speed-of-light'); ?></label></td>
                    <td width="15%" style="position: relative;">
                        <div class="clean-each-option">
                            <input type="text" class="clean-each-text" id="clean_each" size="2"
                                   name="clean_each"
                                   value="40">
                            <select class="clean-each-params ju-select" name="clean_each_params">
                                <?php
                                $checked = '';
                                foreach ($parameters as $k => $v) {
                                    if ($k === 2) {
                                        $checked = 'selected="selected"';
                                    }
                                    $selected = '';
                                    echo '<option '.esc_attr($checked).' value="' . esc_html($k) . '" ' . esc_attr($selected) . '>' . esc_html($v) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="wordpress-container second-container">
            <div class="title"><?php esc_html_e('Wordpress optimization', 'wp-speed-of-light'); ?></div>
            <table>
                <tr>
                    <td width="85%"><label for="remove_query"><?php esc_html_e('Remove query strings', 'wp-speed-of-light'); ?></label></td>
                    <td width="15%">
                        <label class="switch wizard-switch">
                            <input type="checkbox" class="wizard-switch" id="remove_query"
                                   name="remove_query"
                                   value="1"
                                   checked="checked"
                            />
                            <div class="wizard-slider round"></div>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td width="85%"><label for="add_expired"><?php esc_html_e('Add expired headers', 'wp-speed-of-light'); ?></label></td>
                    <td width="15%">
                        <label class="switch wizard-switch">
                            <input type="checkbox" class="wizard-switch" id="add_expired"
                                   name="add_expired"
                                   value="1"
                                   checked="checked"
                            />
                            <div class="wizard-slider round"></div>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td width="85%"><label for="external_script"><?php esc_html_e('Cache external script', 'wp-speed-of-light'); ?></label></td>
                    <td width="15%">
                        <label class="switch wizard-switch">
                            <input type="checkbox" class="wizard-switch" id="external_script"
                                   name="external_script"
                                   value="1"
                            />
                            <div class="wizard-slider round"></div>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td width="85%"><label for="disable_rest"><?php esc_html_e('Disable REST API', 'wp-speed-of-light'); ?></label></td>
                    <td width="15%">
                        <label class="switch wizard-switch">
                            <input type="checkbox" class="wizard-switch" id="disable_rest"
                                   name="disable_rest"
                                   value="1"
                                   checked="checked"
                            />
                            <div class="wizard-slider round"></div>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td width="85%"><label for="disable_rss"><?php esc_html_e('Disable RSS Feed', 'wp-speed-of-light'); ?></label></td>
                    <td width="15%">
                        <label class="switch wizard-switch">
                            <input type="checkbox" class="wizard-switch" id="disable_rss"
                                   name="disable_rss"
                                   value="1"
                                   checked="checked"
                            />
                            <div class="wizard-slider round"></div>
                        </label>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="main-optimization-footer configuration-footer">
        <input type="submit" value="Continue" class="" name="wpsol_save_step" />
    </div>
</form>
