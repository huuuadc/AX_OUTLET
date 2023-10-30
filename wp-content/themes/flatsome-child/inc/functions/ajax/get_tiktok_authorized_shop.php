<?php

use OMS\Tiktok_Api;

add_action( 'wp_ajax_get_tiktok_authorized_shop', 'get_tiktok_authorized_shop' );
add_action( 'wp_ajax_nopriv_get_tiktok_authorized_shop', 'get_tiktok_authorized_shop' );
function get_tiktok_authorized_shop()
{

    if(!isset($_POST['action']) && $_POST['action'] !== 'get_tiktok_authorized_shop') {
        echo response(false,'No action map',[]);
        exit;
    }

    $post = json_decode(json_encode($_POST));

    $log = new WP_REST_API_Log_DB();

    $arg = [
        'route'         =>  '/admin-dashboard?get_tiktok_authorized_shop',
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
    $response = $tiktok_api->get_authorized_shop();

//    write_log($tiktok_api->get_order_detail_v_202309());
    if (count($response) > 0)
    {
        echo response(true,
            'Save success',
            [
                'tiktok_shop_id' => $response['id'] ,
                'tiktok_shop_cipher'=> $response['cipher'],
            ]
        );
        exit;
    }

    echo response(false,'Lỗi get tiktok token',[]);
    exit;

}