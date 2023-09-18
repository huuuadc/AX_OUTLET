<?php

add_filter('woocommerce_add_to_cart_validation', 'add_to_cart_check_stock_ls', 20, 5);
function add_to_cart_check_stock_ls($check, $product_id, $quantity, $variation_id = 0, $variation_code = [])
{

    $arg_data = [
        [
            'product_id' => $product_id,
            'variation_id' => $variation_id,
            'qty' => $quantity,
        ]
    ];

    $product = wc_get_product($product_id);

    $is_check_stock = get_option('admin_dashboard_is_check_stock') ?? '';
    if ($is_check_stock == 'checked' && !check_stock_ls($arg_data)) {
        wc_add_notice('Sản phẩm ' . $product->get_name() . ' không đủ tồn kho trên hệ thống!.', 'error');
        return false;
    }
    return $check;
}



