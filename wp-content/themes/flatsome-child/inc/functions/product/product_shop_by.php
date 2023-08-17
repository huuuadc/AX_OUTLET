<?php

use Wdr\App\Controllers;

/**
 * @param $product_id
 * @return void
 */
function update_lastpiece_task($product_id) {
    $product = wc_get_product( $product_id );
    $stock_qty = 0;
    if($product->is_type('simple')) {
        $stock_qty = $product->get_stock_quantity();
    }
    elseif($product->is_type('variable')) {
        $variations = $product->get_available_variations();
        foreach($variations as $variation){
            $variation_id = $variation['variation_id']; //echo $variation_id;
            $variation_obj = new WC_Product_variation($variation_id); //var_dump($variation_obj);
            $stock = $variation_obj->get_stock_quantity();
            if($stock > 0) {
                $stock_qty += $stock;
            }
        }
    }
    //echo $stock_qty;
    if($stock_qty == 1) {
        wp_set_object_terms($product_id, 'co-hoi-cuoi', 'product_shop',true);
    }
    else {
        wp_remove_object_terms($product_id, 'co-hoi-cuoi', 'product_shop',true);
    }
}

function update_sales_special($product_id, $present_sales = 0,$remove = false) {

    $product = wc_get_product( $product_id );
    $present_discount = 0;
    write_log($remove);

    $manage_dis = new Controllers\ManageDiscount();
    $product_detail_discount = $manage_dis->calculateInitialAndDiscountedPrice($product,1);
    $price_discount = 0;
    if (isset($product_detail_discount['initial_price'])
        && isset($product_detail_discount['discounted_price'])
        && $product_detail_discount['initial_price'] > 0
        && $product_detail_discount['discounted_price'] > 0){
        $price_discount = $product_detail_discount['initial_price'] - $product_detail_discount['discounted_price'];
        $present_discount = (int)( $price_discount * 100 / $product_detail_discount['initial_price']);
    }

    if($present_discount == $present_sales && !$remove) {
        write_log($product_id);
        write_log($present_discount);
        wp_set_object_terms($product_id, 'sale-doc-quyen', 'product_shop',true);
    }

    if ($present_discount == $present_sales && $remove){
        wp_remove_object_terms($product_id, 'sale-doc-quyen', 'product_shop');
    }

}