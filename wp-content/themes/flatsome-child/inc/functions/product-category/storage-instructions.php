<?php
/********************************************Add a custom filed in the categoy*************************/
add_action( 'init', 'wpm_product_cat_register_meta' );
/**
 * Register storage_instructions product_cat meta.
 *
 * Register the storage_instructions metabox for WooCommerce product categories.
 *
 */
function wpm_product_cat_register_meta() {
    register_meta( 'term', 'storage_instructions', 'wpm_sanitize_storage_instructions' );
}
/**
 * Sanitize the storage_instructions custom meta field.
 *
 * @param  string $storage_instructions The existing storage_instructions field.
 * @return string          The sanitized storage_instructions field
 */
function wpm_sanitize_storage_instructions( $storage_instructions ) {
    return wp_kses_post( $storage_instructions );
}

add_action( 'product_cat_add_form_fields', 'wpm_product_cat_add_storage_instructions_meta' );
/**
 * Add a storage_instructions metabox to the Add New Product Category page.
 *
 * For adding a storage_instructions metabox to the WordPress admin when
 * creating new product categories in WooCommerce.
 *
 */
function wpm_product_cat_add_storage_instructions_meta() {
    wp_nonce_field( basename( __FILE__ ), 'wpm_product_cat_storage_instructions_nonce' );
    ?>
    <div class="form-field">
        <label for="wpm-product-cat-storage_instructions"><?php esc_html_e( 'Hướng dẫn bảo quản', 'wpm' ); ?></label>
        <textarea name="wpm-product-cat-storage_instructions" id="wpm-product-cat-storage_instructions" rows="3"></textarea>
    </div>
    <?php
}
add_action( 'product_cat_edit_form_fields', 'wpm_product_cat_edit_storage_instructions_meta' );
/**
 * Add a storage_instructions metabox to the Edit Product Category page.
 *
 * For adding a storage_instructions metabox to the WordPress admin when
 * editing an existing product category in WooCommerce.
 *
 * @param  object $term The existing term object.
 */
function wpm_product_cat_edit_storage_instructions_meta( $term ) {
    $product_cat_storage_instructions = get_term_meta( $term->term_id, 'storage_instructions', true );
    if ( ! $product_cat_storage_instructions ) {
        $product_cat_storage_instructions = '';
    }
    $settings = array(
        'textarea_name' => 'wpm-product-cat-storage_instructions',
        'media_buttons' => false,
        'textarea_rows' => get_option('default_post_edit_rows', 10),
        'quicktags'     => false,
        'tinymce'       => array(
            'toolbar1'      => 'bold,italic,underline,|,bullist,numlist,|,separator,alignleft,aligncenter,alignright,|,separator,link,unlink,undo,redo',
            'toolbar2'      => '',
            'toolbar3'      => '',
        ),
    );
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="wpm-product-cat-storage_instructions"><?php esc_html_e( 'Hướng dẫn bảo quản', 'wpm' ); ?></label></th>
        <td>
            <?php wp_nonce_field( basename( __FILE__ ), 'wpm_product_cat_storage_instructions_nonce' ); ?>
            <?php wp_editor( wpm_sanitize_storage_instructions( $product_cat_storage_instructions ), 'product_cat_storage_instructions', $settings ); ?>
        </td>
    </tr>
    <?php
}
add_action( 'create_product_cat', 'wpm_product_cat_storage_instructions_meta_save' );
add_action( 'edit_product_cat', 'wpm_product_cat_storage_instructions_meta_save' );
/**
 * Save Product Category storage_instructions meta.
 *
 * Save the product_cat storage_instructions meta POSTed from the
 * edit product_cat page or the add product_cat page.
 *
 * @param  int $term_id The term ID of the term to update.
 */
function wpm_product_cat_storage_instructions_meta_save( $term_id ) {
    if ( ! isset( $_POST['wpm_product_cat_storage_instructions_nonce'] ) || ! wp_verify_nonce( $_POST['wpm_product_cat_storage_instructions_nonce'], basename( __FILE__ ) ) ) {
        return;
    }
    $old_storage_instructions = get_term_meta( $term_id, 'storage_instructions', true );
    $new_storage_instructions = isset( $_POST['wpm-product-cat-storage_instructions'] ) ? $_POST['wpm-product-cat-storage_instructions'] : '';
    if ( $old_storage_instructions && '' === $new_storage_instructions ) {
        delete_term_meta( $term_id, 'storage_instructions' );
    } else if ( $old_storage_instructions !== $new_storage_instructions ) {
        update_term_meta(
            $term_id,
            'storage_instructions',
            wpm_sanitize_storage_instructions( $new_storage_instructions )
        );
    }
}