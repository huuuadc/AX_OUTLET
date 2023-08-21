<?php

global $wpdb;

$sql = "select t.term_id, t.name, t.slug, tm.meta_key, tm.meta_value, tt.parent
        from ".$wpdb->prefix."term_taxonomy AS tt
            left join ".$wpdb->prefix."terms AS t ON tt.term_id = t.term_id
                left join ".$wpdb->prefix."termmeta AS tm ON t.term_id = tm.term_id
        where tt.taxonomy = 'product_cat' and tm.meta_key = 'offline_id'
        order by tm.term_id";

$categories = $wpdb->get_results($sql);

?>

<div class="card">
    <div class="card-header">
       Danh mục
    </div>
    <div class="card-body">
        <table id="table_categories" class="table table-bordered table-striped dataTable dtr-inline table_simple">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Slug</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($categories as $category) :?>
            <tr>
                <td><?php echo $category->term_id ?></td>
                <td><?php echo $category->name ?></td>
                <td><?php echo $category->slug ?></td>
            </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <div class="card-footer"></div>
</div>