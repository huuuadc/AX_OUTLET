<?php

/**
 * @throws WC_Data_Exception
 */
function update_discount_price_on_schedule()
{
    global $wpdb;
    write_log('Running update price..............');

    $products = $wpdb->get_results("SELECT `ID` FROM {$wpdb->prefix}posts WHERE `post_status` = 'publish' AND `post_type` = 'product'");
    foreach ($products as $p) {

        $product = wc_get_product($p->ID);

        $discounted_price = apply_filters('advanced_woo_discount_rules_get_product_discount_price_from_custom_price', false, $product, 1, 0, 'all', true);

        if (isset($discounted_price['discounted_price'])){
            $price = (int) $discounted_price['discounted_price'];
        } else {
            $price = (int) $product->get_price();
        }

        if (get_post_meta($product->get_id(),'pricesale',false)){
            update_post_meta($product->get_id(),"pricesale",$price);
        }else{
            add_post_meta($product->get_id(),"pricesale",$price);
        }

        //Update min,max price lookup product filter
        $wpdb->update($wpdb->prefix.'wc_product_meta_lookup',
            array(
                'min_price' => $price,
                'max_price' => $price
            ),
            array('product_id'=>$p->ID));

    }

    write_log('Done update price');
}
add_action( 'update_discount_price_event', 'update_discount_price_on_schedule' );