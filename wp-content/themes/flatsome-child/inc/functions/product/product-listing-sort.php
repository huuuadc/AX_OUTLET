<?php


function add_postmeta_ordering_args( $args_sort) {

    $cw_orderby_value = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) :
        apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );

    switch( $cw_orderby_value ) {
        case 'pricesale_desc':
            $args_sort['orderby'] = 'meta_value_num';
            $args_sort['order'] = 'DESC';
            $args_sort['meta_key'] = 'pricesale';
            break;
        case 'pricesale':
            $args_sort['orderby'] = 'meta_value_num';
            $args_sort['order'] = 'ASC';
            $args_sort['meta_key'] = 'pricesale';
            break;
    }

    return $args_sort;
}

add_filter( 'woocommerce_get_catalog_ordering_args', 'add_postmeta_ordering_args' );

function add_sort_view_orderby( $sort_by ) {
    unset($sort_by['price']);
    unset($sort_by['price-desc']);
    $sort_by['pricesale_desc'] = __( 'Sort by price (desc)', 'woocommerce' );
    $sort_by['pricesale'] = __( 'Sort by price (asc)', 'woocommerce' );
    return $sort_by;
}

add_filter( 'woocommerce_default_catalog_orderby_options', 'add_sort_view_orderby' );
add_filter( 'woocommerce_catalog_orderby', 'add_sort_view_orderby' );