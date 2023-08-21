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

<div class="card collapsed-card">
    <div class="card-header">
       Danh mục
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <table id="table_categories" class="table table-bordered table-striped dataTable dtr-inline table_simple">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Slug</th>
                <th>Parent ID</th>
                <th>Parent name</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($categories as $category) :
                $parent_name = (int)$category->parent != 0 ? get_term((int)$category->parent)->name : ''; ?>
            <tr>
                <td><?php echo $category->term_id ?></td>
                <td><?php echo $category->name ?></td>
                <td><?php echo $category->slug ?></td>
                <td><?php echo $category->parent ?></td>
                <td><?php echo  $parent_name?></td>
            </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <div class="card-footer"></div>
</div>