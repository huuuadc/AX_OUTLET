<?php

global $wpdb;

add_action( 'wp_ajax_run_product_shop_by', 'run_product_shop_by' );
add_action( 'wp_ajax_nopriv_run_product_shop_by', 'run_product_shop_by' );
function run_product_shop_by()
{
    global $wpdb;

    if(!isset($_POST['action']) && $_POST['action'] !== 'run_product_shop_by') {
        echo response(false,'No action map',[]);
        exit;
    }

    $post = json_decode(json_encode ($_POST));

    $log = new WP_REST_API_Log_DB();

    $arg = [
        'route'         =>  '/admin-dashboard?run_product_shop_by',
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

    if ($post->action_payload == 'action_last_piece') {

        $last_piece_qty = (int) $post->last_piece_qty ?? 1;

        if(!add_option('admin_dashboard_last_piece_qty',$last_piece_qty , '','no')){
            update_option('admin_dashboard_last_piece_qty',$last_piece_qty , 'no');
        }

        $products = $wpdb->get_results("SELECT `ID` FROM {$wpdb->prefix}posts WHERE `post_status` = 'publish' AND `post_type` = 'product'");
        foreach ($products as $p) {
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
        $products = $wpdb->get_results("SELECT `ID` FROM {$wpdb->prefix}posts WHERE `post_status` = 'publish' AND `post_type` = 'product'");

        foreach ($products as $p) {
            update_sales_special($p->ID, $present_discount,$checkbox_remove);
        }

        echo response(true,'Đã cập nhật thành công',[]);
        exit;

    }

    if ($post->action_payload == 'action_update_sale_price') {

        $products = $wpdb->get_results("SELECT `ID` FROM {$wpdb->prefix}posts WHERE `post_status` = 'publish' AND `post_type` = 'product'");
        foreach ($products as $p) {

            $product = wc_get_product($p->ID);

            $discounted_price = apply_filters('advanced_woo_discount_rules_get_product_discount_price_from_custom_price', false, $product, 1, 0, 'all', true);

            if (isset($discounted_price['discounted_price'])){
                $price = (int) $discounted_price['discounted_price'];
            } else {
                $price = (int) $product->get_price();
            }

            if (get_post_meta($product->get_id(),'pricesale',false)){
                update_post_meta($product->get_id(),"pricesale",$price);
            }else{
                add_post_meta($product->get_id(),"pricesale",$price);
            }

            //Update min,max price lookup product filter
            $wpdb->update($wpdb->prefix.'wc_product_meta_lookup',
                array(
                    'min_price' => $price,
                    'max_price' => $price
                ),
                array('product_id'=>$p->ID));

        }

        echo response(true,'Đã cập nhật thành công',[]);
        exit;

    }


    if  ($post->action_payload == 'action_update_check_stock_manager'){

        $product_skus = $post->product_skus;
        foreach ($product_skus as $product_sku){
            $product_id = wc_get_product_id_by_sku($product_sku);
            if($product_id && strlen($product_sku) < 9){
                $product = wc_get_product($product_id);
                $product->set_manage_stock(false);
                $product->save();
            }
        }

        echo response(true,'Đã chạy hoàn tất',[]);

        exit;
    }

    echo response(false,'Không thực hiện được thao tác',[]);

    exit;

}