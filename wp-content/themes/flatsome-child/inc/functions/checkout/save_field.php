<?php

add_filter('woocommerce_checkout_posted_data','checkout_check_posted_field');

function checkout_check_posted_field($data){


    if(isset($_POST['is_issue_vat'])){
        if($_POST['vat_company_name'] == '')
            wc_add_notice('<strong>Tên công ty</strong> là mục bắt buộc.','error');
        if($_POST['vat_company_tax_code'] == '')
            wc_add_notice('<strong>Mã số thuế</strong> là mục bắt buộc.','error');
        if($_POST['vat_company_address'] == '')
            wc_add_notice('<strong>Địa chỉ công ty</strong> là mục bắt buộc.','error');
        if($_POST['vat_company_email'] == '')
            wc_add_notice('<strong>Email công ty</strong> là mục bắt buộc.','error');
    }

    return $data;

}

add_action('woocommerce_checkout_update_order_meta','checkout_update_vat_posted_field');

function checkout_update_vat_posted_field($order_id):void{

    $order = new OMS_ORDER($order_id);
    $vat_company_name = $_POST['vat_company_name'] ?? '';
    $vat_company_tax_code = $_POST['vat_company_tax_code'] ?? '';
    $vat_company_address = $_POST['vat_company_address'] ?? '';
    $vat_company_email = $_POST['vat_company_email'] ?? '';

    $order->update_vat_company_name($vat_company_name);
    $order->update_vat_company_tax_code($vat_company_tax_code);
    $order->update_vat_company_address($vat_company_address);
    $order->update_vat_company_email($vat_company_email);

}
