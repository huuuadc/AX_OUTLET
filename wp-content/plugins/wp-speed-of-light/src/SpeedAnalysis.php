<?php
namespace Joomunited\WPSOL;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class SpeedAnalysis
 */
class SpeedAnalysis
{
    /**
     * Api url
     *
     * @var string
     */
    private static $api = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';
    /**
     * Api key
     *
     * @var string
     */
    private static $api_key = 'AIzaSyBK5KSSJPMnWpFu58LMIGoC8iXmqdjuaek';
    /**
     * Test page - load time
     *
     * @return void
     */
    public static function loadPageTime()
    {
        check_ajax_referer('wpsolAnalysisJS', 'ajaxnonce');

        if (isset($_POST['urlPage'])) {
            $url = $_POST['urlPage'];
        }
        if (isset($_POST['stategy_type'])) {
            $stategy_type = $_POST['stategy_type'];
        }

        if (empty($url)) {
            echo json_encode(array('status' => false, 'message' => 'Empty URL!'));
            exit;
        }

        $url = self::setupUrl($url, $stategy_type);
        $result = self::executeAnalysis($url);

        echo json_encode($result);
        exit;
    }

    /**
     * Make url to analysis
     *
     * @param string $url          Current URL
     * @param string $stategy_type Device type
     *
     * @return string
     */
    public static function setupUrl($url, $stategy_type)
    {
        $url = trim($url);

        // Check if localhost, get default url
        if (strpos($url, 'localhost') !== false ||
            strpos($url, '127.0.0.1') !== false ||
            strpos($url, '172.20.0.3') !== false) {
            $url = 'https://www.joomunited.com/';
        }

        $url = self::$api . '?url=' . urlencode($url);

        if (!empty(self::$api_key)) {
            $url .= '&key=' . self::$api_key;
        }

        if (!empty($stategy_type)) {
            $url .= '&strategy=' . strtoupper($stategy_type);
        }

        return $url;
    }

    /**
     * Process analysis
     *
     * @param string $url Url to analysis
     *
     * @return array
     */
    public static function executeAnalysis($url)
    {
        $response = self::getResponseObject($url);

        if (empty($response)) {
            return array('status' => false, 'message' => esc_html__('Couldn\'t get the response object from API', 'wp-speed-of-light'));
        }

        $response = json_decode($response, true);

        if (isset($response['error'])) {
            return array('status' => false, 'message' => $response['error']['errors'][0]['message']);
        }

        /**
         * Action called after a page analysis has been completed on pagespeed insights
         *
         * @param array result from pagespeed
         */
        do_action('wpsol_retrieve_raw_page_analysis', $response);

        $status = self::analyzeResponse($response);

        if (!$status) {
            return array('status' => false, 'message' => esc_html__('An error occurred during analysis!', 'wp-speed-of-light'));
        }

        return array('status' => true, 'message' => esc_html__('Analysis process successfully!', 'wp-speed-of-light'));
    }

