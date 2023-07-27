<?php


add_action('rest_api_init', function () {
    register_rest_route('shipment/v1', '/update_status', array(
        'methods' => 'POST',
        'callback' => 'shipment_order_update_status',
    ));
});


function shipment_order_update_status( WP_REST_Request $request ) {

    write_log($request->get_body());

    $req = json_decode( $request->get_body());

    $order_id =  str_replace('#','', $req->client_order_id);

    write_log($req);

    if (wc_get_order($order_id)){

        $order = new AX_ORDER($order_id);
        update_post_meta($order_id,'shipment_code', $req->status);

    }


}