<?php

global $wpdb;

    $order_sessions = $wpdb->get_results( "
        SELECT *
        FROM ". $wpdb->prefix."woocommerce_sessions
    ");

    $count = 0;

?>

<div class="card">
    <div class="card-header">
        Danh mục
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <table id="table_categories" class="table table-bordered table-striped dataTable dtr-inline table_simple">
            <thead>
            <tr>
                <th>Số TT</th>
                <th>Khách hàng</th>
                <th>Tên khách hàng</th>
                <th>Danh sách sản phẩm</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($order_sessions as $order) :
                $count++;
                $order_value = maybe_unserialize($order->session_value);
                $order_customer = maybe_unserialize($order_value['customer']);
                $carts = isset($order_value['cart']) ? maybe_unserialize($order_value['cart']): [];
                $customer = $order_customer['id'] == 0 ? new WP_User(): get_user_by('id',$order_customer['id']);
            ?>
                <tr>
                    <td><?php echo $count ?></td>
                    <td><?php echo $customer->user_login?></td>
                    <td><?php echo $order_customer['first_name'] . ' ' . $order_customer['last_name']?> </td>
                    <td><?php
                           foreach ($carts as $cart){

                               $product = $cart['variation_id'] == 0 ? wc_get_product($cart['product_id']) : wc_get_product($cart['variation_id']);
                               echo '<a href="'. get_permalink($product->get_id())  .'">';
                               echo $product->get_sku() .' - '.$product->get_id() . ' - ' . $product->get_name();
                               echo '</a>';
                               echo '<br>';
                           }

                        ?></td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <div class="card-footer"></div>
</div>