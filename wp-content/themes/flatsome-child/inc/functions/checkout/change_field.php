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

    $city_args = wp_parse_args( array(
        'type' => 'select',
        'options' => array(
            '' => __( 'Select city' ),
            'HoChiMinh' => 'HoChiMinh',
            'HaNoi' => 'HaNoi',
            'CaoBang'   => 'CaoBang',
            'LongAn' => 'LongAn',
            'SocTrang'    => 'SocTrang',
            'BacLieu'  => 'BacLieu',
        ),
        'input_class' => array(
            'country_select',
        )
    ), $fields['billing']['billing_city'] );

    $fields['billing']['billing_city'] = $city_args;

    $city_args1 = array(
        'type' => 'select',
        'options' => array(
            '' => __( 'Select District' ),
            'AnGiang' => 'An Giang',
        ),
        'input_class' => array(
            'country_select',
        )
    ) ;

    $fields['billing']['billing_district'] = $city_args1;

    write_log($fields);

    return $fields;
}