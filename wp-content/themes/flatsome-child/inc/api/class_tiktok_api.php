<?php

namespace OMS;

Class Tiktok_Api
{
    private string $DOMAIN_AUTH;
    private string $DOMAIN_TOKEN;
    private string $DOMAIN_API;
    private string $access_token;
    private string $refresh_token;
    private string $app_key;
    private string $app_secret;
    private string $shop_id;
    private string $shop_cipher;
    private string $client_secret;
    private string $code_auth;
    private string $version;
    private array $queries ;
    private \WP_REST_API_Log_DB $log ;

    public function __construct()
    {
        $this->version = get_option('tiktok_version');

        $this->DOMAIN_AUTH = get_option('tiktok_auth_url');
        $this->DOMAIN_TOKEN = get_option('tiktok_token_url');
        $this->DOMAIN_API = get_option('tiktok_api_url');

        $this->app_key = get_option('tiktok_app_key');
        $this->app_secret = get_option('tiktok_app_secret');

        $this->access_token = get_option('tiktok_access_token');
        $this->refresh_token = get_option('tiktok_refresh_token');

        $this->shop_id = get_option('tiktok_shop_id');
        $this->shop_cipher = get_option('tiktok_shop_cipher');

        $this->client_secret = get_option('tiktok_client_secret');
        $this->code_auth = get_option('tiktok_code_auth');

        $this->log              = new \WP_REST_API_Log_DB();
    }

    /**
     * @param $url
     * @param $data
     * @param $method
     * @return false|mixed|string[]
     */

    public function sendRequestToTiktok($url,array $data = [],string $method = 'GET')
    {

        try {

            $data_request = json_encode($data);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json')
            );

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_request);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $rep = curl_exec($ch);
            $result = json_decode($rep );

            write_log($rep);

            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            write_log($http_status);

//            //Begin Write log in to WP rect log;
//            $headers = [
//                'access-token'             =>  $this->ACCESS_TOKEN,
//                'method'            =>  $method,
//                'Content-Type'      =>  'application/json',
//                'Content-Length'    =>  strlen($data_request)
//            ];
//            $arg = [
//                'route'         =>  $url,
//                'source'        =>  $this->TIKI_SOURCE,
//                'method'        =>  $method,
//                'status'        =>  $http_status,
//                'request'       =>  [
//                    'headers'    =>  $headers,
//                    'query_params'    =>  [],
//                    'body_params'    =>  $data,
//                    'body'      =>  $data_request,
//                ],
//                'response'      =>  [
//                    'headers'    =>  [],
//                    'body'      =>  $result
//                ]
//
//            ];
//            $this->log->insert($arg);
//            //End write log in to WP rect log;

            if ($http_status != 200) {
                return  $result;
            }

            return $result;
        } catch (\Throwable $th) {

            write_log( $th->getMessage());
            return false;
        }
    }


    public  function get_auth_url()
    {
        return $this->DOMAIN_AUTH;
    }

    public  function get_token_url()
    {
        return $this->DOMAIN_TOKEN;
    }

    public  function get_api_url()
    {
        return $this->DOMAIN_API;
    }

    public  function get_app_key()
    {
        return $this->app_key;
    }
    public  function get_app_secret()
    {
        return $this->app_secret;
    }
    public  function get_client_secret()
    {
        return $this->client_secret;
    }
    public  function get_shop_id()
    {
        return $this->shop_id;
    }
    public  function get_shop_cipher()
    {
        return $this->shop_cipher;
    }

    public function  get_code_auth()
    {
        return $this->code_auth;
    }

    public function  get_access_token()
    {
        return $this->access_token;
    }

    public function  get_refresh_token()
    {
        return $this->refresh_token;
    }

    public function  get_version()
    {
        return $this->version;
    }

    public function get_token()
    {
        $url = $this->get_token_url().'/api/v2/token/get?app_key='.$this->app_key;
        $url .= '&app_secret=' . $this->app_secret;
        $url .= '&auth_code=' . $this->code_auth;
        $url .= '&grant_type=authorized_code';

        $response = $this->sendRequestToTiktok($url,[],'GET');

        if (isset($response->code) && $response->code === 0){
            if(!add_option('tiktok_access_token',$response->data->access_token , '','no')){
                update_option('tiktok_access_token',$response->data->access_token , 'no');
            }

            if(!add_option('tiktok_refresh_token',$response->data->refresh_token , '','no')){
                update_option('tiktok_refresh_token',$response->data->refresh_token , 'no');
            }
        }

        return $response;
    }

    public function get_token_by_refresh_token()
    {
        $url = $this->get_token_url().'/api/v2/token/refresh?app_key='.$this->app_key;
        $url .= '&app_secret=' . $this->app_secret;
        $url .= '&refresh_token=' . $this->refresh_token;
        $url .= '&grant_type=refresh_token';

        $response = $this->sendRequestToTiktok($url,[],'GET');

        if (isset($response->code) && $response->code === 0){
            if(!add_option('tiktok_access_token',$response->data->access_token , '','no')){
                update_option('tiktok_access_token',$response->data->access_token , 'no');
            }

            if(!add_option('tiktok_refresh_token',$response->data->refresh_token , '','no')){
                update_option('tiktok_refresh_token',$response->data->refresh_token , 'no');
            }
        }

        return $response;
    }

    private function get_common_queries()
    {

        $queries['app_key'] = $this->app_key;
        $queries['app_secret'] = $this->app_secret;
        $queries['version'] = $this->version;
        $queries['shop_id'] = $this->shop_id;
        $queries['shop_cipher'] = $this->shop_cipher;
        $queries['access_token'] = $this->access_token;
        $queries['timestamp'] = $this->get_timestamp();

        $this->queries = $queries;
        $input ='';
        foreach ($queries as $key => $value) {
            $input .= $key .'='. $value . '&';
        }

        return $input;
    }

    private function get_timestamp()
    {
        return strtotime('now');
    }




    public function get_sign($secret, $path, $queries): string
    {

        unset($queries['access_token']);
        $keys = array_keys($queries);
        sort($keys);
        $input = $path;
        foreach ($keys as $key) {
            $input .= $key . $queries[$key];
        }
        $input = $secret . $input . $secret;
        return hash_hmac("sha256", $input, $secret);
    }


    public function get_authorized_shop()
    {

        $url = $this->get_api_url().'/api/shop/get_authorized_shop?'.$this->get_common_queries();
        $url.= 'sign='.$this->get_sign($this->app_secret,'/api/shop/get_authorized_shop',$this->queries);
        $response =  $this->sendRequestToTiktok($url,[],'GET');

        if ($response->code === 0)
        {

            if(!add_option('tiktok_shop_id',$response->data->shop_list[0]->shop_id , '','no')){
                update_option('tiktok_shop_id',$response->data->shop_list[0]->shop_id , 'no');
            }

            if(!add_option('tiktok_shop_cipher',$response->data->shop_list[0]->shop_cipher , '','no')){
                update_option('tiktok_shop_cipher',$response->data->shop_list[0]->shop_cipher , 'no');
            }
        }

        return $response;

    }

    public function get_order_list()
    {
        $url = $this->get_api_url().'/api/orders/search?'.$this->get_common_queries();
        $url.= 'sign='.$this->get_sign($this->app_secret,'/api/orders/search',$this->queries);
        $body = [
            'page_size' => 100
        ];
        $response =  $this->sendRequestToTiktok($url,$body,'POST');

        return $response->data;

    }


    public function get_order_detail()
    {
        $url = $this->get_api_url().'/api/orders/detail/query?'.$this->get_common_queries();
        $url.= 'sign='.$this->get_sign($this->app_secret,'/api/orders/detail/query',$this->queries);

        $order_list = $this->get_order_list();
        $order_ids = [];

        foreach ($order_list->order_list as $value)
        {
            $order_ids[] = $value->order_id;
        }

        $body = [
            'order_id_list' => $order_ids,
        ];
        $response =  $this->sendRequestToTiktok($url,$body,'POST');

        return $response->data;

    }

}
