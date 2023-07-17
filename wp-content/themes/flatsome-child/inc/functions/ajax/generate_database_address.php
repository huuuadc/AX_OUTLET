<?php


add_action( 'wp_ajax_generate_database_address', 'generate_database_address' );
add_action( 'wp_ajax_nopriv_generate_database_address', 'generate_database_address' );
function generate_database_address()
{

    if(!isset($_POST['action']) && $_POST['action'] !== 'generate_database_address') {
        echo json_encode(array(
            'status' => '500',
            'messenger' => 'No action map',
            'data' => []
        ));;
        exit;
    }

    if(create_address_shipment()){
        echo json_encode(array(
            'status' => '200',
            'messenger' => 'success',
            'data' => []
        ));
    } else {
        echo json_encode(array(
            'status' => '500',
            'messenger' => 'Create failed',
            'data' => []
        ));
    };

    exit;

}