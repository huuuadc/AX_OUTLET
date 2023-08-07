<?php
if (!defined('ABSPATH')) {
    exit;
}
$optimization = get_option('wpsol_optimization_settings');
$pageExclusion = '';
$moveScriptChecked = '';
$excludeScriptOutput = '';
$disabled_addon_class = 'addon-disabled';
$disabled_addon_attr = 'disabled="disabled"';
$disabled_panel = '';
if (class_exists('\Joomunited\WPSOLADDON\SpeedOptimization')) {
    $disabled_addon_class = '';
    $disabled_addon_attr = '';
    $disabled_panel = 'pannel-addon-enabled';
    // Exclude from minification and grouping
    $excludeFileList = get_option('wpsol_addon_exclude_file_lists');
    $excludeFileListOutput = '';
    if (is_array($excludeFileList)) {
        if (!empty($excludeFileList['js-exclude'])) {
            foreach ($excludeFileList['js-exclude'] as $filePath) {
                $excludeFileListOutput .= $filePath . "\r\n";
            }
        }
        if (!empty($excludeFileList['css-font-exclude'])) {
            foreach ($excludeFileList['css-font-exclude'] as $filePath) {
                $excludeFileListOutput .= $filePath . "\r\n";
            }
        }
    }
    // Exclude from defer css
    $exFromDeferCSSList = get_option('wpsol_exclude_from_defer_css');
    $exFromDeferCSSListOutput = '';
    if (is_array($exFromDeferCSSList) && !empty($exFromDeferCSSList)) {
        foreach ($exFromDeferCSSList as $filePath) {
            $exFromDeferCSSListOutput .= $filePath . "\r\n";
        }
    }

    // Page exclusion
    if (isset($optimization['advanced_features']['wpsol_page_exclusion'])
        && is_array($optimization['advanced_features']['wpsol_page_exclusion'])) {
        $pageExclusionArr = $optimization['advanced_features']['wpsol_page_exclusion'];
        $pageExclusion = implode("\r\n", $pageExclusionArr);
    }

    if (!empty($optimization) &&
        isset($optimization['advanced_features']['move_script_to_footer']) &&
        $optimization['advanced_features']['move_script_to_footer'] === 1) {
        $moveScriptChecked = 'checked="checked"';
    }

    if (!empty($optimization) && !empty($optimization['advanced_features']['exclude_move_to_footer'])) {
        $excludeScriptOutput = implode("\n", $optimization['advanced_features']['exclude_move_to_footer']);
    }
}
?>
<div class="content-group-minify wpsol-optimization">
    <form class="" method="post">
        <input type="hidden" name="action" value="wpsol_save_minification">
        <input type="hidden" name="page-redirect" value="group_and_minify" />
        <?php wp_nonce_field('wpsol_speed_optimization', '_wpsol_nonce'); ?>
        <div class="title">
            <label><?php esc_html_e('Group & Minify', 'wp-speed-of-light')?></label>
        </div>
        <?php //phpcs:ignore WordPress.Security.NonceVerification -- Check request, no action
        if (isset($_REQUEST['settings-updated']) && $_REQUEST['settings-updated'] && isset($_REQUEST['p']) && $_REQUEST['p'] === 'group_and_minify') :  ?>
            <div id="message-group_and_minify" class="ju-notice-success message-optimize">
                <strong><?php esc_html_e('Setting saved', 'wp-speed-of-light'); ?></strong></div>
        <?php endif; ?>
        <div class="content">
            <div class="left">
                <ul class="field">
                    <li class="ju-settings-option full-width">
                        <label for="html-minification" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Minification refers to the process
                                    of removing unnecessary or redundant data
                                    without affecting how the resource is processed
                                     by the browser - e.g. code comments and formatting,
                                     removing unused code, using shorter variable
                                      and function names, and so on', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('HTML minification', 'wp-speed-of-light') ?></label>
                        <div class="ju-switch-button">
                            <label class="switch">
                                <input type="checkbox" class="wpsol-minification" id="html-minification"
                                       name="html-minification"
                                       value="1"
                                    <?php
                                    if (!empty($optimization) &&
                                        isset($optimization['advanced_features']['html_minification']) &&
                                        $optimization['advanced_features']['html_minification'] === 1) {
                                        echo 'checked="checked"';
                                    }
                                    ?>>
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>
                    <li class="ju-settings-option full-width">
                        <label for="js-minification" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Minification refers to the process of removing unnecessary 
                                   or redundant data without affecting how the resource 
                                   is processed by the browser - e.g. code comments and 
                                   formatting, removing unused code,
                                   using shorter variable and function names, and so on', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('JS minification', 'wp-speed-of-light') ?></label>
                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox" class="wpsol-minification" id="js-minification"
                                       name="js-minification"
                                       value="1"
                                    <?php
                                    if (!empty($optimization) &&
                                        isset($optimization['advanced_features']['js_minification']) &&
                                        $optimization['advanced_features']['js_minification'] === 1) {
                                        echo 'checked="checked"';
                                    }
                                    ?>
                                >
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>
                    <li class="ju-settings-option full-width">
                        <label for="jsgroup-minification" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Grouping several Javascript files into a single file will minimize the HTTP
                            requests number.Use with caution and test your website,
                             it may generates conflicts', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Group JS', 'wp-speed-of-light') ?></label>
                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox" class="wpsol-minification" id="jsgroup-minification"
                                       name="jsgroup-minification"
                                       value="1"
                                    <?php
                                    if (!empty($optimization) &&
                                        isset($optimization['advanced_features']['jsgroup_minification']) &&
                                        $optimization['advanced_features']['jsgroup_minification'] === 1) {
                                        echo 'checked="checked"';
                                    }
                                    ?>>
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>
                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="defer-js" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Call JS files at the end of the page load to eliminate render blocking elements', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Defer JS', 'wp-speed-of-light') ?></label>

                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>

                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox" class="wpsol-minification" id="defer-js"
                                       name="defer-js"
                                       value="1"
                                    <?php echo esc_attr($disabled_addon_attr) ?>
                                    <?php
                                    if (!empty($optimization) &&
                                        isset($optimization['advanced_features']['defer_js']) &&
                                        $optimization['advanced_features']['defer_js'] === 1) {
                                        echo 'checked="checked"';
                                    }
                                    ?>
                                >
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>

                    <!--Move script to footer-->
                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="move-script-to-footer" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Move all minified scripts to footer', 'wp-speed-of-light') ?>"
                        ><?php esc_html_e('Move scripts to footer', 'wp-speed-of-light') ?></label>

                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>

                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox" class="wpsol-minification" id="move-script-to-footer"
                                    <?php echo esc_attr($disabled_addon_attr) ?>
                                       name="move-script-to-footer" <?php echo esc_attr($moveScriptChecked) ?>
                                       value="<?php echo esc_html((isset($optimization['advanced_features']['move_script_to_footer'])) ? (int)$optimization['advanced_features']['move_script_to_footer'] : 0) ?>"/>
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>

                    <li class="exclude-script-mtf ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>" style="display: none">
                        <label for="exclude-move-script-to-footer" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Add the script of the pages you want to exclude
                        from move to footer (one URL per line)', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Exclude script move to footer', 'wp-speed-of-light') ?></label>

                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>

                        <p><textarea cols="100" rows="7" <?php echo esc_attr($disabled_addon_attr) ?>
                                     id="exclude-move-script-to-footer" class="wpsol-minification"
                                     name="exclude-move-script-to-footer"><?php echo esc_textarea($excludeScriptOutput) ?></textarea></p>
                    </li>
                    <!--//Move script to footer-->

                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="wpsol-exclude-files-minification" style="max-width: calc(100% - 100px)" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Put each path to file in one line to exclude files from minification and grouping', 'wp-speed-of-light') ?>"
                        ><?php esc_html_e('Exclude files from minification and grouping', 'wp-speed-of-light') ?></label>

                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>
                        <p><textarea placeholder="<?php esc_attr_e('One file per line, example: /wp-content/plugins/aplugin/assets/js/script.js', 'wp-speed-of-light') ?>" cols="100" rows="7" <?php echo esc_attr($disabled_addon_attr) ?>
                                     id="wpsol-exclude-files-minification" class="wpsol-minification"
                                     name="wpsol-exclude-files-minification"><?php if (isset($excludeFileListOutput)) {
                                            echo esc_textarea($excludeFileListOutput);
                                                                             } ?></textarea></p>
                    </li>
                </ul>
            </div>
            <div class="right">
                <ul class="field">
                    <li class="ju-settings-option full-width">
                        <label for="css-minification" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Minification refers to the process of removing unnecessary or redundant data
                            without affecting how the resource is processed 
                            by the browser - e.g. code comments and formatting, 
                            removing unused code, using shorter variable and
                             function names, and so on', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('CSS minification', 'wp-speed-of-light') ?></label>
                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox" class="wpsol-minification" id="css-minification"
                                       name="css-minification"
                                       value="1"
                                    <?php
                                    if (!empty($optimization) &&
                                        isset($optimization['advanced_features']['css_minification']) &&
                                        $optimization['advanced_features']['css_minification'] === 1) {
                                        echo 'checked="checked"';
                                    }
                                    ?>
                                >
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>
                    <li class="ju-settings-option full-width">
                        <label for="cssgroup-minification" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Grouping several CSS files into a single file will
                            minimize the HTTP requests number.
                            Use with caution and test your website,
                             it may generates conflicts', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Group CSS', 'wp-speed-of-light') ?></label>
                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox" class="wpsol-minification" id="cssgroup-minification"
                                       name="cssgroup-minification"
                                       value="1"
                                    <?php
                                    if (!empty($optimization) &&
                                        isset($optimization['advanced_features']['cssgroup_minification']) &&
                                        $optimization['advanced_features']['cssgroup_minification'] === 1) {
                                        echo 'checked="checked"';
                                    }
                                    ?>>
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>
                    <?php
                    $fontGrChecked = '';
                    if (class_exists('\Joomunited\WPSOLADDON\SpeedOptimization')) {
                        if (!empty($optimization) &&
                            isset($optimization['advanced_features']['fontgroup_minification']) &&
                            $optimization['advanced_features']['fontgroup_minification'] === 1) {
                            $fontGrChecked = 'checked="checked"';
                        }
                    }
                    ?>
                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="fontGroup-minification" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Group local fonts and Google fonts
              in a single file to be served faster.', 'wp-speed-of-light') ?>"
                        ><?php esc_html_e('Group fonts and Google fonts', 'wp-speed-of-light') ?></label>

                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>

                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox" class="wpsol-minification" id="fontGroup-minification"
                                       name="fontgroup-minification" <?php echo esc_attr($fontGrChecked) ?>
                                    <?php echo esc_attr($disabled_addon_attr) ?>
                                value="<?php echo esc_html((isset($optimization['advanced_features']['fontgroup_minification'])) ? (int)$optimization['advanced_features']['fontgroup_minification'] : 0) ?>" />
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>

                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="defer-css" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Call CSS files at the end of the page load to eliminate render blocking elements', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Defer CSS', 'wp-speed-of-light') ?></label>

                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>

                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox" class="wpsol-minification" id="defer-css"
                                       name="defer-css"
                                       value="1"
                                    <?php echo esc_attr($disabled_addon_attr) ?>
                                    <?php
                                    if (!empty($optimization) &&
                                        isset($optimization['advanced_features']['defer_css']) &&
                                        $optimization['advanced_features']['defer_css'] === 1) {
                                        echo 'checked="checked"';
                                    }
                                    ?>
                                >
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>
                    <!--Exclude from defer CSS-->
                    <li class="exclude-path-from-defer-css ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>" style="display: none">
                        <label for="exclude-from-defer-css" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Put each path to css file in one line to exclude files from defer CSS', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Exclude files from defer CSS', 'wp-speed-of-light') ?></label>

                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>

                        <p><textarea cols="100" rows="7" <?php echo esc_attr($disabled_addon_attr) ?>
                                    placeholder="<?php esc_html_e('One file per line, example: /wp-content/plugins/aplugin/assets/css/style.css', 'wp-speed-of-light'); ?>"
                                     id="exclude-from-defer-css" class="wpsol-minification"
                                     name="exclude-from-defer-css"><?php if (isset($exFromDeferCSSListOutput)) {
                                            echo esc_textarea($exFromDeferCSSListOutput);
                                                                   } ?></textarea></p>
                    </li>
                    <!--/End Exclude from defer CSS-->

                    <!--Exclude inline style-->
                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="defer-js" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Exclude inline style from minification', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Exclude inline style', 'wp-speed-of-light') ?></label>

                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>

                        <div class="ju-switch-button">
                            <label class="switch ">
                                <input type="checkbox" class="wpsol-minification" id="defer-js"
                                       name="exclude-inline-style"
                                       value="1"
                                    <?php echo esc_attr($disabled_addon_attr) ?>
                                    <?php
                                    if (!empty($optimization) &&
                                        isset($optimization['advanced_features']['exclude_inline_style']) &&
                                        $optimization['advanced_features']['exclude_inline_style'] === 1) {
                                        echo 'checked="checked"';
                                    }
                                    ?>
                                >
                                <div class="slider"></div>
                            </label>
                        </div>
                    </li>
                    <!--//Exclude inline style-->

                    <!--Page exclusion feature-->
                    <li class="ju-settings-option full-width addon-field <?php echo esc_attr($disabled_addon_class); ?>">
                        <label for="wpsol-page-exclusion" style="max-width: calc(100% - 100px)" class="text speedoflight_tool ju-setting-label"
                               alt="<?php esc_html_e('Put each page URL in one line to exclude a page from all optimization that is listed above', 'wp-speed-of-light') ?>"
                        ><?php esc_html_e('Page exclusion', 'wp-speed-of-light') ?></label>

                        <div class="panel-disabled-addon speedoflight_tool <?php echo esc_attr($disabled_panel) ?>"
                             alt="<?php esc_html_e('This feature is available in the PRO ADDON version of the plugin', 'wp-speed-of-light') ?>">
                            <?php esc_html_e('Pro addon', 'wp-speed-of-light') ?></div>
                        <p><textarea placeholder="<?php esc_attr_e('One file per line, example: /my-account/invoices.html', 'wp-speed-of-light') ?>" cols="100" rows="7" <?php echo esc_attr($disabled_addon_attr) ?>
                                     id="wpsol-page-exclusion" class="wpsol-minification"
                                     name="wpsol-page-exclusion">
                                <?php echo esc_textarea($pageExclusion); ?>
                            </textarea></p>
                    </li>
                    <!--//Page exclusion feature-->
                </ul>
            </div>
        </div>
        <div class="clear"></div>

        <div class="footer" style="margin-bottom: 50px">
            <button type="submit"
                   class="ju-button orange-button waves-effect waves-light" id="save-minify-btn">
                <span><?php esc_html_e('Save', 'wp-speed-of-light'); ?></span>
            </button>
        </div>
    </form>
