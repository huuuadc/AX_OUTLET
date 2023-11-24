<?php

global $wpdb;

?>

<div class="card" id="card_task_scheduler">
    <div class="card-header">
        <h3>Task scheduler</h3>
    </div>
    <div class="card-body">
        <div class="form-group">
            <button type="button" onclick="sync_e_commerce_platform('tiktok')" class="btn btn-primary">Đồng bộ sàn</button>
        </div>

        <hr>

        <label for="product_form">Đồng bộ lại đơn hàng từ sàn</label>
        <div class="form-group">
            <label for="order_platform_id">Danh sách đơn hàng (Cách nhau bằng dấu ",")</label>
            <input class="form-control" type="text" name="order_platform_id"/>
        </div>
        <div class="form-group">
            <button type="submit" onclick="sync_e_commerce_platform('order_platform_ids')" class="btn btn-primary">Đồng bộ</button>
        </div>

        <hr>

        <div class="form-group">
            <table class="table no-border">
                <tr>
                    <td class="w-50"><label>Số lượng cuối</label></td>
                    <td><input class="form-control" type="number" name="last_piece_qty" value="<?php echo get_option("admin_dashboard_last_piece_qty")?>" min="1" max="99"/></td>
                </tr>
            </table>
        </div>
        <div class="form-group">
            <button type="button" onclick="run_product_shop_by('last_piece')" class="btn btn-primary">Cơ hội cuối</button>
        </div>
        <hr>
        <div class="form-group">
            <table class="table no-border">
                <tr>
                    <td class="w-50"><label>Phần trăm giảm giá (Trong khoản 1 - 99)</label></td>
                    <td><input class="form-control" type="number" name="present_discount" min="1" max="99"/></td>
                </tr>
                <tr>
                    <td class="w-50"><label>Xóa ra</label></td>
                    <td><input type="checkbox" name="checkbox_remove"/></td>
                </tr>
            </table>
        </div>
        <div class="form-group">
            <button type="submit" onclick="run_product_shop_by('sales_special')" class="btn btn-primary">Sales độc quyền</button>
        </div>
        <hr>
        <div class="form-group">
            <button type="submit" onclick="run_product_shop_by('update_sale_price')" class="btn btn-primary">Cập nhật giảm giá</button>
        </div>
        <hr>
        <div class="form-group">
            <label for="product_skus">Danh sách các sku ( Cách nhau bằng dấu ",")</label>
            <input class="form-control" type="text" name="product_skus"/>
        </div>
        <div class="form-group">
            <button type="submit" onclick="run_product_shop_by('update_check_stock_manager')" class="btn btn-primary">Cập nhật check manager stock</button>
        </div>
    </div>
    <!-- /.card-body -->
</div>