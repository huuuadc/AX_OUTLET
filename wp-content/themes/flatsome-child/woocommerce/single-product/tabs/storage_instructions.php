<?php
/**
 * Description tab
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/tabs/description.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

global $post;

$cate = get_queried_object();
$terms = get_the_terms( $post->ID, 'product_cat' );

foreach ( $terms as $term ) {
    $parent = $term->parent;
    if($parent==0) {
        $term_id = $term->term_id;
        $storage_instructions = get_term_meta( $term_id, 'storage_instructions', true );
        echo apply_filters( 'the_content', wp_kses_post( $storage_instructions ) );
    }
    break;
}
?>
