<?php
    //check_permission admin dashboard
    if (!current_user_can('admin_dashboard')):
       user_permission_failed_content();
    else:
    $quantity_order_new = wc_orders_count('processing');
    $quantity_order_request = wc_orders_count('request');
    $quantity_order_shipping = wc_orders_count('shipping');
    $quantity_order_delivered = wc_orders_count('delivered');

?>
<!-- Small boxes (Stat box) -->
<div class="row">
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <?php wc_get_template('template-parts/dashboard/components/card-item.php'
            , array('quantity' => $quantity_order_new
            ,'class' => 'warning'
            ,'url'=>'./order-list/?offset=1&status=wc-processing'
            ,'title'=>'Đơn hàng mới'
            ,'icon_big'=>'fas fa-arrow-circle-right'));
        ?>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <?php wc_get_template('template-parts/dashboard/components/card-item.php'
             , array('quantity' => $quantity_order_request
            ,'class' => 'info'
            ,'url'=>'./order-list/?offset=1&status=wc-request'
            ,'title'=>'Gọi đơn vị vận chuyển'
            ,'icon_big'=>'fas fa-arrow-circle-right'));
        ?>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <?php wc_get_template('template-parts/dashboard/components/card-item.php'
            , array('quantity' => $quantity_order_shipping
            ,'class' => 'info'
            ,'url'=>'./order-list/?offset=1&status=wc-shipping'
            ,'title'=>'Đang giao hàng'
            ,'icon_big'=>'fas fa-arrow-circle-right'));
        ?>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <?php wc_get_template('template-parts/dashboard/components/card-item.php'
            , array('quantity' => $quantity_order_delivered
            ,'class' => 'success'
            ,'url'=>'./order-list/?offset=1&status=wc-delivered'
            ,'title'=>'Giao hàng thành công'
            ,'icon_big'=>'fas fa-arrow-circle-right'));
        ?>
    </div>
    <!-- ./col -->
</div>
<!-- /.row -->

<div class="row">
    <div class="col-6">
        <?php wc_get_template('template-parts/dashboard/components/card-table-brand.php'); ?>
    </div>
    <div class="col-6">
        <?php wc_get_template('template-parts/dashboard/components/card-table-gender.php'); ?>
    </div>
</div>
<div class="row">
    <div class="col-6">
        <?php wc_get_template('template-parts/dashboard/components/card-table-category.php'); ?>
    </div>
    <div class="col-6">
        <?php wc_get_template('template-parts/dashboard/components/card-table-gender.php'); ?>
    </div>
</div>

<?php endif;