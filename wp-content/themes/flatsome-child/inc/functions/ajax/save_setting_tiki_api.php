<?php


add_action( 'wp_ajax_save_setting_tiki_api', 'save_setting_tiki_api' );
add_action( 'wp_ajax_nopriv_save_setting_tiki_api', 'save_setting_tiki_api' );
function save_setting_tiki_api()
{

    if(!isset($_POST['action']) && $_POST['action'] !== 'save_setting_tiki_api') {
        echo json_encode(array(
            'status' => '500',
            'messenger' => 'No action map',
            'data' => []
        ));;
        exit;
    }


    $post = json_decode(json_encode ($_POST));

    if(!add_option('tiki_base_url_address',$post->tiki_base_url_address , '','no')){
        update_option('tiki_base_url_address',$post->tiki_base_url_address , '','no');
    }

    if(!add_option('tiki_base_url_tnsl',$post->tiki_base_url_tnsl, '','no')){
        update_option('tiki_base_url_tnsl',$post->tiki_base_url_tnsl, '','no');
    }

    if(!add_option('tiki_client_id',$post->tiki_client_id, '','no')){
        update_option('tiki_client_id',$post->tiki_client_id, '','no');
    }

    if(!add_option('tiki_secret_key',$post->tiki_secret_key, '','no')){
        update_option('tiki_secret_key',$post->tiki_secret_key, '','no');
    }

    if(!add_option('tiki_secret_client',$post->tiki_secret_client, '','no')){
        update_option('tiki_secret_client',$post->tiki_secret_client, '','no');
    }

    if(!add_option('tiki_access_token',$post->tiki_access_token, '','no')){
        update_option('tiki_access_token',$post->tiki_access_token, '','no');
    }

    echo json_encode(array(
        'status' => '200',
        'messenger' => 'Save success',
        'data' => []
    ));;

    exit;

}