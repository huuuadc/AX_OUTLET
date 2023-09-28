<?php

use \OMS\OMS_TO;

defined( 'ABSPATH' ) || exit;

if ( ! $args || !$args['id'] ) {
    return;
}

$transfer_order = new OMS_TO($args['id']);
$transfer_order_items = $transfer_order->get_items();

?>

<div id="card_table_line" class="card">
    <div class="card-header">
        Danh sách sản phẩm phiếu <strong>#<?php echo $transfer_order->get_id()?></strong>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped dataTable dtr-inline table_simple_non_btn">
            <thead>
            <tr>
                <th>Mã sản phẩm</th>
                <th>Sku</th>
                <th>Tên sản phẩm</th>
                <th>Số lượng</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($transfer_order_items as $item) :?>
                <tr>
                    <td><?php echo $item['product_id'] ?></td>
                    <td><?php echo 'sku' ?></td>
                    <td><?php echo $item->get_name() ?></td>
                    <td><?php echo $item->get_quantity() ?></td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <div class="card-footer"></div>
</div>