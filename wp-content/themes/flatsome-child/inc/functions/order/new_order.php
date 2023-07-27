<?php

//add_action('woocommerce_new_order','change_order_key',99, 2);

function change_order_key($order_id){

    // get the order object
    $order = wc_get_order( $order_id );
    // gets the current order key
    $order_key = $order->get_order_key();
    // remove the "wc_" prefix
    $new_order_key = str_replace( 'wc_', 'ax_outlet_', $order_key );

    // updates the "_order_key" field of the "wp_postmeta" table
    $order->set_order_key( $new_order_key );
    $order->save();

    // updates the "post_password" field of the "wp_posts" table
    $post = get_post( $order_id );
    $post->post_password = $new_order_key;
    wp_update_post( $post );

}