</div>

<?php
if (class_exists('\Joomunited\WPSOLADDON\SpeedOptimization')) {
    do_action('wpsol_addon_add_advanced_file_popup');
}

$file_group_activation = get_option('wpsol_file_group_activation_popup_settings', false);
if (empty($file_group_activation)) :
    ?>
<!--Dialog-->
<div id="wpsol_check_minify_modal" class="check-minify-dialog" style="display: none">
    <div class="check-minify-icon"><i class="material-icons">info_outline</i></div>
    <div class="check-minify-title"><h2><?php esc_html_e('File group activation', 'wp-speed-of-light'); ?></h2></div>
    <div class="check-minify-content">
        <span><?php esc_html_e('Check carefully the file group effects on your website, this is an advanced optimization that may be a source of conflict. If you encounter some errors on frontend you need to consider disabling it, it has a not-so-big impact on performance.', 'wp-speed-of-light'); ?></span>
    </div>
    <div class="check-minify-sucess">
        <button type="button" data-type="" id="agree" class="agree ju-button orange-button waves-effect waves-light">
            <span><?php esc_html_e('OK activate it', 'wp-speed-of-light') ?></span>
        </button>

        <input type="button" class="cancel" value="<?php esc_html_e('Cancel', 'wp-speed-of-light') ?>">
    </div>
</div>
<?php endif; ?>