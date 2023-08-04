<?php

global $wpdb;

$item_in_page = get_option('admin_dashboard_item_in_page') ?? '';

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
            <label for="item_in_page">Item in page</label>
            <select id="item_in_page" class="form-control select2 select2-hidden-accessible" style="width: 100%;" data-select2-id="2" tabindex="-1" aria-hidden="true">
                <?php foreach ($item_in_page_arg as $value): ?>
                    <option <?php echo $item_in_page == $value ? 'selected': '' ?> value="<?php echo $value?>"><?php echo $value?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="item_in_page">Footer print shipment</label>
            <textarea id="footer_print_shipment">
                <?php echo get_option('admin_dashboard_footer_print_shipment')?>
            </textarea>
        </div>
    </div>
    <!-- /.card-body -->

    <div class="card-footer">
        <button type="button" onclick="save_admin_dashboard_setting()" class="btn btn-primary">Save change</button>
    </div>
</div>