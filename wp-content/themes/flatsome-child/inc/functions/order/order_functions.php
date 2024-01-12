<?php

use function OMS\ls_request_transfer_line;
use function OMS\ls_transactions_request;
use function OMS\ls_payment_request;

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

function check_stock_ls($items = []): array {

    $ls_api = new \OMS\LS_API();

    $data = (object)\OMS\ls_request_check_stock_v3();

    //Khởi tạo inventory = 0;
    $data->Inventory = 0;

    $arg_data = [];
    $data_check = [];
    foreach ($items as $item) {

        $product = wc_get_product($item['product_id']);
        $data->LocationCode = $ls_api->location_code[0] ?? '';
        $data->ItemNo = $product->get_sku();
//            $data->ItemNo = '1117342';
        if ($item['variation_id'] > 0) {
            $product_variant = wc_get_product($item['variation_id']);
            $data->ItemName = $product_variant->get_name();
            $data->BarcodeNo = $product_variant->get_sku();
//                $data->BarcodeNo = '1117342000';
        } else {
            $data->ItemName = $product->get_name();
            $data->BarcodeNo = '';
        }

        $data->Qty = $item['qty'];

        $arg_data[] = (array)$data;
    }

    $response = $ls_api->post_product_check_stock_v3($arg_data);

    foreach ($arg_data as $key => $item){

        if($item['BarcodeNo'] == '' ){
            foreach ($response->data as $value){
                if($value->ItemNo == $item['ItemNo'])
                    $item['Inventory'] = $value->Inventory ?? 0;
                    $arg_data[$key]['Inventory'] = $value->Inventory ?? 0;
            }
        }

        if($item['BarcodeNo'] != ''){
            foreach ($response->data as $value){
                if($value->ItemNo == $item['ItemNo']
                    && ($value->BarcodeNo == $item['BarcodeNo'] || $item['BarcodeNo'] == $value->ItemNo . $value->Variant ))
                    $item['Inventory'] = $value->Inventory ?? 0;
                $arg_data[$key]['Inventory'] = $value->Inventory ?? 0;
            }
        }

        if($item['Inventory'] < $item['Qty']){
            $data_check[] = $item['ItemName'];
        }

    }

    return $data_check;
}

function verify_signature($payload = '', $secret = '',$x_request = ''):bool
{
    if($x_request === '') return false;

    $hash_mac = 'sha1='. hash_hmac('sha1',$payload,$secret);
    if ($hash_mac === $x_request) {
        return true;
    }
    write_log('Tiki TNSL Webhook fai: '.$hash_mac .' - '.$x_request);
    return false;
}

//Add type for order check post type
add_filter('wc_order_types','transfer_order_add');
function transfer_order_add($order_types)
{
    if (isset($_SERVER['REQUEST_URI'])
        && str_contains( $_SERVER['REQUEST_URI'] ,'/inventory-adjustment'))
        $order_types['']  = 'transfer_order';
    if (isset($_POST['payload_action'])) $order_types['']  = 'transfer_order';

    return $order_types;
}


