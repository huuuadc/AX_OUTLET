<?php
if (!defined('ABSPATH')) {
    exit;
}
$lastest = get_option('wpsol_loadtime_analysis_lastest');
$image_dashboard = WPSOL_PLUGIN_URL .'assets/images/default-dashboard.png';
if (isset($lastest['screenshot']) && !empty($lastest['screenshot'])) {
    // Create image dashboard expires for a month
    if (isset($lastest['analysis-time']) && (time() <=  (30 * 24 * 60 * 60) + strtotime($lastest['analysis-time']))) {
        $image_dashboard = $lastest['screenshot'];
    }
}

$permalink = get_site_url();
$find = array( 'http://', 'https://' );
$replace = '';
$output = str_replace($find, $replace, $permalink);

$dashboard = new \Joomunited\WPSOL\Dashboard();
$checkdashboard = $dashboard->checkDashboard();
$checkoptimization = $dashboard->checkOptimization();

$icon = array(
        'success' => '<i class="material-icons success size-24 icon-vertical-mid">check_circle</i>',
        'warning' => '<i class="material-icons info size-24 icon-vertical-mid">info</i>',
        'notice' => '<img class="custom-material-icon size-24 icon-vertical-mid" src="'.WPSOL_PLUGIN_URL.'assets/images/icon-notification.png" />'
);

if (class_exists('\Joomunited\WPSOLADDON\SpeedOptimization')) {
    $badge = __('Pro addon installed', 'wp-speed-of-light');
} else {
    $badge = __('Pro addon feature', 'wp-speed-of-light');
}

$dashboard_info = array(

);

?>

