<?php

use OMS\ADDRESS;
use OMS\LS_API;
use function OMS\ls_request_transfer_line;
use function OMS\ls_transactions_request;
use function OMS\ls_payment_request;

add_action( 'wp_ajax_post_invoice_ls_retail', 'post_invoice_ls_retail' );
add_action( 'wp_ajax_nopriv_post_invoice_ls_retail', 'post_invoice_ls_retail' );
/**
 * @throws Exception
 */
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
    global $wpdb;
    $order          = new OMS_ORDER($order_id);
    //ls_api_2 post SO to Dafc
    $ls_api_2         = new LS_API();

    $base_url_2                 =   get_option('wc_settings_tab_ls_api_url_2') ?? '';
    $username_2                 =   get_option('wc_settings_tab_ls_api_username_2') ?? '';
    $password_2                 =   get_option('wc_settings_tab_ls_api_password_2') ?? '';

    //ls_api post invoice to style outlet
    $ls_api = new LS_API(['user_name' => $username_2, 'user_pass'  => $password_2, 'base_url' => $base_url_2]);

    //get old status
    $old_status     = $order->get_status('value');

    if (!current_user_can('post_ls_all_status')){
        if ($old_status != 'request'){
            $order->set_log('danger',
                $payload_action,
                $commit_note . '. Trạng thái không cho thực hiện thao tác');
            echo response(false,'Trạng thái không cho thực hiện thao tác',[]);
            exit();
        }
    }
    //Check stock với ls
    if (!$order->check_stock_ls()){
        echo response(false,'Không còn tồn trên ls retail',[]);
        exit;
    }

    //
    //
    // Action payment order
    //
    //

    if ($_POST['payload_action'] === 'post_invoice_ls_retail' && $order->get_ls_status() == 'no'){

        if(order_send_tow_ls($order)){
            echo response(false, 'Đã post ls thành công', []);
        }
        echo response(false, 'Xẩy ra lỗi trong quá trình xử lý', []);
        exit;

    }

}