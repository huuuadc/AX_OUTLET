<?php

//check_permission admin dashboard order
if (!current_user_can('admin_dashboard_order')):
    user_permission_failed_content();
else:

    $item_in_page = get_option('admin_dashboard_item_in_page') ?? 10;

    $order_status = $_GET['status'] ?? 'any';

    $range_date = isset($_GET['range_date']) ?  convert_string_to_range_date($_GET['range_date']) : convert_string_to_range_date_default(6);
    $start_date = $range_date['start_date'];
    $end_date = $range_date['end_date'];

    $moment = (strtotime($end_date) - strtotime($start_date))/(86400);
    $default_moment = $moment;
    $filter_order = array(
        'post_status' => explode(',', $order_status),
        'post_type' => array('shop_order'),
        'posts_per_page' => $item_in_page,
        'paged' => $_GET['offset'] ?? 1 ,
        'order_by' => 'modified',
        'date_query' => array(
            array(
                'after' => $start_date,
                'before'=> $end_date
            ),
            'inclusive' => true,
            'relation' => 'AND',
        ),
        'order' => 'DESC'
    );



    $order_query = new WP_Query($filter_order);

    $status_badge = array(
            'reject' => 'badge-secondary',
            'processing' => 'badge-warning',
            'confirm' => 'badge-primary',
            'completed' => 'badge-success',
            'request' => 'badge-info',
            'shipping' => 'badge-info',
            'delivered' => 'badge-success',
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
                            <div class="col-4">
                                <label for="filter_order_status">Trạng thái đơn hàng</label>
                                <select
                                        id="filter_order_status"
                                        class="select2 select2-primary"
                                        multiple="multiple"
                                        data-placeholder="Chọn trạng thái"
                                        style="width: 100%;"
                                        data-dropdown-css-class="select2-primary"
                                >
                                    <?php foreach ($status_badge as $item_key => $item_value) :?>
                                        <option <?php echo str_contains($order_status,$item_key) ? 'selected' :'' ?> value="wc-<?php echo $item_key?>" ><?php echo $item_key?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-4">
                                <label>Theo ngày</label>
                                <div class="input-group">
                                    <button type="button" class="btn btn-default float-right" id="reservation_order" default-moment="<?php echo $default_moment?>">
                                        <i class="far fa-calendar-alt"></i> Phạm vi
                                        <i class="fas fa-caret-down"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-4">
                                <label>Hiển thị từ</label>
                                <div id="reservation_title"><span><?php echo $range_date['text_date'] ?></span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <table id="list_order" class="table table-bordered table-hover dataTable dtr-inline table_order_simple" style="display: block ;overflow-x: scroll; overflow-y: clip;">
                    <thead>
                        <tr class="text-nowrap">
                            <th>STT</th>
                            <th>Loại Đơn Hàng</th>
                            <th>Mã đơn hàng</th>
                            <th>Ngày đặt hàng</th>
                            <th>Khách hàng</th>
                            <th>SL SP</th>
                            <th>Tổng tiền</th>
                            <th>HTTT</th>
                            <th>TT Thanh Toán</th>
                            <th>Mã giao hàng</th>
                            <th>TT giao hàng</th>
                            <th>TT đơn hàng</th>
                            <th>Thao tác</th>
                            <th class="w-100">Khóa đơn hàng</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $count = 0;
                    while ( $order_query->have_posts() ) :
                        $count++;
                        $order_query->the_post();
                        $order = new OMS_ORDER(get_the_ID());

                        $disable = 'disabled' ;

                        ?>
                        <tr class="text-nowrap" id="order_id_<?php echo get_the_ID()?> " value="<?php echo get_the_ID()?>" >
                            <td><?php echo (($order_query->query_vars['paged'] -1)*$order_query->query_vars['posts_per_page']) + $count?></td>
                            <td><span><?php echo $order->get_order_type()?></span></td>
                            <td><a href="<?php echo '/admin-dashboard/order-list/?order_id='.get_the_ID()?>">#<?php the_ID();?></a></td>
                            <td><?php echo wp_date(get_date_format(),strtotime( $order->get_date_created()))?></td>
                            <td class="text-uppercase text-bold"><?php echo $order->get_billing_last_name() . ' ' . $order->get_billing_first_name()?></td>
                            <td><?php echo $order->get_item_count()?></td>
                            <td class="text-right text-bold"><?php echo number_format( $order->get_total(),0,'.',',')?> đ</td>
                            <td class="text-uppercase"><?php echo $order->get_payment_method()?></td>
                            <td id="order_payment_status_<?php echo get_the_ID()?>"><span class="badge <?php echo $order->get_payment_class_name()?>"><?php echo $order->get_payment_title()?></span></td>
                            <td id="order_tracking_id_<?php echo get_the_ID()?>" ><a href="<?php echo $order->get_tracking_url()?>" ><?php echo $order->get_tracking_id()?></a></td>
                            <td id="order_shipment_status_<?php echo get_the_ID()?>"><span class="badge"><?php echo $order->get_meta('shipment_status',true,'value') ?? 'new'?></span></td>
                            <td id="order_status_<?php echo get_the_ID()?>"><span class="badge <?php echo $order->get_status_class_name() ?>"><?php echo $order->get_status_title()?></span></td>
                            <td><div class="btn-group">
                                    <button type="button" class="btn btn-default dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu" role="menu">
                                        <a class="dropdown-item" href="<?php echo '/admin-dashboard/order-list/?order_id='.get_the_ID()?>">Chi tiết đơn hàng</a>
                                        <?php if(current_user_can('admin_dashboard_order_reject')) {?><button class="dropdown-item" onclick="send_update_status(<?php echo get_the_ID() ?>, 'reject')">Từ chối đơn hàng</button><?php }?>
                                        <?php if(current_user_can('admin_dashboard_order_confirm')) {?><button class="dropdown-item" onclick="send_update_status(<?php echo get_the_ID() ?>, 'confirm')">Xác nhận đơn hàng</button><?php }?>
                                        <?php if(current_user_can('admin_dashboard_order_payment')) {?><button class="dropdown-item" onclick="send_update_payment(<?php echo get_the_ID() ?>)">Thanh toán</button><?php }?>
                                        <?php if(current_user_can('admin_dashboard_order_request') && $order->get_order_type() == 'website') {?><button class="dropdown-item" onclick="send_update_status(<?php echo get_the_ID() ?>, 'request')">Gọi đơn vị vận chuyển</button><?php }?>
                                        <?php if(current_user_can('admin_dashboard_order_shipping') && $order->get_order_type() == 'website') {?><button <?php echo $disable?> class="dropdown-item" onclick="send_update_status(<?php echo get_the_ID() ?>, 'shipping')">Đang giao hàng</button><?php }?>
                                        <?php if(current_user_can('admin_dashboard_order_delivered') && $order->get_order_type() == 'website') {?><button <?php echo $disable?> class="dropdown-item" onclick="send_update_status(<?php echo get_the_ID() ?>, 'delivered')">Giao hàng thành công</button><?php }?>
                                        <?php if(current_user_can('admin_dashboard_order_delivered') && $order->get_order_type() == 'website') {?><button <?php echo $disable?> class="dropdown-item" onclick="send_update_status(<?php echo get_the_ID() ?>, 'delivery-failed')">Giao hàng thất bại</button><?php }?>
                                        <?php if(current_user_can('admin_dashboard_order_completed') && $order->get_order_type() != 'website') {?><button class="dropdown-item" onclick="send_update_status(<?php echo get_the_ID() ?>, 'completed')">Giao sàn</button><?php }?>
                                        <?php if(current_user_can('admin_dashboard_order_goods') && $order->get_order_type() == 'website') {?><button class="dropdown-item" onclick="send_update_status(<?php echo get_the_ID() ?>, 'confirm-goods')">Xác nhận hoàn hàng</button><?php }?>
                                        <?php if(current_user_can('admin_dashboard_order_cancel')) {?><button class="dropdown-item" onclick="send_update_status(<?php echo get_the_ID() ?>, 'cancelled')">Hủy đơn hàng</button><?php }?>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo $order->get_order_key()?></td>
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
                        <?php $_GET['offset'] = $page+1 ;
                            $param=[];
                            foreach($_GET as $key=>$value ){
                                $param[] = $key.'='.$value;
                            }
                            ?>
                        <li class="paginate_button page-item <?php echo ($page+1) == $order_query->query_vars['paged']? 'active': ''?>">
                            <a href="./?<?php echo implode('&',$param)?>" aria-controls="list_order" data-dt-idx="<?php echo $page+1 ?>" tabindex="<?php echo $page ?>" class="page-link"><?php echo $page+1 ?></a>
                        </li>
                        <?php endfor;?>
                   </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php endif;