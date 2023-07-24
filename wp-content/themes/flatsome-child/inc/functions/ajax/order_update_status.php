<?php

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

    if(!isset($_POST['action']) && !isset($_POST['payload_action'])) {
        echo json_encode(array(
            'status' => '500',
            'messenger' => 'No action map',
            'data' => []
        ));
        exit;
    }

    // Action reject order
    if ($_POST['payload_action'] == 'order_status_reject'){

        $order_id = $_POST['order_id'];

        $order = wc_get_order($order_id);

        $order->update_status('wc-reject');

        echo json_encode(array(
            'status' => '200',
            'messenger' => 'success',
            'data' => ['<span class="badge ' . $status_badge[$order->get_status()] . '">' . $order->get_status() . '</span>']
        ));


    }


    // Action confirm order
    if ($_POST['payload_action'] == 'order_status_confirm'){

        $order_id = $_POST['order_id'];

        $order = wc_get_order($order_id);

        $order->update_status('wc-confirm');

        echo json_encode(array(
            'status' => '200',
            'messenger' => 'success',
            'data' => ['<span class="badge ' . $status_badge[$order->get_status()] . '">' . $order->get_status() . '</span>']
        ));


    }


    // Action request shipping order
    if ($_POST['payload_action'] == 'order_status_request'){

        $order_id = $_POST['order_id'];

        $order = wc_get_order($order_id);

        $order->update_status('wc-request');

        echo json_encode(array(
            'status' => '200',
            'messenger' => 'success',
            'data' => ['<span class="badge ' . $status_badge[$order->get_status()] . '">' . $order->get_status() . '</span>']
        ));


    }

    // Action vendor update shipping order
    if ($_POST['payload_action'] == 'order_status_shipping'){

        $order_id = $_POST['order_id'];

        $order = wc_get_order($order_id);

        $order->update_status('wc-shipping');

        echo json_encode(array(
            'status' => '200',
            'messenger' => 'success',
            'data' => ['<span class="badge ' . $status_badge[$order->get_status()] . '">' . $order->get_status() . '</span>']
        ));


    }

    // Action vendor update delivered order
    if ($_POST['payload_action'] == 'order_status_delivered'){

        $order_id = $_POST['order_id'];

        $order = wc_get_order($order_id);

        $order->update_status('wc-delivered');

        echo json_encode(array(
            'status' => '200',
            'messenger' => 'success',
            'data' => ['<span class="badge ' . $status_badge[$order->get_status()] . '">' . $order->get_status() . '</span>']
        ));


    }

    // Action vendor update delivered failed order
    if ($_POST['payload_action'] == 'order_status_delivery-failed'){

        $order_id = $_POST['order_id'];

        $order = wc_get_order($order_id);

        $order->update_status('wc-delivery-failed');

        echo json_encode(array(
            'status' => '200',
            'messenger' => 'success',
            'data' => ['<span class="badge ' . $status_badge[$order->get_status()] . '">' . $order->get_status() . '</span>']
        ));


    }

    // Action store update confirm goods order
    if ($_POST['payload_action'] == 'order_status_confirm-goods'){

        $order_id = $_POST['order_id'];

        $order = wc_get_order($order_id);

        $order->update_status('wc-confirm-goods');

        echo json_encode(array(
            'status' => '200',
            'messenger' => 'success',
            'data' => ['<span class="badge ' . $status_badge[$order->get_status()] . '">' . $order->get_status() . '</span>']
        ));


    }

    // Action admin update cancelled order
    if ($_POST['payload_action'] == 'order_status_cancelled'){

        $order_id = $_POST['order_id'];

        $order = wc_get_order($order_id);

        $order->update_status('wc-cancelled');

        echo json_encode(array(
            'status' => '200',
            'messenger' => 'success',
            'data' => ['<span class="badge ' . $status_badge[$order->get_status()] . '">' . $order->get_status() . '</span>']
        ));


    }





    exit;


}