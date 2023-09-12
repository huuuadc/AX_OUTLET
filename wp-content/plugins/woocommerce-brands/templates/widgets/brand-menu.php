<?php
/**
 * Show brand menu
 */
?>
<ul class="brand_menu">
	<?php foreach ( $brands as $index => $brand ) :
		?>
		<li>
            <a href="<?php echo esc_url( get_term_link( $brand->slug, 'brand' ) ); ?>" title="<?php echo esc_attr( $brand->name ); ?>"><?php echo $brand->name; ?></a>
        </li>
	<?php endforeach; ?>
</ul>