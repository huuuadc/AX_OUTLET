<?php

use OMS\ADDRESS;
use OMS\LS_API;
use function OMS\ls_transactions_request;
use function OMS\ls_payment_request;

add_action( 'wp_ajax_post_invoice_ls_retail', 'post_invoice_ls_retail' );
add_action( 'wp_ajax_nopriv_post_invoice_ls_retail', 'post_invoice_ls_retail' );
function post_invoice_ls_retail(){

    //Check have action and payload_action
    //payload action variant status ajax post
    if(!isset($_POST['action']) || !isset($_POST['payload_action'])) {
        echo json_encode(array(
            'status' => false,
            'messenger' => 'Không tìm thấy hành động được gửi',
            'data' => []
        ));
        exit;
    }

    $payload_action = $_POST['payload_action'];

    //Check have post order_id
    if (!isset($_POST['order_id'])){
        echo json_encode(array(
            'status' => false,
            'messenger' => 'Số đơn hàng không có',
            'data' => []
        ));
        exit;
    }

    $order_id = $_POST['order_id'];
    $commit_note = $_POST['commit_note'];

    //Check have order in store
    if (!wc_get_order($order_id)){
        echo json_encode(array(
            'status' => false,
            'messenger' => 'Không tồn tại đơn hàng: ' . $order_id,
            'data' => []
        ));
        exit();
    }

    //
    //
    //create order from order id
    //
    //
    global $wpdb;
    $order          = new OMS_ORDER($order_id);
    $ls_api         = new LS_API();
    //get old status
    $old_status     = $order->get_status('value');


    if ($old_status != 'request'){
        $order->set_log('danger',$payload_action,$commit_note);
        echo response(false,'Trạng thái không cho thực hiện thao tác',[]);
        exit();
    }
    //Check stock với ls
    if (!$order->check_stock_ls()){
        echo response(false,'Không còn tồn trên ls retail',[]);
        exit;
    }

    //
    //
    // Action payment order
    //
    //

    if ($_POST['payload_action'] === 'post_invoice_ls_retail'){

        //===========================================================
        //===========================================================
        //transaction header
        //===========================================================
        //===========================================================

        $location_code = get_option('wc_settings_tab_ls_location_code');
        if (!$location_code) {
          echo response(false,'Chưa cài đặt mã kho',[]);
          exit;
        }
        $item_no_ship = get_option('admin_dashboard_item_fee_ship');
        if (!$item_no_ship) {
          echo response(false,'Chưa cài đặt mã item phí vận chuyển',[]);
          exit;
        }
        $member_card_guest = get_option('admin_dashboard_member_card_guest');
        if (!$member_card_guest) {
          echo response(false,'Chưa cài đặt mã thẻ khách lẽ',[]);
          exit;
        }
        $order_number = $order->get_id();
        $order_no = get_option('web_company_code') ? get_option('web_company_code').$order_number : 'OL'.$order_number;

        $ls_method_type = $order->get_method_type_ls();

        $data_request_payment = (object) ls_payment_request();

        $data_request_payment->Location_Code = $location_code;
        $data_request_payment->Transaction_No_ = $order_no;
        $data_request_payment->LineNo = 30000;
        $data_request_payment->Receipt_No_ = $order_no;
        $data_request_payment->Tender_Type = $ls_method_type['tender_type'];
        $data_request_payment->Amount_Tendered = $order->get_total();
        $data_request_payment->Amount_in_Currency = $order->get_total();
        $data_request_payment->Date = date('Y-m-d') . ' 00:00:00.000';
        $data_request_payment->Time = date('Y-m-d') . ' ' . date('H:i:s.v');
        $data_request_payment->Quantity = 1;
        $data_request_payment->VAT_Buyer_Name = $order->get_formatted_billing_full_name();
        $data_request_payment->VAT_Company_Name = $order->get_billing_company();
        $data_request_payment->VAT_Tax_Code = '';
        $data_request_payment->VAT_Address = $order->get_billing_address_1() . ', ' . $order->get_billing_address_full();
        $data_request_payment->VAT_Payment_Method = $ls_method_type['vat_payment_method'];
        $data_request_payment->VAT_Bank_Account = '';
        $data_request_payment->Member_Phone = $order->get_billing_phone();
        $data_request_payment->THENH = $order->get_number_card_payment();
        $data_request_payment->Cash = $order->get_total();


        //===========================================================
        //===========================================================
        //transaction detail
        //===========================================================
        //===========================================================
        $data_request_transaction_item = (object) ls_transactions_request();

        $data_request_transaction = [];

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
                'Location_Code'         =>          $location_code,
                'Receipt_No_'           =>          $order_no,
                'Transaction_No_'       =>          $order_no,
                'LineNo'                =>          $line_default + $line_no,
                'LineNo_Online'         =>          $item->get_id(),
                'Item_No_'              =>          get_post_meta($product_id, 'offline_id', true),
                'SerialNo'              =>          '',
                'Variant_Code'          =>          $item->get_meta('pa_size'),
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

        }

        //add fee ship

        $ship_fee = $order->get_shipping_total();
        $qty_simple = 1000;

        $qty_ship_fee = intdiv($ship_fee,$qty_simple) ?? 0;
        if(fmod($ship_fee,$qty_simple) > $qty_simple/2){
            $qty_ship_fee = $qty_ship_fee + 1 ;
        }
        $line_no++;
        $data_request_transaction_item->Location_Code = $location_code;
        $data_request_transaction_item->Receipt_No_ = $order_no;
        $data_request_transaction_item->Transaction_No_ = $order_no;
        $data_request_transaction_item->LineNo = $line_default + $line_no;
        $data_request_transaction_item->Item_No_ = $item_no_ship;
        $data_request_transaction_item->SerialNo = '';
        $data_request_transaction_item->Variant_Code = '000';
        $data_request_transaction_item->Trans_Date = date('Y-m-d') . ' ' . date('H:i:s.v');
        $data_request_transaction_item->Quantity = -$qty_ship_fee;
        $data_request_transaction_item->UnitPrice =  $qty_simple;
        $data_request_transaction_item->TotalPrice = $qty_ship_fee* $qty_simple;
        $data_request_transaction_item->DiscountRate = 0;
        $data_request_transaction_item->DiscountAmount = 0;
        $data_request_transaction_item->Disc = 0;
        $data_request_transaction_item->TotalAmt = $qty_ship_fee* $qty_simple;
        $data_request_transaction_item->Member_Card_No_ = $member_card_guest;
        $data_request_transaction_item->Offer_Online_ID = '';
        $data_request_transaction_item->CouponCode = '';
        $data_request_transaction_item->CouponNo = '';
        $data_request_transaction_item->Value_Type = '';
        $data_request_transaction_item->Category_Online_ID = [];

        $data_request_transaction[]  = (array)$data_request_transaction_item;

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
                elseif($order_discounts->rule_cart_adjustments->type == '000') {
                }
            }

                $line_no++;
                $data_request_transaction_item->Location_Code = $location_code;
                $data_request_transaction_item->Receipt_No_ = $order_no;
                $data_request_transaction_item->Transaction_No_ = $order_no;
                $data_request_transaction_item->LineNo = $line_default + $line_no;
                $data_request_transaction_item->Item_No_ = get_option('admin_dashboard_item_fee_ship');
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

        $response_ls_payment = $ls_api->post_payment_outlet((array)$data_request_payment);
