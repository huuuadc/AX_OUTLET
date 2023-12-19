<?php
/**
 * Plugin name: Payoo
 * Description: Tích hợp Cổng thanh toán Payoo vào Woocommerce 3.2.6
 * Version: 1.0.0
 * Author: Duy.Thai
 */

add_action('plugins_loaded', 'woocommerce_PayooVN_init', 0);

function woocommerce_PayooVN_init(){
  if(!class_exists('WC_Payment_Gateway')) return;


  class WC_PayooVN extends WC_Payment_Gateway{
    public function __construct(){

		global $woocommerce;

        $woocommerce_version = function_exists( 'WC' ) ? WC()->version : $woocommerce->version;

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

       $this -> form_fields = array(
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
                    'default' => __('Hỗ trợ thanh toán qua thẻ ATM/Visa/Master/JCB/AMEX/QRCode', 'woocommerce')),
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
                    'title' => __('Payoo CheckOut Url', 'woocommerce'),
                    'type' => 'text',
                    'description' => __(''))
            );
    }

    /**
     *  There are no payment fields for PayooVN, but we want to show the description if set.
     **/
    function payment_fields(){
        if($this -> description) echo wpautop(wptexturize('<img src="https://www.payoo.vn/website/static/css/image/payoo-logo.png" /><br /><br />'.$this -> description));
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
		<customer><name>'.  $name .'</name><phone>'. $phone .'</phone><email>'. $email .'</email></customer><jsonresponse>true</jsonresponse></shop></shops>';
		

		$checksum= hash('sha512',$this -> checksum_key.$str);//sha1($this -> checksum_key.$str);
		
		$ch = curl_init();

		$data = array('data' => $str, 'checksum' => $checksum, 'refer' => $this->shop_domain);

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
          'redirect'  => add_query_arg('order', $order->get_id(), add_query_arg('key', $order->get_order_key(), get_permalink(wc_get_page_id('checkout'))))
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
							
							$res = $order->update_status('processing','Payoo');
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
				  $thankyou_page =  add_query_arg('data',$cs, add_query_arg('key', $order->get_order_key(),get_permalink(wc_get_page_id('checkout')).'/order-received/'.$order->get_id().'/'));
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

    add_filter('woocommerce_payment_gateways', 'woocommerce_add_PayooVN_gateway' );
}