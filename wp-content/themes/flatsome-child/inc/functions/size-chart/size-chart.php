<?php

add_action( 'acf/include_fields', function() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group( array(
        'key' => 'group_64ed9b4c5647e',
        'title' => 'Rules',
        'fields' => array(
            array(
                'key' => 'field_64ed9b4d6845d',
                'label' => 'Brand',
                'name' => 'brand',
                'aria-label' => '',
                'type' => 'taxonomy',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '33',
                    'class' => '',
                    'id' => '',
                ),
                'taxonomy' => 'brand',
                'add_term' => 0,
                'save_terms' => 1,
                'load_terms' => 1,
                'return_format' => 'id',
                'field_type' => 'select',
                'allow_null' => 1,
                'multiple' => 0,
            ),
            array(
                'key' => 'field_64ed9d29d9ab2',
                'label' => 'Gender',
                'name' => 'gender',
                'aria-label' => '',
                'type' => 'taxonomy',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_64ed9b4d6845d',
                            'operator' => '!=empty',
                        ),
                    ),
                ),
                'wrapper' => array(
                    'width' => '33',
                    'class' => '',
                    'id' => '',
                ),
                'taxonomy' => 'gender',
                'add_term' => 0,
                'save_terms' => 1,
                'load_terms' => 1,
                'return_format' => 'id',
                'field_type' => 'select',
                'allow_null' => 1,
                'multiple' => 0,
            ),
            array(
                'key' => 'field_64ed9cbe2b075',
                'label' => 'Category',
                'name' => 'category',
                'aria-label' => '',
                'type' => 'taxonomy',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_64ed9d29d9ab2',
                            'operator' => '!=empty',
                        ),
                    ),
                ),
                'wrapper' => array(
                    'width' => '33',
                    'class' => '',
                    'id' => '',
                ),
                'taxonomy' => 'product_cat',
                'add_term' => 0,
                'save_terms' => 1,
                'load_terms' => 1,
                'return_format' => 'id',
                'field_type' => 'select',
                'allow_null' => 1,
                'multiple' => 0,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'size-chart',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
    ) );
} );

add_action( 'init', function() {
    register_post_type( 'size-chart', array(
        'labels' => array(
            'name' => 'Size Charts',
            'singular_name' => 'Size Chart',
            'menu_name' => 'Size Charts',
            'all_items' => 'All Size Charts',
            'edit_item' => 'Edit Size Chart',
            'view_item' => 'View Size Chart',
            'view_items' => 'View Size Charts',
            'add_new_item' => 'Add New Size Chart',
            'new_item' => 'New Size Chart',
            'parent_item_colon' => 'Parent Size Chart:',
            'search_items' => 'Search Size Charts',
            'not_found' => 'No size charts found',
            'not_found_in_trash' => 'No size charts found in Trash',
            'archives' => 'Size Chart Archives',
            'attributes' => 'Size Chart Attributes',
            'insert_into_item' => 'Insert into size chart',
            'uploaded_to_this_item' => 'Uploaded to this size chart',
            'filter_items_list' => 'Filter size charts list',
            'filter_by_date' => 'Filter size charts by date',
            'items_list_navigation' => 'Size Charts list navigation',
            'items_list' => 'Size Charts list',
            'item_published' => 'Size Chart published.',
            'item_published_privately' => 'Size Chart published privately.',
            'item_reverted_to_draft' => 'Size Chart reverted to draft.',
            'item_scheduled' => 'Size Chart scheduled.',
            'item_updated' => 'Size Chart updated.',
            'item_link' => 'Size Chart Link',
            'item_link_description' => 'A link to a size chart.',
        ),
        'public' => true,
        'show_in_rest' => true,
        'supports' => array(
            0 => 'title',
            1 => 'editor',
        ),
        'delete_with_user' => false,
    ) );
} );