<?php

namespace OMS;
use OMS\COMPANY;

class TIKI_API
{
    private string $ACCESS_TOKEN;
    private string $CLIENT_ID;
    private string $SECRET_KEY;
    private string $SECRET_CLIENT;
    private string $baseURL;
    private string $baseURLTNSL;
    private object $company;
    private string $INVALID_TOKEN = 'INVALID_TOKEN';
    public array  $data_default = array(
        'external_order_id' => '',
        'service_code'  => 'hns_standard',
        'partner_code'  => 'TNSL',
        'cash_on_delivery_amount'   => 0,
        'instruction'   => 'Được phép đồng kiểm khi nhận hàng.',
        'package_info' => array(
            'height'    =>  0,
            'width'     =>  0,
            'depth'     =>  0,
            'weight'    =>  0,
            'total_amount'  => 0
        ),
        'origin'    => array(
            'first_name'    => '',
            'last_name' => '',
            'phone'     => '',
            'email'     => '',
            'street'        => '',
            'ward_name'     => '',
            'district_name' => '',
            'province_name' => '',
            'ward_code'     => ''
        ),
        'destination'    => array(
            'first_name'    => '',
            'last_name' => '',
            'phone'     => '',
            'email'     => '',
            'street'        => '',
            'ward_name'     => '',
            'district_name' => '',
            'province_name' => '',
            'ward_code'     => ''
        ),
        'return_destination'    => array(
            'first_name'    => '',
            'last_name' => '',
            'phone'     => '',
            'email'     => '',
            'street'        => '',
            'ward_name'     => '',
            'district_name' => '',
            'province_name' => '',
            'ward_code'     => ''
        ),
        'product_name'      => '',
        'placed_on'         => 'tiki'
    );

    public array $default_cancel = array (
        'reason_code'  => 'OUT_OF_STOCK',
        'comment'   => 'test api call create shipment'
        );
    private array $URI = array(

        'get_list_regions'          =>          '/v1/countries/VN/regions',
        'get_list_districts'        =>          '/districts',
        'get_list_ward'             =>          '/wards',
        'get_token'                 =>          '/v1/oauth2/token',
        'get_quotes'                =>          '/v1/quotes',
        'sync_order'                =>          '/v1/shipments',
        'cancel_order'              =>          '/v1/shipments',

    );


    /**
     * @throws Exception
     */
    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Credentials: true");
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header('Access-Control-Max-Age: 1000');
        header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');

        $this->baseURL          = get_option('tiki_base_url_address') ?? '';
        $this->baseURLTNSL      = get_option('tiki_base_url_tnsl') ?? '';
        $this->CLIENT_ID        = get_option('tiki_client_id') ?? '';
        $this->SECRET_KEY       = get_option('tiki_secret_key') ?? '';
        $this->SECRET_CLIENT    = get_option('tiki_secret_client') ?? '';
        $this->ACCESS_TOKEN     = get_option('tiki_access_token') ?? '';
        $this->company          = new COMPANY();

