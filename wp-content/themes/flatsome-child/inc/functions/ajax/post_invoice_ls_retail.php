<?php

use OMS\ADDRESS;
use OMS\LS_API;
use function OMS\ls_transactions_request;
use function OMS\ls_payment_request;

add_action( 'wp_ajax_post_invoice_ls_retail', 'post_invoice_ls_retail' );
add_action( 'wp_ajax_nopriv_post_invoice_ls_retail', 'post_invoice_ls_retail' );
function post_invoice_ls_retail(){

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


//    if ($old_status != 'request'){
//        $order->set_log('danger',$payload_action,$commit_note);
//        echo response(false,'Trạng thái không cho thực hiện thao tác',[]);
//        exit();
//    }

    //
    //
    // Action payment order
    //
    //

    if ($_POST['payload_action'] === 'post_invoice_ls_retail'){


        $ls_api = new LS_API();

        $data_request_transaction = (object) ls_transactions_request();
        $data_request_payment = (object) ls_payment_request();

        $data_request_transaction->Location_Code = 'DA0053';

        write_log($data_request_transaction);
        write_log((string) $data_request_transaction);

        if (false){
//            $order->set_log('success',$payload_action,$commit_note.' -- '. $old_payment_status . ' -> ' . 'Đã thanh toán');
//            $data = [
//                'order_payment_title'=> 'Đã thanh toán',
//                'class' => 'success'
//            ];
//            echo response(true,'Đã cập nhật trạng thái thanh toán',$data);
        }else{
            $order->set_log('danger',$payload_action,$commit_note);
            echo response(false,'Post LS không thành công xin kiểm tra lại dữ liệu đơn hàng',[]);
        }

        exit;

    }

}