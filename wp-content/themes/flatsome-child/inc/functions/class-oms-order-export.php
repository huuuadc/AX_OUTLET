<?php

namespace OMS\EXPORT;
use DgoraWcas\Integrations\Plugins\WPRocket\WPRocket;

class OMS_EXPORT {

    private string $DIR_EXPORT = 'export';
    public string $BASEDIR = '';
    public string $BASEURL = '';
    private string $APPEND_FILE = '';
    private \XLSXWriter $xlsxwriter ;
    private string $extend = '.xlsx';
    private string $SHEET_NAME = 'sheet1';
    public string $ORDER_DIR = 'order/';
    public string $INVENTORY_DIR = 'inventory/';
    public function __construct()
    {
        $this->define_constants();
        $this->create_dir_export();
        $this->APPEND_FILE = date('Y_m_d_H_i_s').'.xlsx';
        $this->xlsxwriter = new \XLSXWriter();
    }

    function define_constants(){
        $upload_dir = wp_upload_dir( null, false );
        $this->BASEDIR = $upload_dir['basedir'] . '/export/';
        $this->BASEURL = $upload_dir['baseurl'] . '/export/';
    }

    function create_dir_export(){
        if ( ! is_dir( $this->BASEDIR ) ) {
            wp_mkdir_p( $this->BASEDIR );
            wp_mkdir_p( $this->BASEDIR . $this->ORDER_DIR );
            wp_mkdir_p( $this->BASEDIR . $this->INVENTORY_DIR );
        }

        if ( ! is_dir( $this->BASEDIR . $this->ORDER_DIR  ) ) {
            wp_mkdir_p( $this->BASEDIR . $this->ORDER_DIR );
        }

        if ( ! is_dir(  $this->BASEDIR . $this->INVENTORY_DIR) ) {
            wp_mkdir_p(  $this->BASEDIR . $this->INVENTORY_DIR);
        }
    }

    function order_export($order_status,$filter_start_date,$filter_end_date){

        global $wpdb;

        $order_query = $wpdb->get_results("select * from ecom_posts where post_type = 'shop_order' ");

        $file_name = 'export_order_'.$this->APPEND_FILE;

        $sheet_header = array(
            'STT',
            'Ngày đặt hàng' ,
            'Loại đơn hàng',
            'Mã đơn hàng',
            'Khóa đơn hàng',
            'Khách hàng',
            'Số lượng sản phẩm',
            'Thành tiền',
            'Giảm giá sp',
            'Tiền sau giảm giá',
            'Chiết khấu coupon',
            'Phí vận chuyển',
            'Tổng tiền',
            'Trạng thái giao hàng',
            'Hình thức thanh toán',
            'Trạng thái thanh toán',
            'Đơn vị vận chuyển',
            'Mã vận đơn',
            'Trạng thái vận chuyển',
            'Ngày giao DV CV',
            'Ngày giao hàng Thành công'
        );

        $this->xlsxwriter->writeSheetRow($this->SHEET_NAME, $sheet_header);
        $count = 0;
        foreach ($order_query as $key => $item){
            $count++;

            if (!wc_get_order($item->ID)) continue;
            $order = new \OMS_ORDER($item->ID);

            $row = array(
                $count,
                wp_date(get_date_format(),strtotime( $order->get_date_created())),
                $order->get_type(),
                '#'.$order->get_id(),
                $order->get_order_key(),
                $order->get_billing_last_name() . ' ' . $order->get_billing_first_name(),
                $order->get_item_count(),
                number_format( $order->get_full_price_all_item(),0,'.',','),
                $order->get_full_price_all_item() - $order->get_after_sell_all_item(),
                $order->get_after_sell_all_item(),
                (int)$order->get_total_discount(),
                $order->get_shipping_total('value'),
                number_format( $order->get_total(),0,'.',','),
                $order->get_status(),
                $order->get_payment_method_title(),
                $order->get_payment_method(),
                $order->get_shipping_method(),
                $order->get_tracking_id(),
                $order->get_shipment_status(),
                $order->get_date_paid(),
            );

            $this->xlsxwriter->writeSheetRow($this->SHEET_NAME,$row);
        }

        $this->xlsxwriter->writeToFile($this->BASEDIR . $this->ORDER_DIR . $file_name);


        return true;

    }

