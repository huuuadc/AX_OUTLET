<?php

use OMS\OMS_TO;

add_action( 'wp_ajax_change_transfer_order', 'change_transfer_order' );
add_action( 'wp_ajax_nopriv_change_transfer_order', 'change_transfer_order' );
function change_transfer_order()
{
    //Check have action and payload_action
    //payload action variant status ajax post
    if(!isset($_POST['action']) || !isset($_POST['payload_action'])) {
        echo response(false,'Không tìm thấy hành động được gửi',[]);
        exit;
    }

    $payload_action = $_POST['payload_action'];

    //Check have post order_id
    if (!isset($_POST['transfer_id'])){
        echo response(false,'Số đơn hàng không có',[]);
        exit;
    }

    $transfer_id = $_POST['transfer_id'];

    $transfer_order = new OMS_TO($transfer_id);

//    $transfer_order->set_customer_id(get_current_user_id());
//
//    $product = wc_get_product('29461');
//
//    $transfer_order->add_product($product,-100);

//      wc_reduce_stock_levels($transfer_order);

//      $product_id = wc_get_product_id_by_sku('1138040003');
//      write_log($product_id);
//    $product = wc_get_product($product_id);
//        $transfer_order->add_product($product,3);
////
////      write_log($product);
//    $product_id = wc_get_product_id_by_sku('1119704');
//    write_log($product_id);
//    $product = wc_get_product($product_id);
//    $transfer_order->add_product($product,4);
//
//      write_log($product);
//      $product = wc_get_product_id_by_sku('123');
//
//      write_log($product);

//    $transfer_order->get_order_number();

//    foreach ($transfer_order->get_items() as $item_id => $item) {
//        // Get an instance of corresponding the WC_Product object
//        $product = $item->get_product();
//        $qty = $item->get_quantity(); // Get the item quantity
//        wc_update_product_stock($product, $qty, 'increase');
//    }

//    $transfer_order->update_status('wc-pending');
//    $transfer_order->save();

//    foreach ($transfer_order->get_items() as $item){
//        wc_delete_order_item($item->get_id());
//    }

    //
    //
    // Action payment order
    //
    //

    if ($_POST['payload_action'] === 'change_transfer_order'){
        wc_get_template('template-parts/dashboard/components/inventory/card-table-line.php',['id'=>$transfer_id]);
        exit;

    }

}