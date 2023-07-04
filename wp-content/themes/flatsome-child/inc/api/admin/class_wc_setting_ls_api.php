<?php
/**
 *
 */

class WC_Settings_Tab_Ls_Api {


    /**
     * Bootstraps the class and hooks required actions & filters.
     *
     */
    public static function init() {
        add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
        add_action( 'woocommerce_settings_tabs_settings_tab_ls_api', __CLASS__ . '::settings_tab' );
        add_action( 'woocommerce_update_options_settings_tab_ls_api', __CLASS__ . '::update_settings' );

        //add admin ls api js
        add_action( 'admin_enqueue_scripts',__CLASS__.'::admin_ls_script');

        //add custom type
        add_action( 'woocommerce_admin_field_button', __CLASS__ . '::output_button', 10, 1 );
    }

    /**
     *
     * add script in admin page
     *
     * **/

    public static function admin_ls_script($hook){

        if ( 'woocommerce_page_wc-settings' != $hook ) {
            return;
        }

        wp_enqueue_script( 'admin_ls_api', get_stylesheet_directory_uri() . '/assets/js/admin/admin-ls-api.js',array( 'jquery' ),'',true );

    }

    /**
     *
     * Custom type
     *
     * **/

    public static function output_button($value){
        echo '<div id="toast"></div>';
        echo '<tr valign="top">';
        echo '<th scope="row" class="titledesc"></th>';
        echo '<td><input type="button" name="test_connect" class="button-primary" value="Test Connect Tại Đây" onclick="send_test_connect_ls()" /></td>';
        echo '</tr>';

    }

    /**
     *
     * add notice admin page
     *
     * @return void
     *
     */

    function sample_admin_notice__success() {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e( 'Done!', 'sample-text-domain' ); ?></p>
        </div>
        <?php
    }


    /**
     * Add a new settings tab to the WooCommerce settings tabs array.
     *
     * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
     * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
     */
    public static function add_settings_tab( $settings_tabs ) {
        $settings_tabs['settings_tab_ls_api'] = __( 'LS API', 'woocommerce-settings-ls-api' );
        return $settings_tabs;
    }


    /**
     * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
     *
     * @uses woocommerce_admin_fields()
     * @uses self::get_settings()
     */
    public static function settings_tab() {
        woocommerce_admin_fields( self::get_settings() );
    }


    /**
     * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
     *
     * @uses woocommerce_update_options()
     * @uses self::get_settings()
     */
    public static function update_settings() {
        woocommerce_update_options( self::get_settings() );
    }


    /**
     * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
     *
     * @return array Array of settings for @see woocommerce_admin_fields() function.
     */
    public static function get_settings() {

        $settings = array(
            'section_setting' => array(
                'name'     => __( 'Setting ls api', 'woocommerce-settings-tab-ls-api' ),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'wc_settings_tab_ls_api_setting_section'
            ),
            'ls_username' => array(
                'name'     => __( 'Username', 'woocommerce-settings-tab-ls-api' ),
                'type'     => 'text',
                'desc'     => '',
                'id'       => 'wc_settings_tab_ls_api_username'
            ),
            'ls_password' => array(
                'name'     => __( 'Password', 'woocommerce-settings-tab-ls-api' ),
                'type'     => 'password',
                'desc'     => '',
                'id'       => 'wc_settings_tab_ls_api_password'
            ),
            'base_url' => array(
                'name' => __( 'Base url', 'woocommerce-settings-tab-ls-api' ),
                'type' => 'url',
                'desc' => __( '<strong>test: http://dafc_ecom.dafc.com.vn:1818</strong><br><strong>prod: https://jql_online.dafc.com.vn</strong>', 'woocommerce-settings-tab-ls-api' ),
                'id'   => 'wc_settings_tab_ls_api_url'
            ),
            'ls_test_connect' => array(
                'name'     => __( 'Test Connect', 'woocommerce-settings-tab-ls-api' ),
                'type'     => 'button',
                'desc'     => '',
                'id'       => 'wc_settings_tab_ls_api_test_connect'
            ),
            'section_end' => array(
                'type' => 'sectionend',
                'id' => 'wc_settings_tab_ls_api_section_end'
            )
        );

        return apply_filters( 'wc_settings_tab_ls_api_settings', $settings );
    }

}

WC_Settings_Tab_Ls_Api::init();