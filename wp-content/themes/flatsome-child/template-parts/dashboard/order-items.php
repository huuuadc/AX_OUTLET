<?php

defined( 'ABSPATH' ) || exit;

if ( ! $order ) {
    return;
}

?>

<table class="table table-striped">
    <thead>
    <tr>
        <th>STT</th>
        <th>Sản phẩm</th>
        <th>Mã sản phẩm</th>
        <th>Số lượng</th>
        <th class="text-right">Đơn giá</th>
        <th class="text-right">% Giảm giá</th>
        <th class="text-right">Tạm tính</th>
    </tr>
    </thead>
    <tbody>
    <?php $count= 0; foreach ($order->get_items() as $item_key => $item ): $count++ ?>
        <?php $product =  $item['variation_id'] != 0 ? wc_get_product($item['variation_id']) : wc_get_product($item->get_product_id()) ?>
        <?php
        $full_price = $product->get_regular_price('value') ?? 1;
        $total_full_price = (int)($product->get_regular_price('value')*$item->get_quantity())?>
        <tr>
            <td><?php echo $count?></td>
            <td><?php echo $item->get_name() ?></td>
            <td><?php echo $product->get_sku()  ?></td>
            <td><?php echo $item->get_quantity() ?></td>
            <td class="text-right"><?php echo format_number_default( $product->get_regular_price()) ?> đ</td>
            <td class="text-right"><?php echo format_number_default( 100 * (1 - $order->get_line_subtotal($item,true)/$total_full_price)) ?> %</td>
            <td class="text-right"><?php echo format_number_default($order->get_line_subtotal($item,true)) ?> đ</td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
