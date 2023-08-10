<?php

use OMS\EXPORT\OMS_EXPORT;

$export = new OMS_EXPORT();

if (isset($_GET['export'])) {

    $order_status = $_GET['filter_status'] ?? 'any';

    $order_range_date = '';

    if (isset($_GET['filter_range_date'])){
        $order_range_date = $_GET['filter_range_date'];
        $order_range_date_arg = explode(' - ',$order_range_date);

        $filter_start_date = str_replace('/', '-', $order_range_date_arg['0']);
        $filter_start_date = date('Y-m-d', strtotime($filter_start_date.' - 1 days'));
        $filter_end_date = str_replace('/', '-', $order_range_date_arg['1']);
        $filter_end_date = date('Y-m-d', strtotime($filter_end_date.' + 1 days'));
    } else {
        $filter_start_date = date('Y-m-d',( strtotime( date('Y-m-d').'- 6 days')));
        $filter_end_date = date('Y-m-d');
        $order_range_date = $filter_start_date . ' - ' . $filter_end_date;
        $filter_start_date = date('Y-m-d',( strtotime( date('Y-m-d').'- 7 days')));
        $filter_end_date = date('Y-m-d',(strtotime($filter_end_date.'+ 1 days')));
    }

    if ($_GET['export'] == 'orders'){

        $export->order_export($order_status,$filter_start_date,$filter_end_date);

    }
    if ($_GET['export'] == 'order-detail'){

        $export->order_detail_export($order_status,$filter_start_date,$filter_end_date);

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
        <?php $export->export_show();?>
    </div>
</div>
