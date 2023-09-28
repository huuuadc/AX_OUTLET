<?php

use \OMS\OMS_TO;

defined( 'ABSPATH' ) || exit;

if ( ! $args || !$args['ids'] ) {
    return;
}

$transfer_ids = $args['ids'];

?>

<div class="card">
    <div class="card-header">
        Phiếu điều chuyển hàng
        <div class="card-tools">
            <a type="button" class="btn btn-primary" onclick="transfer_order_add_new()" href="javascript:void(0)">Thêm phiếu</a>
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped dataTable dtr-inline table_simple_non_btn">
            <thead>
            <tr>
                <th>Mã phiếu</th>
                <th>Tên phiếu</th>
                <th>Ngày tạo</th>
                <th>Người tạo</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($transfer_ids as $id) :?>
            <?php $transfer_order = new OMS_TO($id) ?>
                <tr id="transfer_order_<?php echo $id?>"  onclick="change_transfer_order('<?php echo $id ?>')" >
                    <td>#<?php echo $transfer_order->get_id() ?></td>
                    <td><?php echo $transfer_order->get_title() ?></td>
                    <td><?php echo $transfer_order->get_date_created() ?></td>
                    <td><?php echo $transfer_order->get_customer_id() ?></td>
                    <td><?php echo $transfer_order->get_status() ?></td>
                    <td><a href="javasrcipt:void(0)"><span class="badge badge-danger">Hủy phiếu</span></a></td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <div class="card-footer"></div>
</div>