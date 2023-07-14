<?php

add_action('woocommerce_single_product_summary', 'et_print_brands', 10);
add_action('woocommerce_before_shop_loop_item_title', 'et_print_brands', 10);
function et_print_brands(){
    global $post;
    $terms = wp_get_post_terms( $post->ID, 'brand' );
    if(count($terms)>0) {
        ?>
        <p class="product__brand">
            <?php
            foreach($terms as $brand) {
                ?>
                <a href="<?php echo get_term_link($brand); ?>">
                    <?php
                    echo $brand->name;
                    ?>
                </a>
                <?php
            }
            ?>
        </p>
        <?php
    }
}

/* add sale label product */
add_filter( 'woocommerce_get_price_suffix', 'add_label_sale', 9999, 10 );
function add_label_sale( $product ) {
    global $product;
    $discounted = apply_filters('advanced_woo_discount_rules_get_product_discount_price_from_custom_price', false, $product, 1, 0, 'all', true);
    if ($discounted) {
        $initial_price = $discounted['initial_price'];
        $discounted_price = $discounted['discounted_price'];
        $price_discount = ceil(($initial_price-$discounted_price)*100/$initial_price);
        echo '<span class="percent__label">-'.$price_discount.'%</span>';
    }
}

/* category page title */
add_action( 'woocommerce_before_shop_loop', 'add_text', 10 );
function add_text(){
    echo '<div class="category__page--title"><div class="category__page--title_content"><h1 class="title">';
        woocommerce_page_title();
    echo '</h1>';
    echo '<div class="count">'.wc_get_loop_prop( 'total' ). ' sản phẩm</div></div>';
    echo '<div class="category__sort">';
    //woocommerce_catalog_ordering();
    echo do_shortcode('[yith_wcan_filters slug="draft-preset-2"]');
    echo '</div></div>';
}
