<style>

    #barcode_shipment div {
        right: 30px !important;
        position: absolute !important;
    }

</style>
<?php

use Picqer\Barcode\BarcodeGeneratorHTML;
use OMS\COMPANY;

$order_id =  $_GET['order_id'];
date_default_timezone_set('Asia/Ho_Chi_Minh');

if (!isset($_GET['order_id']) || !get_post_type($order_id)):
?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="text-center">Không tìm thấy đơn hàng: <?php echo $order_id?></div>
            </div>
        </div>
    </div>
</section>

<?php

else:

$company = new COMPANY();
$order = new OMS_ORDER($order_id);
$barcode = new BarcodeGeneratorHTML();

$site_logo_id        = flatsome_option( 'site_logo' );
$site_logo           = wp_get_attachment_image_src( $site_logo_id, 'large' );

if ( ! empty( $site_logo_id ) && ! is_numeric( $site_logo_id ) ) {
    $site_logo = array( $site_logo_id, 200, 70 );
}

    if ($order->get_tracking_id() == ''):
        ?>
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="text-center">Đơn hàng: <?php echo $order_id?></div>
                        <div class="text-center">Không tìm thấy mã vận đơn</div>
                    </div>
                </div>
            </div>
        </section>

    <?php
        else:

?>

<div class="content-header">
</div>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Main content -->
                <div class="invoice p-3 mb-3 ">
                        <div class="w-100 border border-dark">
                                <div class="d-flex flex-col p-2">
                                    <div class="pt-2 w-100"><img width="200" height="70"  src="<?php echo $site_logo[0]?>" /></div>
                                    <div class="w-100 text-center"><h1><strong>Phiếu Giao Hàng</strong></h1></div>
                                    <div id="barcode_shipment" class="barcode_shipment w-100 flex justify-content-end"><?php echo $barcode->getBarcode($order->get_tracking_id(),'C128',1, 50) ?></div>
                                </div>
                                <div class="d-flex flex-col">
                                    <div class="pl-3 pb-2 "><?php echo $barcode->getBarcode($order->get_id(),'C128',2) ?></div>
                                </div>
                                <div class="d-flex flex-col pt-1">
                                    <div class="pl-3 w-100"><strong>Mã đơn hàng:</strong> <?php echo $order->get_id() ?></div>
                                    <div class="pl-3 w-100"><strong>Đơn vị vận chuyển:</strong><?php echo $order->get_shipping_method()?></div>
                                </div>
                                <div class="d-flex flex-col pt-1 pb-3">
                                    <div class="pl-3 w-100"><strong>Ngày đặt hàng:</strong> <?php echo wp_date(get_date_format(),strtotime($order->get_date_created('value')))?></div>
                                    <div class="pl-3 w-100"><strong>Mã vận đơn: </strong> <?php echo $order->get_tracking_id()?></div>
                                </div>
                        </div>
                        <div class="w-100 d-flex flex-col border-right border-left border-dark">
                            <div class="p-2 w-100 ">
                                <strong>ĐỊA CHỈ GIAO HÀNG</strong><br>
                                <strong>Cửa hàng: </strong><?php echo $company->get_company_name()?><br>
                                <strong>Địa chỉ: </strong><?php echo $company->get_company_full_address_by_code()?><br>
                                <strong>Số điện thoại: </strong><?php echo $company->get_company_phone()?>
                            </div>
                            <div class="p-2 w-100 border-left border-dark">
                                <strong>ĐỊA CHỈ NHẬN HÀNG</strong><br>
                                <strong>Người nhận: </strong><?php echo $order->get_billing_first_name() . ' ' . $order->get_billing_last_name()?><br>
                                <strong>Địa chỉ: </strong><?php echo $order->get_billing_address_1() . ', ' . $order->get_billing_address_full()?><br>
                                <strong>Số điện thoại: </strong><?php echo $order->get_billing_phone()?><br>
                            </div>
                        </div>
                    <table class="table table-bordered mb-0 p-1">
                        <tr class="font-italic border-dark">
                            <td class="pl-3  border-dark w-5"><strong>STT</strong></td>
                            <td class="border-dark w-35"><strong>Tên sản phẩm</strong></td>
                            <td class="border-dark w-25"><strong>Mã sản phẩm</strong></td>
                            <td class="text-right border-dark w-25 pr-3"><strong>Số lượng</strong></td>
                        </tr>
                        <?php
                        $total_weight = 0;
                        $count= 0;
                        foreach ($order->get_items() as $item_key => $item ): $count++ ?>
                            <?php
                                $product =  $item['variation_id'] != 0 ? wc_get_product($item['variation_id']) : wc_get_product($item->get_product_id());
                                $product_weight = (int)$product->get_weight() ?? 0;
                                $total_weight += floatval( $product_weight * $item->get_quantity() );
                            ?>
                            <tr class="p-1">
                                <td class="pl-3 border-dark"><?php echo $count?></td>
                                <td class="border-dark"><?php echo $item->get_name() ?></td>
                                <td class="border-dark"><?php echo $product->get_sku() ?></td>
                                <td class="text-right border-dark pr-3"><?php echo $item->get_quantity() ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php
                        ?>
                    </table>
                    <div class="w-100 d-flex flex-col border-right border-left border-bottom p-0 border-dark">
                        <div class="p-1 w-100"> <strong>Tiền thu người nhận: </strong><?php  echo $order->get_payment_method() == 'cod' ? number_format($order->get_total(),0,',','.') : '0'?> đ</div>
                        <div class="p-1 w-100 border-left border-dark">
                            <strong>Tổng số lượng: </strong><?php echo $order->get_item_count()?><br>
                            <strong>Tổng khối lượng: </strong><?php echo $order->get_order_contents_weight()?> kg
                        </div>
                    </div>
                    <div class="w-100 d-flex flex-col border-right border-left border-bottom border-dark">
                        <div class="p-1 w-25 "><strong>Ghi chú đơn hàng</strong></div>
                        <div class="p-1 w-75 border-left border-dark"><?php echo $order->get_customer_note('value') ?? 'Không có note' ?></div>
                    </div>
                    <div class="p-2 w-100 text-center border-right border-left border-bottom border-dark">
                        <?php echo get_option('admin_dashboard_footer_print_shipment')?>
                    </div>
                </div>
                <!-- Main content -->
            </div>
        </div>
    </div>
</section>
            <script>
                window.print()
                window.onafterprint = () => window.close();
            </script>
<?php
    endif;
endif;
?>