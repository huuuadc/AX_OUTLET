<?php

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