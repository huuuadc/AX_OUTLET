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

    //
    //
    // Action load line when header change
    //
    //

    if ($_POST['payload_action'] === 'change_transfer_order'){
        wc_get_template('template-parts/dashboard/components/inventory/card-table-line.php',['id'=>$transfer_id]);
        exit;
    }

    //
    //
    // Action change status to cancelled
    //
    //

    if ($_POST['payload_action'] === 'change_transfer_order_cancelled' && $transfer_order->get_status() == 'pending'){
        $transfer_order->update_status('wc-cancelled');
        wc_get_template('template-parts/dashboard/components/inventory/card-table-line.php',['id'=>$transfer_id]);
        exit;

    }

    //
    //
    // Action change status to completed
    //
    //

    if ($_POST['payload_action'] === 'change_transfer_order_completed' && $transfer_order->get_status() == 'pending'){

        foreach ($transfer_order->get_items() as $item_id => $item) {
            // Get an instance of corresponding the WC_Product object
            $product = $item->get_product();
            $qty = $item->get_quantity(); // Get the item quantity
            wc_update_product_stock($product, $qty, 'increase');
        }

        $transfer_order->update_status('wc-completed');
        wc_get_template('template-parts/dashboard/components/inventory/card-table-line.php',['id'=>$transfer_id]);
        exit;

    }

    if($payload_action === 'change_transfer_order_line_delete'){
        //$transfer_id is item id of line
        wc_delete_order_item($transfer_id);
        wc_get_template('template-parts/dashboard/components/inventory/card-table-line.php',['id'=>$transfer_id]);
        exit;
    }

    echo "Không thực hiện được thao tác này";
    exit;

}