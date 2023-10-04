<?php
/**
 * Active filters labels
 *
 * @author  YITH
 * @package YITH WooCommerce Ajax Product Filter
 * @version 4.0.0
 */

/**
 * Variables available for this template:
 *
 * @var $active_filters array
 * @var $show_titles    bool
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly
?>

<?php if ( ! empty( $active_filters ) ) : ?>
	<div class="yith-wcan-active-filters <?php echo ! $show_titles ? 'no-titles' : ''; ?> <?php echo 'custom' === yith_wcan_get_option( 'yith_wcan_filters_style', 'default' ) ? 'custom-style' : ''; ?>">

		<?php do_action( 'yith_wcan_before_active_filters' ); ?>

		<?php if ( ! empty( $labels_heading ) && ! empty( $active_filters ) ) : ?>
            <?php
            $label_active = esc_html( $labels_heading );
            $label_active = str_replace('Active filters','Lọc theo',$label_active);
            ?>
			<h4 class="filter-title-active"><?php echo $label_active; ?></h4>
		<?php endif; ?>

		<?php foreach ( $active_filters as $filter => $options ) : ?>
			<?php
			if ( empty( $options['values'] ) ) :
				continue;
			endif;
			?>
			<div class="active-filter">
				<?php if ( $show_titles ) : ?>
                    <?php
                    $label = esc_html( $options['label'] );
                    $label = str_replace('Sản phẩm','',$label);
                    $label = str_replace('Color','Màu sắc',$label);
                    $label = str_replace('Size','Kích thước',$label);
                    $label = str_replace('Order by','Sắp xếp',$label);
                    $label = str_replace('Sort by popularity','Mức độ phổ biến',$label);
                    $label = str_replace('Sort by latest','Mới nhất',$label);
                    $label = str_replace('Price:','Giá',$label);
                    ?>
					<b><?php echo $label; ?>:</b>
				<?php endif; ?>
				<?php foreach ( $options['values'] as $value ) : ?>
					<span class="active-filter-label" data-filters="<?php echo esc_attr( wp_json_encode( $value['query_vars'] ) ); ?>">
                        <?php
                            $lb_trans = wp_kses_post( $value['label'] );
                            $lb_trans = str_replace('beige','Xám tro',$lb_trans);
                            $lb_trans = str_replace('Black','Đen',$lb_trans);
                            $lb_trans = str_replace('black','Đen',$lb_trans);
                            $lb_trans = str_replace('blue','Xanh dương',$lb_trans);
                            $lb_trans = str_replace('gold','Hoàn kim',$lb_trans);
                            $lb_trans = str_replace('green','Xanh lá',$lb_trans);
                            $lb_trans = str_replace('grey','Xám',$lb_trans);
                            $lb_trans = str_replace('orange','Cam',$lb_trans);
                            $lb_trans = str_replace('pink','Hồng',$lb_trans);
                            $lb_trans = str_replace('purple','Tím',$lb_trans);
                            $lb_trans = str_replace('red','Đỏ',$lb_trans);
                            $lb_trans = str_replace('silver','Bạc',$lb_trans);
                            $lb_trans = str_replace('white','Trắng',$lb_trans);
                            $lb_trans = str_replace('yellow','Vàng',$lb_trans);
                        ?>
						<?php echo $lb_trans; ?>
					</span>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>

		<?php do_action( 'yith_wcan_after_active_filters' ); ?>

	</div>
<?php endif; ?>
