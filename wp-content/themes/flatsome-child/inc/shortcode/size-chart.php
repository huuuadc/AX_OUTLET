<?php
/**
 * Register all shortcodes
 *
 * @return null
 */
add_action( 'woocommerce_after_add_to_cart_button', 'add_size_chart_with_brand', 10 );
function register_shortcodes() {
    add_shortcode( 'Size_Chart', 'add_size_chart_with_brand' );
}
add_action( 'init', 'register_shortcodes' );
/**
 * Produtos Shortcode Callback
 *
 * @param Array $atts
 *
 * @return string
 */
function add_size_chart_with_brand($atts){
    global $post;

    $atts = shortcode_atts( array(
        'product_id' => ''
    ), $atts );

    $product_id = $atts['product_id'] ? $atts['product_id'] : $post->ID;

    $brands = wp_get_post_terms( $product_id, 'brand' );
    $genders = wp_get_post_terms( $product_id, 'gender' );

    $brand_id = $brands[0]->term_id;
    $gender_id = $genders[0]->term_id;

    $categories = get_the_terms( $product_id, 'product_cat' );
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

    $loop = new WP_Query($args);

    $result = null;
    if($loop->have_posts()){
        while($loop->have_posts()) : $loop->the_post();
            $category_id = get_field('category');
            if(in_array($category_id,$categories_ids)){
                $result .= '<style>.mfp-close{display:none}.mfp-close.inside{display:block;}</style>';
                $result .= do_shortcode('[button text="Hướng dẫn chọn kích thước" class="btn--size_chart before_add_to_cart_form" link="#size_chart"][lightbox id="size_chart" width="1440px" padding="20px"]<button title="Close (Esc)" type="button" class="mfp-close inside"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>'.get_the_content().'[/lightbox]');;
                break;
            }
        endwhile;
        echo $result;
    }
    wp_reset_postdata();
}