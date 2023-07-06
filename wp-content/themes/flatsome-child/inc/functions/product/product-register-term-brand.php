<?php

/**
 * @return void
 */
function product_register_term_product_brand() {

    /**
     * Taxonomy: Brand.
     */

    $labels = [
        "name" => __( "Brands", "custom-post-type-ui" ),
        "singular_name" => __( "Brand", "custom-post-type-ui" ),
    ];


    $args = [
        "label" => __( "Brands", "custom-post-type-ui" ),
        "labels" => $labels,
        "public" => true,
        "publicly_queryable" => true,
        "hierarchical" => true,
        "show_ui" => true,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "query_var" => true,
        "rewrite" => [ 'slug' => 'brand', 'with_front' => true, ],
        "show_admin_column" => false,
        "show_in_rest" => true,
        "rest_base" => "brands",
        "rest_controller_class" => "WP_REST_Terms_Product_Controller",
        "show_in_quick_edit" => false,
        "show_in_graphql" => false,
    ];
    register_taxonomy( "brand", [ "product" ], $args );

    /**
     * Taxonomy: Gender.
     */

    $labels = [
        "name" => __( "Genders", "custom-post-type-ui" ),
        "singular_name" => __( "Gender", "custom-post-type-ui" ),
    ];


    $args = [
        "label" => __( "Genders", "custom-post-type-ui" ),
        "labels" => $labels,
        "public" => true,
        "publicly_queryable" => true,
        "hierarchical" => true,
        "show_ui" => true,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "query_var" => true,
        "rewrite" => [ 'slug' => 'gender', 'with_front' => true, ],
        "show_admin_column" => false,
        "show_in_rest" => true,
        "rest_base" => "genders",
        "rest_controller_class" => "WP_REST_Terms_Product_Controller",
        "show_in_quick_edit" => false,
        "show_in_graphql" => false,
    ];
    register_taxonomy( "gender", [ "product" ], $args );
}
add_action( 'init', 'product_register_term_product_brand' );