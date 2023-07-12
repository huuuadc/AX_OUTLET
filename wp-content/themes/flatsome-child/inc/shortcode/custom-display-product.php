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

// add sale badge html beside price
add_filter( 'woocommerce_get_price_suffix', 'add_price_suffix_sale', 9999, 4 );
function add_price_suffix_sale( $html, $product ) {
    if ( ! is_admin() && is_object( $product ) && $product->is_on_sale() ) {
        $html .= wc_get_template_html( 'single-product/sale-flash.php' );
    }
    return $html;
}

/* add sale label product */
add_action( 'woocommerce_before_shop_loop_item', 'add_label_sale', 10 );
function add_label_sale( $product ) {
    global $product;
    if ( ! is_admin() && is_object( $product ) && $product->is_on_sale() ) {
        echo '<span class="sale__label">New to sale</span>';
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
    woocommerce_catalog_ordering();
    echo '</div></div>';
}