function get_qty_product_id_in_orders($product_id = 0, $variant_id = 0, $statuses = ['wc-completed'])
{
    global $wpdb;

    $query_status = '"'.implode('","',$statuses) . '"';

    $qty = $wpdb->get_results("
        SELECT sum(product_qty) qty 
        FROM {$wpdb->prefix}wc_order_product_lookup 
        WHERE product_id = '{$product_id}' 
          and variation_id = {$variant_id} 
          and order_id 
                  in (
                  select id from {$wpdb->prefix}posts where post_status in ({$query_status})
                                            )
    ");

    return  $qty['0']->qty ?? 0;
}

function order_send_tow_ls(WC_Order $order){

    global $wpdb;
    //Post ls
    $ls_api_ls_to         = new \OMS\LS_API();

    $base_url_transaction                 =   get_option('wc_settings_tab_ls_api_url_2') ?? '';
    $username_transaction                 =   get_option('wc_settings_tab_ls_api_username_2') ?? '';
    $password_transaction                 =   get_option('wc_settings_tab_ls_api_password_2') ?? '';

    //ls_api post invoice to style outlet
    $ls_api_transaction = new \OMS\LS_API([
            'user_name' => $username_transaction,
            'user_pass'  => $password_transaction,
            'base_url' => $base_url_transaction]);

    //get old status
    $old_status     = $order->get_status('value');

    $location_code = get_option('wc_settings_tab_ls_location_code');
    if (!$location_code) {
        write_log('No location code');
        return false;
    }
    $location_code2 = get_option('wc_settings_tab_ls_location_code2');
    if (!$location_code2) {
        write_log('No location code 2');
        return false;
    }

    $location_code21 = get_option('wc_settings_tab_ls_location_code_2');
    if (!$location_code21) {
        write_log('No location code 21');
        return false;
    }
    $item_no_ship = get_option('admin_dashboard_item_fee_ship');
    if (!$item_no_ship) {
        write_log('No item fee ship');
        return false;
    }

    //Get member user information
    $rep_member = $ls_api_transaction->get_member_information($order->get_billing_phone());

    if((isset($rep_member->Responcode)) && $rep_member->Responcode == 200){
        $member_card_guest = $rep_member->Model->cardNo ?? get_option('admin_dashboard_member_card_guest') ;
        $order_phone = $order->get_billing_phone();
    }else{
        $member_card_guest = get_option('admin_dashboard_member_card_guest');
        $order_phone = '0000';
    }

    if (!$member_card_guest) {
        write_log('No member card');
        return false;
    }

    //===========================================================
    //transaction header
    //===========================================================

    $order_number = $order->get_id();
    $order_no = get_option('web_company_code') != '' ? get_option('web_company_code').$order_number : 'OL'.$order_number;

    $ls_method_type = $order->get_method_type_ls();

    $data_request_payment = (object) ls_payment_request();

    $vat_address = $order->get_vat_company_address() == '' ?
        'Người mua không cung cấp' :
        $order->get_vat_company_address();

    $data_request_payment->Location_Code = $location_code21;
    $data_request_payment->Transaction_No_ = $order_no;
    $data_request_payment->LineNo = 30000;
    $data_request_payment->Receipt_No_ = $order_no;
    $data_request_payment->Tender_Type = $ls_method_type['tender_type'];
    $data_request_payment->Amount_Tendered = $order->get_total();
    $data_request_payment->Amount_in_Currency = $order->get_total();
    $data_request_payment->Date = date('Y-m-d') . ' 00:00:00.000';
    $data_request_payment->Time = date('Y-m-d') . ' ' . date('H:i:s.v');
    $data_request_payment->Quantity = 1;
    $data_request_payment->VAT_Buyer_Name = $order->get_vat_company_name() == '' ? 'Khách lẻ' : $order->get_formatted_billing_full_name();
    $data_request_payment->VAT_Company_Name = $order->get_vat_company_name();
    $data_request_payment->VAT_Tax_Code = $order->get_vat_company_tax_code();
    $data_request_payment->VAT_Phone_No_ = '';
    $data_request_payment->VAT_Email = $order->get_vat_company_email();
    $data_request_payment->VAT_Address = $vat_address;
    $data_request_payment->VAT_Payment_Method = $ls_method_type['vat_payment_method'];
    $data_request_payment->VAT_Bank_Account = '';
    $data_request_payment->Member_Phone = $order_phone;
    $data_request_payment->THENH = $order->get_number_card_payment();
    $data_request_payment->Cash = $order->get_total();


    //===========================================================
    //===========================================================
    //transaction detail
    //===========================================================
    //===========================================================
    $data_request_transaction_item = (object) ls_transactions_request();
    $data_request_transaction = [];

    $data_request_transfer_line_item = (object) ls_request_transfer_line();
    $data_request_transfer_line = [];

    $line_no = 0;
    $line_default = 10000;

    $CouponCode = '';
    $CouponNo = '';

    foreach( $order->get_coupons() as $order_item_coupon ) {

        $CouponCode = $order_item_coupon->get_code();
        $coupon = new WC_Coupon($CouponCode);
        $discount_type = $coupon->get_discount_type(); // Get coupon discount type
        $coupon_amount = $coupon->get_amount(); // Get coupon amount
        $CouponNo = $coupon->get_meta('CouponNo', true);

    }

    foreach ($order->get_items() as $item) {

        $product  = $item->get_product();

        $product_id = $item['product_id'];
        $variation_id = $item['variation_id'];

        $item_No = get_post_meta($product_id, 'offline_id', true);
        $variant_code = strtoupper($item->get_meta('pa_size')) ?? '';

        //Get list serial_no
        $list_serial_no = [];
        $data_serial_no = $ls_api_ls_to->get_product_check_stock_v1($location_code, $item_No,$variant_code);
        if (isset($data_serial_no->data) && count($data_serial_no->data) > 0){
            $list_serial_no = $data_serial_no->data;
        }

        $provar_id = $variation_id ? $variation_id : $product_id;
        $product_cats_ids = wc_get_product_term_ids( $product_id, 'product_cat' );
        $active_price   = $product->get_price(); // The product active raw price
        $sale_price  = $product->get_sale_price(); // The product raw sale price
        $regular_price     = $product->get_regular_price(); // The product raw regular price
        $product_name   = $item->get_name(); // Get the item name (product name)
        $item_quantity  = $item->get_quantity(); // Get the item quantity
        $item_subtotal  = $item->get_subtotal(); // Get the item line total non discounted
        $item_subto_tax = $item->get_subtotal_tax(); // Get the item line total tax non discounted
        $item_total     = $item->get_total(); // Get the item line total discounted
        $item_total_tax = $item->get_total_tax(); // Get the item line total  tax discounted
        $item_taxes     = $item->get_taxes(); // Get the item taxes array
        $item_tax_class = $item->get_tax_class(); // Get the item tax class
        $item_tax_status= $item->get_tax_status(); // Get the item tax status
        $item_downloads = $item->get_item_downloads(); // Get the item downloads
        $projectsku = $product->get_sku();
        $DiscountRate = 0; //default
        $Value_Type = 0; //kh&#1092;ng c&#1091; gi&#7843;m gi&#1073;
        $DiscountAmount = 0; //default

        $order_item_discounts = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."wdr_order_item_discounts WHERE item_id = " . $provar_id . " AND item_id != 0 AND order_id = " . $order->get_id() ) );

        if($order_item_discounts) {
            $wdr_rule = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."wdr_rules WHERE id = " . $order_item_discounts->rule_id ) );
            if($wdr_rule) {
                $wdr_rule_discount_type = $wdr_rule->discount_type;
                switch ($wdr_rule_discount_type) {
                    case 'wdr_simple_discount':
                        $product_adjustments = json_decode($wdr_rule->product_adjustments, true);
                        if($product_adjustments['type'] == 'percentage') {
                            $Value_Type = 1;
                            $DiscountRate = $product_adjustments['value'];
                        }elseif($product_adjustments['type'] == 'flat') {
                            $Value_Type = 2;
                            $DiscountAmount = $product_adjustments['value'];
                        }
                        break;

                    case 'wdr_cart_discount':
                        $cart_adjustments = json_decode($wdr_rule->cart_adjustments, true);
                        if($cart_adjustments['type'] == 'percentage') {
                            $Value_Type = 1;
                            $DiscountRate = $cart_adjustments['value'];
                        }
                        break;

                    default:
                        break;

                }
            }
        }

        if($order_item_discounts) {
            $UnitPrice = $order_item_discounts->item_price;
            $TotalPrice = $order_item_discounts->item_price;
            $DiscountAmount = 0;
            $Disc = $order_item_discounts->item_price - $order_item_discounts->discounted_price;
            $TotalAmt = $order_item_discounts->discounted_price;
            $Offer_Online_ID = $order_item_discounts->rule_id;
        } else {
            $UnitPrice = $regular_price;
            $TotalPrice = $item_subtotal / $item_quantity;
            $DiscountRate = "";
            $Disc = "";
            $DiscountAmount = "";
            $TotalAmt = $item_total / $item_quantity;
            $Offer_Online_ID = "";
        }

        $product_cats_ids = wc_get_product_term_ids( $product_id, 'product_cat' );

        for($i = 0 ; $i < $item_quantity; $i++){
            $line_no++;
            $data_request_transaction[] = array (
                'Location_Code'         =>          $location_code21,
                'Receipt_No_'           =>          $order_no,
                'Transaction_No_'       =>          $order_no,
                'LineNo'                =>          $line_default + $line_no,
                'LineNo_Online'         =>          $item->get_id(),
                'Item_No_'              =>          $item_No,
                'SerialNo'              =>          $list_serial_no[$i]->SerialNo ?? '',
                'Variant_Code'          =>          $variant_code,
                'Trans_Date'            =>          date('Y-m-d') . ' ' . date('H:i:s.v'),
                'Quantity'              =>          -1,
                'UnitPrice'             =>          $UnitPrice,
                'TotalPrice'            =>          $TotalPrice,
                'DiscountRate'          =>          $DiscountRate,
                'DiscountAmount'        =>          $DiscountAmount,
                'Disc'                  =>          $Disc,
                'TotalAmt'              =>          $TotalAmt,
                'Member_Card_No_'       =>          $member_card_guest,
                'Offer_Online_ID'       =>          $Offer_Online_ID,
                'CouponCode'            =>          $CouponCode,
                'CouponNo'              =>          $CouponNo,
                'Value_Type'            =>          $Value_Type,
                'Category_Online_ID'    =>          $product_cats_ids
            );
        }

        $data_request_transfer_line_item->ItemNo = $item_No;
        $data_request_transfer_line_item->VariantCode = strtoupper($variant_code);
        $data_request_transfer_line_item->Quantity = $item_quantity;

        $data_request_transfer_line[] = (array)$data_request_transfer_line_item;

    }

    //add fee ship
    $ship_fee = $order->get_shipping_total();
    //send fee ship full price
    if($ship_fee > 0) {
        $line_no++;
        $data_request_transaction_item->Location_Code = $location_code21;
        $data_request_transaction_item->Receipt_No_ = $order_no;
        $data_request_transaction_item->Transaction_No_ = $order_no;
        $data_request_transaction_item->LineNo = $line_default + $line_no;
        $data_request_transaction_item->Item_No_ = $item_no_ship;
        $data_request_transaction_item->SerialNo = '';
        $data_request_transaction_item->Variant_Code = '000';
        $data_request_transaction_item->Trans_Date = date('Y-m-d') . ' ' . date('H:i:s.v');
        $data_request_transaction_item->Quantity = -1;
        $data_request_transaction_item->UnitPrice = $ship_fee;
        $data_request_transaction_item->TotalPrice = $ship_fee;
        $data_request_transaction_item->DiscountRate = 0;
        $data_request_transaction_item->DiscountAmount = 0;
        $data_request_transaction_item->Disc = 0;
        $data_request_transaction_item->TotalAmt = $ship_fee;
        $data_request_transaction_item->Member_Card_No_ = $member_card_guest;
        $data_request_transaction_item->Offer_Online_ID = '';
        $data_request_transaction_item->CouponCode = '';
        $data_request_transaction_item->CouponNo = '';
        $data_request_transaction_item->Value_Type = '';
        $data_request_transaction_item->Category_Online_ID = [];

        $data_request_transaction[] = (array)$data_request_transaction_item;
    }


    //add discout cart
    $order_discounts = $wpdb->get_row( $wpdb->prepare( "SELECT oid.*, r.id AS rule_id, r.discount_type AS rule_discount_type, r.cart_adjustments AS rule_cart_adjustments FROM ".$wpdb->prefix."wdr_order_item_discounts oid INNER JOIN ".$wpdb->prefix."wdr_rules r ON oid.rule_id = r.id WHERE oid.item_id = 0 AND oid.order_id = " . $order->get_id() ) );
    if($order_discounts && $order_discounts->rule_discount_type == 'wdr_cart_discount') {
        $Offer_Online_ID = $order_discounts->rule_id;
        $order_discounts->rule_cart_adjustments = json_decode($order_discounts->rule_cart_adjustments);

        if($order_discounts->rule_cart_adjustments->type == 'percentage') {

            $Value_Type = 1;
            $DiscountRate = $order_discounts->rule_cart_adjustments->value;
            $UnitPrice = $TotalPrice = $order->get_subtotal();
            $Disc = $UnitPrice*$DiscountRate/100;
            $DiscountAmount = "";
            $TotalAmt = $TotalPrice - $Disc;

        } else {
            $Value_Type = 2;
            $DiscountRate = "";
            $Disc = "";
            $UnitPrice = "";
            $TotalPrice = "";
            $DiscountAmount = 0;
            $TotalAmt = "";
            if($order_discounts->rule_cart_adjustments->type == 'flat_in_subtotal') {
                $Value_Type = 2;
                $DiscountRate = "";
                $Disc = "";
                $UnitPrice = $TotalPrice = $order->get_subtotal();
                $DiscountAmount = $order_discounts->rule_cart_adjustments->value;
                $TotalAmt = $UnitPrice - $DiscountAmount;
            }
//                    elseif($order_discounts->rule_cart_adjustments->type == '000') {}
        }

        $line_no++;
        $data_request_transaction_item->Location_Code = $location_code21;
        $data_request_transaction_item->Receipt_No_ = $order_no;
        $data_request_transaction_item->Transaction_No_ = $order_no;
        $data_request_transaction_item->LineNo = $line_default + $line_no;
        $data_request_transaction_item->Item_No_ = $item_no_ship;
        $data_request_transaction_item->SerialNo = '';
        $data_request_transaction_item->Variant_Code = '';
        $data_request_transaction_item->Trans_Date = date('Y-m-d') . ' ' . date('H:i:s.v');
        $data_request_transaction_item->Quantity = '';
        $data_request_transaction_item->UnitPrice = $UnitPrice;
        $data_request_transaction_item->TotalPrice = $TotalPrice;
        $data_request_transaction_item->DiscountRate = $DiscountRate;
        $data_request_transaction_item->DiscountAmount = $DiscountAmount;
        $data_request_transaction_item->Disc = $Disc;
        $data_request_transaction_item->TotalAmt = $TotalAmt;
        $data_request_transaction_item->Member_Card_No_ = $member_card_guest;
        $data_request_transaction_item->Offer_Online_ID = $Offer_Online_ID;
        $data_request_transaction_item->CouponCode = '';
        $data_request_transaction_item->CouponNo = '';
        $data_request_transaction_item->Value_Type = $Value_Type;
        $data_request_transaction_item->Category_Online_ID = [];

        $data_request_transaction[]  = (array)$data_request_transaction_item;

    }


    $flag_payment = false;
    $flag_transaction = false;

    $response_ls_payment = $ls_api_transaction->post_payment_styleoutlet((array)$data_request_payment);

    if( isset($response_ls_payment->Responcode) && $response_ls_payment->Responcode == 200) $flag_payment = true;

    $response_ls_transaction = $ls_api_transaction->post_transaction_styleoutlet($data_request_transaction);

    if( isset($response_ls_transaction->Responcode) && $response_ls_transaction->Responcode == 200) $flag_transaction = true;

    if($order->get_to_no() == ''){
        //===========================================================
        //===========================================================
        //post_create_transfer_order
        //===========================================================
        //===========================================================

        $data_transfer_order = array(
            'Vietnamese_Description'    => 'Đơn hàng online StyleOutlet',
            'Store_to'                  =>  $location_code2,
            'Store_from'                =>  $location_code,
            'Order_Date'                =>  date('Y-m-d') . ' ' . date('H:i:s.v'),
            'TOLines'                   =>  $data_request_transfer_line
        );

        $rep_transfer_order = $ls_api_ls_to->create_transfer_order($data_transfer_order);
        if(!isset($rep_transfer_order->Code) || $rep_transfer_order->Code != '200'){
            write_log($rep_transfer_order);
        }else{
            write_log($rep_transfer_order);
            $order->update_to_no($rep_transfer_order->ListData->No);
            $order->update_data_transfer_order(json_encode($rep_transfer_order->ListData));
        }

        //===========================================================
        //===========================================================
        //end_create_transfer_order
        //===========================================================
        //===========================================================
    }

    if ($flag_payment && $flag_transaction)
    {
        $order->set_log('success','post_ls_auto','Thành công');
        $order->set_ls_status();
        return true;
    }
    elseif ($flag_payment && $order->get_ls_status() === 'detail')
    {
        $order->set_log('success','post_ls_auto','Thành công');
        $order->set_ls_status();
        return false;
    }
    elseif ($flag_transaction && $order->get_ls_status() === 'header')
    {
        $order->set_log('success','post_ls_auto','Thành công');
        $order->set_ls_status();
        return false;
    }
    else{
        if (!$flag_payment && !$flag_transaction) {
            $order->set_log(
                'danger',
                'post_ls',
                'Không thể post header và detail. Header response:' .
                json_encode($response_ls_payment) . ' - Detail response: ' .
                json_encode($response_ls_transaction));
            return false;
        }
        if (!$flag_payment && $flag_transaction)
        {
            $order->set_ls_status('detail');
            $order->set_log(
                'danger',
                'post_ls',
                'Post detail thành công, header post lỗi. Header response:' . json_encode($response_ls_payment));
            return false;
        }
        if ($flag_payment && !$flag_transaction) {
            $order->set_ls_status('header');
            $order->set_log(
                'danger',
                'post_ls',
                'Post header thành công, detail post lỗi. Detail response: ' . json_encode($response_ls_transaction));
            $order->set_ls_status('no');
            return false;
        }
    }

    return false;

}

function order_send_one_ls(){

}

