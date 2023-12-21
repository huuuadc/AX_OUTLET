<?php
/**
 * Plugin name: Payoo
 * Description: Tích hợp Cổng thanh toán Payoo vào Woocommerce 3.2.6
 * Version: 1.0.1
 * Author: Duy.Thai
 */

add_action('plugins_loaded', 'woocommerce_PayooVN_init', 0);

function woocommerce_PayooVN_init(){
    if(!class_exists('WC_Payment_Gateway')) return;

    class WC_PayooVN extends WC_Payment_Gateway{
        public function __construct(){

            $woocommerce_version = function_exists( 'WC' ) ? WC()->version : $woocommerce->version;

            global $woocommerce;

            $this->id = 'payoo';
            //$this->icon = apply_filters('woocommerce_Payoo_icon', $woocommerce->plugin_url() . '/assets/images/icons/Payoo.png');
            $this->has_fields = false;
            $this->method_title = __('Payoo', 'woocommerce');

            //load the setting
            $this->init_form_fields();
            $this->init_settings();

            //Define user set variables
            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');
            $this->business_username = $this->get_option('business_username');
            $this->shop_id = $this->get_option('shop_id');
            $this->shop_title = $this->get_option('shop_title');
            $this->shop_domain = $this->get_option('shop_domain');
            $this->checksum_key = $this->get_option('checksum_key');
            $this->url_checkout = $this->get_option('url_checkout');
            $this->url_get_banks_list = $this->get_option('url_get_banks_list');
            $this -> redirect_page_id =  $this->get_option('redirect_page_id');
            $this->form_submission_method = false;

            if ( version_compare( $woocommerce_version, '2.0.8', '>=' ) )
            {
                add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( &$this, 'process_admin_options' ) );

            } else {
                add_action( 'woocommerce_update_options_payment_gateways', array( &$this, 'process_admin_options' ) );
            }
            add_action('woocommerce_receipt_payoo', array($this, 'receipt_page'));
            add_action( 'woocommerce_api_payoo_callback', array( &$this, 'payment_callback'));
        }

        function init_form_fields(){

            $this-> form_fields = array(
                'enabled' => array(
                    'title' => __('ON / OFF', 'woocommerce'),
                    'type' => 'checkbox',
                    'label' => __('Active Payoo Payment gateway', 'woocommerce'),
                    'default' => 'no'),
                'title' => array(
                    'title' => __('Name:', 'woocommerce'),
                    'type'=> 'text',
                    'description' => __('Name of Payment gateway  ( when user choose payment gateway )', 'woocommerce'),
                    'default' => __('Payoo', 'woocommerce')),
                'description' => array(
                    'title' => __('Description:', 'woocommerce'),
                    'type' => 'textarea',
                    'description' => __('Description payment method.', 'woocommerce'),
                    'default' => __('Hỗ trợ thanh toán qua thẻ ATM/Visa/Master/JCB/QRCode', 'woocommerce')),
                'business_username' => array(
                    'title' => __('Merchant\'s username', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('Merchant\'s E-wallet.')),
                'shop_id' => array(
                    'title' => __('Merchant\'s Id', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('Merchant\'s Id')),
                'shop_title' => array(
                    'title' => __('Title', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('Shop title')),
                'shop_domain' => array(
                    'title' => __('Domain', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('Merchant\'s domain.')),
                'checksum_key' => array(
                    'title' => __('Checksum key', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('Secret key')),
                'url_checkout' => array(
                    'title' => __('Checkout Url', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('')),

                'enable_domestic_bank' => array(
                    'title' => __('ON / OFF', 'woocommerce'),
                    'label' => __('List domestic banks', 'woocommerce'),
                    'type' => 'checkbox',
                    'default' => 'no',),

                'enable_payment_method' => array(
                    'title' => __('ON / OFF', 'woocommerce'),
                    'label' => __('Payment methods', 'woocommerce'),
                    'type' => 'checkbox',
                    'default' => 'no',),

            );
            $this->init_payoo_methods();

        }

        function init_payoo_methods(){
            $methods = $this->get_payoo_methods();
            $index = 0;
            foreach ($methods as $key => $method) {
                $this-> form_fields[$key] = [
                    'title' => $index === 0 ? 'Payment methods' : '',
                    'label' => $method,
                    'default' => 'yes',
                    'type' => 'checkbox'
                ];
                $index++;
            }

        }

        function get_payoo_domain() {
            $url_checkout = $this->get_option('url_checkout');
            $url = parse_url(!empty($url_checkout) ? $url_checkout : 'https://payoo.vn');
            if (!empty($url['scheme']) &&  !empty($url['host'])) {
                return $url['scheme'] . '://' . $url['host'];
            }
            return '';
        }

        function get_payoo_methods() {
            $shop_id =  $this->get_option('shop_id');
            $url = $this->get_payoo_domain();
            if (empty($url)) {
                return [];
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, sprintf("%s/v2/api/paynow/get-list-payment-method?shop_id=%s", $url, $shop_id));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);

            $err = curl_error($ch);

            curl_close($ch);

            if ($err)
            {
                return [];
            }
            else
            {

                $dataReponse = (array) json_decode($response, true);
                if (isset($dataReponse['success']) && $dataReponse['success'] === 'fail') {
                    return  [];
                }

                return $dataReponse;
            }
        }

        function get_payoo_banks() {
            $url = $this->get_payoo_domain();

            if (empty($url)) {
                return [];
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, sprintf('%s/v2/api/paynow/get-banks-partner?code=Ecommerce&url=%s&id=%s&seller=%s',$url, $this->shop_domain, $this->shop_id, $this->business_username));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);

            $err = curl_error($ch);

            curl_close($ch);

            if ($err)
            {
                return [];
            }
            else
            {
                $dataReponse = (array) json_decode($response, true);
                return $dataReponse;
            }
        }

        /**
         *  There are no payment fields for PayooVN, but we want to show the description if set.
         **/
        function payment_fields(){
            if ($this->get_option('enable_payment_method') === "yes") {
                $methods = $this->get_payoo_methods();
                $index = 0;
                foreach ($methods as $key => $method) {
                    if ($this->get_option($key) === "yes") {
                        echo $this->get_payment_html(['title' => $method, 'method' => $key, 'checked' => $index == 0]);
                        $index++;
                    }
                }
            } else {
                if($this -> description) echo wpautop(wptexturize('<img src="https://www.payoo.vn/website/static/css/image/payoo-logo.png" /><br /><br />'.$this -> description));
            }


        }

        function get_payment_html($option) {
            $optionIcons = [
                'cc-payment' => ['visa.svg', 'mastercard.svg', 'jcb.svg', 'amex.svg'],
                'bank-payment' => ['napas.svg'],
                'apple-pay' => ['applepay.svg']
            ];

            $bankHtml = '';

            $html = '<div class="payoo-option">%s %s %s</div>';
            $input = '<input style="position: absolute" id="payoo-option-%method%" type="radio" %ischeck% value="%method%" name="payoo_method">';

            $content = '<div class="payoo-content"><div class="payoo-content-method"> %s %s</div></div>';
            $label = '<label style="margin-left: 16px;" for="payoo-option-%method%">%title% </label> %icons% ';
            $image = '<div class="payoo-option-icon"><img class="payoo-checked" height="16" width="16" src="%checked%" /> </div>';

            $icons = '';
            if ($option['method'] == 'bank-payment' && $this->get_option('enable_domestic_bank') === "yes") {
                $banks = $this->get_payoo_banks();

                if (isset($banks['bank_payment']['icons'])) {
                    $bankHtml = '<payoo-bank> %s</payoo-bank>';

                    $bankIcons = '';
                    foreach ($banks['bank_payment']['icons'] as $bank) {
                        $bankIcons .= '<div class="bank-icon" data-name="'. $bank['name']   .'" data-code="' . $bank['code'] . '" ><input class="payoo-bank-radio" type="radio" name="payoo_bank" value="'. $bank['code'] .'" /><div class="bank-icon-wrapper"><img height="32" src="' . $bank['logo_bank'] . '" alt="'. $bank['code'] .'" /></div></div>';
                    }
                    $bankHtml = sprintf($bankHtml,  $bankIcons);
                }
            }

            if (isset($optionIcons[$option['method']])) {
                foreach ($optionIcons[$option['method']] as $icon) {
                    $icons .= '<img   src="'. plugins_url("assets/img/$icon",__FILE__ )  . '" />';
                }
            }
//            $bankHtml= '';

            $content = sprintf($content, $image,$label);
            $html = sprintf($html, $input, $content, $bankHtml);

            return str_replace(
                ['%title%', '%method%', '%ischeck%', '%icons%', '%checked%', ],
                [
                    $option['title'],
                    $option['method'],
                    $option['checked'] ? 'checked' : '',
                    $icons,
                    plugins_url('assets/img/checked.svg',__FILE__ ),
                ], $html);
        }


        /**
         * Receipt Page
         **/
        function receipt_page($order){
            echo '<p>'.__('Chúng tôi đã nhận được đơn mua hàng của bạn. <br /><b>Tiếp theo, hãy bấm nút Thanh toán bên dưới để tiến hành thanh toán an toàn qua Payoo.vn', 'mPayooVN').'</p>';
            echo $this -> generate_PayooVN_form($order);
        }
        /**
         * Generate PayooVN button link
         **/
        public function generate_PayooVN_form($order_id){
            global $woocommerce;

            $order = new WC_Order( $order_id );
            $phone = $order->get_billing_phone();
            $name = trim($order->get_billing_last_name() . ' ' . $order->get_billing_first_name());
            $email = $order->get_billing_email();
            $method = get_post_meta($order_id, 'payoo_method', true );
            $bank = $method == 'bank-account' ?  get_post_meta($order_id, 'payoo_bank', true ) : null;
            $redirect_url = get_site_url() . '/wc-api/payoo_callback/';
            $notify_url = $redirect_url; // url notify after payment successfull
            $order_ship_date = date('d/m/Y'); //  dd/mm/YYYY vd: 31/12/2011, order_ship_date >= datenow()
            $order_ship_days = 0; // ship days

            $validity_time =  date('YmdHis', strtotime('+1 day', time())); // expire date
            $money_total = (int)number_format((float)$order -> order_total, 2, '.', '');

            $payoo_settings = array_filter( (array) get_option( 'woocommerce_payoo_settings', array() ) );
            $chi_tiet_don_hang ='<table class="order-description paycode-description"><thead>
	<tr>
	<th>Thông tin đơn hàng</th>							
	<th>Tiền thanh toán</th>
	</tr>
  </thead>
  <tbody>
	<tr>
	  <td class="row-unit-product">Thanh toán đơn hàng '.$order_id.' từ doanh nghiêp '.$payoo_settings['shop_title'].'</td>
	  <td class="row-total">'.$money_total.'</td>
	</tr>
  </tbody>
</table>';

            $str='<shops><shop><session>'.$order_id.'</session><username>'.$this -> business_username.'</username><shop_id>'.$this->shop_id.'</shop_id><shop_title>'.$this->shop_title.'</shop_title><shop_domain>'.$this->shop_domain.'</shop_domain><shop_back_url>'.urlencode($redirect_url).'</shop_back_url><order_no>'.$order_id.'</order_no><order_cash_amount>'.$money_total.'</order_cash_amount><order_ship_date>'.$order_ship_date.'</order_ship_date><order_ship_days>'.$order_ship_days.'</order_ship_days><order_description>'.urlencode($chi_tiet_don_hang).'</order_description><notify_url>'.$notify_url.'</notify_url><validity_time>'.$validity_time.'</validity_time>
		<customer><name>'.  $name .'</name><phone>'. $phone .'</phone><email>'. $email .'</email></customer><jsonresponse>true</jsonresponse><direct_return_time>10</direct_return_time></shop></shops>';


            $checksum= hash('sha512',$this -> checksum_key.$str);//sha1($this -> checksum_key.$str);

            $ch = curl_init();


            $data = array(
                'data' => $str,
                'checksum' => $checksum,
                'refer' => $this->shop_domain,
                'pm' => $this->get_option('enable_payment_method') === "yes" ? $method : null,
                'bc' => $this->get_option('enable_payment_method') === "yes" ? $bank : null,
                'payment_group' => $this->get_option('enable_payment_method') === "yes" && $method  ? $method : null,
            );

            curl_setopt($ch, CURLOPT_URL, $this -> url_checkout);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);

            $err = curl_error($ch);

            curl_close($ch);

            if ($err)
            {
                echo "cURL Error #:" . $err;
            }
            else
            {


                $dataReponse = (array) json_decode($response, true);
                $ResponseDatas= (array)json_Decode($response,true);

                //var_dump($ResponseDatas);
                echo '<pre>';
                echo print_r($ResponseDatas);
                echo '</pre>';

                header('Location: ' . $ResponseDatas['order']['payment_url']);
            }
        }
        /**
         * Process the payment and return the result
         **/
        function process_payment( $order_id ) {
            $order = new WC_Order( $order_id );
            return array(
                'result'  => 'success',
                'redirect'  => add_query_arg('order', $order->get_id(), add_query_arg('key', $order->get_order_key(), get_permalink(wc_get_page_id('pay'))))
            );

        }

        function showMessage($content){
            return '<div class="box '.$this -> msg['class'].'-box">'.$this -> msg['message'].'</div>'.$content;
        }
        // get all pages
        function get_pages($title = false, $indent = true) {
            $wp_pages = get_pages('sort_column=menu_order');
            $page_list = array();
            if ($title) $page_list[] = $title;
            foreach ($wp_pages as $page) {
                $prefix = '';
                // show indented child pages?
                if ($indent) {
                    $has_parent = $page->post_parent;
                    while($has_parent) {
                        $prefix .=  ' - ';
                        $next_page = get_page($has_parent);
                        $has_parent = $next_page->post_parent;
                    }
                }
                // add to page list array array
                $page_list[$page->ID] = $prefix . $page->post_title;
            }
            return $page_list;
        }

        function payment_callback()
        {
            //$NotifyMessage = stripcslashes($_POST["NotifyData"]);

            $NotifyMessage =  file_get_contents('php://input');
            if($NotifyMessage == null || '' === $NotifyMessage)
            {
                if(isset($_GET['session']) && isset($_GET['order_no']) && isset($_GET['status']))
                {

                    $payoo_settings = array_filter( (array) get_option( 'woocommerce_payoo_settings', array() ) );
                    $checksum_key = $payoo_settings['checksum_key'];
                    $cs = hash('sha512',$checksum_key.$_GET['session'].'.'.$_GET['order_no'].'.'.$_GET['status']);
                    if($cs == $_GET['checksum']) //status = 1 da thanh toan
                    {
                        if($_GET['status'] == 1)
                        {
                            //global $woocommerce;

                            $order = new WC_Order($_GET['order_no']);

                            if (!empty($order))
                            {

                                $res = $order->update_status('completed','Payoo');
                                WC()->cart->empty_cart();
                            }
                        }
                        else if($_GET['status'] == 0)
                        {
                            $order = new WC_Order($_GET['order_no']);
                            if (!empty($order))
                            {
                                $res = $order->update_status('failed','Payoo');
                                WC()->cart->empty_cart();
                            }
                        }
                    }
                    // redirect to thankyou page
                    $thankyou_page =  add_query_arg('data', $cs, add_query_arg('key', $order->get_order_key(),get_permalink(woocommerce_get_page_id('pay')).'/order-received/'));
                    wp_redirect($thankyou_page);
                    exit();
                }
            }
            else if($NotifyMessage != '')
            {
                $response = json_decode($NotifyMessage, true);
                $data = json_decode($response['ResponseData'], true);
                $order_no = $data['OrderNo'];

                $payoo_settings = array_filter( (array) get_option( 'woocommerce_payoo_settings', array() ) );
                $checksum = $payoo_settings['checksum_key'];
                $url_checkout = $payoo_settings['url_checkout'];

                if (strpos($url_checkout, 'https://payoo.vn') === 0  || strpos($url_checkout, 'https://www.payoo.vn') === 0) {
                    $ipRequest = '118.69.206.8'; // Live IP
                } else {
                    $ipRequest = '118.69.56.194'; // Test IP
                }

                if (strtoupper(hash('sha512',$checksum.$response['ResponseData'].$ipRequest)) == strtoupper($response['SecureHash'])) {
                    $status = $data['PaymentStatus'];
                    if($order_no != '' && $status == 1) {
                        $order = new WC_Order($order_no);
                        if (!empty($order)) {
                            $res = $order->update_status('completed','Payoo');
                            if ($res === true) {
                                WC()->cart->empty_cart();
                                ob_clean();
                                echo json_encode(['ReturnCode' => 0, 'Description' => 'NOTIFY_RECEIVED']);
                                exit();
                            }
                        }
                    }
                } else {
                    echo json_encode(['ReturnCode' => -1, 'Description' => 'INVALID_CHECKSUM']);
                }

                exit();
            }
            echo "IPN Listening...";
            exit();
        }

    }
    function woocommerce_add_PayooVN_gateway($methods) {
        $methods[] = 'WC_PayooVN';
        return $methods;
    }

    function woocommerce_add_PayooVN_scripts() {
        wp_register_style('PayooVN', plugins_url('assets/css/main.css',__FILE__ ));
        wp_enqueue_style('PayooVN');
        wp_register_script('PayooVN', plugins_url('assets/js/main.js',__FILE__ ));
        wp_enqueue_script('PayooVN');
    }

    function woocommerce_add_PayooVN_meta( $order_id ) {
        if ( ! empty( $_POST['payoo_method'] ) ) {
            update_post_meta($order_id, 'payoo_method', sanitize_text_field( $_POST['payoo_method'] ) );
        }

        if ( ! empty( $_POST['payoo_bank'] ) ) {
            update_post_meta($order_id, 'payoo_bank', sanitize_text_field( $_POST['payoo_bank'] ) );
        }
    }

    function woocommerce_add_PayooVN_icon( $icon, $gateway_id ){

        if($gateway_id == 'payoo') {
            $icon = '<img style="margin-left: 8px; float: none" src="' . plugins_url("assets/img/payoo.svg",__FILE__ ) . '" height="26" alt="Payoo" />';
        }

        return $icon;
    }

    add_filter('woocommerce_payment_gateways', 'woocommerce_add_PayooVN_gateway' );

    add_action('wp_enqueue_scripts', 'woocommerce_add_PayooVN_scripts');

    add_action('woocommerce_checkout_update_order_meta', 'woocommerce_add_PayooVN_meta' );

    add_filter( 'woocommerce_gateway_icon', 'woocommerce_add_PayooVN_icon', 10, 2);




}

