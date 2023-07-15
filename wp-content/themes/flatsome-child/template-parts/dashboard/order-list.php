<?php

    $filter_order = array(
        'post_status' => 'completed',
        'post_type' => 'shop_order',
        'posts_per_page' => 200,
        'paged' => 1,
        'order_by' => 'modified',
        'order' => 'DESC'
    );

    $order_query = new WP_Query($filter_order);

    $status_badge = array(
            'trash' => 'badge-danger',
            'on-hold' => 'badge-danger',
            'pending' => 'badge-warning',
            'processing' => 'badge-primary',
            'completed' => 'badge-success',
            'shipping' => 'badge-info',
            'canceled' => 'badge-danger',
            'b' => 'badge-danger',
    );

?>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Orders</h1>
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
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Orders</h3>
    </div>
    <!-- /.card-header -->
    <table id="list_order" class="table table-bordered table-striped" style="display: block ;overflow-x: scroll; overflow-y: clip;">
        <thead>
            <tr style="white-space: nowrap">
                <th>Index</th>
                <th>Order id</th>
                <th>Order key</th>
                <th>Order date</th>
                <th>Customer name</th>
                <th>Qty item</th>
                <th>Total amount</th>
                <th>Transport code</th>
                <th>Payment method</th>
                <th>Shipment status</th>
                <th>Order status</th>
                <th>Action</th>
            </tr>
        </thead>
        <?php
        $count = 0;
        while ( $order_query->have_posts() ) :
            $count++;
            $order_query->the_post();
            $order = new WC_Order(get_the_ID());
            ?>
            <tr>
                <td><?php echo $count?></td>
                <td><?php the_ID();?></td>
                <td><?php echo $order->get_order_key()?></td>
                <td><?php echo $order->get_date_created()?></td>
                <td><?php echo $order->get_billing_last_name() . ' ' . $order->get_billing_first_name()?></td>
                <td><?php echo $order->get_item_count()?></td>
                <td class="text-right"><?php echo number_format( $order->get_total(),0,',','.')?> VNĐ</td>
                <td><?php echo $order->get_customer_id()?></td>
                <td><?php echo $order->get_payment_method_title()?></td>
                <td><?php echo $order->get_customer_id()?></td>
                <td><span class="badge <?php echo $status_badge[$order->get_status()] ?>"><?php echo $order->get_status()?></span></td>
                <td><div class="btn-group">
                        <button type="button" class="btn btn-default">Action</button>
                        <button type="button" class="btn btn-default dropdown-toggle dropdown-icon" data-toggle="dropdown">
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <div class="dropdown-menu" role="menu">
                            <a class="dropdown-item" href="<?php echo '/admin-dashboard/order-list?order_id='.get_the_ID()?>">View</a>
                            <a class="dropdown-item" href="#">Call Shipper</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Canceled</a>
                        </div>
                    </div></td>
            </tr>

        <?php endwhile;?>
        <tbody>

        </tbody>
    </table>



</div>