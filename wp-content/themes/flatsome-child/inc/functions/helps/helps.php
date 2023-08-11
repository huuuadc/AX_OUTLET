<?php

function exist_option( $arg ) {

    global $wpdb;
    $prefix = $wpdb->prefix;
    $db_options = $prefix.'options';
    $sql_query = 'SELECT * FROM ' . $db_options . ' WHERE option_name LIKE "' . $arg . '"';

    $results = $wpdb->get_results( $sql_query, OBJECT );

    if ( count( $results ) === 0 ) {
        return false;
    } else {
        return true;
    }
}

function get_date_format(){
    return get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
}

function decode_post_data_in_post($post_data){

    // Details you want injected into WooCommerce session.
    $details = array('billing_first_name', 'billing_last_name', 'billing_company', 'billing_email', 'billing_phone');

    // Parsing data
    $post = array();
    $vars = explode('&', $post_data);
    foreach ($vars as $k => $value){
        $v = explode('=', urldecode($value));
        $post[$v[0]] = $v[1];
    }

    return $post;

}