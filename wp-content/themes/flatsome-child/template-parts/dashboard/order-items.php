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
        <?php $product = wc_get_product($item['variation_id']) ?>
        <tr><td><?php echo $count?></td>
            <td><?php echo $item->get_name() ?></td>
            <td><?php echo $product->get_sku()  ?></td>
            <td><?php echo $item->get_quantity() ?></td>
            <td class="text-right"><?php echo number_format( $product->get_regular_price(), '0',',','.') ?> đ</td>
            <td class="text-right"><?php echo number_format( 100 * (1 - $order->get_line_subtotal($item,true)/(int)($product->get_regular_price('value')*$item->get_quantity())), '0',',','.') ?> %</td>
            <td class="text-right"><?php echo number_format($order->get_line_subtotal($item,true), '0',',','.') ?> đ</td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
