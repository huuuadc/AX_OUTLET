<?php
/**
 * Order details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.8.0
 */

defined( 'ABSPATH' ) || exit;

$order = wc_get_order( $order_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

if ( ! $order ) {
	return;
}

$order_items           = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$show_purchase_note    = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
$downloads             = $order->get_downloadable_items();
$show_downloads        = $order->has_downloadable_item() && $order->is_download_permitted();

if ( $show_downloads ) {
	wc_get_template(
		'order/order-downloads.php',
		array(
			'downloads'  => $downloads,
			'show_title' => true,
		)
	);
}
?>
<section class="woocommerce-order-details">
	<?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>
    <style>
        .cart-container .page__title--cart, .order_details .sort_code, .order_details .iban, .order_details .bic {
            display: none !important;
        }
        .cart-container .woocommerce {
            width: 730px;
            max-width: 100%;
            margin: 0 auto;
            border: solid 1px #ececec;
            padding: 25px 30px;
            border-radius: 4px;
            position: relative;
            background-color: #f8f8f8;
            text-align:center;
        }
        .cart-container .woocommerce .qrcode_caption {
            border-top: solid 1px #ddd;
            padding-top: 25px;
        }
        .cart-container .woocommerce > .row .large-5 {
            margin-left: auto;
            margin-right: auto;
        }
        .cart-container .woocommerce .is-well {
            margin-left: auto !important;
            margin-right: auto !important;
            margin-top: 0;
            border: none;
            background-color: transparent;
            padding: 0;
        }
        .cart-container .woocommerce .is-well ul {
            margin-bottom: 0;
        }
        .cart-container .woocommerce > .row .large-7,
        .cart-container .woocommerce > .row .large-5 {
            width: 100%;
            max-width: 100%;
            flex-basis: 100%;
            padding-bottom: 0;
        }
        @media screen and (max-width: 849px) {
            .cart-container .woocommerce {
                padding: 20px 25px 0 25px;
            }
            .cart-container .woocommerce > .row .large-7,
            .cart-container .woocommerce > .row .large-5 {
                padding: 0;
                margin-left: 10px;
                margin-right: 10px;
            }
            .cart-container .woocommerce > .row .large-7 {
                margin-bottom: 20px;
                border-bottom: solid 1px #ddd;
            }
            .cart-container .woocommerce:after {
                display: none;
            }
            .cart-container .woocommerce .is-well {
                padding: 0 0 20px 0 !important;
            }
        }
    </style>

	<h2 class="woocommerce-order-details__title"><?php esc_html_e( 'Order details', 'woocommerce' ); ?></h2>

	<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">

		<thead>
			<tr>
				<th class="woocommerce-table__product-name product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
				<th class="woocommerce-table__product-table product-total"><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
			</tr>
		</thead>

		<tbody>
			<?php
			do_action( 'woocommerce_order_details_before_order_table_items', $order );

			foreach ( $order_items as $item_id => $item ) {
				$product = $item->get_product();

				wc_get_template(
					'order/order-details-item.php',
					array(
						'order'              => $order,
						'item_id'            => $item_id,
						'item'               => $item,
						'show_purchase_note' => $show_purchase_note,
						'purchase_note'      => $product ? $product->get_purchase_note() : '',
						'product'            => $product,
					)
				);
			}

			do_action( 'woocommerce_order_details_after_order_table_items', $order );
			?>
		</tbody>

		<tfoot>
			<?php
			foreach ( $order->get_order_item_totals() as $key => $total ) {
				?>
					<tr>
						<th scope="row"><?php echo esc_html( $total['label'] ); ?></th>
						<td><?php echo wp_kses_post( $total['value'] ); ?></td>
					</tr>
					<?php
			}
			?>
			<?php if ( $order->get_customer_note() ) : ?>
				<tr>
					<th><?php esc_html_e( 'Note:', 'woocommerce' ); ?></th>
					<td><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
				</tr>
			<?php endif; ?>
		</tfoot>
	</table>

	<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>
</section>

<?php
/**
 * Action hook fired after the order details.
 *
 * @since 4.4.0
 * @param WC_Order $order Order data.
 */
do_action( 'woocommerce_after_order_details', $order );

if ( $show_customer_details ) {
	wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) );
}
