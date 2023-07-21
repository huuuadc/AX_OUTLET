<?php

add_filter('woocommerce_checkout_fields', 'checkout_add_change_fields');

function checkout_add_change_fields($fields){

    write_log(WC()->session);

    foreach ($fields as $key => $value){
        if (isset($fields[$key][$key.'_postcode'])) {
            unset($fields[$key][$key.'_postcode']);
        }
        if (isset($fields[$key][$key.'_country'])) {
            unset($fields[$key][$key.'_country']);
        }

    }

    $fields['billing']['billing_phone']['priority'] = 30;
    $fields['billing']['billing_email']['priority'] = 35;
    $fields['billing']['billing_address_1']['priority'] = 90;

    global $wpdb;

    $arg_city[''] = __( 'Tỉnh/Thành Phố' );
    $arg_districts[''] = __('Quận/Huyện');
    $arg_wards[''] = __('Phường/Xã');

    $data_city = $wpdb->get_results("Select province_id,tiki_code,province_name from {$wpdb->prefix}woocommerce_province");
    foreach ($data_city as $value_city){
        $arg_city[$value_city->tiki_code] = $value_city->province_name;
    }

    $data_district = $wpdb->get_results("Select tiki_code,district_name 
            from {$wpdb->prefix}woocommerce_district where left(tiki_code,5) = '{$data_city['0']->tiki_code}' ");
    foreach ($data_district as $value_city){
        $arg_districts[$value_city->tiki_code] = $value_city->district_name;
    }

    $data_ward = $wpdb->get_results("Select tiki_code,ward_name 
            from {$wpdb->prefix}woocommerce_ward where left(tiki_code,8) = '{$data_district['0']->tiki_code}'");
    foreach ($data_ward as $value_city){
        $arg_wards[$value_city->tiki_code] = $value_city->ward_name;
    }

    $city_args = wp_parse_args( array(
        'type' => 'select',
        'options' => $arg_city,
        'input_class' => array(
            'country_select',
        ),
    ), $fields['billing']['billing_city'] );

    $fields['billing']['billing_city'] = $city_args;

    $district = array(
        'type'          => 'select',
        'label'         => __("Quận/Huyện", "woocommerce") ,
        'options'       => $arg_districts,
        'input_class'   => array(
            'district_select',
        ),
        'priority'      => 75,
        'required'      => 1
    ) ;

    $fields['billing']['billing_district'] = $district;

    $ward = array(
        'type' => 'select',
        'label'  => __("Phường/Xã", "woocommerce") ,
        'options' => $arg_wards,
        'input_class' => array(
            'ward_select',
        ),
        'priority' => 80,
        'required' => 1
    ) ;

    $fields['billing']['billing_ward'] = $ward;

    return $fields;
}