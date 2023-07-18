<?php

add_filter('woocommerce_checkout_posted_data','checkout_save_change_field');

function checkout_save_change_field($data){

    write_log($data);

    return $data;

}