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
            return true;
        }
        return false;
    }

    function order_export($order_status,$filter_start_date,$filter_end_date){

        global $wpdb;

        $order_query = $wpdb->get_results("select * from ecom_posts where post_type = 'shop_order'");

        $file_name = 'export_order_'.$this->APPEND_FILE;

        $sheet_header = array(
            'STT',
            'Ngày đặt hàng' ,
            'Loại đơn hàng',
            'Mã đơn hàng',
            'Khóa đơn hàng',
            'Khách hàng',
            'Số lượng sản phẩm',
            'Tạm tính',
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
                number_format( $order->get_subtotal(),0,'.',','),
                number_format( $order->get_total_discount(),0,'.',','),
                number_format( $order->get_total(),0,'.',','),
                $order->get_coupon_codes(),
                $order->get_shipping_total('value'),
                number_format( $order->get_total(),0,'.',','),
                $order->get_status(),
                $order->get_payment_method_title(),
                $order->get_payment_method(),
                $order->get_shipping_method(),
                $order->get_tracking_id(),
                $order->get_shipment_status(),
                $order->get_date_paid(),
                $order->get_date_paid()
            );

            $this->xlsxwriter->writeSheetRow($this->SHEET_NAME,$row);
        }

        $this->xlsxwriter->writeToFile($this->BASEDIR. $file_name);


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
            '% giảm giá',
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
                    number_format( $product->get_regular_price() ?? 0 , '0',',','.'),
                    number_format( 100 * (1 - $order->get_line_subtotal($value,true)/(int)(($product->get_regular_price() ?? 1)*$value->get_quantity())), '0',',','.'),
                    number_format($order->get_line_subtotal($value,true), '0',',','.'),
                    $order->get_status(),
                    '',
                    ''
                );
                $this->xlsxwriter->writeSheetRow($this->SHEET_NAME,$row);
            }
        }

        $this->xlsxwriter->writeToFile($this->BASEDIR. $file_name);

        return true;

    }

    function export_show(){

        $exports = @scandir($this->BASEDIR,0);
        $count = 0;

        echo '<table class="table table-bordered table-hover dataTable dtr-inline">';
        echo '<tr>
                <th>STT</th>
                <th>Tên tập tin</th>
                <th>Thời gian tạo</th>
                <th></th>
              </tr>';
        foreach ($exports as $key => $value){
            if ($value =='.' || $value == '..') continue;
            $time_create = date ("d-m-Y H-i-s", filemtime($this->BASEDIR.$value))  ;
            $user = fileowner($this->BASEDIR.$value);
            $stat = stat($this->BASEDIR.$value);
            $count ++;
            echo "<tr>
                    <td>{$count}</td>
                    <td>
                        {$value}
                    </td>
                    <td>
                        {$time_create}
                    </td>
                    <td>
                        <a href='{$this->BASEURL}{$value}'>Tải xuống</a>  
                    </td>  
                </tr>";
        }

        echo '</table>';
    }




}