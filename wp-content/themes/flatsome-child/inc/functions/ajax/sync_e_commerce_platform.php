<?php

use OMS\Tiktok_Api;

add_action( 'wp_ajax_sync_e_commerce_platform', 'sync_e_commerce_platform' );
add_action( 'wp_ajax_nopriv_sync_e_commerce_platform', 'sync_e_commerce_platform' );
/**
 * @throws WC_Data_Exception
 */
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

        if ($tiktok_api->sync_orders_v_202309()) {
            echo response(true, 'Save success', []);
            exit;
        } else {
            echo response(false,'Non order list',[]);
            exit;
        }
    }

    echo response(false,'No action match',[]);
    exit;

}