<?php


add_action('wp_ajax_get_address_shipping', 'get_address_shipping');
add_action('wp_ajax_nopriv_get_address_shipping', 'get_address_shipping');
function get_address_shipping()
{
    global  $wpdb;

    if (!isset($_POST['action']) && $_POST['action'] !== 'get_address_shipping' && $_POST['action_payload']) {
        echo json_encode(array(
            'status' => '500',
            'messenger' => 'No action map',
            'data' => []
        ));;
        exit;
    }

    $action_payload = $_POST['action_payload'];
    $id = $_POST['id'];

    if ($action_payload == 'get_district'){
        $data_district = $wpdb->get_results("Select tiki_code,district_name 
            from {$wpdb->prefix}woocommerce_district where left(tiki_code,5) = '{$id}' ");

        $data_ward = $wpdb->get_results("Select tiki_code,ward_name 
            from {$wpdb->prefix}woocommerce_ward where left(tiki_code,8) = '{$data_district['0']->tiki_code}'");
        echo json_encode(array(
            'status' => '200',
            'messenger' => 'Success',
            'data' => array(
                'district'=>$data_district,
                'ward'=>$data_ward
            )
        ));
        exit;
    }

    if ($action_payload == 'get_ward'){
        $data_ward = $wpdb->get_results("Select tiki_code,ward_name 
            from {$wpdb->prefix}woocommerce_ward where left(tiki_code,8) = '{$id}'");
        echo json_encode(array(
            'status' => '200',
            'messenger' => 'Success',
            'data' => $data_ward
        ));

        exit;
    }

    if ($action_payload == 'get_est_shipment'){

        $apiTiki = new \TIKI\TIKI_API();

        $total_amount = WC()->cart->total;


        $data =  array(
            'package_info' => array(
                'height'    =>  20,
                'width'     =>  20,
                'depth'     =>  20,
                'weight'    =>  2000,
                'total_amount'  => (int)$total_amount
            ),
            'destination'    => array(
                'street'        => '182 Lê Đại Hành',
                'ward_name'     => 'Phường 05',
                'district_name' => 'Quận 3',
                'province_name' => 'Hồ Chí Minh',
                'ward_code'     => "$id"
            )
        );


        $estS = $apiTiki->estimate_shipping($data);

        echo json_encode(array(
            'status' => '200',
            'messenger' => 'Success',
            'data' => $estS
        ));

        exit;

    }

    echo json_encode(array(
        'status' => '500',
        'messenger' => 'No result',
        'data' => []
    ));
    exit;

}