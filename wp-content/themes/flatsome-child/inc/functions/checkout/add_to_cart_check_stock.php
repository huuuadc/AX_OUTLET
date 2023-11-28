<?php

add_filter('woocommerce_add_to_cart_validation', 'add_to_cart_check_stock_ls', 20, 5);
function add_to_cart_check_stock_ls($check, $product_id, $quantity, $variation_id = 0, $variation_code = [])
{

    $is_check_stock = get_option('admin_dashboard_is_check_stock') ?? '';

    //get stock on cms by list status
    $remake_qty = get_qty_product_id_in_orders(
        $product_id,
        $variation_id,
        [
            'wc-reject',
            'wc-confirm',
            'wc-request',
            'wc-shipping',
            'wc-delivery-failed',
            'wc-confirm-goods',
            'wc-processing',
            'wc-completed'
        ]);

    write_log($remake_qty);

    $arg_data = [
        [
            'product_id' => $product_id,
            'variation_id' => $variation_id,
            'qty' => $quantity + $remake_qty,
        ]
    ];

    $product = wc_get_product($product_id);

    if ($is_check_stock == 'checked' && count(check_stock_ls($arg_data)) > 0) {
        wc_add_notice('Sản phẩm ' . $product->get_name() . ' không đủ tồn kho trên hệ thống!.', 'error');
        return false;
    }
    return $check;
}



