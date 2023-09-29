<?php

use OMS\OMS_TO;

add_action( 'wp_ajax_transfer_order_import_product', 'transfer_order_import_product' );
add_action( 'wp_ajax_nopriv_transfer_order_import_product', 'transfer_order_import_product' );
function transfer_order_import_product()
{
    //Check have action and payload_action
    //payload action variant status ajax post
    if(!isset($_POST['action']) || !isset($_POST['payload_action']))
    {
        echo response(false,'Không tìm thấy hành động được gửi',[]);
        exit;
    }

    $payload_action = $_POST['payload_action'];

    //Check have post order_id
    if (!isset($_POST['data']) || count($_POST['data']) <= 0)
    {
        echo response(false,'Không có danh sách sản phẩm',[]);
        exit;
    }

    //
    //
    // Action add product transfer order
    //
    //

    if ($_POST['payload_action'] === 'transfer_order_import_product')
    {

        $data = (object)json_decode( json_encode( $_POST['data']));
        $transfer_id = $_POST['transfer_id'];
        $transfer_order = new OMS_TO($transfer_id);
        $data_error = [];
        foreach ($data as $item)
        {
            //Check product sku
            if (!isset($item->product_sku)) continue;
            //Check quantity
            if (!isset($item->quantity) || (int)$item->quantity <= 0 )
            {
                $data_error[] = $item->product_sku;
                continue;
            };

            $product_id = wc_get_product_id_by_sku($item->product_sku);

            if(!$product_id)
            {
                $data_error[] = $item->product_sku;
                continue;
            };
            $product    = wc_get_product($product_id);
            $transfer_order->add_product($product,$item->quantity);
        }
        wc_get_template('template-parts/dashboard/components/inventory/card-table-line.php',['id'=>$transfer_id]);

        if(count($data_error) <=0) exit;

        echo '
            <div class="card">
              <div class="card-header"><span class="badge badge-danger">Item lỗi</span></div>
              <div class="cart-body">
                    <ul>';

        foreach ($data_error as $value)
        {
             echo '<li>'.$value.'</li>';
        }

        echo '       </ul>
              </div>
            </div>';
        exit;

    }

}