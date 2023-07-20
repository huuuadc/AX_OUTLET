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
        ));;
        exit;
    }

    $post = json_decode(json_encode($_POST));

    echo json_encode(array(
        'status' => '200',
        'messenger' => 'Save success',
        'data' => 'access_token'
    ));;

    exit;

}