<?php

use OMS\ADDRESS;
use OMS\LS_API;

/**
 *
 */
class OMS_ORDER extends WC_Order
{

    private string $billing_city_code;
    private string $billing_district_code;
    private string $billing_ward_code;
    private $ax_address;
    public array $ORDER_STATUS_LABEL = array(
        'reject' => [
            'title' => 'Từ chối',
            'class_name' => 'badge-secondary'
        ],
        'trash' => [
            'title' => 'Xóa',
            'class_name' => 'badge-danger'
        ],
        'on-hold' => [
            'title' => 'Đang giữ',
            'class_name' => 'badge-danger'
        ],
        'pending' => [
            'title' => 'Đang đợi thanh toán',
            'class_name' => 'badge-danger'
        ],
        'processing' => [
            'title' => 'Đang xử lý',
            'class_name' => 'badge-warning'
        ],
        'confirm' => [
            'title' => 'Xác nhận',
            'class_name' => 'badge-primary'
        ],
        'completed' => [
            'title' => 'Giao sàn',
            'class_name' => 'badge-success'
        ],
        'request' => [
            'title' => 'Gọi lấy hàng',
            'class_name' => 'badge-info'
        ],
        'shipping' => [
            'title' => 'Đang giao hàng',
            'class_name' => 'badge-info'
        ],
        'delivered' => [
            'title' => 'Đã giao hàng',
            'class_name' => 'badge-success'
        ],
        'delivery-failed' => [
            'title' => 'Giao hàng thất bại',
            'class_name' => 'badge-danger'
        ],
        'cancelled' => [
            'title' => 'Đã hủy',
            'class_name' => 'badge-danger'
        ],
        'auto-draft' => [
            'title' => 'Tự động lưu',
            'class_name' => 'badge-secondary'
        ],
        'confirm-goods' => [
            'title' => 'Đã hoàn hàng',
            'class_name' => 'badge-warning'
        ],
        'draft' => [
            'title' => 'Xóa',
            'class_name' => 'badge-danger'
        ],
    );

    public array $PAYMENT_STATUS = [
        'paid' => [
            'title' => 'Đã thanh toán',
            'class_name' => 'badge-success'
        ],
        'unpaid' => [
            'title' => 'Chưa thanh toán',
            'class_name' => 'badge-danger'
        ]
    ];

    public array $LS_STATUS = [
        'yes' => [
            'title' => 'Đã chuyển qua ls retail',
            'class_name' => 'badge-success'
        ],
        'no' => [
            'title' => 'Chưa chuyển qua ls retail',
            'class_name' => 'badge-danger'
        ],
         'header' => [
            'title' => 'Header yes, detail no',
            'class_name' => 'badge-danger'
        ],
         'detail' => [
            'title' => 'Header no, detail yes',
            'class_name' => 'badge-danger'
        ]
    ];


    function __construct($order = 0)
    {
        parent::__construct($order);
        $this->set_meta_address_order();
        $this->ax_address = new ADDRESS();

    }

    /**
     *
     * @return string
     */
    public function get_ax_address()
    {
        $address = $this->get_billing_address_1() . ',' . $this->get_billing_city();
        return $address;
    }

    /**
     *
     * @return void
     */

    private function set_meta_address_order()
    {

        $this->billing_city_code = $this->get_billing_city();
        $this->billing_district_code = $this->get_meta('_billing_district');
        $this->billing_ward_code = $this->get_meta('_billing_ward');
        $address_more = array(
            'district' => $this->billing_district_code,
            'ward' => $this->billing_ward_code,
        );

        $this->data['billing'] = array_merge($this->data['billing'], $address_more);

        apply_filters('ax_add_more_billing_info', $this->data);

    }

    /**
     * @return string
     */
    public function get_billing_city_code()
    {
        return $this->get_billing_city() ?? '';
    }

    /**
     * @return string
     */
    public function get_billing_city_name()
    {
        return $this->ax_address->get_city_name_by_code($this->billing_city_code);
    }

    /**
     * @return string
     */
    public function get_billing_district_code()
    {
        return $this->get_billing_city() ?? '';
    }

    /**
     * @return string
     */
    public function get_billing_district_name()
    {
        return $this->ax_address->get_district_name_by_code($this->billing_district_code);
    }

    /**
     * @return string
     */
    public function get_billing_ward_code()
    {
        return $this->billing_ward_code;
    }

    /**
     * @return string
     */
    public function get_billing_ward_name()
    {
        return $this->ax_address->get_ward_name_by_code($this->billing_ward_code);
    }

    /**
     * @return string
     */
    public function get_billing_address_full()
    {
        return $this->ax_address->get_full_address_name_by_code($this->billing_ward_code, $this->billing_district_code, $this->billing_city_code);
    }

    /**
     * @return string
     */
    public function get_tracking_id()
    {
        return $this->get_meta('tracking_id', true, 'value') ?? '';
    }

