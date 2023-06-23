<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * WC_Gateway_Alepay class.
 *
 * @extends WC_Payment_Gateway
 */
class WC_Gateway_Alepay extends WC_Payment_Gateway {

    private static $domain;

    /**
     * Logging enabled?
     *
     * @var bool
     */
    public $logging;
    public $language;
    public $currency;

    public function __construct() {
        self::$domain = 'woocommerce-gateway-alepay';

        $this->id = 'alepay';
        $this->has_fields = false;
        $this->method_title = __('Alepay', self::$domain);
        $this->method_description = __('Allows payment with Alepay gateway.', self::$domain);

        $this->alepay = new WC_Alepay_API(array(
            'apiKey' => $this->get_option('api_key'),
            'encryptKey' => $this->get_option('encrypt_key'),
            'checksumKey' => $this->get_option('checksum_key'),
            'callbackUrl' => '',
            'env' => $this->get_option('env') === 'yes' ? 'live' : 'test'
        ));
        
        // Load the form fields.
        $this->init_form_fields();

        // Load the settings.
        $this->init_settings();

        // Define user set variables
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description', 'Chọn một phương thức');
        $this->instructions = $this->get_option('instructions', $this->description);
        $this->order_status = $this->get_option('order_status');
        //$this->logging = 'yes' === $this->get_option( 'logging' );
        $this->logging = true;
        $this->payment_normal = 'yes' === $this->get_option('payment_normal');        
        $this->payment_normal_desc = $this->get_option('payment_normal_desc');
		$this->payment_normal_domestic = 'yes' === $this->get_option('payment_normal_domestic');        
        $this->payment_normal_domestic_desc = $this->get_option('payment_normal_domestic_desc');
        $this->payment_installment = 'yes' === $this->get_option('payment_installment');
        $this->payment_installment_desc = $this->get_option('payment_installment_desc');
        $this->payment_token = 'yes' === $this->get_option('payment_token');
        $this->payment_token_desc = $this->get_option('payment_token_desc');
        $this->language = $this->get_option('language', 'vi');
        $this->currency = $this->get_option('currency', 'VND');
        
        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        add_action( 'woocommerce_thankyou_alepay', array( $this, 'thankyou_page' ) );
    }

    /**
     * Initialise Gateway Settings Form Fields.
     */
    public function init_form_fields() {

        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Enable/Disable', self::$domain),
                'type' => 'checkbox',
                'label' => __('Enable Alepay Payment', self::$domain),
                'default' => 'yes'
            ),
            'language' => array(
                'title' => __('Language', self::$domain),
                'type' => 'select',
                'class' => 'wc-enhanced-select',
                'default' => 'vi',
                'desc_tip' => true,
                'options' => array(
                    'vi' => 'Vietnamese',
                    'eng' => 'English',
                )
            ),
            'currency' => array(
                'title' => __('Currency', self::$domain),
                'type' => 'select',
                'class' => 'wc-enhanced-select',
                'default' => 'VND',
                'desc_tip' => true,
                'options' => array(
                    'VND' => 'VNĐ',
                    'USD' => 'USD',
                )
            ),
            'title' => array(
                'title' => __('Title', self::$domain),
                'type' => 'text',
                'description' => __('This controls the title which the user sees during checkout.', self::$domain),
                'default' => __('Alepay Payment', self::$domain),
                'desc_tip' => true,
            ),
            'description' => array(
                'title' => __('Description', self::$domain),
                'type' => 'textarea',
                'description' => __('This controls the description which the user sees during checkout.', self::$domain),
                'default' => __('Chọn một phương thức', self::$domain),
                'desc_tip' => true,
            ),
            'order_status' => array(
                'title' => __('Order Status', self::$domain),
                'type' => 'select',
                'class' => 'wc-enhanced-select',
                'description' => __('Choose whether status you wish after checkout.', self::$domain),
                'default' => 'wc-processing',
                'desc_tip' => true,
                'options' => wc_get_order_statuses()
            ),
            'api_key' => array(
                'title' => __('API Key', self::$domain),
                'type' => 'textarea',
                //'description' => __( 'Instructions that will be added to the thank you page and emails.', self::$domain ),
                'default' => '',
                //'desc_tip'    => true,
            ),
            'encrypt_key' => array(
                'title' => __('Encrypt Key', self::$domain),
                'type' => 'textarea',
                //'description' => __( 'Instructions that will be added to the thank you page and emails.', self::$domain ),
                'default' => '',
                //'desc_tip'    => true,
            ),
            'checksum_key' => array(
                'title' => __('Checksum Key', self::$domain),
                'type' => 'textarea',
                //'description' => __( 'Instructions that will be added to the thank you page and emails.', self::$domain ),
                'default' => '',
                //'desc_tip'    => true,
            ),
            'env' => array(
                'title' => __('Môi trường (mặc định là test)', self::$domain),
                'type' => 'checkbox',
                'label' => __('Live', self::$domain),
                'default' => 'no'
            ),
            'payment_normal' => array(
                'title' => __('Kiểu thanh toán', self::$domain),
                'type' => 'checkbox',
                'label' => __('Thanh toán bằng thẻ quốc tế', self::$domain),
                'default' => 'yes'
            ),
			
