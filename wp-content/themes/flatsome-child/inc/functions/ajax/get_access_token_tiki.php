<?php

add_action('wp_ajax_get_access_token_tiki', 'get_access_token_tiki');
add_action('wp_ajax_nopriv_get_access_token_tiki', 'get_access_token_tiki');
function get_access_token_tiki()
{

    if (!isset($_POST['action']) && $_POST['action'] !== 'get_access_token_tiki') {
        echo json_encode(array(
            'status' => '500',
            'messenger' => 'No action map',
            'data' => []
        ));
        exit;
    }

    $tiki_api = new \TIKI\TIKI_API();

    $access_token = $tiki_api->get_token();

    if(!add_option('tiki_access_token',$access_token,'','no')){
        update_option('tiki_access_token', $access_token,'','no');
    }

    echo json_encode(array(
        'status' => '200',
        'messenger' => 'Save success',
        'data' => $access_token
    ));;

    exit;

}