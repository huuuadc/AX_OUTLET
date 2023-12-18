<?php


add_filter('woocommerce_register_shop_order_post_statuses','register_order_status',10,1);

/**
 * @param $arg_status_wc
 * @return array
 */

function register_order_status($arg_status_wc): array
{

    $arg_register_status = array(
                            'wc-reject'    => array(
                                'label'                     => _x( 'Store reject', 'Order status', 'woocommerce' ),
                                'public'                    => false,
                                'exclude_from_search'       => false,
                                'show_in_admin_all_list'    => true,
                                'show_in_admin_status_list' => true,
                                /* translators: %s: number of orders */
                                'label_count'               => _n_noop( 'Store reject <span class="count">(%s)</span>', 'Store reject <span class="count">(%s)</span>', 'woocommerce' ),
                            ),
                            'wc-confirm'    => array(
                                'label'                     => _x( 'Store confirm', 'Order status', 'woocommerce' ),
                                'public'                    => false,
                                'exclude_from_search'       => false,
                                'show_in_admin_all_list'    => true,
                                'show_in_admin_status_list' => true,
                                /* translators: %s: number of orders */
                                'label_count'               => _n_noop( 'Store confirm <span class="count">(%s)</span>', 'Store  confirm <span class="count">(%s)</span>', 'woocommerce' ),
                            ),
                            'wc-request'    => array(
                                'label'                     => _x( 'Store request shipment', 'Order status', 'woocommerce' ),
                                'public'                    => false,
                                'exclude_from_search'       => false,
                                'show_in_admin_all_list'    => true,
                                'show_in_admin_status_list' => true,
                                /* translators: %s: number of orders */
                                'label_count'               => _n_noop( 'Store request shipment <span class="count">(%s)</span>', 'Store  request shipment <span class="count">(%s)</span>', 'woocommerce' ),
                            ),
                            'wc-shipping'    => array(
                                'label'                     => _x( 'Shipping', 'Order status', 'woocommerce' ),
                                'public'                    => false,
                                'exclude_from_search'       => false,
                                'show_in_admin_all_list'    => true,
                                'show_in_admin_status_list' => true,
                                /* translators: %s: number of orders */
                                'label_count'               => _n_noop( 'Shipping <span class="count">(%s)</span>', 'Shipping <span class="count">(%s)</span>', 'woocommerce' ),
                            ),
                            'wc-delivered'    => array(
                                'label'                     => _x( 'Delivered', 'Order status', 'woocommerce' ),
                                'public'                    => false,
                                'exclude_from_search'       => false,
                                'show_in_admin_all_list'    => true,
                                'show_in_admin_status_list' => true,
                                /* translators: %s: number of orders */
                                'label_count'               => _n_noop( 'Delivered <span class="count">(%s)</span>', 'Delivered <span class="count">(%s)</span>', 'woocommerce' ),
                            ),
                            'wc-delivery-failed'    => array(
                                'label'                     => _x( 'Delivery failed', 'Order status', 'woocommerce' ),
                                'public'                    => false,
                                'exclude_from_search'       => false,
                                'show_in_admin_all_list'    => true,
                                'show_in_admin_status_list' => true,
                                /* translators: %s: number of orders */
                                'label_count'               => _n_noop( 'Delivery failed <span class="count">(%s)</span>', 'Delivery failed <span class="count">(%s)</span>', 'woocommerce' ),
                            ),
                            'wc-confirm-goods'    => array(
                                'label'                     => _x( 'Confirm goods', 'Order status', 'woocommerce' ),
                                'public'                    => false,
                                'exclude_from_search'       => false,
                                'show_in_admin_all_list'    => true,
                                'show_in_admin_status_list' => true,
                                /* translators: %s: number of orders */
                                'label_count'               => _n_noop( 'Confirm goods <span class="count">(%s)</span>', 'Confirm goods <span class="count">(%s)</span>', 'woocommerce' ),
                            ),
                             'wc-return'    => array(
                                'label'                     => _x( 'Return', 'Order status', 'woocommerce' ),
                                'public'                    => false,
                                'exclude_from_search'       => false,
                                'show_in_admin_all_list'    => true,
                                'show_in_admin_status_list' => true,
                                /* translators: %s: number of orders */
                                'label_count'               => _n_noop( 'Return <span class="count">(%s)</span>', 'Return <span class="count">(%s)</span>', 'woocommerce' ),
                            ),
        );

    return array_merge($arg_status_wc, $arg_register_status);
}

add_filter('wc_order_statuses','register_order_statuses');

/**
 * @param $order_statuses
 * @return array
 */

function register_order_statuses($order_statuses): array
{
    $register_order_statuses = array(
        'wc-reject'    => _x( 'Store reject', 'Order status', 'woocommerce' ),
        'wc-confirm' => _x( 'Store confirm', 'Order status', 'woocommerce' ),
        'wc-request'    => _x( 'Store request shipment', 'Order status', 'woocommerce' ),
        'wc-shipping'  => _x( 'Shipping', 'Order status', 'woocommerce' ),
        'wc-delivered'  => _x( 'Delivered', 'Order status', 'woocommerce' ),
        'wc-delivery-failed'   => _x( 'Delivery failed', 'Order status', 'woocommerce' ),
        'wc-confirm-goods'     => _x( 'Confirm goods', 'Order status', 'woocommerce' ),
        'wc-return'     => _x( 'Return', 'Order status', 'woocommerce' ),
    );
    return array_merge($order_statuses,$register_order_statuses);
}