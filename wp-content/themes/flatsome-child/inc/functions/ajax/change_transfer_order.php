<?php

use OMS\OMS_TO;

add_action( 'wp_ajax_change_transfer_order', 'change_transfer_order' );
add_action( 'wp_ajax_nopriv_change_transfer_order', 'change_transfer_order' );
function change_transfer_order()
{

    write_log($_POST);

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

    write_log($_POST);

    //
    //
    //create order from order id
    //
    //


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