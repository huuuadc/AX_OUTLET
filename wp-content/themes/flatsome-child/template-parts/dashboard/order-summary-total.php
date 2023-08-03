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
            <td class="text-right"><?php echo number_format($order->get_subtotal(), '0', ',', '.'); ?> đ</td>
        </tr>
        <tr>
            <th>Giảm giá coupon:</th>
            <td class="text-right"> - <?php echo  number_format($order->get_total_discount() , '0', ',', '.')?> đ</td>
        </tr>
        <tr>
            <th>Giao hàng:</th>
            <td class="text-right"><?php echo number_format($order->get_shipping_total(), '0', ',', '.')?> đ</td>
        </tr>
        <tr>
            <th>Tổng tiền:</th>
            <td class="text-right"><?php echo number_format($order->get_total() , '0', ',', '.')?> đ</td>
        </tr>
    </table>
</div>