    /**
     * Get result from response
     *
     * @param string $response Response
     *
     * @return integer|null|string
     */
    public static function analyzeResponse($response)
    {
        if (empty($response)) {
            return false;
        }

        $default_analysis = array(
            'id' => md5(time()),
            'url' => '',
            'score' => 0,
            'screenshot' => '',
            'analysis-time' => time(),
            'total-timing' => 0,
            'link-report' => '',
            'field-data' => array(
                'first_contentfull_paint_ms' => 0,
                'first_input_delay_ms' => 0,
                'largest_contentfull_paint_ms' => 0,
                'cumulative_layout_shift_score' => 0
            ),
            'origin-summary' => array(
                'first_contentfull_paint_ms' => 0,
                'first_input_delay_ms' => 0,
                'largest_contentfull_paint_ms' => 0,
                'cumulative_layout_shift_score' => 0
            ),
            'lab-data' => array(
                'first-contentful-paint' => 0,
                'interactive' => 0,
                'speed-index' => 0,
                'total-blocking-time' => 0,
                'largest-contentful-paint' => 0,
                'cumulative-layout-shift' => 0
            )
        );
        $analysis = get_option('wpsol_loadtime_analysis_lastest', $default_analysis);

        $analysis['id'] = md5(time());

        if (isset($response['lighthouseResult']['finalUrl'])) {
            $analysis['link-report'] = 'https://developers.google.com/speed/pagespeed/insights/?url='. urlencode($response['lighthouseResult']['finalUrl']) . '&tab=' . (isset($_POST['stategy_type']) ? $_POST['stategy_type'] : 'desktop'); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Ajax checked in the previous function
        } else {
            $analysis['link-report'] = '';
        }

        $analysis['url'] = isset($response['lighthouseResult']['finalUrl']) ? $response['lighthouseResult']['finalUrl'] : 'no data';
        $analysis['score'] = self::convertValue($response['lighthouseResult']['categories']['performance']['score'], 'percent');
        $analysis['screenshot'] = isset($response['lighthouseResult']['audits']['final-screenshot']['details']['data']) ? $response['lighthouseResult']['audits']['final-screenshot']['details']['data'] : 'no data';
        $analysis['analysis-time'] = date('Y-m-d H:i:s');
        $analysis['total-timing'] = self::convertValue($response['lighthouseResult']['timing']['total'], 'ms');

        $analysis['field-data']['first_contentfull_paint_ms'] = self::convertValue($response['loadingExperience']['metrics']['FIRST_CONTENTFUL_PAINT_MS']['percentile'], 'ms');
        $analysis['field-data']['first_input_delay_ms'] = self::convertValue($response['loadingExperience']['metrics']['FIRST_INPUT_DELAY_MS']['percentile'], 'ms');
        $analysis['field-data']['largest_contentfull_paint_ms'] = self::convertValue($response['loadingExperience']['metrics']['LARGEST_CONTENTFUL_PAINT_MS']['percentile'], 'ms');
        $analysis['field-data']['cumulative_layout_shift_score'] = self::convertValue($response['loadingExperience']['metrics']['CUMULATIVE_LAYOUT_SHIFT_SCORE']['percentile'], 'score');

        $analysis['origin-summary']['first_contentfull_paint_ms'] = self::convertValue($response['originLoadingExperience']['metrics']['FIRST_CONTENTFUL_PAINT_MS']['percentile'], 'ms');
        $analysis['origin-summary']['first_input_delay_ms'] = self::convertValue($response['originLoadingExperience']['metrics']['FIRST_INPUT_DELAY_MS']['percentile'], 'ms');
        $analysis['origin-summary']['largest_contentfull_paint_ms'] = self::convertValue($response['originLoadingExperience']['metrics']['LARGEST_CONTENTFUL_PAINT_MS']['percentile'], 'ms');
        $analysis['origin-summary']['cumulative_layout_shift_score'] = self::convertValue($response['originLoadingExperience']['metrics']['CUMULATIVE_LAYOUT_SHIFT_SCORE']['percentile'], 'score');

        $analysis['lab-data']['first-contentful-paint'] = isset($response['lighthouseResult']['audits']['first-contentful-paint']['displayValue']) ? $response['lighthouseResult']['audits']['first-contentful-paint']['displayValue'] : 'no data';
        $analysis['lab-data']['interactive'] = isset($response['lighthouseResult']['audits']['interactive']['displayValue']) ? $response['lighthouseResult']['audits']['interactive']['displayValue'] : 'no data';
        $analysis['lab-data']['speed-index'] = isset($response['lighthouseResult']['audits']['speed-index']['displayValue']) ? $response['lighthouseResult']['audits']['speed-index']['displayValue'] : 'no data';
        $analysis['lab-data']['total-blocking-time'] = isset($response['lighthouseResult']['audits']['total-blocking-time']['displayValue']) ? $response['lighthouseResult']['audits']['total-blocking-time']['displayValue'] : 'no data';
        $analysis['lab-data']['largest-contentful-paint'] = isset($response['lighthouseResult']['audits']['largest-contentful-paint']['displayValue']) ? $response['lighthouseResult']['audits']['largest-contentful-paint']['displayValue'] : 'no data';
        $analysis['lab-data']['cumulative-layout-shift'] = isset($response['lighthouseResult']['audits']['cumulative-layout-shift']['displayValue']) ? $response['lighthouseResult']['audits']['cumulative-layout-shift']['displayValue'] : 'no data';

        update_option('wpsol_loadtime_analysis_lastest', $analysis);

        // Set 10 lastest speed test
        $total_analysis = get_option('wpsol_loadtime_analysis_total', array());

        array_push($total_analysis, $analysis);
        if (count($total_analysis) > 10) {
            array_shift($total_analysis);
        }

        update_option('wpsol_loadtime_analysis_total', $total_analysis);

        /**
         * Action called after a page analysis has been completed on page insight and processed by WP Speed Of Light
         *
         * @param array Analysis result
         */
        do_action('wpsol_retrieve_page_analysis', $total_analysis);

        return true;
    }

