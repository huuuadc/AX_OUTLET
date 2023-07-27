<?php

    $item_in_page = get_option('admin_dashboard_item_in_page') ?? 10;

    $filter_order = array(
        'post_status' => 'completed',
        'post_type' => 'shop_order',
        'posts_per_page' => $item_in_page,
        'paged' => $_GET['offset'] ?? 1,
        'order_by' => 'modified',
        'order' => 'DESC'
    );

    $order_query = new WP_Query($filter_order);

    $status_badge = array(
            'reject' => 'badge-danger',
            'trash' => 'badge-danger',
            'on-hold' => 'badge-danger',
            'pending' => 'badge-warning',
            'processing' => 'badge-primary',
            'confirm' => 'badge-primary',
            'completed' => 'badge-success',
            'request' => 'badge-info',
            'shipping' => 'badge-info',
            'delivered' => 'badge-info',
            'delivery-failed' => 'badge-danger',
            'cancelled' => 'badge-danger',
            'confirm-goods' => 'badge-primary',
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
                    <li class="breadcrumb-item"><a href="/admin-dashboard">Dashboard</a></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<div id="card_orders" class="card">
    <div class="card-header">
        <h3 class="card-title">Orders</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="row">
            <div class="col-sm-12">
                <table id="list_order" class="table table-bordered table-hover dataTable dtr-inline" style="display: block ;overflow-x: scroll; overflow-y: clip;">
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
                    <tbody>
                    <?php
                    $count = 0;
                    while ( $order_query->have_posts() ) :
                        $count++;
                        $order_query->the_post();
                        $order = new AX_ORDER(get_the_ID());
                        ?>
                        <tr id="order_id_<?php echo get_the_ID()?> " value="<?php echo get_the_ID()?>" >
                            <td><?php echo (($order_query->query_vars['paged'] -1)*$order_query->query_vars['posts_per_page']) + $count?></td>
                            <td>#<?php the_ID();?></td>
                            <td><?php echo $order->get_order_key()?></td>
                            <td><?php echo wp_date(get_date_format(),strtotime( $order->get_date_created()))?></td>
                            <td><?php echo $order->get_billing_last_name() . ' ' . $order->get_billing_first_name()?></td>
                            <td><?php echo $order->get_item_count()?></td>
                            <td class="text-right"><?php echo number_format( $order->get_total(),0,',','.')?> VNĐ</td>
                            <td><a href="<?php echo $order->get_tracking_url()?>" ><?php echo $order->get_tracking_id()?></a></td>
                            <td><?php echo $order->get_payment_method_title()?></td>
                            <td><?php echo $order->get_customer_id()?></td>
                            <td id="order_status_<?php echo get_the_ID()?>"><span class="badge <?php echo $status_badge[$order->get_status()] ?>"><?php echo $order->get_status()?></span></td>
                            <td><div class="btn-group">
                                    <button type="button" class="btn btn-default">Action</button>
                                    <button type="button" class="btn btn-default dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu" role="menu">
                                        <a class="dropdown-item" href="<?php echo '/admin-dashboard/order-list?order_id='.get_the_ID()?>">View Order</a>
                                        <button class="dropdown-item" onclick="send_update_status(<?php echo get_the_ID() ?>, 'reject')">Store reject</button>
                                        <button class="dropdown-item" onclick="send_update_status(<?php echo get_the_ID() ?>, 'confirm')">Store confirm</button>
                                        <button class="dropdown-item" onclick="send_update_status(<?php echo get_the_ID() ?>, 'request')">Store Request</button>
                                        <button class="dropdown-item" onclick="send_update_status(<?php echo get_the_ID() ?>, 'shipping')">Vendor shipping</button>
                                        <button class="dropdown-item" onclick="send_update_status(<?php echo get_the_ID() ?>, 'delivered')">Vendor delivered</button>
                                        <button class="dropdown-item" onclick="send_update_status(<?php echo get_the_ID() ?>, 'delivery-failed')">Vendor delivery failed</button>
                                        <button class="dropdown-item" onclick="send_update_status(<?php echo get_the_ID() ?>, 'confirm-goods')">Store confirm goods</button>
                                        <button class="dropdown-item" onclick="send_update_status(<?php echo get_the_ID() ?>, 'cancelled')">Admin cancel</button>
                                    </div>
                                </div></td>
                        </tr>

                    <?php endwhile;?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-5">
                <div class="dataTables_info" id="list_order_info" role="status" aria-live="polite">
                    Showing <?php echo (($order_query->query_vars['paged'] -1)*$order_query->query_vars['posts_per_page'])+1?>
                    to <?php echo $order_query->query_vars['paged']*$order_query->query_vars['posts_per_page']?>
                    of <?php echo $order_query->found_posts?>
                    entries
                </div>
            </div>
            <div class="col-sm-12 col-md-7">
                <div class="dataTables_paginate paging_simple_numbers" id="list_order_paginate">
                    <ul class="pagination" style="justify-content: flex-end;">
                        <?php for($page=0; $page < $order_query->max_num_pages; $page++):?>
                        <li class="paginate_button page-item <?php echo ($page+1) == $order_query->query_vars['paged']? 'active': ''?>">
                            <a href="/admin-dashboard/order-list?offset=<?php echo $page+1?>" aria-controls="list_order" data-dt-idx="<?php echo $page+1 ?>" tabindex="<?php echo $page ?>" class="page-link"><?php echo $page+1 ?></a>
                        </li>
                        <?php endfor;?>
                   </ul>
                </div>
            </div>
        </div>
    </div>
</div>