        //{"success":false,"errors":[{"message":"Forbidden","code":"INVALID_TOKEN"}],"metadata":{"request_id":"c2e612e212e1a70fdc33dca2a7bada81"}}

    }

    /**
     * @param $url
     * @param $data
     * @param $method
     * @return false|mixed|string[]
     */

    public function sendRequestToTiki($url, $data = '', $method = 'GET')
    {

        try {

            write_log($data);

            $data_request = json_encode($data);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data_request))
            );

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_request);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            if ($this->ACCESS_TOKEN) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $this->ACCESS_TOKEN));
            }

            $result = curl_exec($ch);

            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($http_status != 200) {
                write_log("===========================================");
                write_log("===============request======================");
                write_log($result);
                write_log("===============request======================");
                write_log("===========================================");
                return  json_decode( $result);
            }

            write_log($result);

            return json_decode($result);
        } catch (\Throwable $th) {

            write_log( $th->getMessage());
            return false;
        }
    }

    /**
     * @return mixed
     */

    public function get_regions_tiki(){

        $url = $this->baseURL . $this->URI['get_list_regions'];

        $response = $this->sendRequestToTiki($url,'','GET');

        return $response->data;

    }

    /**
     * @param $region_id
     * @return mixed
     */

    public function get_regions_with_id_tiki($region_id){

        $url = $this->baseURL . $this->URI['get_list_regions'].'/'.$region_id;

        $response = $this->sendRequestToTiki($url,'','GET');

        return $response->data;

    }

    /**
     * @param $region_id
     * @return mixed
     */

    public function get_districts_with_region_tiki($region_id){

        $url = $this->baseURL . $this->URI['get_list_regions'].'/'.$region_id. $this->URI['get_list_districts'];

        $response = $this->sendRequestToTiki($url,'','GET');

        return $response->data;

    }

    /**
     * @param $region_id
     * @param $district_id
     * @return mixed
     */

    public function get_districts_with_region_district_id_tiki($region_id = '', $district_id = ''){

        $url = $this->baseURL . $this->URI['get_list_regions'].'/'.$region_id. $this->URI['get_list_districts'].'/'.$district_id;

        $response = $this->sendRequestToTiki($url,'','GET');

        return $response->data;

    }

    /**
     * @param $region_id
     * @param $district_id
     * @return mixed
     */

    public function get_wards_with_region_district_tiki($region_id = '', $district_id = ''){

        $url = $this->baseURL . $this->URI['get_list_regions'].'/'.$region_id. $this->URI['get_list_districts'].'/'.$district_id. $this->URI['get_list_ward'];

        $response = $this->sendRequestToTiki($url,'','GET');

        return $response->data;

    }

    /**
     * @param $region_id
     * @param $district_id
     * @param $ward_id
     * @return mixed
     */

    public function get_wards_with_region_district_ward_id_tiki($region_id = '', $district_id = '', $ward_id = ''){

        $url = $this->baseURL . $this->URI['get_list_regions'].'/'.$region_id. $this->URI['get_list_districts'].'/'.$district_id. $this->URI['get_list_ward'].'/'.$ward_id;

        $response = $this->sendRequestToTiki($url,'','GET');

        return $response->data;

    }

    /**
     * @return string
     */

    public function get_token(){

        $url = $this->baseURLTNSL . $this->URI['get_token'];

        $data = array(
            'client_id' => $this->CLIENT_ID,
            'secret' => $this->SECRET_KEY
        );

        $rep = $this->sendRequestToTiki($url,$data,'POST');

        return $rep->data->access_token ?? '';
    }

    /**
     * @param $data
     * @return false|mixed|string[]
     */

    public function estimate_shipping($data){
        $url = $this->baseURLTNSL . $this->URI['get_quotes'];

        $this->data_default['origin']['street'] = $this->company->get_company_address();
        $this->data_default['origin']['ward_name'] = $this->company->get_company_ward_name();
        $this->data_default['origin']['district_name'] = $this->company->get_company_district_name();
        $this->data_default['origin']['province_name'] = $this->company->get_company_city_name();
        $this->data_default['origin']['ward_code'] = $this->company->get_company_ward_code();

        $data = array_merge($this->data_default,$data);

        $rep = $this->sendRequestToTiki($url,$data,'POST');

        //Refresh token when invalid token
        if (!$rep->success && $rep->errors[0]->code == $this->INVALID_TOKEN){
            $token = $this->get_token();
            $this->ACCESS_TOKEN = $token;
            if(!add_option('tiki_access_token',$token,'','no')){
                update_option('tiki_access_token', $token,'no');
            }
            $rep = $this->sendRequestToTiki($url,$data,'POST');
        }

        return $rep;

    }

    /**
     * @param $data
     * @return json
     */

    public function post_create_shipping_to_tiki($data){
        $url = $this->baseURLTNSL.$this->URI['sync_order'];

        $this->data_default['origin']['first_name'] = $this->company->get_company_name();
        $this->data_default['origin']['last_name']  = $this->company->get_company_name();
        $this->data_default['origin']['phone'] = $this->company->get_company_phone();
        $this->data_default['origin']['email'] = $this->company->get_company_email();
        $this->data_default['origin']['street'] = $this->company->get_company_address();
        $this->data_default['origin']['ward_name'] = $this->company->get_company_ward_name();
        $this->data_default['origin']['district_name'] = $this->company->get_company_district_name();
        $this->data_default['origin']['province_name'] = $this->company->get_company_city_name();
        $this->data_default['origin']['ward_code'] = $this->company->get_company_ward_code();
        $this->data_default['return_destination']['first_name'] = $this->company->get_company_name();
        $this->data_default['return_destination']['last_name']  = $this->company->get_company_name();
        $this->data_default['return_destination']['phone'] = $this->company->get_company_phone();
        $this->data_default['return_destination']['email'] = $this->company->get_company_email();
        $this->data_default['return_destination']['street'] = $this->company->get_company_address();
        $this->data_default['return_destination']['ward_name'] = $this->company->get_company_ward_name();
        $this->data_default['return_destination']['district_name'] = $this->company->get_company_district_name();
        $this->data_default['return_destination']['province_name'] = $this->company->get_company_city_name();
        $this->data_default['return_destination']['ward_code'] = $this->company->get_company_ward_code();

        $data = array_merge($this->data_default,$data);

        return $this->sendRequestToTiki($url,$data,'POST');;

    }

    /**
     * @param $tracking_id
     * @return json
     */
    public function put_cancelled_shippment($tracking_id){

        $url = $this->baseURLTNSL.$this->URI['sync_order'].'/'.$tracking_id;

        return $this->sendRequestToTiki($url,$this->default_cancel,'PUT');

    }


}