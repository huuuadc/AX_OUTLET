<?php

use OMS\EXPORT\OMS_EXPORT;

//check_permission admin dashboard inventory
if (!current_user_can('admin_dashboard_stock')):
    user_permission_failed_content();
else:

$export = new OMS_EXPORT();

$file_name = '';

if (isset($_GET['export']) && $_GET['export'] = 'inventory') {

    $range_date = isset($_GET['range_date']) ?  convert_string_to_range_date($_GET['range_date']) : convert_string_to_range_date_default(6);

    $start_date = $range_date['start_date'];
    $end_date = $range_date['end_date'];

    $file_name = $export->inventory_export($start_date,$end_date);

}

if (isset($_GET['delete']) && file_exists($export->BASEDIR.$export->INVENTORY_DIR.$_GET['delete'])){
    unlink($export->BASEDIR.$export->INVENTORY_DIR.$_GET['delete']);
    echo "";
}

?>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Báo cáo tồn kho</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/admin-dashboard">Dashboard</a></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<div id="card_report" class="card">
    <div class="card-header">Báo cáo tồn kho</div>
    <div class="card-footer">
        <a id="post_inventory" href="/admin-dashboard/inventory-report/?export=inventory"><button class="btn btn-primary">Xuất tồn kho hiện tại</button></a>
    </div>
    <div class="card-body">
        <?php $export->export_show($export->INVENTORY_DIR,$file_name);?>
    </div>
</div>

    <div class="row">
        <div class="col-12">
                    <?php wc_get_template('template-parts/dashboard/components/card-table-product_price.php'); ?>
        </div>
    </div>

<?php endif;
