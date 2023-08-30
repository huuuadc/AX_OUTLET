<?php

/* Display size chart */
//add_action( 'woocommerce_after_variations_table', 'add_size_chart', 10 );
add_action( 'woocommerce_before_add_to_cart_form', 'add_size_chart_with_brand', 10 );
function add_size_chart_with_brand(){
    global $post;
    ob_start();

    $brands = wp_get_post_terms( $post->ID, 'brand' );
    $genders = wp_get_post_terms( $post->ID, 'gender' );

    $brand_id = $brands[0]->term_id;
    $gender_id = $genders[0]->term_id;

    $categories = get_the_terms( $post->ID, 'product_cat' );
    $categories_ids = array();
    foreach ( $categories as $category ) {
        $categories_ids[$category->term_id] = $category->term_id;
    }

    $args = array(
        'post_type'      => 'size-chart',
        'posts_per_page' => -1,
        'publish_status' => 'published',
        'tax_query' => array(
            array(
                'taxonomy' => 'brand',
                'field'     => 'id',
                'terms'     => $brand_id
            ),
            array(
                'taxonomy' => 'gender',
                'field'     => 'id',
                'terms'     => $gender_id
            ),
        ),
    );

    $query = new WP_Query($args);

    $result = null;
    if($query->have_posts()){
        while($query->have_posts()) : $query->the_post();
            $category_id = get_field('category');
            if(in_array($category_id,$categories_ids)){
                $result .= '<style>.mfp-close{display:none}.mfp-close.inside{display:block;}</style>';
                $result .= do_shortcode('[button text="Hướng dẫn chọn kích thước" class="btn--size_chart before_add_to_cart_form" link="#size_chart"][lightbox id="size_chart" width="1440px" padding="20px"]<button title="Close (Esc)" type="button" class="mfp-close inside"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>'.get_the_content().'[/lightbox]');;
                break;
            }
        endwhile;
        echo $result;
        wp_reset_postdata();
    }

    $content = ob_get_contents();
    ob_end_clean();
    echo $content;
}