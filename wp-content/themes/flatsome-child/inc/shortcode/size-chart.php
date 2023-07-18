<?php

/* Display size chart */
add_action( 'woocommerce_after_add_to_cart_button', 'add_size_chart', 10 );
function add_size_chart(){
    global $post;

    $cate = get_queried_object();
    $terms = get_the_terms( $post->ID, 'product_cat' );

    foreach ( $terms as $term ) {
        $parent = $term->parent;
        if($parent==0) {
            $term_id = $term->term_id;
            $size_chart = get_term_meta( $term_id, 'wh_size_chart', true );
            echo do_shortcode('[button text="Hướng dẫn chọn kích thước" class="btn--size_chart" link="#size_chart"][lightbox id="size_chart" width="1440px" padding="20px"]<img src="'.$size_chart.'" width="100%" height="auto"/>[/lightbox]');
            break;
        }
    }
}

?>