            'payment_normal_desc' => array(
                'title' => __('', self::$domain),
                'type' => 'textbox',
                'label' => __('', self::$domain),
                'default' => 'Thanh toán bằng thẻ quốc tế'
            ),
			'payment_normal_domestic' => array(
                'title' => __('Kiểu thanh toán', self::$domain),
                'type' => 'checkbox',
                'label' => __('Thanh toán bằng thẻ ATM/IB', self::$domain),
                'default' => 'yes'
            ),
			'payment_normal_domestic_desc' => array(
                'title' => __('', self::$domain),
                'type' => 'textbox',
                'label' => __('', self::$domain),
                'default' => 'Thanh toán bằng thẻ ATM/IB'
            ),
            'payment_installment' => array(
                'title' => __('', self::$domain),
                'type' => 'checkbox',
                'label' => __('Trả góp', self::$domain),
                'default' => 'no'
            ),
            'payment_installment_desc' => array(
                'title' => __('', self::$domain),
                'type' => 'textbox',
                'label' => __('', self::$domain),
                'default' => 'Thanh toán trả góp'
            ),
            'payment_token' => array(
                'title' => __('', self::$domain),
                'type' => 'checkbox',
                'label' => __('Tokenization', self::$domain),
                'default' => 'no'
            ),
            'payment_token_desc' => array(
                'title' => __('', self::$domain),
                'type' => 'textbox',
                'label' => __('', self::$domain),
                'default' => 'Thanh toán dùng token'
            ),
        );
    }

    public function process_admin_options() {
        if (!$_POST['woocommerce_alepay_payment_normal'] 
                && !$_POST['woocommerce_alepay_payment_installment'] 
                && !$_POST['woocommerce_alepay_payment_token']) {
            $_POST['woocommerce_alepay_payment_normal'] = '1';
        }
        
        return parent::process_admin_options();
    }

    public function payment_fields() {
        global $wpdb;
        $log_content = 'Step 1 -------------------------------------------------';
        $table_name = $wpdb->prefix . 'alepay_token';
        $user_id = get_current_user_id();
        $checked_normal = 'checked';
        $checked_installment = '';
        $checked_token = '';
		$checked_domestic = '';
        
        if ( $description = $this->get_description() ) {
            echo wpautop( wptexturize( $description ) );
        }
        
        if ($user_id) { // dang nhap
            $row = $wpdb->get_row($wpdb->prepare("SELECT token FROM $table_name WHERE user_id = %d", $user_id));
            $checked_token = ($row && $row->token) ? 'checked' : '';
            
            $log_content .= PHP_EOL.'- User id: '.$user_id;
            if ($checked_token) { // // Neu khach hang da lien ket the
                $log_content .= PHP_EOL.'- Alepay token: '.$row->token;
            } else { // chua lien ket
                $log_content .= PHP_EOL.'- Alepay token: chua lien ket';
            }
        } else { // chua dang nhap
            $log_content .= PHP_EOL.'- User id: chua login';
            $log_content .= PHP_EOL.'- Alepay token: chua lien ket';
        }
        
        // Neu chua lien ket the, set mac dinh thanh toan thuong
        /* if (!$checked_token) {
            $checked_normal = 'checked';
        } */
        
        /*if ($user_id) {
            $row = $wpdb->get_row($wpdb->prepare("SELECT token FROM $table_name WHERE user_id = %d", $user_id));
            $checked = $row->token ? 'checked' : '';
            
            $log_content .= PHP_EOL.'- User id: '.$user_id;
            if ($checked) {
                $log_content .= PHP_EOL.'- Alepay token: '.$row->token;
            } else {
                $log_content .= PHP_EOL.'- Alepay token: chua lien ket';
            }*/
        ?>

        <!--<div id="custom_input">
          <p class="form-row form-row-wide">
            <label style="font-weight: normal;"><input type="checkbox" name="isCardLink" <?php //echo $checked; ?> /> Ghi nhớ thông tin thẻ cho các lần thanh toán tiếp theo</label>
          </p>
        </div>-->

        <div id="custom_input">
          <?php if ($this->payment_normal) { ?>
          <p class="form-row form-row-wide">
            <label style="font-weight: normal; padding-left: 30px;"><input type="radio" value="1" name="payment_alepay" <?php echo $checked_normal; ?> /> <?php echo $this->payment_normal_desc; ?></label>
          </p>
          <?php } if ($this->payment_installment) { ?>
          <p class="form-row form-row-wide">
            <label style="font-weight: normal; padding-left: 30px;"><input type="radio" value="2" name="payment_alepay" <?php echo $checked_installment; ?> /> <?php echo $this->payment_installment_desc; ?></label>
          </p>
		  <?php } if ($this->payment_normal_domestic) { ?>
          <p class="form-row form-row-wide">
            <label style="font-weight: normal; padding-left: 30px;"><input type="radio" value="4" name="payment_alepay" <?php echo $checked_domestic; ?> /> <?php echo $this->payment_normal_domestic_desc; ?></label>
          </p>
          <?php } if ($this->payment_token && $user_id) { ?>
          <p class="form-row form-row-wide">
            <label style="font-weight: normal; padding-left: 30px;"><input type="radio" value="3" name="payment_alepay" <?php echo $checked_token; ?> /> <?php echo $this->payment_token_desc; ?></label>
          </p>
          <?php } ?>
        </div>
        
        <?php
        /*} else {
            //echo '<div id="custom_input"><p class="form-row form-row-wide">Thanh toán qua cổng Alepay</p></div>';
            $log_content .= PHP_EOL.'- User id: chua login';
            $log_content .= PHP_EOL.'- Alepay token: chua lien ket';
        }*/
        
        if (is_ajax()) { // only call 1 time
            $this->log($log_content);
        }
        
        
    }

    /**
     * Process the payment and return the result.
     *
     * @param int $order_id
     * @return array
     */
    public function process_payment($order_id) {
        global $wpdb;
        $log_content = 'Step 2 -----------'; 
        $log_content .= PHP_EOL.'- Order id: '.$order_id;
        $log_content .= PHP_EOL.'- Phuong thuc thanh toan: ';
        $log_content .= $_POST['payment_alepay'] === '3' ? 'token' : ($_POST['payment_alepay'] === '2' ? 'tra gop' : 'thuong');
        $table_name = $wpdb->prefix . 'alepay_token';

        $order = wc_get_order($order_id);
        $order_data = $order->get_data();
        $order_items = $order->get_items();

        // Set order status
       /*  $status = 'wc-' === substr($this->order_status, 0, 3) ? substr($this->order_status, 3) : $this->order_status;
        if ($order->update_status($status, __('Checkout with alepay payment. ', $this->domain))) {
            $log_content .= PHP_EOL.'- Da cap nhat trang thai hoa don sang ['.$status.'] ';
        } else {
            $log_content .= PHP_EOL.'- Co loi khi cap nhat trang thai hoa don sang ['.$status.'] ';
        } */

        // Process alepay
        $alepay = $this->alepay;
        
        // check user logged or not
        /*if (!$order_data['customer_id']) {
            wc_add_notice(__('Cần đăng nhập để thanh toán qua Alepay', 'alepay_payment'), 'error');
            return;
        }*/

        // check connection card
        $row = $wpdb->get_row($wpdb->prepare("SELECT token FROM $table_name WHERE user_id = %d", $order_data['customer_id']));

        // Cancel link card if user dont select token method
        //if ($row->token && $_POST['payment_alepay'] !== '3') { 
        if (isset($row->token) && $row->token && $_POST['payment_alepay'] !== '3') { //namdeveloper
            $alepay->cancelCardLink($row->token);
            $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE token = %s AND user_id = %d", $row->token, $order_data['customer_id']));
            
            $log_content .= PHP_EOL.'- Goi api huy lien ket the';
            
            $row->token = null;
        }

        //if ($row->token && $_POST['payment_alepay'] === '3') { // connected card and continue select token method
        if (isset($row->token) && $row->token && $_POST['payment_alepay'] === '3') { // namdeveloper
            $data = $alepay->createTokenizationPaymentData($row->token);
            $data['orderCode'] = $order_data['order_key'];
            $data['amount'] = $order_data['total'];
            $data['currency'] = $this->currency ? $this->currency : $order_data['currency'];
            $data['orderDescription'] = $order_data['customer_note'] ? $order_data['customer_note'] : $order_data['order_key'];
            $data['returnUrl'] = $this->get_return_url($order);
            $data['cancelUrl'] = $order->get_cancel_order_url_raw();
            $url = $alepay->baseURL[$alepay->env] . $alepay->URI['tokenizationPayment'];
            
            $log_content .= PHP_EOL.'- Da lien ket the khi thanh toan';
        } else { // not connect card
            $data = $alepay->createCheckoutData();
            $data['checkoutType'] = $_POST['payment_alepay'];
            $data['amount'] = $order_data['total'];
            $data['buyerAddress'] = $order_data['billing']['address_1'] ? $order_data['billing']['address_1'] : $order_data['billing']['address_2'];
            $data['buyerCity'] = $order_data['billing']['state'];
            //$data['buyerCountry'] = $order_data['billing']['country'];
            $data['buyerCountry'] = "VN"; //namdeveloper
            $data['buyerEmail'] = $order_data['billing']['email'];
            $data['buyerName'] = trim($order_data['billing']['first_name'] . ' ' . $order_data['billing']['last_name']);
            //$data['buyerPhone'] = '0983986161';
			$data['buyerPhone'] = $order_data['billing']['phone'];
            $data['currency'] = $this->currency ? $this->currency : $order_data['currency'];
            $data['orderCode'] = $order_data['order_key'];
            $data['orderDescription'] = $order_data['customer_note'] ? $order_data['customer_note'] : $order_data['order_key'];
            $totalItem = 0;
            foreach ($order_items as $item_key => $item_values) {
                $item_data = $item_values->get_data();
                $totalItem += (int) $item_data['quantity'];
            }
            $data['totalItem'] = $totalItem;
            $data['returnUrl'] = $this->get_return_url($order);
            $data['cancelUrl'] = $order->get_cancel_order_url_raw();
            // 4 fields to connect card
            if ($_POST['payment_alepay'] === '3') { // token method
                $data['checkoutType'] = '1';
                $data['merchantSideUserId'] = $order_data['customer_id'];
                $data['buyerPostalCode'] = $order_data['billing']['postcode'];
                $data['buyerState'] = $order_data['billing']['state'] ? $order_data['billing']['state'] : 'VN';
                $data['isCardLink'] = true;
            }
			if ($_POST['payment_alepay'] === '4') { // domestic bank
				$data['allowDomestic'] = true;
            }
            //$url = $alepay->baseURL[$alepay->env] . $alepay->URI['requestPayment'].'?lang=eng';
			$url = $alepay->baseURL[$alepay->env] . $alepay->URI['requestPayment'];
            
            $log_content .= PHP_EOL.'- Chua lien ket the khi thanh toan';
        }

        $data['language'] = $this->language;
        $result = $alepay->sendRequestToAlepay($data, $url);
        if ($result->errorCode === '000') { // success
            $res = $alepay->alepayUtils->decryptData($result->data, $alepay->publicKey);
            $res = json_decode($res);
            
            if ($res->checkoutUrl) {
                $log_content .= PHP_EOL.'- Alepay tra ve checkoutUrl: '.$res->checkoutUrl;
                $return_url = $res->checkoutUrl;
            } else {
                $log_content .= PHP_EOL.'- Thanh cong nhung Alepay khong tra ve checkoutUrl';
                $return_url = $this->get_return_url( $order );
            }
            
            $this->log($log_content);

            // Return thankyou redirect
            return array(
                'result' => 'success',
                'redirect' => $return_url
            );
        } else { // failure
            wc_add_notice(__($result->errorDescription, 'alepay_payment'), 'error');
            
            $log_content .= PHP_EOL.'- Alepay tra ve loi: '.$result->errorDescription;
            $this->log($log_content);
            
            return;
        }
    }
    
    /**
     * Output for the order received page.
     */
    public function thankyou_page($order_id) {
        global $wpdb;
        
        $log_content = 'Step 3 -----------'; 
        $table_name = $wpdb->prefix . 'alepay_token';

        if ($this->instructions) {
            //echo wpautop(wptexturize($this->instructions));
        }
        
        $data = $this->alepay->decryptCallbackData($_GET['data'], $this->alepay->publicKey);
        $data = json_decode($data);
        
        if ($data->errorCode === '000') {
            
            // update order status
            $order = wc_get_order($order_id);
            //namdeveloper
            $transactionCode = $data->data;
            $transactionInfo = $this->alepay->getTransactionInfo($transactionCode);
            update_post_meta($order_id, '_alepay_transaction_info', $transactionInfo );
            //var_dump($transactionInfo); die();
            //end - namdeveloper

            if ($order->status !== 'processing') { //namdeveloper
                if ($order->update_status('processing', __('Update processing after penđding on Alepay. ', self::$domain))) {
                    // Reduce stock levels
                    $order->reduce_order_stock();

                    // Remove cart
                    WC()->cart->empty_cart();
                    
                    // Ghi log
                    $log_content .= PHP_EOL.'- Da cap nhat trang thai hoa don sang [processing] ';
                } else {
                    $log_content .= PHP_EOL.'- Co loi khi cap nhat trang thai hoa don sang [processing] ';
                }
            }
            
            // update token
            if ($token = $data->data->alepayToken) {
                $row = $wpdb->get_row($wpdb->prepare("SELECT id FROM $table_name WHERE token = %s", $token));
                if (!$row) {
                    $wpdb->insert(
                        $table_name, array(
                            'token' => $token,
                            'user_id' => get_current_user_id(),
                            'time' => current_time('mysql')
                        )
                    );
                    $log_content .= PHP_EOL.'- Cap nhat token: '.$token;
                }
            }
            
            // ghi log
            $log_content .= PHP_EOL.'- Thanh toan thanh cong';
        } else {
            $log_content .= PHP_EOL.'- Thanh toan loi voi errorCode: '.$data->errorCode;
        }
        
        $this->log($log_content);
    }

    /**
     * Logs
     *
     * @since 3.1.0
     * @version 3.1.0
     *
     * @param string $message
     */
    public function log($message) {
        if ($this->logging) {
            WC_Alepay::log($message);
        }
    }

}
