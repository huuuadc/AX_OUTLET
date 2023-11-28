<?php

add_action('woocommerce_before_checkout_process','checkout_check_stock_ls',10,0);

function checkout_check_stock_ls(){


    $is_check_stock = get_option('admin_dashboard_is_check_stock') ?? '';
    if ($is_check_stock == 'checked'){

        $items = WC()->cart->get_cart();
        $arg_data = [];
        foreach ($items as $item){

            //get stock on cms by list status
            $remake_qty = get_qty_product_id_in_orders(
                $item['product_id'],
                $item['variation_id'],
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

            $arg_data[] = [
                'product_id' => $item['product_id'],
                'variation_id' => $item['variation_id'],
                'qty' => $item['quantity'] + $remake_qty,
            ];

        }

        //get list item name out of stock in ls
        $is_data_stock = check_stock_ls($arg_data);

        if (count($is_data_stock) > 0){

            foreach ($is_data_stock as $item_name){
                wc_add_notice('Sản phẩm '. $item_name .' không đủ tồn kho trên hệ thống!.', 'error');
            }
            return false;
        }
    }

    return true;
}