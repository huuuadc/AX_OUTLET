<?php

global $wpdb;

$sql = "select t.term_id, t.name, t.slug, tm.meta_key, tm.meta_value
        from ".$wpdb->prefix."term_taxonomy AS tt
            left join ".$wpdb->prefix."terms AS t ON tt.term_id = t.term_id
                left join ".$wpdb->prefix."termmeta AS tm ON t.term_id = tm.term_id
        where tt.taxonomy = 'gender'
        order by tm.meta_value";

$genders = $wpdb->get_results($sql);

?>

<div class="card">
    <div class="card-header">
       Giới tính
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
            <?php foreach ($genders as $gender) :?>
            <tr>
                <td><?php echo $gender->term_id ?></td>
                <td><?php echo $gender->name ?></td>
                <td><?php echo $gender->slug ?></td>
            </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <div class="card-footer"></div>
</div>