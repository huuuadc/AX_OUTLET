<?php

use OMS\EXPORT\OMS_EXPORT;

$export = new OMS_EXPORT();

if (isset($_GET['export'])) {

    $order_status = $_GET['filter_status'] ?? 'any';

    $order_range_date = '';

    $range_date = isset($_GET['range_date']) ?  convert_string_to_range_date($_GET['range_date']) : convert_string_to_range_date_default(6);
    $start_date = $range_date['start_date'];
    $end_date = $range_date['end_date'];

    if ($_GET['export'] == 'orders'){

        $export->order_export($order_status,$start_date,$end_date);

    }
    if ($_GET['export'] == 'order-detail'){

        $export->order_detail_export($order_status,$start_date,$end_date);

    }

}

if (isset($_GET['delete']) && file_exists($export->BASEDIR.$_GET['delete'])){
    unlink($export->BASEDIR.$_GET['delete']);
    echo "";
}

?>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Báo cáo đơn hàng</h1>
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
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lọc đơn hàng</h3>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <label>Theo ngày</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text">
                            <i class="far fa-calendar-alt"></i>
                          </span>
                        </div>
                        <input type="text" class="form-control float-right" id="reservation">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="card_report" class="card">
    <div class="card-header">Báo cáo tồn kho</div>
    <div class="card-footer">
        <a href="./?export=orders"><button class="btn btn-primary">Xuất đơn hàng tổng</button></a>
        <a href="./?export=order-detail"><button class="btn btn-primary">Xuất đơn hàng chi tiết</button></a>
        <button class="btn btn-primary">Xuất tồn kho hiện tại</button>
    </div>
    <div class="card-body">
        <?php $export->export_show($export->ORDER_DIR);?>
    </div>
</div>
