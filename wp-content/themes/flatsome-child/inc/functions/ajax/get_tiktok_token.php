<?php

use OMS\Tiktok_Api;

add_action( 'wp_ajax_get_tiktok_token', 'get_tiktok_token' );
add_action( 'wp_ajax_nopriv_get_tiktok_token', 'get_tiktok_token' );
function get_tiktok_token()
{

    if(!isset($_POST['action']) && $_POST['action'] !== 'get_tiktok_token') {
        echo response(false,'No action map',[]);
        exit;
    }

    $post = json_decode(json_encode($_POST));

    $log = new WP_REST_API_Log_DB();

    $arg = [
        'route'         =>  '/admin-dashboard?get_tiktok_token',
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
    if($_POST['action_payload'] == 'by_auth_code')     $response = $tiktok_api->get_token();
    if($_POST['action_payload'] == 'by_refresh_token')     $response = $tiktok_api->get_token_by_refresh_token();

    if (isset($response->code) && $response->code === 0)
    {

        echo response(true,
            'Save success',
            [
                'access_token' => $response->data->access_token ,
                'refresh_token'=> $response->data->refresh_token
            ]
        );
        exit;
    }

    echo response(false,'Lỗi get tiktok token',[]);
    exit;

}