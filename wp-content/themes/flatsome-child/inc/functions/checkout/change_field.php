<?php

add_filter('woocommerce_checkout_fields', 'checkout_add_change_fields');

function checkout_add_change_fields($fields){

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

    $city_args = wp_parse_args( array(
        'type' => 'select',
        'options' => $arg_city,
        'input_class' => array(
            'country_select',
        )
    ), $fields['billing']['billing_city'] );

    $fields['billing']['billing_city'] = $city_args;

    $district = array(
        'type' => 'select',
        'options' => $arg_districts,
        'input_class' => array(
            'country_select',
        ),
        'priority' => 75
    ) ;

    $fields['billing']['billing_district'] = $district;

    $ward = array(
        'type' => 'select',
        'options' => $arg_wards,
        'input_class' => array(
            'country_select',
        ),
        'priority' => 80
    ) ;

    $fields['billing']['billing_ward'] = $ward;


    return $fields;
}