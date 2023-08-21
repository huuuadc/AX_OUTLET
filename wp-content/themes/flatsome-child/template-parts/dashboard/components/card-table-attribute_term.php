<?php

global $wpdb;

$sql = "select wat.attribute_id, t.term_id, t.name, t.slug, tm.meta_key, tm.meta_value, tt.taxonomy
        from ".$wpdb->prefix."term_taxonomy AS tt
            inner join ".$wpdb->prefix."woocommerce_attribute_taxonomies wat ON tt.taxonomy = CONCAT('pa_', wat.attribute_name) 
                left join ".$wpdb->prefix."terms AS t ON tt.term_id = t.term_id
                    left join ".$wpdb->prefix."termmeta AS tm ON t.term_id = tm.term_id
        where tt.taxonomy LIKE 'pa_%'
        order by wat.attribute_id";

$att_terms = $wpdb->get_results($sql);

?>

<div class="card">
    <div class="card-header">
       Giá trị thuốc tính
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped dataTable dtr-inline table_simple">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Slug</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($att_terms as $att) :?>
            <tr>
                <td><?php echo $att->term_id ?></td>
                <td><?php echo $att->name ?></td>
                <td><?php echo $att->slug ?></td>
            </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <div class="card-footer"></div>
</div>