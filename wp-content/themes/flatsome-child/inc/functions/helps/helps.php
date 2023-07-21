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