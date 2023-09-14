<?php


add_action( 'wp_ajax_save_admin_dashboard_setting', 'save_admin_dashboard_setting' );
add_action( 'wp_ajax_nopriv_save_admin_dashboard_setting', 'save_admin_dashboard_setting' );
function save_admin_dashboard_setting()
{

    if(!isset($_POST['action']) && $_POST['action'] !== 'save_admin_dashboard_setting') {
        echo json_encode(array(
            'status' => '500',
            'messenger' => 'No action map',
            'data' => []
        ));;
        exit;
    }

    $post = json_decode(json_encode ($_POST));

    if(!add_option('admin_dashboard_item_in_page',$post->item_in_page , '','no')){
        update_option('admin_dashboard_item_in_page',$post->item_in_page , 'no');
    }

    if(!add_option('admin_dashboard_footer_print_shipment',$post->footer_print_shipment , '','no')){
        update_option('admin_dashboard_footer_print_shipment',$post->footer_print_shipment , 'no');
    }

    if(!add_option('admin_dashboard_product_return_policy',$post->product_return_policy , '','no')){
        update_option('admin_dashboard_product_return_policy',$post->product_return_policy , 'no');
    }

    if(!add_option('admin_dashboard_item_fee_ship',$post->item_fee_ship , '','no')){
        update_option('admin_dashboard_item_fee_ship',$post->item_fee_ship , 'no');
    }

    if(!add_option('admin_dashboard_member_card_guest',$post->member_card_guest , '','no')){
        update_option('admin_dashboard_member_card_guest',$post->member_card_guest , 'no');
    }

    echo json_encode(array(
        'status' => '200',
        'messenger' => 'Save success',
        'data' => []
    ));;

    exit;

}