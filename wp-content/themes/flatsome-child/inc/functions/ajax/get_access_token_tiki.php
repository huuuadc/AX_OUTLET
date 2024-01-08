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

    $tiki_api = new \OMS\TIKI_API();


    if (isset($_POST['payload_action']) && $_POST['payload_action'] == 'get_token') {

        $access_token = $tiki_api->get_token();

        if ($access_token !== '') {
            if (!add_option('tiki_access_token', $access_token, '', 'no')) {
                update_option('tiki_access_token', $access_token, 'no');
            }

            echo json_encode(array(
                'status' => '200',
                'messenger' => 'Save success',
                'data' => ['token' => $access_token]
            ));
            exit;
        }
    }

    if (isset($_POST['payload_action']) && $_POST['payload_action'] == 'register_webhook') {

        if ($tiki_api->post_register_webhook()){
            echo json_encode(array(
                'status' => '200',
                'messenger' => 'Register webhook success',
                'data' => ''
            ));
        }else{
            echo json_encode(array(
                'status' => '500',
                'messenger' => 'Register webhook failed',
                'data' => ''
            ));
        };

        exit;

    }

    echo json_encode(array(
        'status' => '500',
        'messenger' => 'Failed get token',
        'data' => ''
    ));
    exit;

}