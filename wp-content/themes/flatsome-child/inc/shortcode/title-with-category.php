<?php

function ttit_add_element_ux_builder(){
    add_ux_builder_shortcode('title_with_cat', array(
        'name'      => __('Title With Category'),
        'category'  => __('Content'),
        'info'      => '{{ text }}',
        'wrap'      => false,
        'options' => array(
            'cat_ids_main' => array(
                'type' => 'select',
                'heading' => 'Main category',
                'param_name' => 'ids1',
                'config' => array(
                    'multiple' => false,
                    'placeholder' => 'Select...',
                    'termSelect' => array(
                        'post_type' => 'product_cat',
                        'taxonomies' => 'product_cat'
                    )
                )
            ),
            'ttit_cat_ids' => array(
                'type' => 'select',
                'heading' => 'Sub categories',
                'param_name' => 'ids',
                'config' => array(
                    'multiple' => true,
                    'placeholder' => 'Select...',
                    'termSelect' => array(
                        'post_type' => 'product_cat',
                        'taxonomies' => 'product_cat'
                    )
                )
            ),
            'text' => array(
                'type'       => 'textfield',
                'heading'    => 'Custom category title',
                'default'    => 'Category title',
            ),
            'link' => array(
                'type'    => 'textfield',
                'heading' => 'Custom category link',
                'default' => '',
            ),
            'tag_name' => array(
                'type'    => 'select',
                'heading' => 'Tag',
                'default' => 'h3',
                'options' => array(
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                ),
            ),
        ),
    ));
}
add_action('ux_builder_setup', 'ttit_add_element_ux_builder');
function title_with_cat_shortcode( $atts, $content = null ){
    extract( shortcode_atts( array(
        '_id' => 'title-'.rand(),
        'class' => '',
        'visibility' => '',
        'text' => 'Category Title',
        'tag_name' => 'h3',
        'link' => '',
        'target' => '',
    ), $atts ) );
    $classes = array('title-menu-category');

    if ( $class ) $classes[] = $class;
    if ( $visibility ) $classes[] = $visibility;

    $classes = implode(' ', $classes);

    if ( isset( $atts[ 'cat_ids_main' ] ) ) {
        $ids1 = explode( ',', $atts[ 'cat_ids_main' ] );
        $ids1 = array_map( 'trim', $ids1 );
        $parent = '';
        $orderby = 'include';
    } else {
        $ids1 = array();
    }

    if ( isset( $atts[ 'ttit_cat_ids' ] ) ) {
        $ids = explode( ',', $atts[ 'ttit_cat_ids' ] );
        $ids = array_map( 'trim', $ids );
        $parent = '';
        $orderby = 'include';
    } else {
        $ids = array();
    }
    $args1 = array(
        'taxonomy' => 'product_cat',
        'include'    => $ids1,
        'pad_counts' => true,
        'child_of'   => 0,
    );
    $args = array(
        'taxonomy' => 'product_cat',
        'include'    => $ids,
        'pad_counts' => true,
        'child_of'   => 0,
    );

    $link_output = $text;
    $category_main = get_terms($args1);
    if($category_main){
        foreach ( $category_main as $cate_main ) {
            $link_output = '<a href="'.get_term_link( $cate_main ).'" title="'.$cate_main->name.'">'.$cate_main->name.'</a>';
        }
    }else{
        if($link) $link_output = '<a href="'.$link.'" target="'.$target.'">'.$text.'</a>';
    }

    $product_categories = get_terms( $args );
    $html_show_cat = '';
    if ( $product_categories ) {
        foreach ( $product_categories as $category ) {
            $term_link = get_term_link( $category );
            $thumbnail_id = get_woocommerce_term_meta( $category->term_id, 'thumbnail_id', true  );
            if ( $thumbnail_id ) {
                $image = wp_get_attachment_image_src( $thumbnail_id, $thumbnail_size);
                $image = $image[0];
            } else {
                $image = wc_placeholder_img_src();
            }
            $html_show_cat .= '<div class="ux-menu-link flex menu-item"><a href="'.$term_link.'" class="ux-menu-link__link flex"><i class="ux-menu-link__icon text-center icon-angle-right"></i><span class="ux-menu-link__text">'.$category->name.'</span></a></div>';
        }
    }
    return '<div class="'.$classes.'"><'.$tag_name.'>'.$link_output.'</'.$tag_name.'>'.'
                <div class="ux-menu stack stack-col justify-start">'.$html_show_cat.'</div>
            </div>';
}
add_shortcode('title_with_cat', 'title_with_cat_shortcode');