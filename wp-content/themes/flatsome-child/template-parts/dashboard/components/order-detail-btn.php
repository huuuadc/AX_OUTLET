<?php
defined( 'ABSPATH' ) || exit;

if ( !isset($args) || !isset($args['order_id'])) {
    return;
}

$order_id = $args['order_id'];
$order_ax = $args['order'] ?? new OMS_ORDER($order_id);

?>

        <a href="<?php echo '/admin-dashboard/order-list?order_id='.$order_ax->get_id().'&print=invoice'?>" target="_blank"  rel="noopener noreferrer">
            <button class="btn btn-default">
                <i class="fas fa-print"></i> In hóa đơn</button></a>
        <a href="<?php echo '/admin-dashboard/order-list?order_id='.$order_ax->get_id().'&print=shipment'?>" target="_blank"  rel="noopener noreferrer">
            <button class="btn btn-default">
                <i class="fas fa-print"></i> In phiếu giao hàng</button></a>
        <?php if(current_user_can('admin_dashboard_order_goods')
                && $order_ax->get_order_type() == 'website'
                && in_array($order_ax->get_status(), [1,'delivery-failed'], true)) {?>
        <button onclick="send_update_status(<?php echo $order_id?>,'confirm-goods')" type="button" class="btn btn-success float-right">
                <i class="far fa-calendar-check"></i> Xác nhận hoàn hàng</button><?php }?>
        <?php if(current_user_can('admin_dashboard_order_completed')
                && $order_ax->get_order_type() != 'website') {?>
        <button onclick="send_update_status(<?php echo $order_id?>,'completed')" type="button" class="btn btn-success float-right">
                <i class="far fa-calendar-check"></i> Giao sàn</button><?php }?>
        <?php if(current_user_can('admin_dashboard_order_post_ls')
                && $order_ax->get_order_type() == 'website'
                && !in_array($order_ax->get_status(), [1,'delivered','return','cancelled'], true)) {?>
        <button onclick="post_invoice_ls_retail(<?php echo $order_id?>)" type="button" class="btn btn-default float-right mr-1">
                <i class="far fa-calendar-check"></i> Post LS</button><?php }?>
        <?php if(current_user_can('admin_dashboard_order_payment')
                && !in_array($order_ax->get_status(), [1,'cancelled','return','reject','confirm','processing'], true)
                && $order_ax->get_payment_status()=='unpaid') {?>
        <button onclick="send_update_payment(<?php echo $order_id?>)" type="button" class="btn btn-success float-right mr-1">
                <i class="far fa-calendar-check"></i>Thanh toán</button><?php }?>
        <?php if(current_user_can('admin_dashboard_order_request')
                && $order_ax->get_order_type() == 'website'
                && in_array($order_ax->get_status(), [1,'confirm'], true)) {?>
        <button onclick="send_update_status(<?php echo $order_id?>,'request')" type="button" class="btn btn-info float-right mr-1">
                <i class="fas fa-people-carry"> </i> Gọi giao hàng</button><?php }?>
        <?php if(current_user_can('admin_dashboard_order_confirm')
                && in_array($order_ax->get_status(), [1,'processing','reject'], true)) {?>
        <button onclick="send_update_status(<?php echo $order_id?>,'confirm')" type="button" class="btn btn-primary float-right mr-1" >
                <i class="fa fa-check"></i> Xác nhận</button><?php }?>
        <?php if(current_user_can('admin_dashboard_order_reject')
                && in_array($order_ax->get_status(), [1,'processing'], true)) {?>
        <button onclick="send_update_status(<?php echo $order_id?>,'reject')" type="button" class="btn btn-secondary float-right mr-1" >
                <i class="fas fa-ban"></i> Từ chối</button><?php }?>
        <?php if(current_user_can('admin_dashboard_order_return')
                && in_array($order_ax->get_status(), [1,'delivered'], true)) {?>
        <button onclick="send_update_status(<?php echo $order_id?>,'return')" type="button" class="btn btn-danger float-right mr-1" >
                <i class="fas fa-times"></i> Trả hàng</button><?php }?>
        <?php if(current_user_can('admin_dashboard_order_cancel')
                && !in_array($order_ax->get_status(), [1,'delivered','return','cancelled'], true)) {?>
        <button onclick="send_update_status(<?php echo $order_id?>,'cancelled')" type="button" class="btn btn-danger float-right mr-1" >
                <i class="fas fa-times"></i> Hủy đơn</button><?php }?>