    /**
     * @return bool
     */
    public function set_tracking_id($tracking_id)
    {
        if ('' != $tracking_id) {
            update_post_meta($this->get_id(), 'tracking_id', $tracking_id);
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function get_tracking_url()
    {
        return $this->get_meta('tracking_url', true, 'value') ?? '';
    }

    /**
     * @return bool
     */
    public function set_tracking_url($url = '')
    {
        if ('' != $url) {
            update_post_meta($this->get_id(), 'tracking_url', $url);
            return true;
        }
        return false;
    }


    /**
     * @return string
     */
    public function get_shipment_status()
    {
        return $this->get_meta('shipment_status', true, 'value') ?? '';
    }

    /**
     * @return bool
     */
    public function set_shipment_status($shipment_status = '')
    {
        if ('' != $shipment_status) {
            update_post_meta($this->get_id(), 'shipment_status', $shipment_status);
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function get_log()
    {
        return explode('|', $this->get_meta('order_user_log', true, 'value') ?? []);
    }


    public function set_log($type = 'info', $payload = '', $note = '')
    {
        //get current user
        $current_login = wp_get_current_user();
        $user_name = $current_login->nickname ?? 'Customer';
        $order_log = $this->get_log();
        $order_log[] =  $user_name . '; ' . date('Y-m-d H:i:s') . '; ' . $payload . '; '. $type .'; '. $note ;
        update_post_meta($this->get_id(),'order_user_log',implode('|',$order_log));

    }

    public function get_full_price_all_item()
    {
        $total_price = 0;

        if ($this->get_item_count() <= 0) return $total_price;

        foreach ($this->get_items() as $item) {
            if ($item['variation_id'] == 0 && $item->get_product_id() == 0) continue;
            $product = $item['variation_id'] != 0 ? wc_get_product($item['variation_id']) : wc_get_product($item->get_product_id());
            $total_price += (int)$product->get_regular_price() * $item->get_quantity() ?? 0;
        }
        return $total_price;
    }

    public function get_after_sell_all_item(): int
    {
        $total_price = 0;

        if ($this->get_item_count() <= 0) return $total_price;

        foreach ($this->get_items() as $item) {
            if ($item['variation_id'] == 0 && $item->get_product_id() == 0) continue;
            $total_price += (int)$this->get_line_subtotal($item, true);
        }
        return $total_price;
    }

    public function set_order_type($type_name = ''): bool
    {
        $type_name = $type_name ?? 'website';
        update_post_meta($this->get_id(), 'order_type', $type_name);
        return true;
    }

    public function get_order_type(): string
    {
        $order_type = $this->get_meta('order_type', true, 'value');
        return $order_type == '' ? 'website' : $order_type;
    }

    public function set_payment_status(string $payment_status = 'paid'): bool
    {
        update_post_meta($this->get_id(), 'payment_status', $payment_status);
        return true;
    }

    public function get_payment_status(): string
    {
        $payment_status = $this->get_meta('payment_status', true, 'value');
        if (!isset($this->PAYMENT_STATUS[$payment_status])) {
            $payment_status = 'unpaid';
        }

        return $payment_status;
    }

    public function get_payment_title(): string
    {
        return $this->PAYMENT_STATUS[$this->get_payment_status()]['title'];
    }

    public function get_payment_class_name(): string
    {
        return $this->PAYMENT_STATUS[$this->get_payment_status()]['class_name'];
    }


    public function set_ls_status(string $ls_status = 'yes'): bool
    {
        update_post_meta($this->get_id(), 'ls_status', $ls_status);
        return true;
    }

    public function get_ls_status(): string
    {
        $ls_status = $this->get_meta('ls_status', true, 'value');
        if (!isset($this->LS_STATUS[$ls_status])) {
            $ls_status = 'no';
        }

        return $ls_status;
    }

    public function get_ls_title(): string
    {
        return $this->LS_STATUS[$this->get_ls_status()]['title'];
    }

    public function get_ls_class_name(): string
    {
        return $this->LS_STATUS[$this->get_ls_status()]['class_name'];
    }

    public function get_status_title()
    {
        return $this->ORDER_STATUS_LABEL[$this->get_status()]['title'];
    }

    public function get_status_class_name()
    {
        return $this->ORDER_STATUS_LABEL[$this->get_status()]['class_name'];
    }

    public function get_order_contents_weight()
    {
        $weight = 0.0;

        foreach ($this->get_items() as $item_key => $values) {
            if ($values->get_product()->get_weight() > 0) {
                $weight += (float)$values->get_product()->get_weight() * $values->get_quantity();
            }
        }

        return $weight;
    }

    public function get_method_type_ls() {

        switch ($this->get_payment_method()) {
            case 'cod':
                $Tender_Type = 1;
                break;

            case 'alepay':

                $Tender_Type = 2;
                break;

            case 'bacs':
                $Tender_Type = 14;
                break;

            default:
                $Tender_Type = 0;
                break;

        }
        $VAT_Payment_Method = 'TM/CK';

        return [
            'tender_type' => $Tender_Type,
            'vat_payment_method' => $VAT_Payment_Method
        ];

    }

    public function get_number_card_payment(){
        if($this->get_payment_method() == "alepay") {
            $alepay_transaction_info = get_post_meta($this->get_id(), '_alepay_transaction_info', true);
            if(isJson($alepay_transaction_info)) {
                $alepay_transaction_info = json_decode($alepay_transaction_info);
                return substr($alepay_transaction_info->cardNumber, -4);
            }
        }
        return '';
    }

    public function check_stock_ls(): bool {

        $ls_api = new LS_API();
        $items = $this->get_items();
        $data = (object)\OMS\ls_request_check_stock_v3();

        //Khởi tạo inventory = 0;
        $data->Inventory = 0;

        $arg_data = [];
        foreach ($items as $item) {

            $product = wc_get_product($item->get_product_id());
            $data->LocationCode = $ls_api->location_code[0] ?? '';
            $data->ItemNo = $product->get_sku();
//            $data->ItemNo = '1117342';
            if ($item['variation_id'] > 0) {
                $product_variant = wc_get_product($item['variation_id']);
                $data->BarcodeNo = $product_variant->get_sku();
//                $data->BarcodeNo = '1117342000';
            } else {
                $data->BarcodeNo = '';
            }

            $data->Qty = $item->get_quantity();

            $arg_data[] = (array)$data;
        }

        $response = $ls_api->post_product_check_stock_v3($arg_data);
        $safe_stock = true;
        foreach ($arg_data as $key => $item){

            if($item['BarcodeNo'] == ''){
                foreach ($response->data as $value){
                    if($value->ItemNo == $item['ItemNo'])
                        $item['Inventory'] = $value->Inventory ?? 0;
                        $arg_data[$key]['Inventory'] = $value->Inventory ?? 0;
                }
            }

            if($item['BarcodeNo'] != ''){
                foreach ($response->data as $value){
                    if($value->ItemNo == $item['ItemNo']
                        && $value->BarcodeNo == $item['BarcodeNo'])
                        $item['Inventory'] = $value->Inventory ?? 0;
                        $arg_data[$key]['Inventory'] = $value->Inventory ?? 0;
                }
            }

            if($item['Inventory'] < $item['Qty']){
                write_log('không còn tồn '. $item['ItemNo']);
                $this->set_log('danger','check_stock',$item['ItemNo'] . ' không còn tồn' . ' : '. json_encode( $arg_data[$key]));
                $safe_stock = false;
            }

        }

        return $safe_stock;
    }

    public function update_billing_ward($ward):void
    {
        update_post_meta($this->get_id(),'_billing_ward',$ward);
    }

    public function update_billing_district($district):void
    {
        update_post_meta($this->get_id(),'_billing_district',$district);
    }
    public function update_vat_company_name($company_name):void
    {
        update_post_meta($this->get_id(),'vat_company_name',$company_name);
    }
    public function update_vat_company_tax_code($company_tax_code):void
    {
        update_post_meta($this->get_id(),'vat_company_tax_code',$company_tax_code);
    }
    public function update_vat_company_address($company_address):void
    {
        update_post_meta($this->get_id(),'vat_company_address',$company_address);
    }
    public function update_vat_company_email($company_email):void
    {
        update_post_meta($this->get_id(),'vat_company_email',$company_email);
    }
    public function get_vat_company_name()
    {
        return  $this->get_meta('vat_company_name', true, 'value') ?? '';
    }
    public function get_vat_company_tax_code()
    {
        return  $this->get_meta('vat_company_tax_code', true, 'value') ?? '';
    }
    public function get_vat_company_address()
    {
        return  $this->get_meta('vat_company_address', true, 'value') ?? '';
    }
    public function get_vat_company_email()
    {
        return  $this->get_meta('vat_company_email', true, 'value') ?? '';
    }

    public function update_date_send_shipper($date):void
    {
        update_post_meta($this->get_id(),'date_send_shipper',$date);
    }

    public function get_date_send_shipper()
    {
        return  $this->get_meta('date_send_shipper', true, 'value') ?? '';
    }
    public function update_date_success_delivered($date):void
    {
        update_post_meta($this->get_id(),'date_success_delivered',$date);
    }

    public function get_date_success_delivered()
    {
        return  $this->get_meta('date_success_delivered', true, 'value') ?? '';
    }

    public function update_to_no($to_no):void
    {
        update_post_meta($this->get_id(),'to_no',$to_no);
    }

    public function get_to_no()
    {
        return  $this->get_meta('to_no', true, 'value') ?? '';
    }

    public function update_data_transfer_order($to_no):void
    {
        update_post_meta($this->get_id(),'data_transfer_order',$to_no);
    }

    public function get_data_transfer_order()
    {
        return  $this->get_meta('data_transfer_order', true, 'value') ?? '';
    }


}