    /**
     * Get response from API
     *
     * @param string $url Current URL
     *
     * @return integer|null|string
     */
    public static function getResponseObject($url)
    {
        $output = '';
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $output = curl_exec($ch);
            curl_close($ch);
        } else {
            $file_headers = get_headers($url);

            if (strpos(strtolower($file_headers[0]), '200 ok') !== false) {
                $output = file_get_contents($url);
            }
        }

        return $output;
    }

    /**
     * Convert value type
     *
     * @param string $value Value
     * @param string $type  Type needed
     *
     * @return float|string
     */
    public static function convertValue($value, $type)
    {
        if (is_null($value)) {
            return 'no data';
        }

        if ($type === 'percent') {
            $value = round($value * 100);
        }
        if ($type === 'score') {
            $value = (float)number_format($value / 100, 4);
        }
        if ($type === 'ms') {
            $value = (float)number_format($value / 1000, 4);
            $value = (string)$value . ' s';
        }

        return $value;
    }

    /**
     *  Delete details
     *
     * @return void
     */
    public static function deleteDetails()
    {
        check_ajax_referer('wpsolAnalysisJS', 'ajaxnonce');
        $id = $_POST['id'];
        $total_analysis = get_option('wpsol_loadtime_analysis_total');
        foreach ($total_analysis as $k => $v) {
            if ($v['id'] === $id) {
                unset($total_analysis[$k]);
            }
        }
        update_option('wpsol_loadtime_analysis_total', $total_analysis);
        echo(esc_html($id));
        exit;
    }

    /**
     * Get result of scan queries
     *
     * @return array
     */
    public function getInfoQueries()
    {
        $result = array('theme' => array(
            'load_time' => 0,
            'type' => array(
                'SELECT' => 0,
                'UPDATE' => 0,
                'SHOW' => 0,
                'INSERT' => 0,
                'DESCRIBE' => 0
            ),
        ),
            'core' => array(
                'load_time' => 0,
                'type' => array(
                    'SELECT' => 0,
                    'UPDATE' => 0,
                    'SHOW' => 0,
                    'INSERT' => 0,
                    'DESCRIBE' => 0
                ),
            ),
            'plugin' => array(
                'total_plugin' => 0,
                'load_time' => 0,
                'details' => array(),
            ));
        $queries = get_option('wpsol_scan_queries');
        if (!empty($queries)) {
            foreach ($queries['dbs']['$wpdb']->rows as $row) {
                $i = 0;
                //get theme
                $compare_theme = strpos($row['stack'], 'theme');
                $compare_core1 = strpos($row['stack'], 'wp-load');
                $compare_core2 = strpos($row['stack'], 'wp-settings');
                $compare_core3 = strpos($row['stack'], 'wp-config');
                $compare_core4 = strpos($row['stack'], 'wp-admin');
                $compare_core5 = strpos($row['stack'], 'wp-blog-header');
                $compare_plugin = strpos($row['stack'], 'plugins');
//            var_dump($compare_plugin);
                if ($compare_theme !== false) {
                    $result['theme']['load_time'] += round($row['ltime'], 5);

                    switch ($row['type']) {
                        case 'SELECT':
                            $i++;
                            $result['theme']['type']['SELECT'] += $i;
                            break;
                        case 'SHOW':
                            $i++;
                            $result['theme']['type']['SHOW'] += $i;
                            break;
                        case 'INSERT':
                            $i++;
                            $result['theme']['type']['INSERT'] += $i;
                            break;
                        case 'UPDATE':
                            $i++;
                            $result['theme']['type']['UPDATE'] += $i;
                            break;
                        case 'DESCRIBE':
                            $i++;
                            $result['theme']['type']['DESCRIBE'] += $i;
                            break;
                    }
                } elseif ($compare_core1 !== false || $compare_core2 !== false ||
                    $compare_core3 !== false || $compare_core4 !== false || $compare_core5 !== false) {
                    $result['core']['load_time'] += round($row['ltime'], 5);

                    switch ($row['type']) {
                        case 'SELECT':
                            $i++;
                            $result['core']['type']['SELECT'] += $i;
                            break;
                        case 'SHOW':
                            $i++;
                            $result['core']['type']['SHOW'] += $i;
                            break;
                        case 'INSERT':
                            $i++;
                            $result['core']['type']['INSERT'] += $i;
                            break;
                        case 'UPDATE':
                            $i++;
                            $result['core']['type']['UPDATE'] += $i;
                            break;
                        case 'DESCRIBE':
                            $i++;
                            $result['core']['type']['DESCRIBE'] += $i;
                            break;
                    }
                }
                if ($compare_plugin !== false) {
                    $stacks = explode(',', $row['stack']);
                    foreach ($stacks as $stack) {
                        if (strpos($stack, 'plugins') !== false) {
                            $str = strstr($stack, 'plugins');
                            $str = rtrim($str, "')");
                            $str = substr($str, 8);
                            $arr = explode('\\', $str);
                            $result['plugin']['details'][$arr[0]]['load_time'] = round($row['ltime'], 5);
                            $result['plugin']['details'][$arr[0]]['type'] = array(
                                'SELECT' => 0,
                                'SHOW' => 0,
                                'INSERT' => 0,
                                'UPDATE' => 0,
                                'DESCRIBE' => 0,
                            );
                            switch ($row['type']) {
                                case 'SELECT':
                                    $i++;
                                    $result['plugin']['details'][$arr[0]]['type']['SELECT'] += $i;
                                    break;
                                case 'SHOW':
                                    $i++;
                                    $result['plugin']['details'][$arr[0]]['type']['SHOW'] += $i;
                                    break;
                                case 'INSERT':
                                    $i++;
                                    $result['plugin']['details'][$arr[0]]['type']['INSERT'] += $i;
                                    break;
                                case 'UPDATE':
                                    $i++;
                                    $result['plugin']['details'][$arr[0]]['type']['UPDATE'] += $i;
                                    break;
                                case 'DESCRIBE':
                                    $i++;
                                    $result['plugin']['details'][$arr[0]]['type']['DESCRIBE'] += $i;
                                    break;
                            }
                            $result['plugin']['load_time'] = array_sum($result['plugin']['details'][$arr[0]]);
                        }
                    }
                    $result['plugin']['total_plugin'] = count($result['plugin']['details']);
                }
            }
        }
        update_option('wpsol_database_queries_analysis', $result);
        return $result;
    }

    /**
     *  Scan tab 2
     *
     * @return void
     */
    public static function startScanQuery()
    {
        check_ajax_referer('wpsolAnalysisJS', 'ajaxnonce');
        $filename = sanitize_file_name(basename($_POST['wpsol_scan_name_query']));
        // filename option
        $opt = get_option('wpsol_profiles_option');
        if (empty($opt) || !is_array($opt)) {
            $opt = array();
            $flag = false;
        } else {
            $flag = true;
        }
        $opt['query_enabled'] = array(
            'name' => $filename,
        );
        update_option('wpsol_profiles_option', $opt);

        if (false === $flag) {
            self::ajaxDie(0);
        } else {
            self::ajaxDie(1);
        }
    }

    /**
     * Stop scan tab2
     *
     * @return void
     */
    public static function stopScanQuery()
    {
        $opts = get_option('wpsol_profiles_option');
        // Turn off scanning
        $opts['query_enabled'] = false;
        update_option('wpsol_profiles_option', $opts);
        if (!empty($opts) && is_array($opts) && array_key_exists('name', $opts)) {
            self::ajaxDie('');
        } else {
            self::ajaxDie(0);
        }
    }

    /**
     * Stop ajax
     *
     * @param string $message Message display
     *
     * @return void
     */
    public static function ajaxDie($message)
    {
        global $wp_version;
        if (version_compare($wp_version, '3.4') >= 0) {
            wp_die(esc_html($message));
        } else {
            die(esc_html($message));
        }
    }

    /**
     * Display more details
     *
     * @return void
     */
    public static function moreDetails()
    {
        check_ajax_referer('wpsolAnalysisJS', 'ajaxnonce');
        $output = '';
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
        }

        $total_analysis = get_option('wpsol_loadtime_analysis_total');
        foreach ($total_analysis as $v) {
            if ($v['id'] === $id) {
                $output .= '<tr><th class="tooltipped" data-position="bottom" data-tooltip="' .
                    __('Thumbnail details', 'wp-speed-of-light') . '">' .
                    __('Thumbnail', 'wp-speed-of-light') . '</th>';
                $output .= '<th class="tooltipped" data-position="bottom" data-tooltip="' .
                    __('Link to the full report of the google page speed website', 'wp-speed-of-light') . '">' .
                    __('Full report', 'wp-speed-of-light') . '</th>';
                $output .= '<th class="tooltipped" data-position="bottom" data-tooltip="' .
                    __('Lighthouse performance scoring', 'wp-speed-of-light') . '">' .
                    __('Score', 'wp-speed-of-light') . '</th>';
                $output .= '<th class="tooltipped" data-position="bottom" data-tooltip="' .
                    __('Time to load page details', 'wp-speed-of-light') . '">' .
                    __('Load time', 'wp-speed-of-light') . '</th>';
                $output .= '<th class="tooltipped" data-position="bottom" data-tooltip="' .
                    __('First Contentful Paint marks the time at which the first text or image is painted', 'wp-speed-of-light') . '">' .
                    __('First Contentful Paint', 'wp-speed-of-light') . '</th>';
                $output .= '<th class="tooltipped" data-position="bottom" data-tooltip="' .
                    __('Time to Interactive is the amount of time it takes for the page to become fully interactive', 'wp-speed-of-light') . '">' .
                    __('Time to Interactive', 'wp-speed-of-light') . '</th>';
                $output .= '<th class="tooltipped" data-position="bottom" data-tooltip="' .
                    __('Speed Index shows how quickly the contents of the page are visibly populated', 'wp-speed-of-light') . '">' .
                    __('Speed Index', 'wp-speed-of-light') . '</th>';
                $output .= '<th class="tooltipped" data-position="bottom" data-tooltip="' .
                    __('The total amount of time between First Contentful Paint (FCP) and Time to Interactive (TTI) where the main thread was blocked for long enough to prevent input responsiveness.', 'wp-speed-of-light') . '">' .
                    __('Total Blocking Time', 'wp-speed-of-light') . '</th>';
                $output .= '<th class="tooltipped" data-position="bottom" data-tooltip="' .
                    __('The Largest Contentful Paint (LCP) metric reports the render time of the largest image or text block visible within the viewport.', 'wp-speed-of-light') . '">' .
                    __('Largest Contentful Paint', 'wp-speed-of-light') . '</th>';
                $output .= '<th class="tooltipped" data-position="bottom" data-tooltip="' .
                    __('CLS measures the sum total of all individual layout shift scores for every unexpected layout shift that occurs during the entire lifespan of the page.', 'wp-speed-of-light') . '">' .
                    __('Cumulative Layout Shift', 'wp-speed-of-light') . '</th></tr>';

                $output .= '<tr><td><img src="' . $v['screenshot'] . '"></td>';
                $output .= '<td>' . (!empty($v['link-report']) ? '<a href="'.$v['link-report'].'" target="_blank" class="link-to-full-report"><span class="material-icons tooltipped" data-position="top" data-tooltip="'.esc_html('Open Google Page Speed full report', 'wp-speed-of-light').'">public</span></a>' : '') . '</td>';
                $output .= '<td>' . $v['score'] . '</td>';
                $output .= '<td>' . $v['total-timing'] . '</td>';
                $output .= '<td>' . $v['lab-data']['first-contentful-paint'] . '</td>';
                $output .= '<td>' . $v['lab-data']['interactive'] . '</td>';
                $output .= '<td>' . $v['lab-data']['speed-index'] . '</td>';
                $output .= '<td>' . $v['lab-data']['total-blocking-time'] . '</td>';
                $output .= '<td>' . $v['lab-data']['largest-contentful-paint'] . '</td>';
                $output .= '<td>' . $v['lab-data']['cumulative-layout-shift'] . '</td>';
            }
        }
        echo json_encode('<table class="wpsol-table-detail" style="width:100%;border-collapse: collapse;">' .
            $output .
            '</table>');
        exit;
    }

    /**
     * Get resulte of total query
     *
     * @param string $queriesParameter Query get parameter
     * @param string $method           Method of query
     *
     * @return integer
     */
    public function getTotalResultQueries($queriesParameter, $method)
    {
        $type = 0;
        $type += $queriesParameter['theme']['type'][$method];
        $type += $queriesParameter['core']['type'][$method];
        foreach ($queriesParameter['plugin']['details'] as $k => $v) {
            $type += $v['type'][$method];
        }
        return $type;
    }
}
