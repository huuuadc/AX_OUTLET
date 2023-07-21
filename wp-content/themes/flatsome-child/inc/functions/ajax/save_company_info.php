<?php


add_action( 'wp_ajax_save_company_info', 'save_company_info' );
add_action( 'wp_ajax_nopriv_save_company_info', 'save_company_info' );
function save_company_info()
{

    if(!isset($_POST['action']) && $_POST['action'] !== 'save_company_info') {
        echo json_encode(array(
            'status' => '500',
            'messenger' => 'No action map',
            'data' => []
        ));;
        exit;
    }

    $post = json_decode(json_encode ($_POST));

    if(!add_option('web_company_name',$post->web_company_name , '','no')){
        update_option('web_company_name',$post->web_company_name , 'no');
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

    echo json_encode(array(
        'status' => '200',
        'messenger' => 'Save success',
        'data' => []
    ));;

    exit;

}