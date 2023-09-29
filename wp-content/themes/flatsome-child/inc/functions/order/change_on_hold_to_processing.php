<?php

/* Change Default Order Status from On-HOld for Purchase Orders to Processing */
add_action( 'woocommerce_order_status_changed', 'woocommerce_auto_processing_orders');
function woocommerce_auto_processing_orders( $order_id ) {
    if ( ! $order_id )
        return;

    $order = wc_get_order( $order_id );

    if ( !$order)
        return;

    // If order is "on-hold" update status to "processing"
    if( $order->has_status( 'on-hold' ) ) {
        $order->update_status( 'processing' );
    }
}