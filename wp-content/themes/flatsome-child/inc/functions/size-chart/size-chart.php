<?php
add_action('product_cat_add_form_fields', 'wh_taxonomy_add_new_size_chart_field', 10, 1);
add_action('product_cat_edit_form_fields', 'wh_taxonomy_edit_size_chart_field', 10, 1);
function wh_taxonomy_add_new_size_chart_field() {
    ?>
    <div class="form-field">
        <label for="wh_size_chart"><?php _e('Size chart', 'wh'); ?></label>
        <input type="text" name="wh_size_chart" id="wh_size_chart">
        <p class="description"><?php _e('Đường dẫn hình ảnh size chart', 'wh'); ?></p>
    </div>
    <?php
}
function wh_taxonomy_edit_size_chart_field($term) {
    $term_id = $term->term_id;
    $wh_size_chart = get_term_meta($term_id, 'wh_size_chart', true);
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="wh_size_chart"><?php _e('Size chart', 'wh'); ?></label></th>
        <td>
            <input type="text" name="wh_size_chart" id="wh_size_chart" value="<?php echo esc_attr($wh_size_chart) ? esc_attr($wh_size_chart) : ''; ?>">
            <p class="description"><?php _e('Đường dẫn hình ảnh size chart', 'wh'); ?></p>
        </td>
    </tr>
    <?php
}
add_action('edited_product_cat', 'wh_save_taxonomy_custom_meta', 10, 1);
add_action('create_product_cat', 'wh_save_taxonomy_custom_meta', 10, 1);
// Save extra taxonomy fields callback function.
function wh_save_taxonomy_custom_meta($term_id) {
    $wh_size_chart = filter_input(INPUT_POST, 'wh_size_chart');
    update_term_meta($term_id, 'wh_size_chart', $wh_size_chart);
}