<?php


add_action( 'wp_ajax_run_product_shop_by', 'run_product_shop_by' );
add_action( 'wp_ajax_nopriv_run_product_shop_by', 'run_product_shop_by' );
function run_product_shop_by()
{
    global $wpdb;

    if(!isset($_POST['action']) && $_POST['action'] !== 'run_product_shop_by') {
        echo json_encode(array(
            'status' => '500',
            'messenger' => 'No action map',
            'data' => []
        ));;
        exit;
    }

    $post = json_decode(json_encode ($_POST));

    if ($post->action_payload == 'action_last_piece') {

        $last_piece_qty = (int) $post->last_piece_qty ?? 1;

        if(!add_option('admin_dashboard_last_piece_qty',$last_piece_qty , '','no')){
            update_option('admin_dashboard_last_piece_qty',$last_piece_qty , 'no');
        }

        $ps = $wpdb->get_results("SELECT `ID` FROM {$wpdb->prefix}posts WHERE `post_status` = 'publish' AND `post_type` = 'product'");
        foreach ($ps as $p) {
            update_lastpiece_task($p->ID, $last_piece_qty);
        }

        echo json_encode(array(
            'status' => '200',
            'messenger' => 'Đã cập nhật thành công',
            'data' => []
        ));
        exit;

    }

    if ($post->action_payload == 'action_sales_special'  && $post->present_discount) {

        $checkbox_remove = $post->checkbox_remove == 'true';
        $present_discount = (int) $post->present_discount;
        write_log($post);
        $ps = $wpdb->get_results("SELECT `ID` FROM {$wpdb->prefix}posts WHERE `post_status` = 'publish' AND `post_type` = 'product'");
        foreach ($ps as $p) {
            update_sales_special($p->ID, $present_discount,$checkbox_remove);
        }

        echo json_encode(array(
            'status' => '200',
            'messenger' => 'Đã cập nhật thành công',
            'data' => []
        ));
        exit;

    }

    echo json_encode(array(
        'status' => '500',
        'messenger' => 'Không thực hiện được thao tác',
        'data' => []
    ));

    exit;

}