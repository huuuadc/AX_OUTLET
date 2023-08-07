<?php
namespace Joomunited\WPSOL\Install;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class InstallWizard
 */
class InstallWizard
{
    /**
     * Init step params
     *
     * @var array
     */
    protected $steps = array(
            'environment' => array(
                    'name' => 'Environment Check',
                    'view' => 'environment',
                    'action' => 'saveEvironment'
            ),
            'quick_config' => array(
                    'name' => 'Quick Configuration',
                    'view' => 'quickConfig',
                    'action' => 'saveQuickConfig'
            ),
            'main_optimization' => array(
                    'name' => 'Main Optimization',
                    'view' => 'mainOptimization',
                    'action' => 'saveMainOptimization',
            ),
            'advanced_config' => array(
                    'name' => 'Advanced Configuration',
                    'view' => 'advancedConfig',
                    'action' => 'saveAdvancedConfig'
            )
    );
    /**
     * Init current step params
     *
     * @var array
     */
    protected $current_step = array();
    /**
     * InstallWizard constructor.
     */
    public function __construct()
    {
        if (current_user_can('manage_options')) {
            add_action('admin_menu', array($this, 'adminMenus'));
            add_action('admin_init', array($this, 'runWizard'));
        }
    }
    /**
     * Add admin menus/screens.
     *
     * @return void
     */
    public function adminMenus()
    {
        add_dashboard_page('', '', 'manage_options', 'wpsol-wizard', '');
    }

    /**
     * Execute wizard
     *
     * @return void
     */
    public function runWizard()
    {
        // phpcs:disable WordPress.Security.NonceVerification.Recommended -- View request, no action
        if (!isset($_GET['page']) || 'wpsol-wizard' !== $_GET['page']) {
            return;
        }
        // Enqueue script and style
        wp_enqueue_style(
            'wpsol_wizard',
            WPSOL_PLUGIN_URL . 'assets/css/install-wizard.css',
            array(),
            WPSOL_VERSION
        );
        wp_enqueue_style(
            'wpsol-css-framework',
            WPSOL_PLUGIN_URL.'assets/css/wp-css-framework/style.css'
        );

        wp_enqueue_script(
            'wpsol_wizard_js',
            WPSOL_PLUGIN_URL . 'assets/js/install-wizard.js',
            array('jquery'),
            WPSOL_VERSION,
            true
        );
        $nonce1 =  wp_create_nonce('wpsolSpeedOptimizationSystem');
        wp_localize_script('wpsol_wizard_js', 'speedoptimizeNonce', array('ajaxnonce' => $nonce1));
        wp_localize_script('wpsol_wizard_js', 'ajaxURL', array('define' => admin_url('admin-ajax.php')));

        // Get step
        $this->steps = apply_filters('wpsol_setup_wizard_steps', $this->steps);
        $this->current_step  = isset($_GET['step']) ? sanitize_key($_GET['step']) : current(array_keys($this->steps));

        // Save action
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- No action, nonce is not required
        if (!empty($_POST['wpsol_save_step']) && isset($this->steps[$this->current_step]['action'])) {
            call_user_func(array('Joomunited\WPSOL\Install\Wizard', $this->steps[$this->current_step]['action']), $this->current_step);
        }

        // Render
        $this->setHeader();
        if (!isset($_GET['step'])) {
            require_once(WPSOL_PLUGIN_DIR . 'src/views/wizard/wizard.php');
        } elseif (isset($_GET['step']) && $_GET['step'] === 'wizard_done') {
            require_once(WPSOL_PLUGIN_DIR . 'src/views/wizard/dashboard.php');
        } else {
            $this->setMenu();
            $this->setContent();
        }
        $this->setFooter();
        // phpcs:enable
        exit();
    }


    /**
     * Get next link step
     *
     * @param string $step Current step
     *
     * @return string
     */
    public function getNextLink($step = '')
    {
        if (!$step) {
            $step = $this->current_step;
        }

        $keys = array_keys($this->steps);

        if (end($keys) === $step) {
            return add_query_arg('step', 'wizard_done', remove_query_arg('activate_error'));
        }

        $step_index = array_search($step, $keys, true);
        if (false === $step_index) {
            return '';
        }

        return add_query_arg('step', $keys[$step_index + 1], remove_query_arg('activate_error'));
    }

    /**
     * Output the menu for the current step.
     *
     * @return void
     */
    public function setMenu()
    {
        $output_steps = $this->steps;
        ?>
        <div class="wpsol-wizard-steps">
            <ul class="wizard-steps">
                <?php
                $i = 0;
                foreach ($output_steps as $key => $step) {
                    $position_current_step = array_search($this->current_step, array_keys($this->steps), true);
                    $position_step = array_search($key, array_keys($this->steps), true);
                    $is_visited = $position_current_step > $position_step;
                    $i ++;
                    if ($key === $this->current_step) {
                        ?>
                        <li class="actived"><div class="layer"><?php echo esc_html($i) ?></div></li>
                        <?php
                    } elseif ($is_visited) {
                        ?>
                        <li class="visited">
                            <a href="<?php echo esc_url(add_query_arg('step', $key, remove_query_arg('activate_error'))); ?>">
                                <div class="layer"><?php echo esc_html($i) ?></div></a>
                        </li>
                        <?php
                    } else {
                        ?>
                        <li><div class="layer"><?php echo esc_html($i) ?></div></li>
                        <?php
                    }
                }
                ?>
            </ul>
        </div>
        <?php
    }


    /**
     * Output the content for the current step.
     *
     * @return void
     */
    public function setContent()
    {
        echo '<div class="wizard-content">';
        if (!empty($this->steps[$this->current_step]['view'])) {
            require_once(WPSOL_PLUGIN_DIR . 'src/views/wizard/' . $this->steps[$this->current_step]['view'] . '.php');
        }
        echo '</div>';
    }

    /**
     * Setup Wizard Header.
     *
     * @return void
     */
    public function setHeader()
    {
        set_current_screen();
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta name="viewport" content="width=device-width" />
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title><?php esc_html_e('Speed Of Light &rsaquo; Setup Wizard', 'wp-speed-of-light'); ?></title>
            <?php do_action('admin_print_styles'); ?>
            <?php do_action('admin_head'); ?>
        </head>
        <body class="wpsol-wizard-setup wp-core-ui">
        <div class="wpsol-wizard-content">
        <?php
    }

    /**
     * Setup Wizard Footer.
     *
     * @return void
     */
    public function setFooter()
    {
        ?>
        </div>
        </body>
        <?php wp_print_footer_scripts(); ?>
        </html>
        <?php
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
}
