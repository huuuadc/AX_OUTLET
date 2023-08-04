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

//        $myfile = fopen(__DIR__ . "/logs/update_lastpiece_".date('Y-m-d_H-i-s').".txt", "w") or die("Unable to open file!");
//        $txt = date('Y-m-d_H-i-s');
//        fwrite($myfile, $txt);
//        fclose($myfile);


        $ps = $wpdb->get_results("SELECT `ID` FROM {$wpdb->prefix}posts WHERE `post_status` = 'publish' AND `post_type` = 'product'");
        foreach ($ps as $p) {
            update_lastpiece_task($p->ID);
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