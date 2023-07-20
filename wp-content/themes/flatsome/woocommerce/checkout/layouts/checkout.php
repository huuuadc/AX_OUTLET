<?php
/**
 * Default checkout layout.
 *
 * @package          Flatsome/WooCommerce/Templates
 * @flatsome-version 3.16.0
 */

get_header();
global $post;
?>

<?php while ( have_posts() ) : the_post(); ?>

	<?php
	wc_get_template( 'checkout/header.php' );

	echo '<div class="cart-container container page-wrapper page-checkout">';
	echo '<h1 class="page__title--cart">'.$post->post_title.'</h1>';
	wc_print_notices();
	the_content();
	echo '</div>';
	?>

<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>
