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
        Danh sách sản phẩm phiếu
        <strong>#<?php echo $transfer_order->get_id()?></strong>
        <span class="badge <?php echo $transfer_order->get_status_class_name()?>">
            <?php echo $transfer_order->get_status_title() ?>
        </span>
        <div class="card-tools">
            <a type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-import-product"
               href="javascript:void(0)">Import sản phẩm</a>
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped dataTable dtr-inline table_simple_non_btn table_simple_non_line">
            <thead>
            <tr>
                <th>Mã sản phẩm</th>
                <th>Sku</th>
                <th>Tên sản phẩm</th>
                <th>Số lượng</th>
                <th>Thao tác</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($transfer_order_items as $item) :?>
                <?php $product =  $item['variation_id'] != 0 ?
                    wc_get_product($item['variation_id']) :
                    wc_get_product($item->get_product_id()) ?>
                <tr>
                    <td><?php echo $item['product_id'] ?></td>
                    <td><?php echo $product->get_sku() ?></td>
                    <td><?php echo $item->get_name() ?></td>
                    <td><?php echo $item->get_quantity() ?></td>
                    <td>
                        <?php if(!($transfer_order->get_status() == 'confirm'
                            || $transfer_order->get_status() == 'reject'
                            || $transfer_order->get_status() == 'confirm-goods'
                        )) :?>
                        <a type="button" class="btn btn-danger" href="javascript:void(0)"
                           onclick="change_transfer_order('<?php echo $transfer_order->get_id()?>','change_transfer_order_line_delete','<?php echo $item->get_id()?>' )" >
                            Xóa
                        </a>
                        <?php else:?>
                            <span class="badge <?php echo $transfer_order->get_status_class_name()?>">
                                <?php echo $transfer_order->get_status_title() ?>
                            </span>
                        <?php endif;?>
                    </td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <div class="card-footer"></div>
</div>

<div class="modal fade" id="modal-import-product" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Import danh sách sản phẩm</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="importProduct" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                    <label class="custom-file-label" for="importProduct">Choose file</label>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                <button type="button" onclick="transfer_order_import_product(<?php echo $transfer_order->get_id()?>)" class="btn btn-primary">Import</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>