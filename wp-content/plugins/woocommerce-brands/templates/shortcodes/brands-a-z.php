<?php
/**
 * Brand A-Z listing
 *
 * @usedby [brand_list]
 */
?>
<div id="brands_a_z">

	<ul class="brands_index">
		<?php
		foreach ( $index as $i ) {
			if ( isset( $brands[ $i ] ) ) {
				echo '<li><a href="#brands-' . esc_attr( $i ) . '">' . esc_html( $i ) . '</a></li>';
			} elseif ( $show_empty ) {
				echo '<li><span>' . esc_html( $i ) . '</span></li>';
			}
		}
		?>
	</ul>

	<?php foreach ( $index as $i ) if ( isset( $brands[ $i ] ) ) : ?>

		<h3 id="brands-<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $i ); ?></h3>

		<ul class="brands">
			<?php
			foreach ( $brands[ $i ] as $brand ) {
				printf(
					'<li><a href="%s">%s</a></li>',
					esc_url( get_term_link( $brand->slug, 'brand' ) ),
					esc_html( $brand->name )
				);
			}
			?>
		</ul>

		<?php if ( $show_top_links ) : ?>
			<a class="top" href="#brands_a_z"><?php esc_html_e( '&uarr; Top', 'woocommerce-brands' ); ?></a>
		<?php endif; ?>

	<?php endif; ?>

</div>
