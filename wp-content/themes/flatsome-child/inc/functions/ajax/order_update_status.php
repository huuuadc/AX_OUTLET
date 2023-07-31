<?php

use AX\ADDRESS;
use TIKI\TIKI_API;

add_action( 'wp_ajax_post_order_update_status', 'order_update_status' );
add_action( 'wp_ajax_nopriv_post_order_update_status', 'order_update_status' );
function order_update_status(){

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
    $order = new AX_ORDER($order_id);
    //create tiki api
    $tiki_connect = new TIKI_API();

    //get old status
    $old_status = $order->get_status('value');

    //
    //
    // Action reject order
    //
    //
    if ($_POST['payload_action'] === 'order_status_reject' && 'order_status_processing' == 'order_status_'.$old_status){

            if ($order->update_status('wc-reject')){
                $order->set_log('success',$payload_action,$commit_note);
                echo json_encode(array(
                    'status' => true,
                    'messenger' => "Đã cập nhật trạng thái từ {$old_status} sang reject",
                    'data' => array(
                        'order_status' => $order->get_status(),
                        'class' => 'secondary'
                    )
                ));
            }else{
                $order->set_log('danger',$payload_action,$commit_note);
                echo json_encode(array(
                    'status' => false,
                    'messenger' => "Cập nhật trạng thái không thành công. Trạng thái hiện tại là {$old_status}",
                    'data' => []
                ));
            }

            exit;

    }


    //
    //
    // Action confirm order
    //
    //
    if ($_POST['payload_action'] == 'order_status_confirm' && 'order_status_processing' == 'order_status_'.$old_status){

            if ($order->update_status('wc-confirm')){
                $order->set_log('success',$payload_action,$commit_note);
                echo json_encode(array(
                    'status' => true,
                    'messenger' => "Đã cập nhật trạng thái từ {$old_status} sang confirm",
                    'data' => array(
                        'order_status' => $order->get_status(),
                        'class' => 'primary'
                    )
                ));
            }else{
                $order->set_log('danger',$payload_action,$commit_note);
                echo json_encode(array(
                    'status' => false,
                    'messenger' => "Cập nhật trạng thái không thành công. Trạng thái hiện tại là {$old_status}",
                    'data' => []
                ));
            }

            exit;

    }

    //
    //
    // Action request shipping order
    //
    //
    if ($_POST['payload_action'] == 'order_status_request' && 'order_status_confirm' == 'order_status_'.$old_status){

            //Get total amount order
            $total_amount = $order->get_total('value');
            $cash_on_delivery_amount = 0;

            if ($order->get_payment_method() === 'cod') $cash_on_delivery_amount = $order->get_total('value');


            $item_names = [];
            $total_height = 20;
            $total_width = 20;
            $total_depth = 20;
            $total_weight = 2000;

            $items = $order->get_items();
            foreach ($items as $item){
                $item_names[] = $item->get_name('value') . ' (x' . $item->get_quantity() . ')';
            }

            $data =  array(
                'external_order_id' => '#'.$order_id,
                'service_code'  => 'hns_standard',
                'partner_code'  => 'TNSL',
                'cash_on_delivery_amount'   => (int)$cash_on_delivery_amount,
                'instruction'   => $order->get_customer_note('value') ?? '',
                'package_info' => array(
                    'height'    =>  $total_height ,
                    'width'     =>  $total_width ,
                    'depth'     =>  $total_depth ,
                    'weight'    =>  $total_weight ,
                    'total_amount'  => (int)$total_amount
                ),
                'destination'    => array(
                    'first_name'    => $order->get_billing_first_name() ?? '',
                    'last_name' => $order->get_billing_last_name() ?? '',
                    'phone'     => $order->get_billing_phone() ?? '',
                    'email'     => $order->get_billing_email() ?? '',
                    'street'        => $order->get_billing_address_1(),
                    'ward_name'     => $order->get_billing_ward_name() ?? '',
                    'district_name' => $order->get_billing_district_name() ?? '',
                    'province_name' => $order->get_billing_city_name() ?? '',
                    'ward_code'     => $order->get_billing_ward_code()
                ),
                'product_name'      => implode(' \n ',$item_names),
                'placed_on'         => 'tiki'
            );

            $rep = $tiki_connect->post_create_shipping_to_tiki($data);

            write_log($rep);

            if (!$rep->success){
                $order->set_log('danger',$payload_action,$commit_note);
                echo json_encode(array(
                    'status' => false,
                    'messenger' => "Gọi giao hàng không thành công!",
                    'data' => json_encode($rep)
                ));
                exit;
            }

            $order->set_tracking_id($rep->data->tracking_id);
            $order->set_tracking_url($rep->data->tracking_url);
            $order->set_shipment_status($rep->data->status);

            add_post_meta($order_id, 'shipment_estimated_timeline_pickup',$rep->data->quote->estimated_timeline->pickup, true);
            add_post_meta($order_id, 'shipment_estimated_timeline_dropoff',$rep->data->quote->estimated_timeline->dropoff, true);


            if ($order->update_status('wc-request')){
                $order->set_log('success',$payload_action,$commit_note);
                echo json_encode(array(
                    'status' => true,
                    'messenger' => "Đã cập nhật trạng thái từ {$old_status} sang request",
                    'data' => array(
                        'order_status' => $order->get_status(),
                        'class' => 'info',
                        'tracking_id' =>$rep->data->tracking_id,
                        'tracking_url' =>$rep->data->tracking_url,
                        'shipment_status' => $rep->data->status
                    )
                ));
            }else{
                $order->set_log('danger',$payload_action,$commit_note);
                echo json_encode(array(
                    'status' => false,
                    'messenger' => "Cập nhật trạng thái không thành công. Trạng thái hiện tại là {$old_status}",
                    'data' => json_encode($rep->data)
                ));
            };

            exit;


    }

    //
    //
    // Action vendor update shipping order
    //
    //

    if ($_POST['payload_action'] == 'order_status_shipping' && 'order_status_request' == 'order_status_'.$old_status){


        if ($order->update_status('wc-shipping')){
            $order->set_log('success',$payload_action,$commit_note);
            echo json_encode(array(
                'status' => true,
                'messenger' => "Đã cập nhật trạng thái từ {$old_status} sang shipping",
                'data' => array(
                    'order_status' => $order->get_status(),
                    'class' => 'info'
                )
            ));
        }else{
            $order->set_log('danger',$payload_action,$commit_note);
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

    if ($_POST['payload_action'] == 'order_status_delivered' && 'order_status_shipping' == 'order_status_'.$old_status){

        if ($order->update_status('wc-delivered')){
            $order->set_log('success',$payload_action,$commit_note);
            echo json_encode(array(
                'status' => true,
                'messenger' => "Đã cập nhật trạng thái từ {$old_status} sang delivered",
                'data' => array(
                    'order_status' => $order->get_status(),
                    'class' => 'info'
                )
            ));
        }else{
            $order->set_log('danger',$payload_action,$commit_note);
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
    if ($_POST['payload_action'] == 'order_status_delivery-failed' && 'order_status_shipping' == 'order_status_'.$old_status){


        if ($order->update_status('wc-delivery-failed')){
            echo json_encode(array(
                'status' => true,
                'messenger' => "Đã cập nhật trạng thái từ {$old_status} sang delivery failed",
                'data' => array(
                    'order_status' => $order->get_status(),
                    'class' => 'secondary'
                )
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

    if ($_POST['payload_action'] == 'order_status_confirm-goods' && 'order_status_delivery-failed' == 'order_status_'.$old_status){

        if ($order->update_status('wc-confirm-goods')){
            $order->set_log('success',$payload_action,$commit_note);
            echo json_encode(array(
                'status' => true,
                'messenger' => "Đã cập nhật trạng thái từ {$old_status} sang confirm goods",
                'data' => array(
                    'order_status' => $order->get_status(),
                    'class' => 'warning'
                )
            ));
        }else{
            $order->set_log('danger',$payload_action,$commit_note);
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


        $tiki_connect->default_cancel['comment'] = $commit_note;

        $rep = $tiki_connect->put_cancelled_shippment($order->get_tracking_id());

        if (!$rep->success){
            $order->set_log('danger',$payload_action,$commit_note);
            echo json_encode(array(
                'status' => false,
                'messenger' => "Request failed",
                'data' => json_encode($rep)
            ));
            exit;
        }

        if ($order->update_status('wc-cancelled')) {
            $order->set_log('success',$payload_action,$commit_note);
            echo json_encode(array(
                'status' => true,
                'messenger' => "Đã cập nhật trạng thái từ {$old_status} sang cancelled",
                'data' => array(
                    'order_status' => $order->get_status(),
                    'class' => 'danger'
                )
            ));
        } else {
            $order->set_log('danger',$payload_action,$commit_note);
            echo json_encode(array(
                'status' => false,
                'messenger' => "Cập nhật trạng thái không thành công. Trạng thái hiện tại là {$old_status}",
                'data' => []
            ));
        };

        exit;
    }

    $order->set_log('info',$payload_action,$commit_note);
    echo json_encode(array(
        'status' => false,
        'messenger' => "Trạng thái đơn hàng không cho phép thao tác này",
        'data' => []
    ));
    exit;

}