<?php

use OMS\Tiktok_Api;

add_action( 'wp_ajax_sync_e_commerce_platform', 'sync_e_commerce_platform' );
add_action( 'wp_ajax_nopriv_sync_e_commerce_platform', 'sync_e_commerce_platform' );
function sync_e_commerce_platform()
{

    if(!isset($_POST['action']) && $_POST['action'] !== 'sync_e_commerce_platform') {
        echo response(false,'No action map',[]);
        exit;
    }

    $post = json_decode(json_encode($_POST));

    $log = new WP_REST_API_Log_DB();

    $arg = [
        'route'         =>  '/admin-dashboard?sync_e_commerce_platform',
        'source'        =>  'admin_dashboard',
        'method'        =>  'POST',
        'status'        =>  '200',
        'request'       =>  [
            'headers'    =>  [],
            'query_params'    =>  [],
            'body_params'    =>  $post,
            'body'      =>  json_encode($_POST),
        ],
        'response'      =>  [
            'headers'    =>  [],
            'body'      =>  array(
                'status' => '200',
                'messenger' => 'Save success',
                'data' => []
            )
        ]

    ];

    $log->insert($arg);

    $tiktok_api = new Tiktok_Api();
    if($_POST['action_payload'] === 'tiktok') {

        $response = $tiktok_api->get_order_detail();

        foreach ($response->order_list as $order)
        {
            write_log($order);
            $new_order = new OMS_ORDER();
            $new_order->set_billing_first_name('huu');
            $new_order->set_payment_method('BACS');
            $new_order->set_shipping_total($order->payment_info->shipping_fee);
            $new_order->set_total($order->payment_info->total_amount);
            $new_order->set_status('processing');
            $new_order->save();

            $new_order->set_order_type('tiktok');

            $product_id = wc_get_product_id_by_sku('1138040003');

            $product    = wc_get_product($product_id);
            $new_order->add_product($product,'1');

        }


        echo response(true,
            'Save success',
            [
                'access_token' => '',
                'refresh_token'=> '',
            ]
        );
        exit;
    }

    echo response(false,'Error get order',[]);
    exit;

}