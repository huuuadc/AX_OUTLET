<?php

add_filter('woocommerce_product_tabs','product_tab_public_info');

function product_tab_public_info($tabs){

    $tabs['storage_instructions'] = array(
        'title' => 'Hướng dẫn bảo quản',
        'priority' => 11,
        'callback' => 'woocommerce_product_storage_instructions_tab'
      );

    $tabs['return_policy'] = array(
        'title' => 'Chính sách đổi trả',
        'priority' => 12,
        'callback' => 'woocommerce_product_return_policy_tab'
    );

    write_log($tabs);

    return $tabs;
}

if ( ! function_exists( 'woocommerce_product_storage_instructions_tab' ) ) {

    /**
     * Output the storage instructions tab content.
     */
    function woocommerce_product_storage_instructions_tab(): void
    {
        wc_get_template( 'single-product/tabs/storage_instructions.php' );
    }
}

if ( ! function_exists( 'woocommerce_product_return_policy_tab' ) ) {

    /**
     * Output the return policy tab content.
     */
    function woocommerce_product_return_policy_tab(): void
    {
        wc_get_template( 'single-product/tabs/return_policy.php' );
    }
}
