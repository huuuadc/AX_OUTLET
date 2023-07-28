<?php

add_filter( 'dgwt/wcas/indexer/taxonomies', function ( $taxonomies ) {
    $taxonomies[] = array(
        'taxonomy'      => 'brand',
        'labels'        => array(
            'name'          => 'Thương hiệu',
            'singular_name' => 'Thương hiệu',
        ),
        'image_support' => true,
    );
    return $taxonomies;
});
add_filter( 'dgwt/wcas/term/thumbnail_src', function ( $src, $term_id, $size, $term ) {
    if ( $term->getTaxonomy() !== 'brand' ) {
        return $src;
    }
    return (string) get_field( 'brand_image_url', $term->getTermObject() );
}, 10, 4 );

add_filter( 'dgwt/wcas/tnt/indexer/readable/product/data', function ( $data, $product_id, $product ) {
    $terms = get_the_terms( $product_id, 'brand' );
    if(count($terms)>0) {
        foreach($terms as $brand) {
            $data['meta']['brand'] = $brand->name;
        }
    }
    return $data;
}, 10, 3 );

/*add_filter( 'dgwt/wcas/search_query/args', function ( $args ) {
    if ( current_user_can( 'manage_options' ) ) {
        $args['post_status'] = [ 'publish', 'private' ];
    }
    return $args;
});*/