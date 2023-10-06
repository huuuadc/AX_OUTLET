<?php

add_filter('woocommerce_cart_totals_coupon_label', 'change_coupon_label',10,2);

function change_coupon_label($label,$coupon){

    if ($coupon->get_description() != ''){
        $label = str_replace($coupon->get_code(),$coupon->get_description(),$label);
    }

    return $label;
}
