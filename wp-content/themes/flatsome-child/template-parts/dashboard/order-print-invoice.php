<div class="content-header">
</div>
<?php

use AX\COMPANY;

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
    'delivered' => 'badge-info',
    'delivery-failed' => 'badge-danger',
    'cancelled' => 'badge-danger',
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
    $order_ax = new AX_ORDER($order_id);
    $company = new COMPANY();
    ?>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <!-- Main content -->
                    <div class="invoice p-3 mb-3">
                        <span>Liên 1(Giao cho khách)</span><span> giờ in: <?php echo date('H:m:s d-m-Y')  ?></span>
                        <!-- title row -->
                        <div class="row">
                            <div class="col-12 text-center">
                                <h1>Chi Tiết Đơn Hàng Bán Lẻ</h1>
                            </div>
                            <div class="col-12 pt-3">
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
                            <div class="col-sm-2 invoice-col">
                                Hóa đơn <br><b> #<?php echo $order_id?></b><br>
                            </div>
                            <!-- /.col -->
                            <!-- /.col -->
                            <div id="card_orders" class="col-sm-2 invoice-col no-print">
                                Trạng thái <br>
                                <b id="order_status_<?php echo get_the_ID()?>">
                                <span class="badge <?php echo $status_badge[$order_ax->get_status()] ?>"><?php echo $order_ax->get_status()?>
                                </span>
                                </b><br>
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
                                <p class="lead">Thông tin giao hàng:</p>
                                Đơn vị giao hàng: <span><?php echo $order_ax->get_shipping_method()?></span><br>
                                Trạng thái giao hàng: <span><?php echo $order_ax->get_meta('shipment_status',true)?></span><br>
                                Ngày lấy hàng dự kiến: <span><?php echo wp_date( get_date_format(), strtotime( $order_ax->get_meta('shipment_estimated_timeline_pickup',true)))?></span><br>
                                Ngày giao hàng thành: <span><?php echo wp_date( get_date_format(), strtotime( $order_ax->get_meta('shipment_estimated_timeline_dropoff',true)))?></span><br>
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

                    </div>
                    <!-- /.invoice -->

                    <!-- Main content -->
                    <div class="invoice p-3 mb-3">
                        <span>Liên 2(Lưu tại cửa hàng)</span><span> giờ in: <?php echo date('H:m:s d-m-Y') ?></span>
                        <!-- title row -->
                        <div class="row">
                            <div class="col-12 text-center">
                                <h1>Chi Tiết Đơn Hàng Bán Lẻ</h1>
                            </div>
                            <div class="col-12 pt-3">
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
                            <div class="col-sm-2 invoice-col">
                                Hóa đơn <br><b> #<?php echo $order_id?></b><br>
                            </div>
                            <!-- /.col -->
                            <!-- /.col -->
                            <div id="card_orders" class="col-sm-2 invoice-col no-print">
                                Trạng thái <br>
                                <b id="order_status_<?php echo get_the_ID()?>">
                                <span class="badge <?php echo $status_badge[$order_ax->get_status()] ?>"><?php echo $order_ax->get_status()?>
                                </span>
                                </b><br>
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
                                <p class="lead">Thông tin giao hàng:</p>
                                Đơn vị giao hàng: <span><?php echo $order_ax->get_shipping_method()?></span><br>
                                Trạng thái giao hàng: <span><?php echo $order_ax->get_meta('shipment_status',true)?></span><br>
                                Ngày lấy hàng dự kiến: <span><?php echo wp_date( get_date_format(), strtotime( $order_ax->get_meta('shipment_estimated_timeline_pickup',true)))?></span><br>
                                Ngày giao hàng thành: <span><?php echo wp_date( get_date_format(), strtotime( $order_ax->get_meta('shipment_estimated_timeline_dropoff',true)))?></span><br>
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
                    </div>
                    <!-- /.invoice -->
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
    <script>
        window.print()
        window.onafterprint = () => window.close();
    </script>

<?php endif;