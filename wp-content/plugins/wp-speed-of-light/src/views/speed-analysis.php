<?php

use Joomunited\WPSOL\SpeedAnalysis;

if (!defined('ABSPATH')) {
    exit;
}

$wpsol_SpeedAnalysis = new SpeedAnalysis();
$queriesParameter = $wpsol_SpeedAnalysis->getInfoQueries();
$conf = get_option('wpsol_configuration');
// total result queries
if (!empty($queriesParameter)) {
    $plugin_time = 0;
    $select = $wpsol_SpeedAnalysis->getTotalResultQueries($queriesParameter, 'SELECT');
    $show = $wpsol_SpeedAnalysis->getTotalResultQueries($queriesParameter, 'SHOW');
    $update = $wpsol_SpeedAnalysis->getTotalResultQueries($queriesParameter, 'UPDATE');
    foreach ($queriesParameter['plugin']['details'] as $k => $v) {
        $plugin_time += $v['load_time'];
    }
    $time = $queriesParameter['theme']['load_time'] + $queriesParameter['core']['load_time'] + $plugin_time;
}
$active_plugins = 0;
// count total plugin
$active_plugins = count(get_mu_plugins());
foreach (get_plugins() as $plug => $junk) {
    if (is_plugin_active($plug)) {
        $active_plugins++;
    }
}

