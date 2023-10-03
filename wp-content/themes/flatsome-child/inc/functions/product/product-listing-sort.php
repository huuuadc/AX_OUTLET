<?php


function add_postmeta_ordering_args( $args_sort_cw, $orderby ) {

    write_log($orderby);

    $cw_orderby_value = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) :
        apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );

    switch( $cw_orderby_value ) {
        case 'pricesale-desc':
            $args_sort_cw['orderby'] = 'meta_value_num';
            $args_sort_cw['order'] = 'DESC';
            $args_sort_cw['meta_key'] = 'pricesale';
            break;
        case 'pricesale':
            $args_sort_cw['orderby'] = 'meta_value_num';
            $args_sort_cw['order'] = 'ASC';
            $args_sort_cw['meta_key'] = 'pricesale';
            break;
    }

    return $args_sort_cw;
}

add_filter( 'woocommerce_get_catalog_ordering_args', 'add_postmeta_ordering_args',10,2 );

function cw_add_new_postmeta_orderby( $sort_by ) {
    unset($sort_by['price']);
    unset($sort_by['price-desc']);
    $sort_by['pricesale-desc'] = __( 'Sort by price (desc)', 'woocommerce' );
    $sort_by['pricesale'] = __( 'Sort by price (asc)', 'woocommerce' );
    return $sort_by;
}

add_filter( 'woocommerce_default_catalog_orderby_options', 'cw_add_new_postmeta_orderby' );
add_filter( 'woocommerce_catalog_orderby', 'cw_add_new_postmeta_orderby' );