<?php

/**
 * Get all order channel statuses.
 *
 * @since 2.2
 * @used-by OMS_ORDER::set_order_type()
 * @return array
 */
function oms_get_channel_statuses() {
    return  array(
        'website'       => _x( 'Website', 'Channel type', 'oms' ),
        'lazada'        => _x( 'Lazada', 'Channel type', 'oms' ),
        'shopee'        => _x( 'Shopee', 'Channel type', 'oms' ),
        'tiktok'        => _x( 'Tik tok', 'Channel type', 'oms' ),
        'tiki'          => _x( 'Tiki', 'Channel type', 'oms' ),
        'toout'          => _x( 'TO Out', 'Channel type', 'oms' ),
    );
}




add_action('woocommerce_admin_order_data_after_order_details','add_meta_box_order_channel_type');

function add_meta_box_order_channel_type(WC_Order $order){
    $oms_order = new OMS_ORDER($order->get_id())
    ?>
    <p class="form-field form-field-wide wc-order-status">
        <label for="channel_status">
            <?php
            esc_html_e( 'Chanel type:', 'woocommerce' );
            ?>
        </label>
        <select id="channel_status" name="channel_status" class="wc-enhanced-select">
            <?php
            $statuses = oms_get_channel_statuses();
            foreach ( $statuses as $status => $status_name ) {
                echo '<option value="' . esc_attr( $status ) . '" ' . selected( $status,  $oms_order->get_order_type(), false ) . '>' . esc_html( $status_name ) . '</option>';
            }
            ?>
        </select>
    </p>
    <?php
}

add_action('woocommerce_process_shop_order_meta','save_meta_box_channel_type');

function save_meta_box_channel_type($post_id){
    $oms_order = new OMS_ORDER($post_id);
    if (isset($_POST['channel_status'])) $oms_order->set_order_type($_POST['channel_status']) ;
    return true;
}

function check_stock_ls($items = []): bool {

    $ls_api = new \OMS\LS_API();

    $data = (object)\OMS\ls_request_check_stock_v3();

    //Khởi tạo inventory = 0;
    $data->Inventory = 0;

    $arg_data = [];
    foreach ($items as $item) {

        $product = wc_get_product($item['product_id']);
        $data->LocationCode = $ls_api->location_code[0] ?? '';
        $data->ItemNo = $product->get_sku();
//            $data->ItemNo = '1117342';
        if ($item['variation_id'] > 0) {
            $product_variant = wc_get_product($item['variation_id']);
            $data->BarcodeNo = $product_variant->get_sku();
//                $data->BarcodeNo = '1117342000';
        } else {
            $data->BarcodeNo = '';
        }

        $data->Qty = $item['qty'];

        $arg_data[] = (array)$data;
    }

    $response = $ls_api->post_product_check_stock_v3($arg_data);

    $safe_stock = true;
    foreach ($arg_data as $key => $item){

        if($item['BarcodeNo'] == ''){
            foreach ($response->data as $value){
                if($value->ItemNo == $item['ItemNo'])
                    $item['Inventory'] = $value->Inventory ?? 0;
                    $arg_data[$key]['Inventory'] = $value->Inventory ?? 0;
            }
        }

        if($item['BarcodeNo'] != ''){
            foreach ($response->data as $value){
                if($value->ItemNo == $item['ItemNo']
                    && $value->BarcodeNo == $item['BarcodeNo'])
                    $item['Inventory'] = $value->Inventory ?? 0;
                $arg_data[$key]['Inventory'] = $value->Inventory ?? 0;
            }
        }

        if($item['Inventory'] < $item['Qty']){
            write_log('LS Check Stock: Không còn stock'. json_encode($response->data));
            $safe_stock = false;
        }

    }

    return $safe_stock;
}