$latest_analysis = get_option('wpsol_loadtime_analysis_lastest');
$total_analysis = get_option('wpsol_loadtime_analysis_total');
if (!empty($total_analysis)) {
    $total_analysis = array_reverse($total_analysis);
}
?>
<div id="wpsol-speed-analysis">
    <div class="ju-main-wrapper" style="margin: 0">
        <div class="ju-right-panel" style="margin: 0; width: auto;background: transparent;" >
            <div class="ju-top-tabs-wrapper">
                <ul class="tabs ju-top-tabs horizontal-tabs">
                    <li class="tab">
                        <a class="link-tab waves-effect waves-light" href="#speedtest">
                            <?php esc_html_e('Loading time', 'wp-speed-of-light') ?>
                        </a>
                    </li>
                    <li class="tab">
                        <a class="link-tab waves-effect waves-light" href="#analysis">
                            <?php esc_html_e('Database queries', 'wp-speed-of-light') ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div id="speedtest" class="tab-content">
        <!--analysis-->
        <div class="tab-analysis-content">
            <div class="header-analysis">
                <div class="title"><?php esc_html_e('Speed analysis', 'wp-speed-of-light') ?></div>
                <div class="panel">
                    <div class="panel-content">
                        <label for="insert-url"
                               class="insert-url"><?php esc_html_e('URL to analyse : ', 'wp-speed-of-light') ?>
                            <?php echo esc_url(home_url()) . '/'; ?></label>
                        <div class="panel-input">
                            <input id="insert-url" type="text" placeholder="<?php esc_html_e('Typing here...', 'wp-speed-of-light') ?>"
                                   name="wpsol_url_speed" value="" class="wpsol_url_speed ju-input"/>
                            <input id="main-url" type="text" readonly="true" data-url="<?php echo esc_url(home_url()); ?>"
                                   style="display: none;"/>
                            <input id="speed-button" type="button" value="<?php esc_html_e('Launch speed test', 'wp-speed-of-light') ?>"
                                   name="loadtime-button" class="btn waves-effect waves-light btn-analysis"/>
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <div class="message">
                <div id="message-scan" style="display: none; margin-top:20px;" class="notice notice-success">
                    <strong><?php esc_html_e('The speed test is running… it may takes between 1 and 5 minutes,
             keep calm and stay cool :)', 'wp-speed-of-light'); ?>
                    </strong>
                </div>
                <!--progess-->
                <div class="scan-test-progress progress" style="display:none">
                    <div class="indeterminate"></div>
                </div>
                <?php if (empty($latest_analysis)) : ?>
                <div id="message-first-scan" class=" notice notice-success">
                    <?php esc_html_e('Run free website speed test using real browsers and at real consumer connection speeds.
             Your results will provide diagnostic information including resources,
              loading time and optimization.', 'wp-speed-of-light') ?>
                </div>
                <?php endif; ?>
                <div id="message-result-scan" style="display: none; padding: 10px;" class="notice">
                    <strong>
                        <div class="reload-notice"><br><?php echo sprintf(esc_html__('This page will be reloaded after %s seconds!', 'wp-speed-of-light'), '<span id="time-reload"></span>');?></div>
                    </strong>
                </div>
            </div>

            <div class="result-content analysis-result-content">
                <div class="box-result">
                    <ul>
                        <li class="col2">
                            <div class="icon icon-1">
                                <i class="material-icons">settings_input_hdmi</i>
                            </div>
                            <div class="panel">
                                <div class="title"><?php esc_html_e('Total plugins ', 'wp-speed-of-light') ?></div>
                                <div class="number blue"><?php echo esc_html($active_plugins); ?></div>
                                <div class="note"><?php esc_html_e('The number of plugins currently activated', 'wp-speed-of-light') ?></div>
                            </div>
                        </li>
                        <li class="col2">
                            <div class="icon icon-2">
                                <i class="material-icons">alarm</i>
                            </div>
                            <div class="panel">
                                <div class="title"><?php esc_html_e('Loading time', 'wp-speed-of-light') ?></div>
                                <div class="number green"><?php echo (isset($latest_analysis['total-timing'])) ? esc_html($latest_analysis['total-timing']) : 0; ?></div>
                                <div class="note"><?php esc_html_e('Lastest speed analysis test result', 'wp-speed-of-light') ?></div>
                            </div>
                        </li>
                        <li class="col2">
                            <div class="icon icon-2">
                                <i class="material-icons">grade</i>
                            </div>
                            <div class="panel">
                                <div class="title"><?php esc_html_e('Performance score', 'wp-speed-of-light') ?></div>
                                <div class="number green"><?php echo (isset($latest_analysis['score'])) ? esc_html($latest_analysis['score']) : 0; ?></div>
                                <div class="note"><?php esc_html_e('A score which summarizes the page’s performance', 'wp-speed-of-light') ?></div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="lastest-details">
                    <div class="loadtime-title"><?php esc_html_e('Latest analysis details', 'wp-speed-of-light') ?></div>
                    <table width="100%" class="lastest-details-table" style="border-collapse: collapse;">
                        <tr>
                            <th class="tooltipped" data-position="bottom"
                                 data-tooltip="<?php esc_html_e('Link to the full report of the google page speed website', 'wp-speed-of-light') ?>">
                                <?php esc_html_e('Full report', 'wp-speed-of-light') ?>
                            </th>
                            <th class="tooltipped" data-position="bottom"
                                data-tooltip="<?php esc_html_e('First Contentful Paint marks the time at which the first text or image is painted', 'wp-speed-of-light') ?>">
                                <?php esc_html_e('First Contentful Paint', 'wp-speed-of-light') ?></th>
                            <th class="tooltipped" data-position="bottom"
                                data-tooltip="<?php esc_html_e('Time to Interactive is the amount of time it takes for the page to become fully interactive', 'wp-speed-of-light') ?>">
                                <?php esc_html_e('Time to Interactive', 'wp-speed-of-light') ?></th>
                            <th class="tooltipped" data-position="bottom"
                                data-tooltip="<?php esc_html_e('Speed Index shows how quickly the contents of the page are visibly populated', 'wp-speed-of-light') ?>">
                                <?php esc_html_e('Speed Index', 'wp-speed-of-light') ?></th>
                            <th class="tooltipped" data-position="bottom"
                                data-tooltip="<?php esc_html_e('The total amount of time between First Contentful Paint (FCP) and Time to Interactive (TTI) where the main thread was blocked for long enough to prevent input responsiveness.', 'wp-speed-of-light') ?>"
                                style='text-align: left;padding-left: 45px;'>
                                <?php esc_html_e('Total Blocking Time', 'wp-speed-of-light') ?></th>
                            <th class="tooltipped" data-position="bottom"
                                data-tooltip="<?php esc_html_e('The Largest Contentful Paint (LCP) metric reports the render time of the largest image or text block visible within the viewport.', 'wp-speed-of-light') ?>"
                                style='text-align: left;padding-left: 45px;'>
                                <?php esc_html_e('Largest Contentful Paint', 'wp-speed-of-light') ?></th>
                            <th class="tooltipped" data-position="bottom"
                                data-tooltip="<?php esc_html_e('CLS measures the sum total of all individual layout shift scores for every unexpected layout shift that occurs during the entire lifespan of the page.', 'wp-speed-of-light') ?>"
                                style='text-align: left;padding-left: 45px;'>
                                <?php esc_html_e('Cumulative Layout Shift', 'wp-speed-of-light') ?></th>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid #EEEEEE;">
                                <?php if (!empty($latest_analysis) && !empty($latest_analysis['link-report'])) : ?>
                                <a href="<?php echo esc_url($latest_analysis['link-report']); ?>" target="_blank" class="link-to-full-report"><span class="material-icons tooltipped" data-position="top" data-tooltip="<?php esc_html_e('Open Google Page Speed full report', 'wp-speed-of-light') ?>">public</span></a>
                                <?php endif; ?>
                            <td><?php echo (isset($latest_analysis['lab-data']['first-contentful-paint']) ? esc_html($latest_analysis['lab-data']['first-contentful-paint']) : 'no data') ; ?></td>
                            <td><?php echo (isset($latest_analysis['lab-data']['interactive']) ? esc_html($latest_analysis['lab-data']['interactive']) : 'no data'); ?></td>
                            <td><?php echo (isset($latest_analysis['lab-data']['speed-index']) ? esc_html($latest_analysis['lab-data']['speed-index']) : 'no data'); ?></td>
                            <td><?php echo (isset($latest_analysis['lab-data']['total-blocking-time']) ? esc_html($latest_analysis['lab-data']['total-blocking-time']) : 'no data'); ?></td>
                            <td><?php echo (isset($latest_analysis['lab-data']['largest-contentful-paint']) ? esc_html($latest_analysis['lab-data']['largest-contentful-paint']) : 'no data'); ?></td>
                            <td><?php echo (isset($latest_analysis['lab-data']['cumulative-layout-shift']) ? esc_html($latest_analysis['lab-data']['cumulative-layout-shift']) : 'no data'); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="lastest-speed">
                    <div class="loadtime-title"><?php esc_html_e('10 lastest speed tests', 'wp-speed-of-light') ?></div>
                    <table width="100%" class="lastest-details-table  ten-latest-table" id="ten-details"
                           style="border-collapse: collapse;">
                        <tr>
                            <th class="tooltipped" data-position="bottom"
                                data-tooltip="<?php esc_html_e('Thumbnail', 'wp-speed-of-light') ?>">
                                <?php esc_html_e('Thumbnail', 'wp-speed-of-light') ?></th>
                            <th class="tooltipped" data-position="bottom"
                                data-tooltip="<?php esc_html_e('Url', 'wp-speed-of-light') ?>">
                                <?php esc_html_e('URL', 'wp-speed-of-light') ?></th>
                            <th class="tooltipped" data-position="bottom"
                                data-tooltip="<?php esc_html_e('Load time', 'wp-speed-of-light') ?>">
                                <?php esc_html_e('Load time', 'wp-speed-of-light') ?></th>
                            <th class="tooltipped" data-position="bottom"
                                data-tooltip="<?php esc_html_e('Details', 'wp-speed-of-light') ?>"></th>
                        </tr>
                        <?php if (!empty($total_analysis)) : ?>
                            <?php foreach ($total_analysis as $v) : ?>
                                <tr id="<?php echo esc_html($v['id']); ?>">
                                    <td style="text-align: center;width:25%"><img src="<?php echo esc_html($v['screenshot']) ?>"/>
                                    </td>
                                    <td style="text-align: left;width:35%"><a href="<?php echo esc_url($v['url']) ?>"
                                                                              style="text-decoration: none;"
                                                                              target="_blank"><?php echo esc_url($v['url']) ?></a>
                                    </td>
                                    <td style="text-align: center;width:15%"><?php echo esc_html($v['total-timing']) ?> sec</td>
                                    <td style="width:15% ;"><input type="button" value="<?php esc_html_e('More details', 'wp-speed-of-light') ?>"
                                                                   data-id="<?php echo esc_html($v['id']); ?>"
                                                                   class="wpsol-more-details btn waves-effect waves-light"/>
                                    </td>
                                    <td style="width:10% ;">
                                        <img src="<?php echo esc_url(WPSOL_PLUGIN_URL . 'assets/images/icon-delete.svg')?>"
                                             alt="Delete icon"
                                             class="clear-test tooltipped" data-position="top"
                                             data-tooltip="<?php esc_html_e('Remove speed test', 'wp-speed-of-light') ?>"
                                             data-id="<?php echo esc_html($v['id']); ?>"
                                        />
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </table>

                </div>
            </div>
        </div>
    </div>
    <div id="analysis" class="tab-content">
        <div class="tab-analysis-content">
            <div class="header-analysis">
                <div class="title"><?php esc_html_e('Speed analysis', 'wp-speed-of-light') ?></div>
                <div class="panel">
                    <div class="panel-content">
                        <label for="insert-url-queries"
                               class="insert-url"><?php esc_html_e('URL to analyse : ', 'wp-speed-of-light') ?>
                            <?php echo esc_url(home_url()) . '/'; ?></label>
                        <div class="panel-input">
                            <input type="hidden" value="<?php echo esc_url(home_url()); ?>" id="main-url-queries"/>
                            <input id="insert-url-queries" type="text" placeholder="<?php esc_html_e('Typing here...', 'wp-speed-of-light') ?>"
                                   name="wpsol_url_queries" value="" class="wpsol_url_queries"/>
                            <input id="query-button" type="button" value="<?php esc_html_e('Launch analysis', 'wp-speed-of-light') ?>"
                                   class="btn waves-effect waves-light btn-analysis"/>
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <div class="result-content">
                <!--box result-->
                <div class="box-result">
                    <ul>
                        <li class="col3">
                            <div class="icon icon-1">
                                <i class="material-icons">settings_input_hdmi</i>
                            </div>
                            <div class="panel">
                                <div class="title"><?php esc_html_e('Total plugins ', 'wp-speed-of-light') ?></div>
                                <div class="number blue"><?php echo esc_html($queriesParameter['plugin']['total_plugin']); ?></div>
                                <div class="note"><?php esc_html_e('Queries time =', 'wp-speed-of-light') ?>
                                    &nbsp;<?php echo ($plugin_time) ? esc_html($plugin_time) : 0; ?>
                                    &nbsp;<?php esc_html_e('sec', 'wp-speed-of-light'); ?></div>
                            </div>
                        </li>
                        <li class="col3">
                            <div class="icon icon-2">
                                <i class="material-icons">import_contacts</i>
                            </div>
                            <div class="panel">
                                <div class="title"><?php esc_html_e('Theme ', 'wp-speed-of-light') ?></div>
                                <div class="number green"><?php echo esc_html($queriesParameter['theme']['load_time']); ?>
                                    <?php esc_html_e('Sec', 'wp-speed-of-light'); ?></div>
                                <div class="note"><?php esc_html_e('Lastest speed analysis test result', 'wp-speed-of-light') ?></div>
                            </div>
                        </li>
                        <li class="col3">
                            <div class="icon icon-3">
                                <span class="dashicons dashicons-wordpress"></span>
                            </div>
                            <div class="panel">
                                <div class="title"><?php esc_html_e('WP Core ', 'wp-speed-of-light') ?></div>
                                <div class="number red"><?php echo esc_html($queriesParameter['core']['load_time']); ?>
                                    <?php esc_html_e('Sec', 'wp-speed-of-light'); ?></div>
                                <div class="note"><?php esc_html_e('WP Core Queries time', 'wp-speed-of-light') ?></div>
                            </div>
                        </li>
                    </ul>
                </div>

                <!--table plugins-->
                <div class="table-queries">
                    <table id="table-sorter-queries" class="tablesorter" align="center">
                        <thead>
                        <tr>
                            <th class="top-header"><?php esc_html_e('WordPress & Plugins', 'wp-speed-of-light'); ?></th>
                            <th><?php esc_html_e('Select', 'wp-speed-of-light'); ?></th>
                            <th><?php esc_html_e('Show', 'wp-speed-of-light'); ?></th>
                            <th><?php esc_html_e('Update', 'wp-speed-of-light'); ?></th>
                            <th><?php esc_html_e('Time', 'wp-speed-of-light'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($queriesParameter as $k => $v) :
                            if ($k === 'theme' || $k === 'core') :
                                ?>
                                <tr>
                                    <td class="top-header" style="text-transform: capitalize"><?php echo esc_html($k); ?></td>
                                    <td>
                                        <?php
                                        if (isset($v['type']['SELECT'])) {
                                            echo esc_html($v['type']['SELECT']);
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (isset($v['type']['SHOW'])) {
                                            echo esc_html($v['type']['SHOW']);
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (isset($v['type']['UPDATE'])) {
                                            echo esc_html($v['type']['UPDATE']);
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php echo esc_html($v['load_time']); ?>
                                    </td>
                                </tr>

                                <?php
                            endif;
                            if ($k === 'plugin') :
                                foreach ($v['details'] as $key => $value) :
                                    ?>
                                    <tr>
                                        <td class="top-header"><?php echo 'Plugin: ' . esc_html($key); ?></td>
                                        <td>
                                            <?php
                                            if (isset($value['type']['SELECT'])) {
                                                echo esc_html($value['type']['SELECT']);
                                            } else {
                                                echo 0;
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if (isset($value['type']['SHOW'])) {
                                                echo esc_html($value['type']['SHOW']);
                                            } else {
                                                echo 0;
                                            }

                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if (isset($value['type']['UPDATE'])) {
                                                echo esc_html($value['type']['UPDATE']);
                                            } else {
                                                echo 0;
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php echo esc_html($value['load_time']); ?>
                                        </td>
                                    </tr>
                                    <?php
                                endforeach;
                            endif;
                        endforeach;
                        ?>

                        </tbody>
                        <tr>
                            <td></td>
                            <td class="total-select-analysis"><?php echo ($select) ? esc_html($select) : 0; ?></td>
                            <td class="total-show-analysis"><?php echo ($show) ? esc_html($show) : 0; ?></td>
                            <td class="total-update-analysis"><?php echo ($update) ? esc_html($update) : 0; ?></td>
                            <td class="total-time-analysis"><?php echo ($time) ? esc_html($time) : 0; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dialog for iframe scanner -->
<div id="wpsol-scanner-dialog" class="wpsol-dialog">
    <iframe id="wpsol-scan-frame" frameborder="0"
            data-defaultsrc="">
    </iframe>
    <div id="wpsol-scan-caption">
        <?php esc_html_e('The scanner will analyze the speed and resource usage of all active plugins on your website.
         It may take several minutes, and this window must
         remain open for the scan to finish successfully.', 'wp-speed-of-light'); ?>
    </div>
</div>

<!-- Dialog for progress bar -->
<div id="wpsol-progress-dialog" class="wpsol-dialog">
    <div id="wpsol-scanning-caption">
        <?php esc_html_e('Scanning ...', 'wp-speed-of-light'); ?>
    </div>
    <div id="wpsol-progress"></div>

    <!-- View results button -->
    <div class="wpsol-big-button" id="wpsol-view-results-buttonset" style="display: none;">
        <input type="checkbox" id="wpsol-view-results-submit" class="view-results-button" checked="checked"
               data-scan-name=""/>
        <label for="wpsol-view-results-submit"
               class="btn waves-effect waves-light"><?php esc_html_e('View Results', 'wp-speed-of-light'); ?></label>
    </div>
</div>

<!-- Dialog for more details -->

<div id="wpsol-more-details-dialog" class="wpsol-modal" style="display: none;">
    <span class="wpsol-close">×</span>
    <div class="wpsol-modal-content">
    </div>
</div>

<!--Dialog-->
<div id="wpsol_analysis_strategy_modal" style="display: none">
    <div class="icon"><i class="material-icons">important_devices</i></div>
    <div class="title"><h2><?php esc_html_e('Strategy device', 'wp-speed-of-light'); ?></h2></div>
    <div class="content">
        <span><?php esc_html_e('The device running strategy to be used in analysis', 'wp-speed-of-light'); ?></span>
    </div>
    <div class="button-field">
        <button type="button" id="desktop" class="strategy-type desktop ju-button orange-button waves-effect waves-light">
            <span><?php esc_html_e('Desktop', 'wp-speed-of-light') ?></span>
        </button>

        <button type="button" id="mobile" class="strategy-type mobile ju-button orange-button waves-effect waves-light">
            <span><?php esc_html_e('MOBILE', 'wp-speed-of-light') ?></span>
        </button>
    </div>
</div>