//        write_log($data_request_payment);
//        $response_ls_payment = '';

        if( isset($response_ls_payment->Responcode) && $response_ls_payment->Responcode == 200) $flag_payment = true;

        $response_ls_transaction = $ls_api->post_transaction_outlet($data_request_transaction);
//        write_log($data_request_transaction);
//        $response_ls_transaction = '';

        if( isset($response_ls_transaction->Responcode) && $response_ls_transaction->Responcode == 200) $flag_transaction = true;

        if ($flag_payment && $flag_transaction){
            $order->set_log('success','post_ls','Thành công');
            echo response(true,'Đã post ls retail',[]);
        }else{
            if (!$flag_payment && !$flag_transaction) $order->set_log(
                'danger',
                'post_ls',
                'Không thể post header và detail. Header response:' . json_encode($response_ls_payment).' - Detail response: ' . json_encode($response_ls_transaction));
            if (!$flag_payment && $flag_transaction) $order->set_log(
                'danger',
                'post_ls',
                'Post detail thành công, header post lỗi. Header response:' . json_encode($response_ls_payment));
            if ($flag_payment && !$flag_transaction) $order->set_log(
                'danger',
                'post_ls',
                'Post header thành công, detail post lỗi. Detail response: ' . json_encode($response_ls_transaction));
            echo response(false,'Post LS không thành công xin kiểm tra lại dữ liệu đơn hàng',[]);
        }

        exit;

    }

}