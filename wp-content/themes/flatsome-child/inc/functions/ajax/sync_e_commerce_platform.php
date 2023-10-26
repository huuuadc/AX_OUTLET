<?php

use OMS\Tiktok_Api;

add_action( 'wp_ajax_sync_e_commerce_platform', 'sync_e_commerce_platform' );
add_action( 'wp_ajax_nopriv_sync_e_commerce_platform', 'sync_e_commerce_platform' );
/**
 * @throws WC_Data_Exception
 */
function sync_e_commerce_platform()
{

    if(!isset($_POST['action']) && $_POST['action'] !== 'sync_e_commerce_platform') {
        echo response(false,'No action map',[]);
        exit;
    }

    $post = json_decode(json_encode($_POST));

    $log = new WP_REST_API_Log_DB();

    $arg = [
        'route'         =>  '/admin-dashboard?sync_e_commerce_platform',
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

    $tiktok_api = new Tiktok_Api();
    if($_POST['action_payload'] === 'tiktok') {

        $response = $tiktok_api->get_order_detail();

        foreach ($response->order_list as $order)
        {

            $new_order = new OMS_ORDER();
            $new_order->set_billing_last_name($order->recipient_address->name);
            $new_order->set_billing_address_1($order->recipient_address->full_address);
            $new_order->set_billing_city($order->recipient_address->city);
            $new_order->set_billing_country('VN');
            $new_order->set_billing_phone($order->recipient_address->phone);
            $new_order->set_billing_postcode($order->recipient_address->zipcode);
            $new_order->set_billing_state('VN');
            $new_order->set_customer_note('Tiktok Order');

            $new_order->set_order_key($order->order_id);

            //Add product
            foreach ($order->item_list as $value)
            {
                //Get product id by product sku
                $product_id = wc_get_product_id_by_sku($value->seller_sku);

                //Get product by id
                $product    = wc_get_product($product_id);
                if($product){
                    //Add product item
                    $new_order->add_product($product,$value->quantity);
                }
            }

            //add shipping rate
            $shipping_info = new WC_Order_Item_Shipping();
            $shipping_info->set_method_title('Tiktok shipping');
            $shipping_info->set_total($order->payment_info->shipping_fee);
            $new_order->add_item($shipping_info);

            //add payment method
            $new_order->set_payment_method('TIKTOK_'.$order->payment_method );
            $new_order->set_payment_method_title($order->payment_method_name);

            $new_order->calculate_totals();

            $new_order->set_status('processing');

            $new_order->save();

            $new_order->set_order_type('tiktok');

        }

        echo response(true, 'Save success', []);
        exit;
    }

    echo response(false,'Error get order',[]);
    exit;

}