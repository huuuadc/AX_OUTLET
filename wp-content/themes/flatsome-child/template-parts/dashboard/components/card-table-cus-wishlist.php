<?php

    $count = 0;

    $data_store = WC_Data_Store::load( 'wishlist-item' );
    $items = $data_store->query(
        array(
            'user_id'     => false,
            'session_id'  => false,
            'wishlist_id' => 'all',
        )
    );

    $list = [];

    foreach ( $items as $item ) {
        /**
         * Wishlist item
         *
         * @var $item YITH_WCWL_Wishlist_Item
         */
        $user = $item->get_user();

        if ( ! $user ) {
            continue;
        }

        $list[$user->user_login][]= $item->get_product_id();
    }

?>

<div class="card">
    <div class="card-header">
        Danh mục
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <table id="table_categories" class="table table-bordered table-striped dataTable dtr-inline table_simple">
            <thead>
            <tr>
                <th>Số TT</th>
                <th>Khách hàng</th>
                <th>Tên Tên khách hàng</th>
                <th>Danh sách sản phẩm</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($list as $key => $values) :$count++;
                $user = get_user_by('login',$key)
            ?>
                <tr>
                    <td><?php echo $count ?></td>
                    <td><?php echo $key ?></td>
                    <td><?php echo $user->display_name ?></td>
                    <td><?php
                        foreach ($values as $value){
                            $product = wc_get_product($value);
                            echo '<a href="'. get_permalink($product->get_id())  .'">';
                            echo $product->get_sku() .' - '.$product->get_id() . ' - ' . $product->get_name();
                            echo '</a>';
                            echo '<br>';
                        }
                        ?></td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <div class="card-footer"></div>
</div>