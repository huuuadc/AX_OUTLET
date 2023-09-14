<?php

/**
 * Get all order channel statuses.
 *
 * @since 2.2
 * @used-by OMS_ORDER::set_order_type()
 * @return array
 */
function oms_get_channel_statuses() {
    return  array(
        'website'       => _x( 'Website', 'Channel type', 'oms' ),
        'lazada'        => _x( 'Lazada', 'Channel type', 'oms' ),
        'shopee'        => _x( 'Shopee', 'Channel type', 'oms' ),
        'tiktok'        => _x( 'Tik tok', 'Channel type', 'oms' ),
        'tiki'          => _x( 'Tiki', 'Channel type', 'oms' ),
        'toout'          => _x( 'TO Out', 'Channel type', 'oms' ),
    );
}




add_action('woocommerce_admin_order_data_after_order_details','add_meta_box_order_channel_type');

function add_meta_box_order_channel_type(WC_Order $order){
    $oms_order = new OMS_ORDER($order->get_id())
    ?>
    <p class="form-field form-field-wide wc-order-status">
        <label for="channel_status">
            <?php
            esc_html_e( 'Chanel type:', 'woocommerce' );
            ?>
        </label>
        <select id="channel_status" name="channel_status" class="wc-enhanced-select">
            <?php
            $statuses = oms_get_channel_statuses();
            foreach ( $statuses as $status => $status_name ) {
                echo '<option value="' . esc_attr( $status ) . '" ' . selected( $status,  $oms_order->get_order_type(), false ) . '>' . esc_html( $status_name ) . '</option>';
            }
            ?>
        </select>
    </p>
    <?php
}

add_action('woocommerce_process_shop_order_meta','save_meta_box_channel_type');

function save_meta_box_channel_type($post_id){
    $oms_order = new OMS_ORDER($post_id);
    if (isset($_POST['channel_status'])) $oms_order->set_order_type($_POST['channel_status']) ;
    return true;
}