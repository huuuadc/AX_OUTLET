<?php

add_action('rest_api_init', function () {
    register_rest_route('shipment/v1', '/update_status', array(
        'methods' => 'POST',
        'callback' => 'shipment_order_update_status',
        'permission_callback' => '__return_true'
    ));
});


function shipment_order_update_status( WP_REST_Request $request ) {

    $secret_client = get_option('tiki_secret_client');

    $x_signature = $request->get_header('x_signature');

    write_log($request->get_body());

//    if(!verify_signature($request->get_body(),$secret_client,$x_signature)) return false;

    $req = json_decode( $request->get_body());

    $order_id =  str_replace('#','', $req->client_order_id);


    if (wc_get_order($order_id)){

        $order = new OMS_ORDER($order_id);

        update_post_meta($order_id,'shipment_status', $req->status);

        $shipment_log = explode('|', $order->get_meta('order_shipment_log',true,'value') ?? '');
        $shipment_log[] =   $request->get_body() ;
        update_post_meta($order_id,'order_shipment_log',implode('|',$shipment_log));

        if($req->status == 'picked'){
            $order->update_date_send_shipper($req->timestamp);
        }
        if($req->status == 'delivering'){
            $order->update_status('wc-shipping');
        }
        if($req->status == 'successful_delivery'){
            //If order status is "delivered". no update order status
            if($order->get_status() !== 'delivered') {
                $order->update_status('wc-delivered');
                $order->update_date_success_delivered($req->timestamp);
            }

            order_send_tow_ls($order);


        }
        if($req->status == 'failed_shipment'){
            $order->update_status('wc-delivery-failed');
        }

    }

    return true;

}