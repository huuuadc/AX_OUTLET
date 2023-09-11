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
    $order = new OMS_ORDER($order_id);

    //get old status
    $old_status = $order->get_status('value');


//    if ($old_status != 'request'){
//        $order->set_log('danger',$payload_action,$commit_note);
//        echo response(false,'Trạng thái không cho thực hiện thao tác',[]);
//        exit();
//    }

    //
    //
    // Action payment order
    //
    //

    if ($_POST['payload_action'] === 'post_invoice_ls_retail'){


        $ls_api = new LS_API();

        $location_code= 'DA0053';
        $ls_method_type = $order->get_method_type_ls();

        $data_request_payment = (object) ls_payment_request();

        $data_request_payment->Location_Code = $location_code;
        $data_request_payment->Transaction_No_ = $order->get_id();
        $data_request_payment->LineNo = 30000;
        $data_request_payment->Receipt_No_ = $order->get_id();
        $data_request_payment->Tender_Type = $ls_method_type['tender_type'];
        $data_request_payment->Amount_Tendered = $order->get_total();
        $data_request_payment->Amount_in_Currency = $order->get_total();
        $data_request_payment->Date = date('Y-m-d') . ' 00:00:00.000';
        $data_request_payment->Time = date('Y-m-d') . ' ' . date('H:i:s.v');
        $data_request_payment->Quantity = 1;
        $data_request_payment->VAT_Buyer_Name = $order->get_formatted_billing_full_name();
        $data_request_payment->VAT_Company_Name = $order->get_billing_company();
        $data_request_payment->VAT_Tax_Code = '';
        $data_request_payment->VAT_Address = $order->get_billing_address_full();
        $data_request_payment->VAT_Payment_Method = $ls_method_type['vat_payment_method'];
        $data_request_payment->VAT_Bank_Account = '';
        $data_request_payment->Member_Phone = $order->get_billing_phone();
        $data_request_payment->THENH = 'THENGANHANG';
        $data_request_payment->Cash = $order->get_total();




        $data_request_transaction_item = (object) ls_transactions_request();


        $data_request_transaction = [];

        $line_no = 0;

        foreach ($order->get_items() as $item) {

            for($i = 0 ; $i < $item->get_quantity(); $i++){

                $line_no++;

                $product  = $item->get_product();

                $data_request_transaction_item->Location_Code = $location_code;
                $data_request_transaction_item->Receipt_No_ = $order->get_id();
                $data_request_transaction_item->Transaction_No_ = $order->get_id();
                $data_request_transaction_item->LineNo = 10000 + $line_no;
                $data_request_transaction_item->Item_No_ = $item->get_id();
                $data_request_transaction_item->SerialNo = 'SN0000000001';
                $data_request_transaction_item->Variant_Code = $item->get_meta('pa_size');
                $data_request_transaction_item->Trans_Date = $order->get_date_created()->date('Y-m-d H:i:s.v');;
                $data_request_transaction_item->Quantity = -1;
                $data_request_transaction_item->UnitPrice = $product->get_regular_price();
                $data_request_transaction_item->TotalPrice = $item->get_subtotal()/$item->get_quantity();
                $data_request_transaction_item->DiscountRate = 0;
                $data_request_transaction_item->DiscountAmount = 0;
                $data_request_transaction_item->Disc = 0;
                $data_request_transaction_item->TotalAmt = 0;
                $data_request_transaction_item->Member_Card_No_ = 'MB11111';
                $data_request_transaction_item->Offer_Online_ID = 'MB11111';
                $data_request_transaction_item->CouponCode = 'MB11111';
                $data_request_transaction_item->CouponNo = 'MB11111';
                $data_request_transaction_item->Value_Type = 'MB11111';
                $data_request_transaction_item->Category_Online_ID = [];
            }

            $data_request_transaction[]  = $data_request_transaction_item;

        }

//        $ls_api->post_payment_outlet($data_request_payment);
//        $ls_api->post_transaction_outlet($data_request_transaction);

        write_log($data_request_transaction);
        write_log($data_request_payment);

        if (false){
//            $order->set_log('success',$payload_action,$commit_note.' -- '. $old_payment_status . ' -> ' . 'Đã thanh toán');
//            $data = [
//                'order_payment_title'=> 'Đã thanh toán',
//                'class' => 'success'
//            ];
//            echo response(true,'Đã cập nhật trạng thái thanh toán',$data);
        }else{
            $order->set_log('danger',$payload_action,$commit_note);
            echo response(false,'Post LS không thành công xin kiểm tra lại dữ liệu đơn hàng',[]);
        }

        exit;

    }

}