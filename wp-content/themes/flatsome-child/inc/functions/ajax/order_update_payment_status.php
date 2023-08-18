<?php

use OMS\ADDRESS;

add_action( 'wp_ajax_post_order_update_payment_status', 'order_update_payment_status' );
add_action( 'wp_ajax_nopriv_post_order_update_payment_status', 'order_update_payment_status' );
function order_update_payment_status(){

    //Check have action and payload_action
    //payload action variant status ajax post
    if(!isset($_POST['action']) || !isset($_POST['payload_action'])) {
        echo json_encode(array(
            'status' => false,
            'messenger' => 'Không tìm thấy hành động được gửi',
            'data' => []
        ));
        exit;
    }

    $payload_action = $_POST['payload_action'];

    //Check have post order_id
    if (!isset($_POST['order_id'])){
        echo json_encode(array(
            'status' => false,
            'messenger' => 'Số đơn hàng không có',
            'data' => []
        ));
        exit;
    }

    $order_id = $_POST['order_id'];
    $commit_note = $_POST['commit_note'];

    //Check have order in store
    if (!wc_get_order($order_id)){
        echo json_encode(array(
            'status' => false,
            'messenger' => 'Không tồn tại đơn hàng: ' . $order_id,
            'data' => []
        ));
        exit();
    }

    //
    //
    //create order from order id
    //
    //
    $order = new OMS_ORDER($order_id);

    //get old status
    $old_status = $order->get_status('value');
    $old_payment_status = $order->get_payment_title();

    //
    //
    // Action payment order
    //
    //

    if ($_POST['payload_action'] === 'order_update_payment'){

        if ($order->set_payment_status('paid')){
            $order->set_log('success',$payload_action,$commit_note.' -- '. $old_payment_status . ' -> ' . 'Đã thanh toán');
            $data = [
                'order_payment_title'=> 'Đã thanh toán',
                'class' => 'success'
            ];
            echo response(true,'Đã cập nhật trạng thái thanh toán',$data);
        }else{
            $order->set_log('danger',$payload_action,$commit_note);
            echo response(false,'Cập nhật trạng thái thanh toán không thành công',[]);
        }

        exit;

    }

}