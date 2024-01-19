<?php

use OMS\Tiktok_Api;

function product_tiktok_sync():bool
{

    $tiktok_api = new Tiktok_Api();
    $flag = true;
    $next_page_token = '';
    $wc_logs = new WC_Logger();

    while ($flag){

        $res = $tiktok_api->get_products_v2(50,$next_page_token);

        if(isset($res->products)){
            foreach ($res->products as $product){
                $flag_update = false;
                foreach ($product->skus as $key => $sku){
                    $product_id = wc_get_product_id_by_sku($sku->seller_sku);
                    $product_parent_id = wp_get_post_parent_id($product_id);
                    //Not found product on OMS continue
                    if($product_id == 0 && !$product_parent_id ) {
                        $wc_logs->log('info'
                            ,'Không tim thấy sản phẩm trên OMS. id '. $product->id . ' | Sku: ' . $sku->seller_sku );
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
                        $wc_logs->log('info'
                            ,'Mã sản phẩm trên OMS ' . $product_oms->get_id()
                            .'|.Số lượng trên OMS: ' . $product_oms->get_stock_quantity()
                            .'|.Số lượng đã đặt trên OMS: '. $remake_qty
                            .'|.Mã trên TIKTOK: ' . $product->id
                            .'|.Id sku trên TIKTOK: ' . $product->skus[$key]->id
                            .'|.Số lượng trên TIKTOK: ' . $product->skus[$key]->inventory[0]->quantity
                            .'|.Seller sku: ' . $sku->seller_sku);

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
                        $wc_logs->log('info','Đã được cập nhật');
                    }else{
                        $wc_logs->log('info','Không được cập nhật');
                    }
                }

            }
            $next_page_token = $res->next_page_token;
            if (!$next_page_token) $flag = false;
        }else{
            $flag = false;
        }
    }

    return true;

}