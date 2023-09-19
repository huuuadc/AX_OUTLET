<?php

add_action('check_admin_referer', 'logout_without_confirm', 10, 2);
function logout_without_confirm($action, $result)
{
    /**
     * Allow logout without confirmation
     */
    if ($action == "log-out" && !isset($_GET['_wpnonce'])) {
        $redirect_to = wc_get_page_permalink( 'myaccount' );
        $location = str_replace('&amp;', '&', wp_logout_url($redirect_to));;
        header("Location: $location");
        die();
    }
}

/*function action_woocommerce_edit_account_form() {
    woocommerce_form_field( 'user_registration_user_birthday', array(
        'type'        => 'text',
        'label'       => __( 'Ngày sinh', 'woocommerce' ),
        'placeholder' => __( 'ngày/tháng/năm', 'woocommerce' ),
        'required'    => true,
    ), get_user_meta( get_current_user_id(), 'user_registration_user_birthday', true ));
}
add_action( 'woocommerce_edit_account_form', 'action_woocommerce_edit_account_form' );*/

// Validate - my account
function action_woocommerce_save_account_details_errors( $args ){
    if ( isset($_POST['user_registration_user_birthday']) && empty($_POST['user_registration_user_birthday']) ) {
        $args->add( 'error', __( 'Vui lòng nhập ngày sinh nhật của bạn', 'woocommerce' ) );
    }
}
add_action( 'woocommerce_save_account_details_errors','action_woocommerce_save_account_details_errors', 10, 1 );

// Save - my account
function action_woocommerce_save_account_details( $user_id ) {
    if( isset($_POST['user_registration_user_birthday']) && ! empty($_POST['user_registration_user_birthday']) ) {
        update_user_meta( $user_id, 'user_registration_user_birthday', sanitize_text_field($_POST['user_registration_user_birthday']) );
    }
}
add_action( 'woocommerce_save_account_details', 'action_woocommerce_save_account_details', 10, 1 );


add_filter('woocommerce_default_address_fields','wc_change_field_default_address_fields', 10);
function wc_change_field_default_address_fields($fields){

    global $wpdb;

    $arg_city['']       = __( 'Tỉnh/Thành Phố' );
    $arg_districts['']  = __('Quận/Huyện');
    $arg_wards['']      = __('Phường/Xã');

    $data_city = $wpdb->get_results("Select province_id,tiki_code,province_name from {$wpdb->prefix}woocommerce_province");
    foreach ($data_city as $value_city){
        $arg_city[$value_city->tiki_code] = $value_city->province_name;
    }

    $fields['city'] =  array(
        'type'          => 'select',
        'label'         => __("Quận/Huyện", "woocommerce") ,
        'options'       => $arg_city,
        'autocomplete'  =>  'address-level1',
        'input_class'   => array(
            'city_select',
        ),
        'priority'      => 40,
        'required'      => true
    ) ;
    $fields['district'] =  array(
        'type'          => 'select',
        'label'         => __("Quận/Huyện", "woocommerce") ,
        'options'       => $arg_districts,
        'autocomplete'  =>  'address-level1',
        'input_class'   => array(
            'district_select',
        ),
        'priority'      => 41,
        'required'      => true
    ) ;
    $fields['ward'] =  array(
        'type'          => 'select',
        'label'        => __( 'Phường / Xã', 'woocommerce' ),
        'required'     => true,
        'options'       => $arg_wards,
        'input_class'   => array(
            'ward_select',
        ),
        'autocomplete' => 'address-level1',
        'priority'     => 42,
    );
    return $fields;
}

add_filter('woocommerce_address_to_edit','address_user_field_change',10,2);
function address_user_field_change($address, $load_address){

    if(isset($address['billing_postcode']['class'])) $address['billing_postcode']['class'][]        = 'hidden';
    if(isset($address['billing_country']['class'])) $address['billing_country']['class'][]          = 'hidden';
    if(isset($address['billing_phone']['class'])) $address['billing_phone']['class'][]              = 'hidden';
    if(isset($address['billing_email']['class'])) $address['billing_email']['class'][]              = 'hidden';

    write_log($address);

    return $address;
}

