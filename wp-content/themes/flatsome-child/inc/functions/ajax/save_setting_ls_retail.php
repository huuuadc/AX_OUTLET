<?php


add_action( 'wp_ajax_save_setting_ls_retail', 'save_setting_ls_retail' );
add_action( 'wp_ajax_nopriv_save_setting_ls_retail', 'save_setting_ls_retail' );
function save_setting_ls_retail()
{

    if(!isset($_POST['action']) && $_POST['action'] !== 'save_setting_ls_retail') {
        echo json_encode(array(
            'status' => '500',
            'messenger' => 'No action map',
            'data' => []
        ));;
        exit;
    }


    $post = json_decode(json_encode ($_POST));

    if(!add_option('wc_settings_tab_config_name',$post->wc_settings_tab_config_name ?? '' , '','no')){
        update_option('wc_settings_tab_config_name',$post->wc_settings_tab_config_name ?? '' , '');
    }

    if(!add_option('wc_settings_tab_ls_api_url',$post->wc_settings_tab_ls_api_url ?? '' , '','no')){
        update_option('wc_settings_tab_ls_api_url',$post->wc_settings_tab_ls_api_url ?? '' , '');
    }

    if(!add_option('wc_settings_tab_ls_api_username',$post->wc_settings_tab_ls_api_username ?? '' , '','no')){
        update_option('wc_settings_tab_ls_api_username',$post->wc_settings_tab_ls_api_username ?? '' , '');
    }

    if(!add_option('wc_settings_tab_ls_api_password',$post->wc_settings_tab_ls_api_password ?? '' , '','no')){
        update_option('wc_settings_tab_ls_api_password',$post->wc_settings_tab_ls_api_password ?? '' , '');
    }

    if(!add_option('wc_settings_tab_ls_location_code',$post->wc_settings_tab_ls_location_code ?? '' , '','no')){
        update_option('wc_settings_tab_ls_location_code',$post->wc_settings_tab_ls_location_code ?? '' , '');
    }

    if(!add_option('wc_settings_tab_ls_location_code2',$post->wc_settings_tab_ls_location_code2 ?? '' , '','no')){
        update_option('wc_settings_tab_ls_location_code2',$post->wc_settings_tab_ls_location_code2 ?? '' , '');
    }

    if(!add_option('wc_settings_tab_config_name_2',$post->wc_settings_tab_config_name_2 ?? '' , '','no')){
        update_option('wc_settings_tab_config_name_2',$post->wc_settings_tab_config_name_2 ?? '' , '');
    }

    if(!add_option('wc_settings_tab_ls_api_url_2',$post->wc_settings_tab_ls_api_url_2 ?? '' , '','no')){
        update_option('wc_settings_tab_ls_api_url_2',$post->wc_settings_tab_ls_api_url_2 ?? '' , '');
    }

    if(!add_option('wc_settings_tab_ls_api_username_2',$post->wc_settings_tab_ls_api_username_2 ?? '' , '','no')){
        update_option('wc_settings_tab_ls_api_username_2',$post->wc_settings_tab_ls_api_username_2 ?? '' , '');
    }

    if(!add_option('wc_settings_tab_ls_api_password_2',$post->wc_settings_tab_ls_api_password_2 ?? '' , '','no')){
        update_option('wc_settings_tab_ls_api_password_2',$post->wc_settings_tab_ls_api_password_2 ?? '' , '');
    }

    if(!add_option('wc_settings_tab_ls_location_code_2',$post->wc_settings_tab_ls_location_code_2 ?? '' , '','no')){
        update_option('wc_settings_tab_ls_location_code_2',$post->wc_settings_tab_ls_location_code_2 ?? '' , '');
    }

    if(!add_option('wc_settings_tab_ls_location_code2_2',$post->wc_settings_tab_ls_location_code2_2 ?? '' , '','no')){
        update_option('wc_settings_tab_ls_location_code2_2',$post->wc_settings_tab_ls_location_code2_2 ?? '' , '');
    }

    if(!add_option('wc_settings_tab_ls_access_token',$post->wc_settings_tab_ls_access_token ?? '' , '','no')){
        update_option('wc_settings_tab_ls_access_token',$post->wc_settings_tab_ls_access_token ?? '' , '');
    }

    if(!add_option('wc_settings_tab_ls_access_token_2',$post->wc_settings_tab_ls_access_token_2 ?? '' , '','no')){
        update_option('wc_settings_tab_ls_access_token_2',$post->wc_settings_tab_ls_access_token_2 ?? '' , '');
    }

    $log = new WP_REST_API_Log_DB();

    $arg = [
        'route'         =>  '/admin-dashboard?save_setting_ls_retail',
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