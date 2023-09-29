<?php

use OMS\OMS_TO;

add_action( 'wp_ajax_transfer_order_add_new', 'transfer_order_add_new' );
add_action( 'wp_ajax_nopriv_transfer_order_add_new', 'transfer_order_add_new' );
function transfer_order_add_new()
{
    //Check have action and payload_action
    //payload action variant status ajax post
    if(!isset($_POST['action']) || !isset($_POST['payload_action'])) {
        echo response(false,'Không tìm thấy hành động được gửi',[]);
        exit;
    }

    if ($_POST['payload_action'] === 'transfer_order_add_new'){
        $transfer_order = new OMS_TO();
        $transfer_order->set_customer_id(get_current_user_id());
        $transfer_order->set_billing_first_name(wp_get_current_user()->display_name);
        $transfer_order->save();
        $transfer_order_id = $transfer_order->get_order_number();
        $transfer_ids = wc_get_orders(['post_type'=>'transfer_order','return'=>'ids','numberposts' => -1]);
        echo ' <div class="card mt-3">';
        wc_get_template('template-parts/dashboard/components/inventory/card-table-header.php',['ids'=>$transfer_ids, 'new_id' => $transfer_order_id]);
        echo '</div><div id="inventory_card_line" class="card mt-3">';
        wc_get_template('template-parts/dashboard/components/inventory/card-table-line.php',['id'=>$transfer_ids[0]]);
        echo '</div>';
        exit;

    }

}