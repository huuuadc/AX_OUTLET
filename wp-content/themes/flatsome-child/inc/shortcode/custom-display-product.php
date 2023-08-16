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
// Display product brand in Cart and checkout pages
add_filter( 'woocommerce_cart_item_name', 'customizing_cart_item_name', 10, 3 );
//add_action( 'yith_wcwl_table_before_product_name', 'customizing_cart_item_name', 10 );
function customizing_cart_item_name( $product_name, $cart_item, $cart_item_key ) {
    $terms = wp_get_post_terms( $cart_item['product_id'], 'brand' );
    if(count($terms)>0) {
        foreach($terms as $brand) {
            return '<p class="product__brand" style="color:#999;"><a href="' . get_term_link($brand) . '" style="color:inherit;">' . $brand->name . '</a></p>'.$product_name;
        }
    }else{
        return $product_name;
    }
}

// Display product brand in order pages and email notification
add_filter( 'woocommerce_order_item_name', 'customizing_order_item_name', 10, 2 );
function customizing_order_item_name( $product_name, $item ) {
    $terms = wp_get_post_terms( $item->get_product_id(), 'brand' );
    if(count($terms)>0) {
        foreach($terms as $brand) {
            return '<p class="product__brand" style="color:"><a href="' . get_term_link($brand) . '">' . $brand->name . '</a></p>'.$product_name;
        }
    }else{
        return $product_name;
    }
}

/* add sale label product */
add_filter( 'woocommerce_get_price_html', 'add_label_sale', 9999, 10 );
function add_label_sale( $price, $product) {
    global $product;
    $discounted = apply_filters('advanced_woo_discount_rules_get_product_discount_price_from_custom_price', false, $product, 1, 0, 'all', true);
    if ($discounted) {
        $regular_price = (float) $discounted['initial_price'];
        $sale_price = (float) $discounted['discounted_price'];

        $precision = 1;
        $saving_percentage = round( 100 - ( $sale_price / $regular_price * 100 ), $precision ) . '%';
        $price .= sprintf( __('<span class="percent__label">-%s</span>', 'woocommerce' ), $saving_percentage );
    }
    return $price;
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
