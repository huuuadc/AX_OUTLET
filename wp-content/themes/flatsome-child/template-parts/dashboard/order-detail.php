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

use AX\COMPANY;

    $order_id =  $_GET['order_id'];

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
    $order_ax = new AX_ORDER($order_id);
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
                            Hóa đơn <br><b> #<?php echo $order_id?></b><br>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <!-- Table row -->
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>SKU</th>
                                    <th>Sản phẩm</th>
                                    <th>Số lượng</th>
                                    <th class="text-right">Tiền</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $count= 0; foreach ($order_ax->get_items() as $item_key => $item ): $count++ ?>
                                <tr><td><?php echo $count?></td>
                                    <td><?php echo get_post_meta( $item['variation_id'], '_sku', true ) ?></td>
                                    <td><?php echo $item->get_name() ?></td>
                                    <td><?php echo $item->get_quantity() ?></td>
                                    <td class="text-right"><?php echo number_format( $item->get_total(), '0',',','.') ?> đ</td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
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
                            <p class="lead">Thông tin giao hàng:</p>
                            Đơn vị giao hàng: <span><?php echo $order_ax->get_shipping_method()?></span><br>
                            Trạng thái giao hàng: <span><?php echo $order_ax->get_meta('shipment_status',true)?></span><br>
                            Ngày lấy hàng dự kiến: <span><?php echo wp_date( get_date_format(), strtotime( $order_ax->get_meta('shipment_estimated_timeline_pickup',true)))?></span><br>
                            Ngày giao hàng thành: <span><?php echo wp_date( get_date_format(), strtotime( $order_ax->get_meta('shipment_estimated_timeline_dropoff',true)))?></span><br>
                        </div>
                        <!-- /.col -->
                        <div class="col-6">
                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <th>Tiền:</th>
                                        <td class="text-right"><?php echo number_format($order_ax->get_total() - $order_ax->get_shipping_total(), '0', ',', '.'); ?> đ</td>
                                    </tr>
                                    <tr>
                                        <th>Giảm giá</th>
                                        <td class="text-right"><?php echo number_format($order_ax->get_total_discount() , '0', ',', '.')?> đ</td>
                                    </tr>
                                    <tr>
                                        <th>Giao hàng:</th>
                                        <td class="text-right"><?php echo number_format($order_ax->get_shipping_total(), '0', ',', '.')?> đ</td>
                                    </tr>
                                    <tr>
                                        <th>Tổng tiền:</th>
                                        <td class="text-right"><?php echo number_format($order_ax->get_total() , '0', ',', '.')?> đ</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <!-- this row will not appear when printing -->
                    <div class="row no-print padding10">
                        <div class="col-12">
                            <button onclick="window.print()" rel="noopener" target="_blank" class="btn btn-default">
                                <i class="fas fa-print"></i> In hóa đơn</button>
                            <a href="/orders/" ><button rel="noopener" target="_blank" class="btn btn-default">
                                    <i class="fas fa-print"></i> In phiếu giao hàng</button></a>
                            <button onclick="send_update_status(<?php echo $order_id?>,'confirm-goods')" type="button" class="btn btn-success float-right">
                                <i class="fas fa-file-invoice-dollar"></i> Xác nhận đã nhận hàng</button>
                            <button onclick="send_update_status(<?php echo $order_id?>,'request')" type="button" class="btn btn-primary float-right"  style="margin-right: 5px;">
                                <i class="fas fa-shipping-fast"> </i> Gọi giao hàng</button>
                            <button onclick="send_update_status(<?php echo $order_id?>,'cancelled')" type="button" class="btn btn-danger float-right"  style="margin-right: 5px;">
                                <i class="fas fa-times"></i> Hủy đơn</button>
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