<?php

global $wpdb;

$sql = "select t.term_id, t.name, t.slug 
        from ".$wpdb->prefix."term_taxonomy AS tt 
            left join ".$wpdb->prefix."terms AS t ON tt.term_id = t.term_id
            where tt.taxonomy = 'brand' ";

$brands = $wpdb->get_results($sql);

?>

<div class="card">
    <div class="card-header">
       Thương hiệu
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
            <?php foreach ($brands as $brand) :?>
            <tr>
                <td><?php echo $brand->term_id ?></td>
                <td><?php echo $brand->name ?></td>
                <td><?php echo $brand->slug ?></td>
            </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <div class="card-footer"></div>
</div>