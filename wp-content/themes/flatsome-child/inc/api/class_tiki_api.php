<?php

namespace TIKI;

class TIKI_API
{
    private string $ACCESS_TOKEN;

    private string $CLIENT_ID;

    private string $SECRET_KEY;

    private string $SECRET_CLIENT;

    public string $baseURL;

    public string $baseURLTNSL;

    public array $URI = array(

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
                write_log($result);
                return array(
                    'messenger' => 'error http status code: ' . $http_status
                );
            }

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

        $data_default = array(
            'package_info' => array(
                'height'    =>  20,
                'width'     =>  20,
                'depth'     =>  20,
                'weight'    =>  2000,
                'total_amount'  => 1234567
            ),
            'origin'    => array(
                'street'        => '528 Huỳnh Tấn Phát',
                'ward_name'     => 'Phường Bình Thuận',
                'district_name' => 'Quận 7',
                'province_name' => 'Hồ Chí Minh',
                'ward_code'     => 'VN039015001'
            ),
            'destination'    => array(
            'street'        => '182 Lê Đại Hành',
            'ward_name'     => 'Phường 05',
            'district_name' => 'Quận 3',
            'province_name' => 'Hồ Chí Minh',
            'ward_code'     => 'VN039011005'
            )
        );

        $data = array_merge($data_default,$data);

        return $this->sendRequestToTiki($url,$data,'POST');;

    }


}