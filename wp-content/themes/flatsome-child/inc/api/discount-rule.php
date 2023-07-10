<?php
use Wdr\App\Models\DBTable;
class WC_REST_Discount_Controller {
    /**
     * You can extend this class with
     * WP_REST_Controller / WC_REST_Controller / WC_REST_Products_V2_Controller / WC_REST_CRUD_Controller etc.
     * Found in packages/woocommerce-rest-api/src/Controllers/
     */
    protected $namespace = 'wc/v3';

    protected $rest_base = 'discounts';

    public function register_routes() {

        register_rest_route(
            $this->namespace, '/' . $this->rest_base, array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_items' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    //'args'                => $this->get_collection_params(),
                ),
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'create_item' ),
                    'permission_callback' => array( $this, 'create_item_permissions_check' ),
                    // 'args'                => array_merge(
                    // 	$this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ), array(
                    // 		'code' => array(
                    // 			'description' => __( 'Coupon code.', 'woocommerce' ),
                    // 			'required'    => true,
                    // 			'type'        => 'string',
                    // 		),
                    // 	)
                    // ),
                ),
                //'schema' => array( $this, 'get_public_item_schema' ),
            )
        );

        register_rest_route(
            $this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', array(
                'args'   => array(
                    'id' => array(
                        'description' => __( 'Unique identifier for the resource.', 'woocommerce' ),
                        'type'        => 'integer',
                    ),
                ),
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_item' ),
                    'permission_callback' => array( $this, 'get_item_permissions_check' ),
                    // 'args'                => array(
                    // 	'context' => $this->get_context_param( array( 'default' => 'view' ) ),
                    // ),
                ),
                array(
                    'methods'             => WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_item' ),
                    'permission_callback' => array( $this, 'update_item_permissions_check' ),
                    // 'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
                ),
                array(
                    'methods'             => WP_REST_Server::DELETABLE,
                    'callback'            => array( $this, 'delete_item' ),
                    'permission_callback' => array( $this, 'delete_item_permissions_check' ),
                    'args'                => array(
                        'force' => array(
                            'default'     => false,
                            'type'        => 'boolean',
                            'description' => __( 'Whether to bypass trash and force deletion.', 'woocommerce' ),
                        ),
                    ),
                ),
                //'schema' => array( $this, 'get_public_item_schema' ),
            )
        );

        register_rest_route(
            $this->namespace, '/' . $this->rest_base . '/batch', array(
                array(
                    'methods'             => WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'batch_items' ),
                    'permission_callback' => array( $this, 'batch_items_permissions_check' ),
                    // 'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
                ),
                //'schema' => array( $this, 'get_public_batch_schema' ),
            )
        );
    }

    /**
     * Check whether a given request has permission to read customers.
     *
     * @param  WP_REST_Request $request Full details about the request.
     * @return WP_Error|boolean
     */
    public function get_items_permissions_check( $request ) {
        if ( ! wc_rest_check_user_permissions( 'read' ) ) {
            return new WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
        }

        return true;
    }

    /**
     * Check if a given request has access create customers.
     *
     * @param  WP_REST_Request $request Full details about the request.
     *
     * @return bool|WP_Error
     */
    public function create_item_permissions_check( $request ) {
        // if ( ! wc_rest_check_user_permissions( 'create' ) ) {
        // 	return new WP_Error( 'woocommerce_rest_cannot_create', __( 'Sorry, you are not allowed to create resources.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
        // }

        return true;
    }

    /**
     * Check if a given request has access to read a customer.
     *
     * @param  WP_REST_Request $request Full details about the request.
     * @return WP_Error|boolean
     */
    public function get_item_permissions_check( $request ) {
        $id = (int) $request['id'];

        if ( ! wc_rest_check_user_permissions( 'read', $id ) ) {
            return new WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot view this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
        }

        return true;
    }

    /**
     * Check if a given request has access update a customer.
     *
     * @param  WP_REST_Request $request Full details about the request.
     *
     * @return bool|WP_Error
     */
    public function update_item_permissions_check( $request ) {
        $id = (int) $request['id'];

        if ( ! wc_rest_check_user_permissions( 'edit', $id ) ) {
            return new WP_Error( 'woocommerce_rest_cannot_edit', __( 'Sorry, you are not allowed to edit this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
        }

        return true;
    }

    /**
     * Check if a given request has access delete a customer.
     *
     * @param  WP_REST_Request $request Full details about the request.
     *
     * @return bool|WP_Error
     */
    public function delete_item_permissions_check( $request ) {
        $id = (int) $request['id'];

        if ( ! wc_rest_check_user_permissions( 'delete', $id ) ) {
            return new WP_Error( 'woocommerce_rest_cannot_delete', __( 'Sorry, you are not allowed to delete this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
        }

        return true;
    }

    /**
     * Check if a given request has access batch create, update and delete items.
     *
     * @param  WP_REST_Request $request Full details about the request.
     *
     * @return bool|WP_Error
     */
    public function batch_items_permissions_check( $request ) {
        if ( ! wc_rest_check_user_permissions( 'batch' ) ) {
            return new WP_Error( 'woocommerce_rest_cannot_batch', __( 'Sorry, you are not allowed to batch manipulate this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
        }

        return true;
    }

    public function get_items( ) {
        global $wpdb;
        //$rules = DBTable::getRules(null, null, 'all');
        $rules = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wdr_rules WHERE deleted = 0 ORDER BY `priority` ASC");
        $results = array();
        foreach ($rules as $i => $rule) {
            $rule = $this->prepare_item_for_response($rule, array());
            $results[$i] = $rule;
        }
        return $results;
    }

    public function create_item( $request ) {
        try {
            if ( ! empty( $request['id'] ) ) {
                throw new WC_REST_Exception( 'woocommerce_rest_customer_exists', __( 'Cannot create existing resource.', 'woocommerce' ), 400 );
            }

            // $discount_data = $request;
            // foreach ($discount_data as $key => $value) {
            // 	if(is_array($value)) {
            // 		$discount_data[$key] = json_encode($value);
            // 	}
            // }
            $id = $this->saveRule($request, NULL);
            $discount_data = $this->getRule($id);

            $response = $this->prepare_item_for_response( $discount_data, $request );
            $response = rest_ensure_response( $response );
            $response->set_status( 201 );

            return $response;
        } catch ( Exception $e ) {
            return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
        }
    }

    public function get_item( $request ) {
        global $wpdb;
        $id        = (int) $request['id'];
        $discount_data = $this->getRule($id);
        //var_dump($discount_data);

        if ( empty( $id ) || empty( $discount_data->id ) ) {
            return new WP_Error( 'woocommerce_rest_invalid_id', __( 'Invalid resource ID.', 'woocommerce' ), array( 'status' => 404 ) );
        }

        $rule = $this->prepare_item_for_response( $discount_data, $request );
        $response = rest_ensure_response( $rule );

        return $response;
    }

    public function prepare_item_for_response( $discount_data, $request ) {
        $result = array();
        foreach ($discount_data as $key => $value) {
            if($this->isJson($value)) {
                $value = json_decode($value, true);
                if(is_array($value)) {
                    $must_be_array = false;
                    foreach ($value as $k => $v) {
                        if((int)$k > 0) {
                            $must_be_array = true;
                            break;
                        }
                    }
                    if($must_be_array) {
                        $value = array_values($value);
                    }
                }
            }
            $result[$key] = $value;
        }
        return $result;
    }

    private function isJson($string) {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    private function getRule($rule_id)
    {
        global $wpdb;
        return $wpdb->get_row("SELECT * FROM {$wpdb->prefix}wdr_rules WHERE id={$rule_id} AND deleted = 0");
    }

    private function saveRule($values, $rule_id = NULL)
    {
        global $wpdb;
        $rules_table_name = $wpdb->prefix.'wdr_rules';
        $rule_title = $values['title'];
        $enabled = $values['enabled'];
        $exclusive = $values['exclusive'];
        $usage_limits = $values['usage_limits'];
        $date_from = $values['date_from'];
        $date_to = $values['date_to'];
        $rule_filters = $values['filters'];
        $rule_conditions = $values['conditions'];
        $rule_additional = $values['additional'];
        $product_adjustments = $values['product_adjustments'];
        $cart_adjustments = $values['cart_adjustments'];
        $buyx_getx_adjustments = $values['buyx_getx_adjustments'];
        $buy_x_get_y_adjustments = $values['buy_x_get_y_adjustments'];
        $bulk_adjustments = $values['bulk_adjustments'];
        $rule_language = $values['language'];
        $set_adjustments = $values['set_adjustments'];
        $discount_badge = $values['discount_badge'];
        $discount_type = $values['discount_type'];
        $awdr_coupon_names = $values['awdr_coupon_names'];
        $title = $values['title'];
        $arg = array(
            'title' => sanitize_text_field($rule_title),
            'enabled' => intval($enabled),
            'exclusive' => intval($exclusive),
            'usage_limits' => intval($usage_limits),
            'date_from' => $date_from,
            'date_to' => $date_to,
            'filters' => json_encode($rule_filters),
            'conditions' => json_encode($rule_conditions),
            'additional' => json_encode($rule_additional),
            'product_adjustments' => json_encode($product_adjustments),
            'cart_adjustments' => json_encode($cart_adjustments),
            'buy_x_get_x_adjustments' => json_encode($buyx_getx_adjustments),
            'buy_x_get_y_adjustments' => json_encode($buy_x_get_y_adjustments),
            'bulk_adjustments' => json_encode($bulk_adjustments),
            'rule_language' => json_encode($rule_language),
            'set_adjustments' => json_encode($set_adjustments),
            'advanced_discount_message' => json_encode($discount_badge),
            'discount_type' => esc_sql($discount_type),
            'used_coupons' => json_encode($awdr_coupon_names),
        );
        $current_date_time = '';
        if (function_exists('current_time')) {
            $current_time = current_time('timestamp');
            $current_date_time = date('Y-m-d H:i:s', $current_time);
        }
        $current_user = get_current_user_id();
        if (!is_null($rule_id) && !empty($rule_id)) {
            $arg['modified_by'] = intval($current_user);
            $arg['modified_on'] = esc_sql($current_date_time);
            $column_format = array('%s', '%d', '%d', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s');
        }else{
            $arg['created_by'] = intval($current_user);
            $arg['created_on'] = esc_sql($current_date_time);
            $arg['modified_by'] = intval($current_user);
            $arg['modified_on'] = esc_sql($current_date_time);
            $column_format = array('%s', '%d', '%d', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%d', '%s');
        }
        $rule_id = DBTable::saveRule($column_format, $arg, $rule_id);
        // if (!is_null($rule_id) && !empty($rule_id)) {
        //     $rule_id = intval($rule_id);
        //     $wpdb->update($rules_table_name, $values, array('id' => $rule_id), $column_format, array('%d'));
        // } else {
        //     $wpdb->insert($rules_table_name, $values, $column_format);
        //     $rule_id = $wpdb->insert_id;
        //     $wpdb->update($rules_table_name, array('priority' => $rule_id), array('id' => $rule_id), array('%d'), array('%d'));
        // }
        return $rule_id;
    }
}
add_filter( 'woocommerce_rest_api_get_rest_namespaces', 'woo_custom_api_discount' );
function woo_custom_api_discount( $controllers ) {
    $controllers['wc/v3']['discounts'] = 'WC_REST_Discount_Controller';

    return $controllers;
}