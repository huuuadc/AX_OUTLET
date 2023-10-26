<?php


add_action( 'wp_ajax_save_tiktok_api_setting', 'save_tiktok_api_setting' );
add_action( 'wp_ajax_nopriv_save_tiktok_api_setting', 'save_tiktok_api_setting' );
function save_tiktok_api_setting()
{

    if(!isset($_POST['action']) && $_POST['action'] !== 'save_tiktok_api_setting') {
        echo response(false,'No action map',[]);
        exit;
    }

    $post = json_decode(json_encode ($_POST));

    if(!add_option('tiktok_auth_url',$post->tiktok_auth_url , '','no')){
        update_option('tiktok_auth_url',$post->tiktok_auth_url , 'no');
    }
    if(!add_option('tiktok_token_url',$post->tiktok_token_url , '','no')){
        update_option('tiktok_token_url',$post->tiktok_token_url , 'no');
    }
    if(!add_option('tiktok_api_url',$post->tiktok_api_url , '','no')){
        update_option('tiktok_api_url',$post->tiktok_api_url , 'no');
    }
    if(!add_option('tiktok_client_secret',$post->tiktok_client_secret , '','no')){
        update_option('tiktok_client_secret',$post->tiktok_client_secret , 'no');
    }
    if(!add_option('tiktok_app_key',$post->tiktok_app_key , '','no')){
        update_option('tiktok_app_key',$post->tiktok_app_key , 'no');
    }
    if(!add_option('tiktok_app_secret',$post->tiktok_app_secret , '','no')){
        update_option('tiktok_app_secret',$post->tiktok_app_secret , 'no');
    }
    if(!add_option('tiktok_version',$post->tiktok_version , '','no')){
        update_option('tiktok_version',$post->tiktok_version , 'no');
    }

    $log = new WP_REST_API_Log_DB();

    $arg = [
        'route'         =>  '/admin-dashboard?save_tiktok_api_setting',
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

    echo response(true,'Save success',[]);

    exit;

}