    function order_detail_export($order_status,$filter_start_date,$filter_end_date){

        global $wpdb;

        $order_query = $wpdb->get_results("select * from ecom_posts where post_type = 'shop_order' ");

        $file_name = 'export_order_detail_'.$this->APPEND_FILE;

        $sheet_header = array(
            'STT',
            'Ngày đặt hàng' ,
            'Mã đơn hàng',
            'Khách hàng',
            'SKU',
            'Item No.',
            'Variant Code',
            'Tên SP',
            'Brand',
            'DDVT',
            'Số lượng',
            'Đơn giá',
            'Thành tiền',
            '% giảm giá',
            'Tiền giảm giá',
            'Tiền sau giảm giá SP',
            'Tình trạng đơn hàng',
            'Ngày giao cho DVVC',
            'Ngày giao thành công'
        );

        $this->xlsxwriter->writeSheetRow($this->SHEET_NAME, $sheet_header);

        $count = 0;
        foreach ($order_query as $key => $item){

            if (!wc_get_order($item->ID)) continue;
            $order = new \OMS_ORDER($item->ID);

            foreach ($order->get_items() as $value){

                $count++;

                if ($value['variation_id'] == 0 && $value->get_product_id() == 0) continue;

                $product =  $value['variation_id'] != 0 ? wc_get_product($value['variation_id']) : wc_get_product($value->get_product_id());

                $full_price = (int)($product->get_regular_price());

                $total_price = (int)($full_price * $value->get_quantity());
                if ($total_price == 0) $total_price = 1;
                $persen_down = 100 * (1 - $order->get_line_subtotal($value,true) / $total_price );

                $row = array(
                    $count,
                    wp_date(get_date_format(),strtotime( $order->get_date_created())),
                    '#'.$order->get_id(),
                    $order->get_billing_last_name() . ' ' . $order->get_billing_first_name(),
                    $product->get_sku() ,
                    $product->get_sku(),
                    substr( $product->get_sku(), -3),
                    $value->get_name(),
                    '',
                    'CAI',
                    $value->get_quantity(),
                    (int)$product->get_regular_price(),
                    $total_price,
                    number_format($persen_down , '0',',','.'),
                    $total_price - $order->get_line_subtotal($value,true),
                    number_format($order->get_line_subtotal($value,true) , '0',',','.'),
                    $order->get_status(),
                    '',
                    ''
                );
                $this->xlsxwriter->writeSheetRow($this->SHEET_NAME,$row);
            }
        }

        $this->xlsxwriter->writeToFile($this->BASEDIR. $this->ORDER_DIR. $file_name);

        return true;

    }

    function inventory_export($start_date,$end_date)
    {
        $args = array(
            'posts_per_page' => -1,
            'post_type'      => 'product',
            'hide_empty'     => 1,
            'meta_query' => array(
                array(
                    'key' => '_stock_status',
                    'value' => 'instock',
                    'compare' => '=',
                ))
        );

        $query = new \WP_Query( $args );

        $file_name = 'export_inventory_'.$this->APPEND_FILE;

        $sheet_header = array(
            'STT',
            'Loại sản phẩm' ,
            'SKU',
            'Tên sản phẩm',
            'item no',
            'variant code',
            'brand',
            'Tồn kho',
        );

        $this->xlsxwriter->writeSheetRow($this->SHEET_NAME, $sheet_header);

        $count = 0;

        while ( $query->have_posts() ) :
            $query->the_post();
            $product = wc_get_product();
            $brand = get_product_brand_name($product->get_id());

            if ($product->get_type() == 'variable'){
                $variations = $product->get_available_variations();

                foreach ($variations as $item):
                $item_variant = wc_get_product($item['variation_id']);
                $variant_att = $item_variant->get_attributes('value');
                write_log($variant_att);
                $count++;
                $row = array(
                    $count,
                    $item_variant->get_type(),
                    $item_variant->get_sku(),
                    $item_variant->get_name(),
                    get_post_meta($product->get_id(),'offline_id',true),
                    strlen($item_variant->get_sku()) > 7 ? (string)substr($item_variant->get_sku(),-3): '',
                    $brand,
                    $item_variant->get_stock_quantity()
                );

                $this->xlsxwriter->writeSheetRow($this->SHEET_NAME,$row);

                endforeach;
            }else{
                $count++;
                $row = array(
                    $count,
                    $product->get_type(),
                    $product->get_sku(),
                    $product->get_name(),
                    get_post_meta($product->get_id(),'offline_id',true),
                    '',
                    $brand,
                    $product->get_stock_quantity()
                );

                $this->xlsxwriter->writeSheetRow($this->SHEET_NAME,$row);
            }

        endwhile;

        $this->xlsxwriter->writeToFile($this->BASEDIR. $this->INVENTORY_DIR. $file_name);

        return true;

    }

    function export_show(string $sub_dir = ''){

        $file_dir = !$sub_dir ? $this->BASEDIR : $this->BASEDIR .  $sub_dir;
        $url_dir = !$sub_dir ? $this->BASEURL : $this->BASEURL .  $sub_dir;

        $exports =  @scandir($file_dir ,0);
        $files= array();
        foreach ($exports as $file){
            $files[$file] = filemtime($file_dir.$file);
        }

        arsort($files);
        $files = array_keys($files);

        $count = 0;


        echo '<table class="table table-bordered table-hover dataTable dtr-inline">';
        echo '<tr>
                <th>STT</th>
                <th>Tên tập tin</th>
                <th>Thời gian tạo</th>
                <th></th>
              </tr>';
        foreach ($files as $key => $value){
            if ($value =='.' || $value == '..') continue;
            $time_create = date ("d-m-Y H:i:s", filemtime($file_dir.$value))  ;
            $user = fileowner($file_dir.$value);
            $stat = stat($file_dir.$value);
            $count ++;
            echo "<tr>
                    <td>{$count}</td>
                    <td>
                        <a href='{$url_dir}{$value}'>{$value}</a>
                    </td>
                    <td>
                        {$time_create}
                    </td>
                    <td>
                        <a class='btn btn-danger' href='./?delete={$value}'>Xóa</a>  
                    </td>  
                </tr>";
        }

        echo '</table>';
    }




}