<div class="wpsol-dashboard">
    <div class="header padding-top-bottom-20 wpsol-width-100">
        <div class="title"><span><?php esc_html_e('Speedup Overview', 'wp-speed-of-light') ?></span></div>
        <div class="sub-title"><a href="<?php echo esc_url($output); ?>" ><?php echo esc_url($output); ?></a></div>
    </div>

    <div class="dashboard-info wpsol-width-100 padding-top-bottom-20">
        <!--        IMAGE  -->
        <div class="dashboard-info-left wpsol-left">
            <img class="image-dashboard tooltipped" data-position="top"
                 data-tooltip="<?php esc_html_e('Latest performance check page preview', 'wp-speed-of-light') ?>"
                 src="<?php echo esc_html($image_dashboard) ?>" />
        </div>
        <!--        RIGHT INFO -->
        <div class="dashboard-info-right wpsol-right">
            <div class="ju-settings-option tooltipped hover-section" data-position="top"
                 data-tooltip="<?php esc_html_e('Check for WP Speed of Light cache system activation', 'wp-speed-of-light') ?>">
                <div class="wpsol-row-full cache-activation">
                    <label class="ju-setting-label">
                        <i class="material-icons size-24 icon-vertical-mid left grey">delete</i>
                    </label>
                    <label class="ju-setting-label label-dash-widgets"><?php esc_html_e('Cache activation', 'wp-speed-of-light') ?></label>
                    <label class="ju-setting-label link-info">
                        <a href="<?php echo esc_url(admin_url('admin.php?page=wpsol_speed_optimization#speedup'))?>"><i class="material-icons size-24 icon-vertical-mid grey">link</i></a>
                    </label>
                    <div class="right-checkbox">
                        <?php
                        //phpcs:ignore WordPress.Security.EscapeOutput -- Echo icon directly
                        echo $icon[$checkdashboard['cache']]
                        ?>
                    </div>
                </div>
            </div>
            <div class="ju-settings-option tooltipped" data-position="top"
                 data-tooltip="<?php esc_html_e('Your PHP version is: ', 'wp-speed-of-light') ?>
                        <?php echo esc_html(phpversion()) ?>
                        <?php esc_html_e('It’s better to use PHP 7.4 because comparing to previous 5.6 versions the execution time of PHP 7.X is more than twice as fast and has 30 percent lower memory consumption. PHP 7.4 offer a small additional speed optimization', 'wp-speed-of-light') ?>">
                <div class="wpsol-row-full php-version">
                    <label class="ju-setting-label">
                        <i class="material-icons size-24 icon-vertical-mid left grey">code</i>
                    </label>
                    <label class="ju-setting-label label-dash-widgets"><?php esc_html_e('PHP Version', 'wp-speed-of-light') ?></label>
                    <div class="right-checkbox">
                        <?php
                        //phpcs:ignore WordPress.Security.EscapeOutput -- Echo icon directly
                        echo $icon[$checkdashboard['php-version']]
                        ?>
                    </div>
                </div>
            </div>
            <div class="ju-settings-option tooltipped" data-position="top"
                 data-tooltip="<?php esc_html_e('Check if Gzip data compression is activated on your server, if not WP Speed of Light will force the activation calling an apache module', 'wp-speed-of-light') ?>">
                <div class="wpsol-row-full gzip-compression">
                    <label class="ju-setting-label">
                        <i class="material-icons size-24 icon-vertical-mid left grey">description</i>
                    </label>
                    <label class="ju-setting-label label-dash-widgets"><?php esc_html_e('Gzip compression', 'wp-speed-of-light') ?></label>
                    <div class="right-checkbox">
                    </div>
                </div>
            </div>
            <div class="ju-settings-option tooltipped hover-section" data-position="top"
                 data-tooltip="<?php esc_html_e('Expires headers gives instruction to the browser whether it should request a specific file from the server or whether they should grab it from the browser\'s cache (it’s faster)', 'wp-speed-of-light') ?>">
                <div class="wpsol-row-full expires-header">
                    <label class="ju-setting-label">
                        <i class="material-icons size-24 icon-vertical-mid left grey">desktop_mac</i>
                    </label>
                    <label class="ju-setting-label label-dash-widgets"><?php esc_html_e('Expire headers', 'wp-speed-of-light') ?></label>
                    <label class="ju-setting-label link-info">
                        <a href="<?php echo esc_url(admin_url('admin.php?page=wpsol_speed_optimization#speedup'))?>"><i class="material-icons size-24 icon-vertical-mid grey">link</i></a>
                    </label>
                    <div class="right-checkbox">
                    </div>
                </div>
            </div>
            <div class="ju-settings-option tooltipped hover-section" data-position="top"
                 data-tooltip="<?php esc_html_e('Check if database cleanup has been made recently or scheduled in the PRO ADDON', 'wp-speed-of-light') ?>">
                <div class="wpsol-row-full clean-cache">
                    <label class="ju-setting-label">
                        <img class="custom-material-icon size-24 icon-vertical-mid left grey" src="<?php echo esc_url(WPSOL_PLUGIN_URL.'assets/images/icon-cache-clean-up.png')?>" />
                    </label>
                    <label class="ju-setting-label label-dash-widgets"><?php esc_html_e('Database cleanup', 'wp-speed-of-light') ?></label>
                    <label class="ju-setting-label link-info">
                        <a href="<?php echo esc_url(admin_url('admin.php?page=wpsol_speed_optimization#database_cleanup'))?>"><i class="material-icons size-24 icon-vertical-mid grey">link</i></a>
                    </label>
                    <div class="right-checkbox">
                        <?php
                        //phpcs:ignore WordPress.Security.EscapeOutput -- Echo icon directly
                        echo $icon[$checkdashboard['cache-clean']]
                        ?>
                    </div>
                </div>
            </div>
            <div class="ju-settings-option tooltipped hover-section" data-position="top"
                 data-tooltip="<?php esc_html_e('Disable the WordPress REST API (API to retrieve data using GET requests, used by developers)', 'wp-speed-of-light') ?>">
                <div class="wpsol-row-full rest-api">
                    <label class="ju-setting-label">
                        <i class="material-icons size-24 icon-vertical-mid left grey">settings</i>
                    </label>
                    <label class="ju-setting-label label-dash-widgets"><?php esc_html_e('Rest API', 'wp-speed-of-light') ?></label>
                    <label class="ju-setting-label link-info">
                        <a href="<?php echo esc_url(admin_url('admin.php?page=wpsol_speed_optimization#wordpress'))?>"><i class="material-icons size-24 icon-vertical-mid grey">link</i></a>
                    </label>
                    <div class="right-checkbox">
                        <?php
                        //phpcs:ignore WordPress.Security.EscapeOutput -- Echo icon directly
                        echo $icon[$checkdashboard['rest']]
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="dashboard-analysis wpsol-width-100 padding-top-bottom-20">
        <div class="dashboard-title padding-top-bottom-20">
            <label><?php esc_html_e('Latest performance check', 'wp-speed-of-light') ?></label>
            <?php if (empty($lastest)) : ?>
            <div class="dashboard-title-link">
                <a href="<?php echo esc_url(admin_url('admin.php?page=wpsol_speed_analysis'))?>"><?php esc_html_e('Run a first test now >>', 'wp-speed-of-light') ?></a>
            </div>
            <?php endif; ?>
        </div>
        <div class="ju-settings-option">
            <div class="wpsol-row-full panel-1">
                <div class="icon"><i class="material-icons">alarm</i></div>
                <div class="analysis">
                    <label class="wpsol-width-100 wpsol-left grey"><?php esc_html_e('First Contentful Paint', 'wp-speed-of-light') ?></label>
                    <span class="wpsol-width-100 wpsol-left"><?php echo (isset($lastest['lab-data']['first-contentful-paint'])) ? esc_html($lastest['lab-data']['first-contentful-paint']) : '-- : --' ?></span>
                </div>
            </div>
        </div>
        <div class="ju-settings-option">
            <div class="wpsol-row-full panel-2">
                <div class="icon"><i class="material-icons">settings</i></div>
                <div class="analysis">
                    <label class="wpsol-width-100 wpsol-left grey"><?php esc_html_e('Speed Index', 'wp-speed-of-light') ?></label>
                    <span class="wpsol-width-100 wpsol-left"><?php echo (isset($lastest['lab-data']['speed-index'])) ? esc_html($lastest['lab-data']['speed-index']) : '-- : --' ?></span>
                </div>
            </div>
        </div>
        <div class="ju-settings-option">
            <div class="wpsol-row-full panel-3">
                <div class="icon"><i class="material-icons">description</i></div>
                <div class="analysis">
                    <label class="wpsol-width-100 wpsol-left grey"><?php esc_html_e('Largest Contentful Paint', 'wp-speed-of-light') ?></label>
                    <span class="wpsol-width-100 wpsol-left"><?php echo (isset($lastest['lab-data']['largest-contentful-paint'])) ? esc_html($lastest['lab-data']['largest-contentful-paint']) : '-- : --' ?></span>
                </div>
            </div>
        </div>
        <div class="ju-settings-option margin-right-none">
            <div class="wpsol-row-full panel-4">
                <div class="icon"><i class="material-icons">compare</i></div>
                <div class="analysis">
                    <label class="wpsol-width-100 wpsol-left grey"><?php esc_html_e('Cumulative Layout Shift', 'wp-speed-of-light') ?></label>
                    <span class="wpsol-width-100 wpsol-left"><?php echo (isset($lastest['lab-data']['cumulative-layout-shift'])) ? esc_html($lastest['lab-data']['cumulative-layout-shift']) : '-- : --' ?></span>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="dashboard-features wpsol-width-100">
        <div class="ju-settings-option">
            <div class="dashboard-title padding-top-bottom-20">
                <label class="tooltipped" data-position="top"
                      data-tooltip="<?php esc_html_e('This is the next optimizations to go for a really better performance', 'wp-speed-of-light') ?>">
                    <?php esc_html_e('Additional optimization', 'wp-speed-of-light') ?>
                </label>
            </div>
            <div class="dashboard-features-panel">
                <div class="none-margin white hover-section">
                    <div class="wpsol-row-full">
                        <div class="ju-setting-label text-title"><?php esc_html_e('Image compression', 'wp-speed-of-light') ?></div>
                        <label class="ju-setting-label link-info">
                            <a href="<?php echo esc_url(admin_url('admin.php?page=wpsol_speed_optimization#image_compression'))?>">
                                <i class="material-icons size-24 icon-vertical-mid grey">link</i>
                            </a>
                        </label>
                        <div class="right-checkbox">
                            <div class="panel-addon"><?php echo esc_html($badge); ?></div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    <div class="panel">
                        <span>
                        <?php
                        if ($checkoptimization['image_compression']) {
                            esc_html_e('The image compression is activated. It helps to reduce your page size significantly while preserving the image quality', 'wp-speed-of-light');
                        } else {
                            esc_html_e('The image compression is not activated. It helps to reduce your page size significantly while preserving the image quality', 'wp-speed-of-light');
                        } ?>
                        </span>
                    </div>
                </div>
                <div class="none-margin lightness hover-section">
                    <div class="wpsol-row-full">
                        <div class="ju-setting-label text-title"><?php esc_html_e('Image lazy loading', 'wp-speed-of-light') ?></div>
                        <label class="ju-setting-label link-info">
                            <a href="<?php echo esc_url(admin_url('admin.php?page=wpsol_speed_optimization#speedup'))?>">
                                <i class="material-icons size-24 icon-vertical-mid grey">link</i>
                            </a>
                        </label>
                        <div class="right-checkbox">
                            <div class="panel-addon"><?php echo esc_html($badge); ?></div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    <div class="panel">
                        <span>
                        <?php
                        if ($checkoptimization['lazy_loading']) {
                            esc_html_e('Loading is activated. Load only images when it\'s visible in the by user (on scroll)', 'wp-speed-of-light');
                        } else {
                            esc_html_e('Lazy loading is not activated. Load only images when it’s visible in the by user (on scroll)', 'wp-speed-of-light');
                        }
                        ?>
                         </span>
                    </div>
                </div>
                <div class="none-margin white hover-section">
                    <div class="wpsol-row-full">
                        <div class="ju-setting-label text-title"><?php esc_html_e('Database auto cleanup', 'wp-speed-of-light') ?></div>
                        <label class="ju-setting-label link-info">
                            <a href="<?php echo esc_url(admin_url('admin.php?page=wpsol_speed_optimization#database_cleanup'))?>">
                                <i class="material-icons size-24 icon-vertical-mid grey">link</i>
                            </a>
                        </label>
                        <div class="right-checkbox">
                            <div class="panel-addon"><?php echo esc_html($badge); ?></div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    <div class="panel">
                        <span>
                            <?php
                            if ($checkoptimization['database_clean']) {
                                esc_html_e('Database automatic cleanup is activated. Database cleanup remove post revisions, trashed items, comment spam... up to 11 database optimization', 'wp-speed-of-light');
                            } else {
                                esc_html_e('Database automatic cleanup is not activated. Database cleanup remove post revisions, trashed items, comment spam... up to 11 database optimization', 'wp-speed-of-light');
                            }
                            ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="ju-settings-option">
            <div class="dashboard-title padding-top-bottom-20">
                <label class="tooltipped" data-position="top"
                       data-tooltip="<?php esc_html_e('Advanced optimization settings, require some deep tests on your website to advoid some plugin incompatility issues', 'wp-speed-of-light') ?>">
                    <?php esc_html_e('Advanced optimization', 'wp-speed-of-light') ?>
                </label>
            </div>
            <div class="dashboard-features-panel">
                <div class="none-margin white hover-section">
                    <div class="wpsol-row-full">
                        <div class="ju-setting-label text-title"><?php esc_html_e('File minification', 'wp-speed-of-light') ?></div>
                        <label class="ju-setting-label link-info">
                            <a href="<?php echo esc_url(admin_url('admin.php?page=wpsol_speed_optimization#group_and_minify'))?>">
                                <i class="material-icons size-24 icon-vertical-mid grey">link</i>
                            </a>
                        </label>
                    </div>
                    <div class="clear"></div>
                    <div class="panel">
                            <span>
                            <?php
                            if ($checkoptimization['minify_files']) {
                                esc_html_e('At least one of your JS, CSS or HTML resources is currently minified', 'wp-speed-of-light');
                            } else {
                                esc_html_e('None of your JS, CSS or HTML resources is currently minified', 'wp-speed-of-light');
                            }?>
                            </span>
                    </div>
                </div>
                <div class="none-margin lightness hover-section">
                    <div class="wpsol-row-full">
                        <div class="ju-setting-label text-title"><?php esc_html_e('Group files', 'wp-speed-of-light') ?></div>
                        <label class="ju-setting-label link-info">
                            <a href="<?php echo esc_url(admin_url('admin.php?page=wpsol_speed_optimization#group_and_minify'))?>">
                                <i class="material-icons size-24 icon-vertical-mid grey">link</i>
                            </a>
                        </label>
                    </div>
                    <div class="clear"></div>
                    <div class="panel">
                            <span>
                                <?php
                                if ($checkoptimization['group_files']) {
                                    esc_html_e('At least one of your resources, CSS or JS, is currently grouped', 'wp-speed-of-light');
                                } else {
                                    esc_html_e('None of you resources, CSS or JS, is currently grouped', 'wp-speed-of-light');
                                }?>
                            </span>
                    </div>
                </div>
                <div class="none-margin white hover-section">
                    <div class="wpsol-row-full">
                        <div class="ju-setting-label text-title"><?php esc_html_e('Group fonts', 'wp-speed-of-light') ?></div>
                        <label class="ju-setting-label link-info">
                            <a href="<?php echo esc_url(admin_url('admin.php?page=wpsol_speed_optimization#group_and_minify'))?>">
                                <i class="material-icons size-24 icon-vertical-mid grey">link</i>
                            </a>
                        </label>
                        <div class="right-checkbox">
                            <div class="panel-addon"><?php echo esc_html($badge); ?></div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    <div class="panel">
                            <span><?php
                            if ($checkoptimization['group_fonts']) {
                                esc_html_e('Local fonts Google fonts are properly grouped', 'wp-speed-of-light');
                            } else {
                                esc_html_e('None of your local fonts and Google Fonts are grouped', 'wp-speed-of-light');
                            }?>
                            </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="ju-settings-option margin-right-none">
            <div class="dashboard-title padding-top-bottom-20">
                <label><?php esc_html_e('Other recommendations', 'wp-speed-of-light') ?></label>
            </div>
            <div class="dashboard-features-panel">
                <div class="none-margin white">
                    <div class="panel" style="padding-top: 20px">
                        <?php
                        if ($checkoptimization['plugins_enable'] >= 20) :
                            ?>
                            <span>
                                    <?php esc_html_e('You have more than 20 plugins installed and activated, the less you have better it is for your loading time', 'wp-speed-of-light') ?>
                                </span>
                            <?php
                        endif;
                        ?>
                        <?php
                        if ($checkoptimization['plugins_disable'] >= 5) :
                            ?>
                            <span>
                                    <?php esc_html_e('You have more than 5 plugins disabled, you may consider removing them if they’re not useful', 'wp-speed-of-light') ?>
                                </span>
                        <?php endif; ?>

                        <?php
                        if ($checkoptimization['plugins_disable'] < 5 &&
                            $checkoptimization['plugins_enable'] < 20) :
                            ?>
                            <span><?php esc_html_e('Everything look sparking-clean here', 'wp-speed-of-light') ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
