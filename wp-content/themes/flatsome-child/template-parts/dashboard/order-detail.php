<?php
    $order_id =  $_GET['order_id'];
    $order = wc_get_order($order_id);
?>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Order Detail</h1>
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
                                <i class="fas fa-globe"></i> AX OUTLET
                                <small class="float-right">Date: <?php echo $order->get_date_created()?></small>
                            </h4>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- info row -->
                    <div class="row invoice-info">
                        <div class="col-sm-4 invoice-col">
                            From
                            <address>
                                <strong>AX Outlet</strong><br>
                                72-74 Nguyễn Thị Minh Khai<br>
                                Quận 3, Tp Hồ Chí Minh<br>
                                Phone: 0326 473 067<br>
                                Email: huu.tran@dafc.com.vn
                            </address>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                            To
                            <address>
                                <strong><?php echo $order->get_billing_last_name() .' '. $order->get_billing_first_name() ?></strong><br>
                                <?php echo $order->get_billing_address_1()?><br>
                                <?php echo $order->get_billing_city().' ,'. $order->get_billing_country()?><br>
                                Phone: <?php echo $order->get_billing_phone()?><br>
                                Email: <?php echo $order->get_billing_email()?>
                            </address>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                            <b>Invoice #<?php echo $order_id?></b><br>
                            <br>
                            <b>Order ID:</b> <?php echo $order_id?><br>
                            <b>Payment Due:</b> <?php echo $order->get_date_paid()?><br>
                            <b>Account:</b> <?php echo $order->get_customer_id()?>
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
                                    <th>Index</th>
                                    <th>Qty</th>
                                    <th>Product</th>
                                    <th>Serial #</th>
                                    <th>Description</th>
                                    <th>Subtotal</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $count= 0; foreach ($order->get_items() as $item_key => $item ): $count++ ?>
                                <tr>
                                    <td><?php echo $count  ?></td>
                                    <td><?php echo $item->get_quantity() ?></td>
                                    <td><?php echo $item->get_name() ?></td>
                                    <td><?php echo get_post_meta( $item['variation_id'], '_sku', true ) ?></td>
                                    <td><?php echo $item->get_order_id() ?></td>
                                    <td><?php echo number_format( $item->get_total(), '0',',','.') ?> VNĐ</td>
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
                        <div class="col-6">
                            <p class="lead">Payment Methods:</p><span><?php echo $order->get_payment_method() . ' - ' . $order->get_payment_method_title()?></span>
                        </div>
                        <!-- /.col -->
                        <div class="col-6">
                            <p class="lead">Amount Due 2/22/2014</p>

                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <th style="width:50%">Subtotal:</th>
                                        <td style="text-align: right; padding-right: 30px "><?php echo number_format($order->get_total() - $order->get_shipping_total(), '0', ',', '.'); ?> VNĐ</td>
                                    </tr>
<!--                                    <tr>-->
<!--                                        <th>Tax (8%)</th>-->
<!--                                        <td style="text-align: right; padding-right: 30px">--><?php //echo number_format($order->get_total() * 0.08 / 1.08, '0', ',', '.')?><!-- VNĐ</td>-->
<!--                                    </tr>-->
                                    <tr>
                                        <th>Shipping:</th>
                                        <td style="text-align: right; padding-right: 30px"><?php echo number_format($order->get_shipping_total(), '0', ',', '.')?> VNĐ</td>
                                    </tr>
                                    <tr>
                                        <th>Total:</th>
                                        <td style="text-align: right; padding-right: 30px"><?php echo number_format($order->get_total() , '0', ',', '.')?> VNĐ</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <!-- this row will not appear when printing -->
                    <div class="row no-print">
                        <div class="col-12">
                            <button onclick="window.print()" rel="noopener" target="_blank" class="btn btn-default">
                                <i class="fas fa-print"></i> Print</button>
                            <button type="button" class="btn btn-success float-right">
                                <i class="fas fa-file-invoice-dollar"></i> Post E-Invoice</button>
                            <button type="button" class="btn btn-primary float-right"  style="margin-right: 5px;">
                                <i class="fas fa-shipping-fast"> Call Shipper</i></button>
                            <button onclick="" type="button" class="btn btn-primary float-right"  style="margin-right: 5px;">
                                <i class="fas fa-shipping-fast"> Call Shipper</i></button>
                        </div>
                    </div>
                </div>
                <!-- /.invoice -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->