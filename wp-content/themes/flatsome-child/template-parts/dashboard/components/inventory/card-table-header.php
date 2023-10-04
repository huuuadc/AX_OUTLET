<?php

use \OMS\OMS_TO;

defined( 'ABSPATH' ) || exit;

if ( ! $args ) {
    return;
}

$transfer_ids   = $args['ids'];
$new_id         = $args['new_id'] ?? '0';
$count          =   0;

?>

<div id="card_table_header" class="card">
    <div class="card-header">
        Phiếu điều chuyển hàng
        <div class="card-tools">
            <a type="button" class="btn btn-primary" onclick="transfer_order_add_new()" href="javascript:void(0)">Thêm phiếu mới</a>
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped dataTable dtr-inline table_simple_non_btn">
            <thead>
            <tr>
                <th>STT</th>
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
                <tr <?php if($id == $new_id)
                    echo 'class="bg-info bg-gradient"' ?>
                        id="transfer_order_<?php echo $id?>"
                        onclick="change_transfer_order('<?php echo $id ?>','change_transfer_order')" >
                    <td><?php echo ++$count ?></td>
                    <td>#<?php echo $transfer_order->get_id(); ?></td>
                    <td><?php echo $transfer_order->get_title() ?></td>
                    <td><?php echo wp_date( get_date_format(), strtotime($transfer_order->get_date_created())) ?></td>
                    <td><?php echo $transfer_order->get_billing_first_name() ?></td>
                    <td><span class="badge <?php echo $transfer_order->get_status_class_name()?>">
                            <?php echo $transfer_order->get_status_title() ?></span></td>
                    <td>
                        <?php if(!($transfer_order->get_status() === 'confirm' || $transfer_order->get_status() === 'reject'))  : ?>
                        <a type="button" class="btn btn-danger" href="javascript:void(0)"
                           onclick="change_transfer_order('<?php echo $id ?>', 'change_transfer_order_reject')">
                            Hủy phiếu
                        </a>
                            <a type="button" class="btn btn-primary" href="javascript:void(0)"
                               onclick="change_transfer_order('<?php echo $id ?>','change_transfer_order_confirm')">
                                Xác nhận
                            </a>
                        <?php endif;?>
                        <?php if($transfer_order->get_status() == 'confirm')  : ?>
                        <a type="button" class="btn btn-warning" href="javascript:void(0)"
                           onclick="change_transfer_order('<?php echo $id ?>','change_transfer_order_restock')">
                            Trả lại tồn kho
                        </a>
                        <?php endif;?>
                    </td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <div class="card-footer"></div>
</div>