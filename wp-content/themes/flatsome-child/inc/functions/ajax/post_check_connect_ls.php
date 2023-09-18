<?php

use OMS\LS_API;

add_action( 'wp_ajax_post_check_connect_ls', 'check_connect_ls' );
add_action( 'wp_ajax_nopriv_post_check_connect_ls', 'check_connect_ls' );
/**
 * @throws Exception
 */
function check_connect_ls(){

    if(!isset($_POST['action']) && $_POST['action'] !== 'post_check_connect_ls') {
            echo json_encode(array(
                'status' => '500',
                'messenger' => 'No action map',
                'data' => []
            ));;
            exit;
    }

    $username = $_POST['username'];
    $password = $_POST['password'];
    $baseurl = $_POST['base_url'];

    $payload_login = array('user_name'=> $username, 'user_pass'=> $password, 'base_url'=> $baseurl);

    $api_ls = new LS_API($payload_login);

    $token = $api_ls->checkConnectLs();

    if ($token['status']){
        echo json_encode(array(
            'status' => '200',
            'messenger' => '',
            'data' => []
        ));
    } else {
        echo json_encode(array(
            'status' => '500',
            'messenger' => $token['rep'],
            'data' => []
        ));
    }
    exit;


}