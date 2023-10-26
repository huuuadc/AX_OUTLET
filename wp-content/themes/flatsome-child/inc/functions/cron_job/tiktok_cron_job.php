<?php
use \OMS\Tiktok_Api;
// function that registers new custom schedule

function bf_add_custom_schedule( $schedules )
{
    $schedules[ 'every_five_minutes' ] = array(
        'interval' => 300,
        'display'  => 'Every 5 minutes',
    );

    return $schedules;
}

// function that schedules custom event

function bf_schedule_custom_event()
{
    // the actual hook to register new custom schedule

    add_filter( 'cron_schedules', 'bf_add_custom_schedule' );

    // schedule custom event

    if( !wp_next_scheduled( 'bf_your_custom_event' ) )
    {
        wp_schedule_event( time(), 'every_five_minutes', 'bf_your_custom_event' );
    }
}
add_action( 'init', 'bf_schedule_custom_event' );

// fire custom event

function bf_do_something_on_schedule()
{
    write_log('Đang chay');

    $tiktok_api = new Tiktok_Api();

    $response = $tiktok_api->get_order_detail();

    if(!isset($response->order_list)) return;

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
}
add_action( 'bf_your_custom_event', 'bf_do_something_on_schedule' );