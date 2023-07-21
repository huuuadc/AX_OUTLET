<?php

//add_action('woocommerce_shipping_init', 'request_shipping_quote_method');
function request_shipping_quote_method() {

    if ( ! class_exists( 'WC_Request_Shipping_Quote_Method' ) ) {
        class WC_Request_Shipping_Quote_Method extends WC_Shipping_Method {

            public function __construct( $instance_id = 0) {
                $this->id = 'request_shipping_quote';
                $this->instance_id = absint( $instance_id );
                $this->domain = 'rasq';
                $this->method_title = __( 'Request a Shipping Quote', $this->domain );
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
                        'default'       => __( 'Request a Quote ', $this->domain ),
                    ),
                    'cost' => array(
                        'type'          => 'text',
                        'title'         => __('Coast', $this->domain),
                        'description'   => __( 'Enter a cost', $this->domain ),
                        'default'       => '',
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

//add_filter('woocommerce_shipping_methods', 'add_request_shipping_quote');
function add_request_shipping_quote( $methods ) {
    $methods['request_shipping_quote'] = 'WC_Request_Shipping_Quote_Method';
    return $methods;
}