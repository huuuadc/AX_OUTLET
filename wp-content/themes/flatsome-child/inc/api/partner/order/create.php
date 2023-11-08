<?php


add_action('rest_api_init', function () {
    register_rest_route('v1/orders', '/create', array(
        'methods' => 'POST',
        'callback' => 'v1_order_create',
        'permission_callback' => '__return_true'
    ));
});

function v1_order_create( WP_REST_Request $request ) {

    $request_data = (object)json_decode($request->get_body());
    $errors =[];
    //check request
    //Check order key
    if ($request_data->order_key == ''){
        $errors[] = [
            'message'   =>  'order_key bắt buộc.'
        ];
    }
    $order_id = wc_get_order_id_by_order_key($request_data->order_key);
    if ($order_id){
        $errors[] = [
            'message'   =>  'order_key đã tồn tại.'
        ];
    }

    //Check order type
    $oms_channel = oms_get_channel_statuses();
    if (strtolower($request_data->order_type) == '' || !isset( $oms_channel[strtolower($request_data->order_type)])){
        $errors[] = [
            'message'   =>  'order_type không tồn tại.'
        ];
    }

    //Check payment method
    if (!in_array( strtolower($request_data->payment_method),['tiktok','shopee','lazada','cod','bacs'])){
        $errors[] = [
            'message'   =>  'payment_method không tồn tại.'
        ];
    }

    //Check payment method
    if (!in_array( strtolower($request_data->shipping_method),['tiktok','shopee','lazada','tnsl','sbp'])){
        $errors[] = [
            'message'   =>  'shipping_method không tồn tại.'
        ];
    }

    if (!isset($request_data->items) || count($request_data->items) <=0){
        $errors[] = [
            'message'   =>  'items bắt buộc.'
        ];
    }
    $count = 0;
    foreach ($request_data->items as $item){
        $count++;
        if ($item->sku == ''){
            $errors[] = [
                'message'   =>  'item thứ '. $count .' sku bắt buộc.'
            ];
        }
        if ($item->barcode == ''){
            $errors[] = [
                'message'   =>  'item thứ '. $count .' barcode bắt buộc.'
            ];
        }
        if ($item->price == ''){
            $errors[] = [
                'message'   =>  'item thứ '. $count .' price bắt buộc.'
            ];
        }
        if (!is_numeric($item->price)){
            $errors[] = [
                'message'   =>  'item thứ '. $count .' price không đúng định dạng.'
            ];
        }

    }

    if(count($errors)){
        return response_api(
            false,
            [],
            'failed',
            $errors
        );
    }

    $new_order = new OMS_ORDER();
    $new_order->set_billing_last_name( $request_data->billing->last_name ?? '');
    $new_order->set_billing_first_name( $request_data->billing->first_name ?? '');
    $new_order->set_billing_address_1( $request_data->billing->full_address ?? '');
    $new_order->set_billing_city($request_data->billing->city ?? '');
    $new_order->set_billing_country("VN");
    $new_order->set_billing_company($request_data->billing->company ?? '');
    $new_order->set_billing_phone($request_data->billing->phone ?? '');
    $new_order->set_billing_email($request_data->billing->email ?? '');
    $new_order->set_customer_note($request_data->buyer_message);

    $new_order->set_order_key($request_data->order_key);

    //Add product
    foreach ($request_data->items as $value)
    {
        //Get product id by product sku
        $product_id = wc_get_product_id_by_sku($value->sku);

        //Get product by id
        $product    = wc_get_product($product_id);

        if($product){
            //Add product item
            $item_id = $new_order->add_product(
                $product,
                1,
                [
                    'subtotal'  =>  $value->price,
                    'total'     =>  $value->price,
                ]);
            //update barcode item
            wc_update_order_item_meta($item_id, 'barcode',$value->barcode);
        }
    }

    //add shipping rate
    $shipping_info = new \WC_Order_Item_Shipping();
    $shipping_info->set_method_title($request_data->shipping_method);
    $shipping_info->set_total($request_data->payment->shipping_fee);
    $new_order->add_item($shipping_info);

    //add payment method

    $new_order->set_payment_method($request_data->payment_method);
    $new_order->set_payment_method_title('Thanh toán qua sàn');

    $new_order->calculate_totals();

    $new_order->set_status('processing');

    $new_order->save();

    $new_order->set_order_type($request_data->order_type);
    $new_order->update_billing_district($request_data->billing->district);
    $new_order->update_billing_ward($request_data->billing->ward);
    $new_order->set_tracking_id($request_data->tracking_id);

    $order = wc_get_order($new_order->get_id());

    return response_api(
        true,
        $order->get_data(),
        'success');

}