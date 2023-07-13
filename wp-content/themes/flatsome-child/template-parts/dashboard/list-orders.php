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

?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">List Order</h3>
    </div>
    <!-- /.card-header -->
    <table id="list_order" class="table table-bordered table-striped" style="display: block; overflow-x: auto">
        <thead>
            <tr style="white-space: nowrap">
                <th>Index</th>
                <th>Order id</th>
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
                <td><?php echo $order->get_date_created()?></td>
                <td><?php echo $order->get_billing_last_name() . ' ' . $order->get_billing_first_name()?></td>
                <td><?php echo $order->get_item_count()?></td>
                <td><?php echo $order->get_total()?></td>
                <td><?php echo $order->get_customer_id()?></td>
                <td><?php echo $order->get_payment_method()?></td>
                <td><?php echo $order->get_customer_id()?></td>
                <td><?php echo $order->get_status()?></td>
                <td><?php echo ''?></td>
            </tr>

        <?php endwhile;?>
        <tbody>

        </tbody>
    </table>



</div>