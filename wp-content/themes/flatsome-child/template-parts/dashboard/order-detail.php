<?php
use OMS\COMPANY;


//check_permission admin dashboard order
if (!current_user_can('admin_dashboard_order')):
    user_permission_failed_content();
else:
?>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Chi tiết đơn hàng</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<?php

    $order_id =  $_GET['order_id'];

    $status_badge = array(
        'reject' => 'badge-secondary',
        'trash' => 'badge-danger',
        'on-hold' => 'badge-danger',
        'pending' => 'badge-danger',
        'processing' => 'badge-warning',
        'confirm' => 'badge-primary',
        'completed' => 'badge-success',
        'request' => 'badge-info',
        'shipping' => 'badge-info',
        'delivered' => 'badge-success',
        'delivery-failed' => 'badge-danger',
        'cancelled' => 'badge-danger',
        'auto-draft' => 'badge-secondary',
        'confirm-goods' => 'badge-warning',
    );

if (!isset($_GET['order_id']) || !get_post_type($order_id)):
    ?>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="text-center">Không tìm thấy đơn hàng: <?php echo $order_id?></div>
                </div>
            </div>
        </div>
    </section>

<?php
else:
    $order_ax = new OMS_ORDER($order_id);
    $company = new COMPANY();
    ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <!-- Main content -->
                <div class="invoice p-3 mb-3">

                    <!-- title row -->
                    <div class="row">
                        <div class="col-12">
                            <h4>
                                <i class="fas fa-globe"></i> <?php echo $company->get_company_name()?>
                                <small class="float-right">Ngày đặt hàng: <?php echo wp_date( get_date_format(), strtotime($order_ax->get_date_created()))?></small>
                            </h4>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- info row -->
                    <div class="row invoice-info">
                        <div class="col-sm-4 invoice-col">
                            Từ
                            <address>
                                <strong><?php echo $company->get_company_name()?></strong><br>
                                Địa chỉ: <?php echo $company->get_company_address()?><br>
                                <?php echo $company->get_company_ward_name()?>
                                , <?php echo $company->get_company_district_name()?><br>
                                Thành phố: <?php echo $company->get_company_city_name()?><br>
                                Điện thoại: <?php echo $company->get_company_phone()?><br>
                                Email: <?php echo $company->get_company_email()?>
                            </address>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                            Đến
                            <address>
                                <strong><?php echo $order_ax->get_billing_last_name() .' '. $order_ax->get_billing_first_name() ?></strong><br>
                                Địa chỉ: <?php echo $order_ax->get_billing_address_1()?><br>
                                <?php echo $order_ax->get_billing_ward_name()?>
                                , <?php echo $order_ax->get_billing_district_name()?><br>
                                Thành phố: <?php echo $order_ax->get_billing_city_name()?><br>
                                Điện thoại: <?php echo $order_ax->get_billing_phone()?><br>
                                Email: <?php echo $order_ax->get_billing_email()?>
                            </address>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                            <div class="row">
                                <div class="col-sm-4">
                                    Số đơn hàng <br><b> #<?php echo $order_id?></b><br>
                                </div>
                                <!-- /.col -->
                                <div id="card_orders" class="col-sm-4 invoice-col no-print">
                                    Trạng thái <br>
                                    <b id="order_status_<?php echo $order_id?>">
                                    <span class="badge <?php echo $order_ax->get_status_class_name() ?>"><?php echo $order_ax->get_status_title()?>
                                    </span>
                                    </b><br>
                                    <b id="order_payment_status_<?php echo $order_id?>">
                                    <span class="badge <?php echo $order_ax->get_payment_class_name() ?>"><?php echo $order_ax->get_payment_title()?>
                                    </span>
                                    </b>
                                </div>
                                <div id="card_order_type" class="col-sm-4 invoice-col no-print">
                                    Loại đơn hàng <br>
                                    <span class="badge badge-info">
                                        <?php echo $order_ax->get_order_type()?><br>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 pt-3">
                                    Ghi chú của khách hàng:<br>
                                    <b><?php echo $order_ax->get_customer_note()?> </b>
                                </div>
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <!-- Table row -->
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <?php
                            wc_get_template(
                                'template-parts/dashboard/order-items.php',
                                array(
                                    'order' => $order_ax,
                                )
                            );
                            ?>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <div class="row">
                        <!-- accepted payments column -->
                        <div class="col-3">
                            <p class="lead">Phương thức thanh toán:</p>
                            <span><?php echo $order_ax->get_payment_method() . ' - ' . $order_ax->get_payment_method_title()?></span>
                        </div>
                        <!-- /.col -->
                        <!-- accepted payments column -->
                        <div class="col-3">
                            <div class="no-print">
                            <p class="lead">Thông tin giao hàng:</p>
                            Đơn vị giao hàng: <span><?php echo $order_ax->get_shipping_method()?></span><br>
                            Trạng thái giao hàng: <span class="badge badge-info"><?php echo $order_ax->get_meta('shipment_status',true)?></span><br>
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-6">
                            <?php
                                wc_get_template(
                                    'template-parts/dashboard/order-summary-total.php',
                                    array(
                                        'order' => $order_ax,
                                    )
                                );
                            ?>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <!-- this row will not appear when printing -->
                    <div class="row no-print padding10">
                        <div class="col-12">
                            <a href="<?php echo '/admin-dashboard/order-list?order_id='.$order_ax->get_id().'&print=invoice'?>" target="_blank"  rel="noopener noreferrer">
                                <button rel="noopener" target="_blank" class="btn btn-default">
                                    <i class="fas fa-print"></i> In hóa đơn</button></a>
                            <a href="<?php echo '/admin-dashboard/order-list?order_id='.$order_ax->get_id().'&print=shipment'?>" target="_blank"  rel="noopener noreferrer">
                                <button rel="noopener" target="_blank" class="btn btn-default">
                                    <i class="fas fa-print"></i> In phiếu giao hàng</button></a>
                            <?php if(current_user_can('admin_dashboard_order_goods')) {?>
                            <button onclick="send_update_status(<?php echo $order_id?>,'confirm-goods')" type="button" class="btn btn-success float-right">
                                <i class="far fa-calendar-check"></i> Xác nhận hoàn hàng</button><?php }?>
                            <?php if(current_user_can('admin_dashboard_order_payment')) {?>
                            <button onclick="send_update_payment(<?php echo $order_id?>)" type="button" class="btn btn-success float-right" style="margin-right: 5px;">
                                <i class="far fa-calendar-check"></i>Thanh toán</button><?php }?>
                            <?php if(current_user_can('admin_dashboard_order_request')) {?>
                            <button onclick="send_update_status(<?php echo $order_id?>,'request')" type="button" class="btn btn-info float-right"  style="margin-right: 5px;">
                                <i class="fas fa-people-carry"> </i> Gọi giao hàng</button><?php }?>
                            <?php if(current_user_can('admin_dashboard_order_confirm')) {?>
                            <button onclick="send_update_status(<?php echo $order_id?>,'confirm')" type="button" class="btn btn-primary float-right"  style="margin-right: 5px;">
                                <i class="fa fa-check"></i> Xác nhận</button><?php }?>
                            <?php if(current_user_can('admin_dashboard_order_reject')) {?>
                            <button onclick="send_update_status(<?php echo $order_id?>,'reject')" type="button" class="btn btn-secondary float-right"  style="margin-right: 5px;">
                                <i class="fas fa-ban"></i> Từ chối</button><?php }?>
                            <?php if(current_user_can('admin_dashboard_order_cancel')) {?>
                            <button onclick="send_update_status(<?php echo $order_id?>,'cancelled')" type="button" class="btn btn-danger float-right"  style="margin-right: 5px;">
                                <i class="fas fa-times"></i> Hủy đơn</button><?php }?>
                        </div>
                    </div>

                </div>
                <!-- /.invoice -->

                <div class="card no-print">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="timeline">
                                    <!-- timeline time label -->
                                    <div class="time-label">
                                        <span class="bg-primary"><i class="fas fa-history"></i> <?php echo 'Lịch sử đơn hàng'?></span>
                                    </div>
                                    <!-- /.timeline-label -->

                                <?php
                                $order_log = $order_ax->get_meta('order_user_log',true,'value');
                                $order_logs = explode('|' , $order_log);
                                foreach (array_reverse($order_logs) as $value):
                                    $value_log = explode(';', $value);
                                    if (!$value_log[0]) continue;
                                    $status_log = trim( str_replace('order_status_','',$value_log[2]));
                                    ?>
                                    <!-- timeline item -->
                                    <div>
                                        <i class="fas fa-comments bg-<?php echo trim($value_log[3])?>"></i>
                                        <div class="timeline-item">
                                            <span class="time"><i class="fas fa-clock"></i><?php echo $value_log[1]?></span>
                                            <h3 class="timeline-header"><?php echo  $value_log[0]  . ' - '. $status_log . ' - ' .trim($value_log[3])?></h3>

                                            <div class="timeline-body">
                                               <?php echo $value_log[4]?? '' ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                    <!-- END timeline item -->
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="timeline">
                                    <!-- timeline time label -->
                                    <div class="time-label">
                                        <span class="bg-warning"><i class="fas fa-history"></i> <?php echo 'Lịch sử giao hàng'?></span>
                                    </div>
                                    <!-- /.timeline-label -->

                                <?php
                                $shipment_log = $order_ax->get_meta('order_shipment_log',true,'value');
                                $shipment_logs = explode('|' , $shipment_log);
                                foreach (array_reverse($shipment_logs) as $value):
                                    $value_log = json_decode($value);
                                    if (!$value_log) continue;
                                    ?>
                                    <!-- timeline item -->
                                    <div>
                                        <i class="fas fa-comments bg-blue"></i>
                                        <div class="timeline-item">
                                            <span class="time"><i class="fas fa-clock"></i><?php echo wp_date( get_date_format(),strtotime($value_log->timestamp))?></span>
                                            <h3 class="timeline-header">
                                                <a href="<?php echo$value_log->tracking_url?>"><?php echo$value_log->tracking_id?></a>
                                            </h3>
                                            <div class="timeline-body">
                                                <span>Trạng thái giao hàng: </span><strong><?php echo $value_log->status; ?></strong><br>
                                                 <?php if($value_log->reason_code) : ?> <span>Cancel note: </span><strong><?php echo $value_log->reason_code ; ?></strong><br>
                                                <?php endif;?>
                                                <span>Họ tên shipper: </span><strong><?php echo $value_log->driver->name ?></strong><br>
                                                <span>Số điện thoại: </span><strong><?php echo $value_log->driver->phone ?></strong><br>
                                                <span>License plate: </span><strong><?php echo $value_log->driver->license_plate ?></strong><br>
                                                <span>Linh hình: </span><strong><?php echo $value_log->driver->photo_url ?></strong><br>
                                                <span>Vị trí: </span>
                                                    <strong><?php echo $value_log->driver->current_coordinates->latitude ?></strong>
                                                    <strong><?php echo $value_log->driver->current_coordinates->longitude ?></strong><br>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                    <!-- END timeline item -->
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<?php endif;
    endif;