<?php

use AX\ADDRESS;
use TIKI\TIKI_API;

add_action( 'wp_ajax_post_order_update_status', 'order_update_status' );
add_action( 'wp_ajax_nopriv_post_order_update_status', 'order_update_status' );
function order_update_status(){

    $status_badge = array(
        'reject' => 'badge-danger',
        'trash' => 'badge-danger',
        'on-hold' => 'badge-danger',
        'pending' => 'badge-warning',
        'processing' => 'badge-primary',
        'confirm' => 'badge-primary',
        'completed' => 'badge-success',
        'request' => 'badge-info',
        'shipping' => 'badge-info',
        'delivered' => 'badge-info',
        'delivery-failed' => 'badge-danger',
        'cancelled' => 'badge-danger',
        'confirm-goods' => 'badge-primary',
    );

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
    $order = new AX_ORDER($order_id);
    //create tiki api
    $tiki_connect = new TIKI_API();

    $old_status = $order->get_status('value');


    //
    //
    // Action reject order
    //
    //
    if ($_POST['payload_action'] === 'order_status_reject' && 'order_status_reject' !== 'order_status_'.$old_status){

        if ($order->update_status('wc-reject')){
            echo json_encode(array(
                'status' => true,
                'messenger' => "Đã cập nhật trạng thái từ {$old_status} sang reject",
                'data' => ['<span class="badge ' . $status_badge[$order->get_status()] . '">' . $order->get_status() . '</span>']
            ));
        }else{
            echo json_encode(array(
                'status' => false,
                'messenger' => "Cập nhật trạng thái không thành công. Trạng thái hiện tại là {$old_status}",
                'data' => []
            ));
        };

        exit;
    }


    //
    //
    // Action confirm order
    //
    //
    if ($_POST['payload_action'] == 'order_status_confirm' && 'order_status_confirm' !== 'order_status_'.$old_status){

        if ($order->update_status('wc-confirm')){
            echo json_encode(array(
                'status' => true,
                'messenger' => "Đã cập nhật trạng thái từ {$old_status} sang confirm",
                'data' => ['<span class="badge ' . $status_badge[$order->get_status()] . '">' . $order->get_status() . '</span>']
            ));
        }else{
            echo json_encode(array(
                'status' => false,
                'messenger' => "Cập nhật trạng thái không thành công. Trạng thái hiện tại là {$old_status}",
                'data' => []
            ));
        };

        exit;

    }

    //
    //
    // Action request shipping order
    //
    //
    if ($_POST['payload_action'] == 'order_status_request' && 'order_status_request' !== 'order_status_'.$old_status){

        if('order_status_confirm' ===  'order_status_'.$old_status ){
            //Get total amount order
            $total_amount = $order->get_total('value');

            $item_names = [];
            $total_height = 0;
            $total_width = 0;
            $total_depth = 0;
            $total_weight = 0;

            $items = $order->get_items();
            foreach ($items as $item){
                $item_names[] = $item->get_name('value') . ' (x' . $item->get_quantity() . ')';
            }

            $data =  array(
                'external_order_id' => '#'.$order_id,
                'service_code'  => 'hns_standard',
                'partner_code'  => 'TNSL',
                'cash_on_delivery_amount'   => 0,
                'instruction'   => $commit_note,
                'package_info' => array(
                    'height'    =>  20,
                    'width'     =>  20,
                    'depth'     =>  20,
                    'weight'    =>  2000,
                    'total_amount'  => (int)$total_amount
                ),
                'destination'    => array(
                    'first_name'    => $order->get_billing_first_name(),
                    'last_name' => $order->get_billing_last_name(),
                    'phone'     => $order->get_billing_phone(),
                    'email'     => $order->get_billing_email(),
                    'street'        => $order->get_billing_address_1(),
                    'ward_name'     => $order->get_billing_ward_name() ?? '',
                    'district_name' => $order->get_billing_district_name() ?? '',
                    'province_name' => $order->get_billing_city_name() ?? '',
                    'ward_code'     => $order->get_billing_ward_code()
                ),
                'product_name'      => implode(' \n ',$item_names),
                'placed_on'         => 'ax_outlet'
            );

            $rep = $tiki_connect->post_create_shipping_to_tiki($data);

            write_log($rep);

            if (!$rep->success){
                echo json_encode(array(
                    'status' => false,
                    'messenger' => "Gọi giao hàng không thành công!",
                    'data' => json_encode($rep)
                ));
                exit;
            }

            add_post_meta($order_id, 'tracking_id', $rep->data->tracking_id, true);
            add_post_meta($order_id, 'tracking_url', $rep->data->tracking_url, true);


            if ($order->update_status('wc-request')){
                echo json_encode(array(
                    'status' => true,
                    'messenger' => "Đã cập nhật trạng thái từ {$old_status} sang request",
                    'data' => ['<span class="badge ' . $status_badge[$order->get_status()] . '">' . $order->get_status() . '</span>']
                ));
            }else{
                echo json_encode(array(
                    'status' => false,
                    'messenger' => "Cập nhật trạng thái không thành công. Trạng thái hiện tại là {$old_status}",
                    'data' => []
                ));
            };

            exit;
        } else {
            echo json_encode(array(
                'status' => false,
                'messenger' => "Trạng thái này không được gọi giao",
                'data' => []
            ));
            exit;
        }




    }

    //
    //
    // Action vendor update shipping order
    //
    //

    if ($_POST['payload_action'] == 'order_status_shipping' && 'order_status_shipping' !== 'order_status_'.$old_status){


        if ($order->update_status('wc-shipping')){
            echo json_encode(array(
                'status' => true,
                'messenger' => "Đã cập nhật trạng thái từ {$old_status} sang shipping",
                'data' => ['<span class="badge ' . $status_badge[$order->get_status()] . '">' . $order->get_status() . '</span>']
            ));
        }else{
            echo json_encode(array(
                'status' => false,
                'messenger' => "Cập nhật trạng thái không thành công. Trạng thái hiện tại là {$old_status}",
                'data' => []
            ));
        };

        exit;


    }

    //
    //
    // Action vendor update delivered order
    //
    //

    if ($_POST['payload_action'] == 'order_status_delivered' && 'order_status_delivered' !== 'order_status_'.$old_status){

        if ($order->update_status('wc-delivered')){
            echo json_encode(array(
                'status' => true,
                'messenger' => "Đã cập nhật trạng thái từ {$old_status} sang delivered",
                'data' => ['<span class="badge ' . $status_badge[$order->get_status()] . '">' . $order->get_status() . '</span>']
            ));
        }else{
            echo json_encode(array(
                'status' => false,
                'messenger' => "Cập nhật trạng thái không thành công. Trạng thái hiện tại là {$old_status}",
                'data' => []
            ));
        };

        exit;


    }

    //
    //
    // Action vendor update delivered failed order
    //
    //
    if ($_POST['payload_action'] == 'order_status_delivery-failed' && 'order_status_delivery-failed' !== 'order_status_'.$old_status){


        if ($order->update_status('wc-delivery-failed')){
            echo json_encode(array(
                'status' => true,
                'messenger' => "Đã cập nhật trạng thái từ {$old_status} sang delivery failed",
                'data' => ['<span class="badge ' . $status_badge[$order->get_status()] . '">' . $order->get_status() . '</span>']
            ));
        }else{
            echo json_encode(array(
                'status' => false,
                'messenger' => "Cập nhật trạng thái không thành công. Trạng thái hiện tại là {$old_status}",
                'data' => []
            ));
        };

        exit;


    }

    //
    //
    // Action store update confirm goods order
    //
    //

    if ($_POST['payload_action'] == 'order_status_confirm-goods' && 'order_status_confirm-goods' !== 'order_status_'.$old_status){

        if ($order->update_status('wc-confirm-goods')){
            echo json_encode(array(
                'status' => true,
                'messenger' => "Đã cập nhật trạng thái từ {$old_status} sang confirm goods",
                'data' => ['<span class="badge ' . $status_badge[$order->get_status()] . '">' . $order->get_status() . '</span>']
            ));
        }else{
            echo json_encode(array(
                'status' => false,
                'messenger' => "Cập nhật trạng thái không thành công. Trạng thái hiện tại là {$old_status}",
                'data' => []
            ));
        };

        exit;


    }

    //
    //
    // Action admin update cancelled order
    //
    //
    if ($_POST['payload_action'] == 'order_status_cancelled' && 'order_status_cancelled' !== 'order_status_'.$old_status) {

        $rep = $tiki_connect->put_cancelled_shippment($order->get_tracking_id());

        if (!$rep->success){
            echo json_encode(array(
                'status' => false,
                'messenger' => "Request failed",
                'data' => json_encode($rep)
            ));
            exit;
        }

        if ($order->update_status('wc-cancelled')) {
            echo json_encode(array(
                'status' => true,
                'messenger' => "Đã cập nhật trạng thái từ {$old_status} sang cancelled",
                'data' => ['<span class="badge ' . $status_badge[$order->get_status()] . '">' . $order->get_status() . '</span>']
            ));
        } else {
            echo json_encode(array(
                'status' => false,
                'messenger' => "Cập nhật trạng thái không thành công. Trạng thái hiện tại là {$old_status}",
                'data' => []
            ));
        };

        exit;
    }

}