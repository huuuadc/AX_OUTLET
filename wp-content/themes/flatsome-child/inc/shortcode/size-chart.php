<?php

/* Display size chart */
//add_action( 'woocommerce_after_variations_table', 'add_size_chart', 10 );
add_action( 'woocommerce_before_add_to_cart_form', 'add_size_chart', 10 );
function add_size_chart(){
    global $post;

    $cate = get_queried_object();
    $terms = get_the_terms( $post->ID, 'product_cat' );

    foreach ( $terms as $term ) {
        $parent = $term->parent;
        if($parent==0) {
            $term_id = $term->term_id;
            $size_chart = get_term_meta( $term_id, 'wh_size_chart', true );
            if($size_chart){
                echo '<style>.mfp-close{display:none}.mfp-close.inside{display:block;}</style>';
                echo do_shortcode('[button text="Hướng dẫn chọn kích thước" class="btn--size_chart before_add_to_cart_form" link="#size_chart"][lightbox id="size_chart" width="1440px" padding="20px"]<button title="Close (Esc)" type="button" class="mfp-close inside"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button><img src="'.$size_chart.'" width="100%" height="auto"/>[/lightbox]');
            }
            break;
        }
    }
}

?>
