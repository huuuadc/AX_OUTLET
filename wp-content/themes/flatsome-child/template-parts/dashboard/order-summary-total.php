<?php

defined( 'ABSPATH' ) || exit;

if ( ! $order ) {
    return;
}

?>

<div class="table-responsive">
    <table class="table">
        <tr>
            <th>Thành tiền:</th>
            <td class="text-right"><?php echo format_number_default($order->get_subtotal()) ?> đ</td>
        </tr>
        <tr>
            <th>Mã coupon:</th>
            <td class="text-right"><?php
                foreach( $order->get_coupons() as $order_item_coupon ) {
                    echo '<span class="badge badge-success"> ' . $order_item_coupon->get_code() . '<span><dr>';
                }
                ?></td>
        </tr>
        <tr>
            <th>Giảm giá coupon:</th>
            <td class="text-right"> - <?php echo  format_number_default($order->get_total_discount() )?> đ</td>
        </tr>
        <tr>
            <th>Giao hàng:</th>
            <td class="text-right"><?php echo format_number_default($order->get_shipping_total())?> đ</td>
        </tr>
        <tr>
            <th>Tổng tiền:</th>
            <td class="text-right"><?php echo format_number_default($order->get_total() )?> đ</td>
        </tr>
    </table>
</div>