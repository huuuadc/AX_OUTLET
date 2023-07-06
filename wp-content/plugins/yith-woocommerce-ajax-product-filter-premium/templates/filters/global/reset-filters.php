<?php
/**
 * Reset filters button
 *
 * @author  YITH
 * @package YITH WooCommerce Ajax Product Filter
 * @version 4.0.0
 */

/**
 * Variables available for this template:
 *
 * @var $preset YITH_WCAN_Preset
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly
?>

<button class="btn btn-primary yith-wcan-reset-filters reset-filters">
	<?php echo esc_html( apply_filters( 'yith_wcan_filter_button', _x( 'Reset filters', '[FRONTEND] Reset button for preset shortcode', 'yith-woocommerce-ajax-navigation' ) ) ); ?>
</button>
