<?php

use OMS\ADDRESS;
use TIKI\TIKI_API;

add_action('woocommerce_shipping_init', 'tiki_tnsl_method');
function tiki_tnsl_method() {

    if ( ! class_exists( 'WC_TIKI_TNSL_Method' ) ) {
        class WC_TIKI_TNSL_Method extends WC_Shipping_Method {

            public function __construct( $instance_id = 0) {
                $this->id = 'tiki_tnsl';
                $this->instance_id = absint( $instance_id );
                $this->domain = 'ax_outlet';
                $this->method_title = __( 'Tiki TNSL', $this->domain );
                $this->method_description = __( 'Shipping method to be used where the exact shipping amount needs to be quoted', $this->domain );
                $this->supports = array(
                    'shipping-zones',
                    'instance-settings',
                    'instance-settings-modal',
                );
                $this->init();
            }

            ## Load the settings API
            function init() {
                $this->init_form_fields();
                $this->init_settings();
                $this->enabled = $this->get_option( 'enabled', $this->domain );
                $this->title   = $this->get_option( 'title', $this->domain );
                $this->info    = $this->get_option( 'info', $this->domain );
                add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
            }

            function init_form_fields() {
                $this->instance_form_fields = array(
                    'title' => array(
                        'type'          => 'text',
                        'title'         => __('Title', $this->domain),
                        'description'   => __( 'Title to be displayed on site.', $this->domain ),
                        'default'       => __( 'Tiki TNSL ', $this->domain ),
                    ),
                    'cost' => array(
                        'type'          => 'text',
                        'title'         => __('Coast', $this->domain),
                        'description'   => __( 'Enter a cost', $this->domain ),
                        'default'       => 0,
                    ),
                );
            }

            public function calculate_shipping( $packages = array() ) {
                $rate = array(
                    'id'       => $this->id,
                    'label'    => $this->title,
                    'cost'     => '0',
                    'calc_tax' => 'per_item'
                );
                $this->add_rate( $rate );
            }
        }
    }
}

add_filter('woocommerce_shipping_methods', 'add_tiki_tnsl');
function add_tiki_tnsl( $methods ) {
    $methods['tiki_tnsl'] = 'WC_TIKI_TNSL_Method';
    return $methods;
}

add_filter('woocommerce_shipping_packages','update_cost_shipping_tiki_tnsl',100,1);
function update_cost_shipping_tiki_tnsl($arg){

    global $tiki_ward;
    global $shipping_cost;

    if( isset($arg[0]['rates']['tiki_tnsl']) && $arg[0]['rates']['tiki_tnsl']->cost == 0)
    $arg[0]['rates']['tiki_tnsl']->cost = $shipping_cost;

    if (!isset($_POST['ward']) ){
        return $arg;
    }
    if ($tiki_ward == $_POST['ward']){
        return $arg;
    }

    $tiki_ward = $_POST['ward'];

    if ($_POST['shipping_method'][0] == 'tiki_tnsl'  ){

        $location = new ADDRESS();

        $apiTiki = new TIKI_API();

        $total_amount = WC()->cart->total;

        $total_weight = WC()->cart->get_cart_contents_weight() * 1000;

        $total_weight = $total_weight == 0 ? 1:$total_weight;


        $data =  array(
            'package_info' => array(
                'height'    =>  1,
                'width'     =>  1,
                'depth'     =>  1,
                'weight'    =>  $total_weight,
                'total_amount'  => (int)$total_amount
            ),
            'destination'    => array(
                'street'        => $_POST['address'],
                'ward_name'     => $location->get_ward_name_by_code($_POST['ward']) ?? '',
                'district_name' => $location->get_district_name_by_code($_POST['district']) ?? '',
                'province_name' => $location->get_city_name_by_code($_POST['city']) ?? '',
                'ward_code'     => $_POST['ward']
            )
        );

        $estimate = $apiTiki->estimate_shipping($data);

        if ($estimate->success){
            $shipping_cost = $estimate->data->quotes[0]->fee->amount;
        }
    }

    return $arg;
}


add_action('woocommerce_checkout_create_order','woocommerce_checkout_process_shipping_tiki_tnsl',10.2);
function woocommerce_checkout_process_shipping_tiki_tnsl(WC_Order $order)
{
    try {
        if (isset($_POST['shipping_method']) && $_POST['shipping_method'][0] == 'tiki_tnsl') {

            $location = new ADDRESS();

            $apiTiki = new TIKI_API();

            $shipping_cost = 0;

            $total_amount = $order->get_total('value');

            $total_weight = WC()->cart->get_cart_contents_weight() * 1000;

            $total_weight = $total_weight == 0 ? 1:$total_weight;

            $data =  array(
                'package_info' => array(
                    'height'    =>  1,
                    'width'     =>  1,
                    'depth'     =>  1,
                    'weight'    =>  $total_weight,
                    'total_amount'  => (int)$total_amount
                ),
                'destination'    => array(
                    'street'        => $_POST['billing_address_1'],
                    'ward_name'     => $location->get_ward_name_by_code($_POST['billing_ward']) ?? '',
                    'district_name' => $location->get_district_name_by_code($_POST['billing_district']) ?? '',
                    'province_name' => $location->get_city_name_by_code($_POST['billing_city']) ?? '',
                    'ward_code'     => $_POST['billing_ward']
                )
            );

            $estimate = $apiTiki->estimate_shipping($data);

            if ($estimate->success){
                $shipping_cost = $estimate->data->quotes[0]->fee->amount;
            }

            $order->set_shipping_total((int)$shipping_cost);
            $order->set_total($order->get_shipping_total() + $order->get_total('value'));
        }
    } catch (Throwable $e){
        write_log($e->getMessage());
    }
}
