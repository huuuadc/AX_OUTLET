<?php


function checkout_check_stock_ls(){
    $is_check_stock = get_option('admin_dashboard_is_check_stock') ?? '';
    if ($is_check_stock == 'checked'){
        return '';
    }
    return '';
}