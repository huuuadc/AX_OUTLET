<?php

use Wdr\App\Controllers;

global $wpdb;

$args = array(
    'status'   => array(  'publish' ),
    'limit'    => -1,
    'page'     => 1,
    'orderby'  => array(
        'ID' => 'ASC',
    ),
);

$products = wc_get_products( $args );


?>

<div class="card collapsed-card">
    <div class="card-header">
       Giá sản phẩm
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
                <th>Mã sp</th>
                <th>Offline Id</th>
                <th>Tên sp</th>
                <th class="text-right">Giá gốc</th>
                <th class="text-right">Giá giảm</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $product) :

                $manage_dis = new Controllers\ManageDiscount();
                $product_detail_discount = $manage_dis->calculateInitialAndDiscountedPrice($product,1);
                $full_price = $product->get_price();
                $mark_down_price = $product->get_price();
                if (isset($product_detail_discount['initial_price'])
                    && isset($product_detail_discount['discounted_price'])
                    && $product_detail_discount['initial_price'] > 0
                    && $product_detail_discount['discounted_price'] > 0){
                    $full_price = $product_detail_discount['initial_price'];
                    $mark_down_price =$product_detail_discount['discounted_price'];
                }

                ?>
            <tr>
                <td><?php echo $product->get_id() ?></td>
                <td><?php echo $product->get_meta('offline_id') ?></td>
                <td><?php echo $product->get_name() ?></td>
                <td class="text-right"><?php echo $full_price ?></td>
                <td class="text-right"><?php echo $mark_down_price ?></td>
            </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <div class="card-footer"></div>
</div>