<?php

global $wpdb;

?>

<div class="card" id="card_task_scheduler">
    <div class="card-header">
        <h3>Task scheduler</h3>
    </div>
    <div class="card-body">
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
    </div>
    <!-- /.card-body -->
</div>