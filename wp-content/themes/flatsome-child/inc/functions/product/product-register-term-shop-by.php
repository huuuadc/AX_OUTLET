<?php

/**
 * @return void
 */

function product_register_term_product_shop() {

    /**
     * Taxonomy: Shop By.
     */

    $labels = [
        "name" => __( "Shop By", "ax_outlet" ),
        "singular_name" => __( "Shop By", "ax_outlet" ),
    ];


    $args = [
        "label" => __( "Shop By", "ax_outlet" ),
        "labels" => $labels,
        "public" => true,
        "publicly_queryable" => true,
        "hierarchical" => true,
        "show_ui" => true,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "query_var" => true,
        "rewrite" => [ 'slug' => 'shop', 'with_front' => true, ],
        "show_admin_column" => false,
        "show_in_rest" => true,
        "rest_base" => "shop",
        "rest_controller_class" => "WP_REST_Terms_Controller",
        "show_in_quick_edit" => false,
        "show_in_graphql" => false,
    ];
    register_taxonomy( "product_shop", [ "product" ], $args );
}
add_action( 'init', 'product_register_term_product_shop' );