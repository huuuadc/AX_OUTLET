<?php


add_action( 'wp_ajax_save_setting_viettel_vinvoice', 'save_setting_viettel_vinvoice' );
add_action( 'wp_ajax_nopriv_save_setting_viettel_vinvoice', 'save_setting_viettel_vinvoice' );
function save_setting_viettel_vinvoice()
{

    if(!isset($_POST['action']) && $_POST['action'] !== 'save_setting_viettel_vinvoice') {
        echo json_encode(array(
            'status' => '500',
            'messenger' => 'No action map',
            'data' => []
        ));;
        exit;
    }


    $post = json_decode(json_encode ($_POST));

    if(!add_option('viettel_base_url',$post->viettel_base_url ?? '' , '','no')){
        update_option('viettel_base_url',$post->viettel_base_url ?? '' , '');
    }

    if(!add_option('viettel_username',$post->viettel_username ?? '' , '','no')){
        update_option('viettel_username',$post->viettel_username ?? '' , '');
    }

    if(!add_option('viettel_password',$post->viettel_password ?? '' , '','no')){
        update_option('viettel_password',$post->viettel_password ?? '' , '');
    }

//    test viettel invoice
//    $viettel_vinvoice = new \OMS\Viettel_Invoice();
//    $viettel_vinvoice->create_invoice_by_order_id(81796);

    $log = new WP_REST_API_Log_DB();

    $arg = [
        'route'         =>  '/admin-dashboard?save_setting_viettel_vinvoice',
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

    echo json_encode(array(
        'status' => '200',
        'messenger' => 'Save success',
        'data' => []
    ));;

    exit;

}