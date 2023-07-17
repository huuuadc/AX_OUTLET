<?php

namespace TIKI;

class TIKI_API
{

    private string $LS_TOKEN = 'ls_token';

    private string $user_name = '';
    private string $user_pass = '';
    private string $api_token = '';

    private string $encrypt_key = 'daf_ls_api';

    public string $env = 'test';

    public string $baseURL = 'https://api.tala.xyz/directory';

    public array $URI = array(

        'get_list_regions'          =>          '/v1/countries/VN/regions',
        'get_list_districts'        =>          '/districts',
        'get_list_ward'             =>          '/wards'
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

    }

    public function sendRequestToTiki($url, $data = '', $method = 'GET')
    {

        try {

            $data_string = json_encode($data);
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data_string))
            );

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            if (false) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $this->api_token));
            }

            $result = curl_exec($ch);

            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($http_status != 200) {
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

    public function get_regions_tiki(){

        $url = $this->baseURL . $this->URI['get_list_regions'];

        $response = $this->sendRequestToTiki($url,'','GET');

        return $response->data;

    }

    public function get_regions_with_id_tiki($region_id){

        $url = $this->baseURL . $this->URI['get_list_regions'].'/'.$region_id;

        $response = $this->sendRequestToTiki($url,'','GET');

        return $response->data;

    }

    public function get_districts_with_region_tiki($region_id){

        $url = $this->baseURL . $this->URI['get_list_regions'].'/'.$region_id. $this->URI['get_list_districts'];

        $response = $this->sendRequestToTiki($url,'','GET');

        return $response->data;

    }

    public function get_districts_with_region_district_id_tiki($region_id = '', $district_id = ''){

        $url = $this->baseURL . $this->URI['get_list_regions'].'/'.$region_id. $this->URI['get_list_districts'].'/'.$district_id;

        $response = $this->sendRequestToTiki($url,'','GET');

        return $response->data;

    }

    public function get_wards_with_region_district_tiki($region_id = '', $district_id = ''){

        $url = $this->baseURL . $this->URI['get_list_regions'].'/'.$region_id. $this->URI['get_list_districts'].'/'.$district_id. $this->URI['get_list_ward'];

        $response = $this->sendRequestToTiki($url,'','GET');

        return $response->data;

    }

    public function get_wards_with_region_district_ward_id_tiki($region_id = '', $district_id = '', $ward_id = ''){

        $url = $this->baseURL . $this->URI['get_list_regions'].'/'.$region_id. $this->URI['get_list_districts'].'/'.$district_id. $this->URI['get_list_ward'].'/'.$ward_id;

        $response = $this->sendRequestToTiki($url,'','GET');

        return $response->data;

    }


}