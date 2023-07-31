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

?>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Danh sách đơn hàng</h1>
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
    <!-- /.card-header -->
    <div class="card-body">
        <div class="row">
            <div class="col-sm-12">
                <table id="list_order" class="table table-bordered table-hover dataTable dtr-inline" style="display: block ;overflow-x: scroll; overflow-y: clip;">
                    <thead>
                        <tr style="white-space: nowrap">
                            <th>STT</th>
                            <th>Mã đơn hàng</th>
                            <th>Khóa đơn hàng</th>
                            <th>Ngày đặt hàng</th>
                            <th>Khách hàng</th>
                            <th>Số lượng sản phẩm</th>
                            <th>Tổng tiền</th>
                            <th>Phương thức thanh toán</th>
                            <th>Mã giao hàng</th>
                            <th>Trạng thái giao hàng</th>
                            <th>Trạng thái đơn hàng</th>
                            <th>Thao tác</th>
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
                            <td class="text-right"><?php echo number_format( $order->get_total(),0,',','.')?> đ</td>
                            <td><?php echo $order->get_payment_method_title()?></td>
                            <td id="order_tracking_id_<?php echo get_the_ID()?>" ><a href="<?php echo $order->get_tracking_url()?>" ><?php echo $order->get_tracking_id()?></a></td>
                            <td id="order_shipment_status_<?php echo get_the_ID()?>"><span class="badge"><?php echo $order->get_meta('shipment_status',true,'value') ?? 'new'?></span></td>
                            <td id="order_status_<?php echo get_the_ID()?>"><span class="badge <?php echo $status_badge[$order->get_status()] ?>"><?php echo $order->get_status()?></span></td>
                            <td><div class="btn-group">
                                    <button type="button" class="btn btn-default dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu" role="menu">
                                        <a class="dropdown-item" href="<?php echo '/admin-dashboard/order-list?order_id='.get_the_ID()?>">Chi tiết đơn hàng</a>
                                        <button class="dropdown-item" onclick="send_update_status(<?php echo get_the_ID() ?>, 'reject')">Từ chối đơn hàng</button>
                                        <button class="dropdown-item" onclick="send_update_status(<?php echo get_the_ID() ?>, 'confirm')">Xác nhận đơn hàng</button>
                                        <button class="dropdown-item" onclick="send_update_status(<?php echo get_the_ID() ?>, 'request')">Gọi đơn vị vận chuyển</button>
                                        <button disabled class="dropdown-item" onclick="send_update_status(<?php echo get_the_ID() ?>, 'shipping')">Đang giao hàng</button>
                                        <button disabled class="dropdown-item" onclick="send_update_status(<?php echo get_the_ID() ?>, 'delivered')">Giao hàng thành công</button>
                                        <button disabled class="dropdown-item" onclick="send_update_status(<?php echo get_the_ID() ?>, 'delivery-failed')">Giao hàng thất bại</button>
                                        <button class="dropdown-item" onclick="send_update_status(<?php echo get_the_ID() ?>, 'confirm-goods')">Xác nhận còn hàng</button>
                                        <button class="dropdown-item" onclick="send_update_status(<?php echo get_the_ID() ?>, 'cancelled')">Hủy đơn hàng</button>
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
                    Hiển thị <?php echo (($order_query->query_vars['paged'] -1)*$order_query->query_vars['posts_per_page'])+1?>
                    từ <?php echo $order_query->query_vars['paged']*$order_query->query_vars['posts_per_page']?>
                    của <?php echo $order_query->found_posts?>
                    đơn hàng
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