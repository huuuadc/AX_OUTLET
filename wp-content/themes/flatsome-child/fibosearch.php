<?php

add_filter( 'dgwt/wcas/tnt/search_results/suggestion/product', function ( $data, $suggestion ) {
    if ( ! empty( $suggestion->meta['brand'] ) ) {
        $data['title_before'] = '<span class="search__result--brand">' . $suggestion->meta['brand'].'</span>';
    }
    return $data;
}, 10, 2 );
