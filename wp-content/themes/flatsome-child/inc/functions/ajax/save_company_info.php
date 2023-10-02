<?php


add_action( 'wp_ajax_save_company_info', 'save_company_info' );
add_action( 'wp_ajax_nopriv_save_company_info', 'save_company_info' );
function save_company_info()
{

    if(!isset($_POST['action']) && $_POST['action'] !== 'save_company_info') {
        echo response(false,'No action map',[]);
        exit;
    }

    $post = json_decode(json_encode ($_POST));

    if(!add_option('web_company_name',$post->web_company_name , '','no')){
        update_option('web_company_name',$post->web_company_name , 'no');
    }

    if(!add_option('web_company_code',$post->web_company_code , '','no')){
        update_option('web_company_code',$post->web_company_code , 'no');
    }

    if(!add_option('web_company_email',$post->web_company_email, '','no')){
        update_option('web_company_email',$post->web_company_email, 'no');
    }

    if(!add_option('web_company_phone',$post->web_company_phone, '','no')){
        update_option('web_company_phone',$post->web_company_phone, 'no');
    }

    if(!add_option('web_company_country',$post->web_company_country, '','no')){
        update_option('web_company_country',$post->web_company_country, 'no');
    }

    if(!add_option('web_company_city',$post->web_company_city, '','no')){
        update_option('web_company_city',$post->web_company_city, 'no');
    }

    if(!add_option('web_company_district',$post->web_company_district, '','no')){
        update_option('web_company_district',$post->web_company_district, 'no');
    }

    if(!add_option('web_company_ward',$post->web_company_ward, '','no')){
        update_option('web_company_ward',$post->web_company_ward, 'no');
    }

    if(!add_option('web_company_address',$post->web_company_address, '','no')){
        update_option('web_company_address',$post->web_company_address, 'no');
    }

    $log = new WP_REST_API_Log_DB();

    $arg = [
        'route'         =>  '/admin-dashboard?save_company_info',
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