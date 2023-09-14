<?php

global $wpdb;

//check_permission admin dashboard order
if (!current_user_can('admin_dashboard_setting')):
    user_permission_failed_content();
else:

$item_in_page = get_option('admin_dashboard_item_in_page') ?? '';
$item_fee_ship = get_option('admin_dashboard_item_fee_ship') ?? '';
$member_card_guest = get_option('admin_dashboard_member_card_guest') ?? '';

$item_in_page_arg = [
        '10'    => 10,
        '20'    => 20,
        '50'    => 50,
        '100'   => 100,
        '200'   => 200,
        '500'   => 500,
        '1000'  => 1000
];

?>

<div class="card" id="card_admin_dashboard">
    <div class="card-header">
        <h3>Admin dashboard setting</h3>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label for="member_card_guest">Mã thẻ thành viên khách lẽ</label>
            <input type="text" name="member_card_guest" class="form-control" id="member_card_guest" placeholder="Nhập mã số thẻ khách lẽ" value="<?php echo $member_card_guest?>"/>
        </div>
        <div class="form-group">
            <label for="item_fee_ship">Item phí vận chuyển</label>
            <input type="text" name="item_fee_ship" class="form-control" id="item_fee_ship" placeholder="Nhập mã phí vận chuyển" value="<?php echo $item_fee_ship?>"/>
        </div>
        <div class="form-group">
            <label for="item_in_page">Item in page</label>
            <select id="item_in_page" class="form-control" style="width: 100%;" >
                <?php foreach ($item_in_page_arg as $value): ?>
                    <option <?php echo $item_in_page == $value ? 'selected': '' ?> value="<?php echo $value?>"><?php echo $value?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="footer_print_shipment">Footer print shipment</label>
            <textarea id="footer_print_shipment">
                <?php echo get_option('admin_dashboard_footer_print_shipment')?>
            </textarea>
        </div>
        <div class="form-group">
            <label for="product_return_policy">Product return policy</label>
            <textarea id="product_return_policy">
                <?php echo get_option('admin_dashboard_product_return_policy')?>
            </textarea>
        </div>
    </div>
    <!-- /.card-body -->

    <div class="card-footer">
        <button type="button" onclick="save_admin_dashboard_setting()" class="btn btn-primary">Save change</button>
    </div>
</div>

<?php endif;