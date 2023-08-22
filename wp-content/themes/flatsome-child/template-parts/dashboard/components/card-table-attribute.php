<?php

global $wpdb;

$sql = "select * from ".$wpdb->prefix."woocommerce_attribute_taxonomies ";
$attributes = $wpdb->get_results($sql);

?>

<div class="card collapsed-card">
    <div class="card-header">
       Thuộc tính
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped dataTable dtr-inline table_simple">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Type</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($attributes as $att) :?>
            <tr>
                <td><?php echo $att->attribute_id ?></td>
                <td><?php echo $att->attribute_name ?></td>
                <td><?php echo $att->attribute_type ?></td>
            </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <div class="card-footer"></div>
</div>