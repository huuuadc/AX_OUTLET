<?php

use OMS\Tiktok_Api;

function product_tiktok_sync():bool
{

    $tiktok_products = [];

    $tiktok_api = new Tiktok_Api();

    $flag = true;
    $flag_update = false;
    $next_page_token = '';

    while ($flag){

        $res = $tiktok_api->get_products_v2(1,$next_page_token);

        if(isset($res->products)){
            foreach ($res->products as $product){
                $flag_update = false;
                foreach ($product->skus as $key => $sku){
                    $product_id = wc_get_product_id_by_sku($sku->seller_sku);
                    $product_parent_id = wp_get_post_parent_id($product_id);
                    //Not found product on OMS continue
                    if($product == 0 && $product_parent_id == 0) {
                        write_log('Không tim thấy sản phẩm trên OMS');
                        write_log('Tiktok sku'.$sku->seller_sku);
                        write_log('Mã trên TIKTOK:' . $product->id);
                        continue;
                    };
                    if($product_parent_id == 0){
                        $product_oms = new WC_Product($product_id);
                        $remake_qty = get_qty_product_id_in_orders(
                            $product_id,
                            0,
                            [
                                'wc-reject',
                                'wc-confirm',
                                'wc-request',
                                'wc-shipping',
                                'wc-delivery-failed',
                                'wc-confirm-goods',
                                'wc-processing'
                            ]);
                    } else {
                        $product_oms = new WC_Product_Variation($product_id);
                        $remake_qty = get_qty_product_id_in_orders(
                            $product_parent_id,
                            $product_id,
                            [
                                'wc-reject',
                                'wc-confirm',
                                'wc-request',
                                'wc-shipping',
                                'wc-delivery-failed',
                                'wc-confirm-goods',
                                'wc-processing'
                            ]);
                    }

                    $qty_oms = $product_oms->get_stock_quantity() - $remake_qty;
                    $qty_oms = $qty_oms > 0 ? $qty_oms : 0;
                    if($qty_oms <> $sku->inventory[0]->quantity) {

                        write_log('Mã sản phẩm trên OMS' . $product_oms->get_id());
                        write_log('Số lượng trên OMS:' . $product_oms->get_stock_quantity());
                        write_log('Số lượng đã đặt trên OMS: '. $remake_qty);
                        write_log('Mã trên TIKTOK:' . $product->id);
                        write_log('Id sku trên TIKTOK:' . $product->skus[$key]->id);
                        write_log('Số lượng trên TIKTOK:' . $product->skus[$key]->inventory[0]->quantity);

                        $product->skus[$key]->inventory[0]->quantity = $qty_oms;
                        $flag_update = true;
                        unset($product->skus[$key]->price);
                        unset($product->skus[$key]->seller_sku);
                    }else{
                        unset($product->skus[$key]);
                    }


                }

                if($flag_update){
                    $data = [];
                    foreach ($product->skus as $value){
                        $data['skus'][] =$value;
                    }

                    if( $tiktok_api->update_product_stock($product->id,$data)){
                        write_log('Đã cập nhật');
                    }else{
                        write_log('Không cập nhật');
                    }
                }

            }
            $next_page_token = $res->next_page_token;
            if (!$next_page_token) $flag = false;
        }else{
            $flag = false;
        }
    }

//    write_log($tiktok_products);

    return true;

}