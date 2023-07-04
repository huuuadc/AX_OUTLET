<?php

namespace LS;

class LS_API
{

    private string $LS_TOKEN = 'ls_token';

    private string $user_name = '';
    private string $user_pass = '';
    private string $api_token = '';

    private string $encrypt_key = 'daf_ls_api';

    public string $env = 'test';

    public string $baseURL = '';

    public array $URI = array(

        'get_token' => '/api/user/loginInput',

        'get_member_info' => '/api/member/MemberInformation',
        'get_member_his' => '/api/member/MemberHistory',
        'get_member_check' => '/api/member/CheckMember',
        'post_member_create' => '/api/member/MemberCreate',
        'post_member_update' => '/api/member/MemberUpdate',
        'post_member_outlet' => '/api/member/CheckMemberOutlet',

        'get_product_master_file' => '/api/product/GetMasterFile',
        'get_product_inventory' => '/api/product/GetInventory',
        'get_product_check_stock2' => '/api/product/CheckStockV2',
        'post_product_check_stock' => '/api/product/CheckStock',
        'post_product_check_price' => '/api/product/CheckPrice',

        'get_promotion' => '/api/promotion/GetPromotion',

        'post_transaction' => '/api/transactions/Transaction_Outlet',
        'post_payment' => '/api/transactions/Payment_Outlet',

    );


    /**
     * @throws Exception
     */
    public function __construct($opts)
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Credentials: true");
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header('Access-Control-Max-Age: 1000');
        header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');

        /*
         * Require curl and json extension
         */

        if (!function_exists('curl_init')) {
            throw new Exception('LS needs the CURL PHP extension.');
        }
        if (!function_exists('json_decode')) {
            throw new Exception('LS needs the JSON PHP extension.');
        }

        if (isset($opts) && !empty($opts["user_name"])) {
            $this->user_name = $opts["user_name"];
        }

        if (isset($opts) && !empty($opts["user_pass"])) {
            $this->user_pass = $opts["user_pass"];
        }

        $this->baseURL = get_option('wc_settings_tab_ls_api_url');

        if (isset($opts) && !empty($opts["base_url"])) {
            $this->baseURL = $opts["base_url"];
        }

        if ($this->api_token == ''){
            $this->api_token = $this->getLsToken();
        }

    }

    function notification_ls(){
        return 'Lõi chưa cài đặt';
    }

    /**
     * write log to file
     */

    function writeApiLogs($name, $error)
    {

        try {


        } catch (\Throwable $th) {

            return false;
        }


    }

    /**
     *  send request to ls
     * @param  $url : string
     * @param  $data : array
     * @param  $method : string default GET, GET, POST, PUT, PATCH, DELETE, OPTIONS
     *
     * @return array
     *
     */

    public function sendRequestToLS($url, $data = '', $method = 'GET')
    {

        try {
            write_log($data);
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

            if ($this->api_token) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $this->api_token));
            }

            $result = curl_exec($ch);

            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($http_status != 200) {
                return array(
                    'messenger' => 'error http status code: ' . $http_status
                );
            }

            write_log(json_decode($result));
            write_log('============================================');

            return json_decode($result);
        } catch (\Throwable $th) {

            write_log( $th->getMessage());
            return false;
        }
    }

    public function return_json($error, $message = "", $data = array()): void
    {
        header('Content-Type: application/json');
        echo json_encode(array(
            "error" => $error,
            "message" => $message,
            "data" => $data
        ));
    }

    /**
     * @param $string
     * @param $key
     * @return string
     *
     */

    public function encrypt($string, $key): string
    {

        $result = '';
        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) + ord($keychar));
            $result .= $char;
        }
        return base64_encode($result);
    }

    /**
     * @param $string
     * @param $key
     * @return string
     *
     */

    public function decrypt($string, $key): string
    {

        $result = '';
        $string = base64_decode($string);
        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) - ord($keychar));
            $result .= $char;
        }

        return $result;
    }

    /**
     * @return string
     *
     */

    public function getLsToken(): string
    {

        // get token in cookie
        if (isset($_COOKIE[$this->LS_TOKEN])) {
            return base64_decode($_COOKIE[$this->LS_TOKEN]);
        }

        $url = $this->baseURL . $this->URI['get_token'] . '?UserNames=' . $this->user_name . '&Password=' . $this->user_pass;

        $response = $this->sendRequestToLS($url, '', 'POST');

        if (isset($response->status) && $response->status == 200) {

            $ls_token = substr($response->token, 7, strlen($response->token));
        } else {
            $ls_token = '';
            write_log('Login'. json_encode($response));
        }

        if (version_compare(phpversion(),'8.0.0') >= 0) {
//            setcookie($this->LS_TOKEN, base64_encode($ls_token), time() + (1800), "/","/", secure: true,httponly: true ); // 86400 = 1 day  php > 8.
            setcookie($this->LS_TOKEN, base64_encode($ls_token), time() + (1800), "/");
        }
        if (version_compare(phpversion(),'8.0.0') < 0) {
            setcookie($this->LS_TOKEN, base64_encode($ls_token), time() + (1800), "/"); // 86400 = 1 day  php > 8.
        }



        return $ls_token;

    }

    /**
     * @return string
     */

    public function checkLsToken(): array
    {

        $status = false;

        $url = $this->baseURL . $this->URI['get_token'] . '?UserNames=' . $this->user_name . '&Password=' . $this->user_pass;

        $response = $this->sendRequestToLS($url, '', 'POST');


        if (isset($response->status) && $response->status == 200) {
            $status = true;
            $ls_token = substr($response->token, 7, strlen($response->token));
        } else {
            $ls_token = json_encode($response);
        }


        return array('status'=>$status, 'rep'=>$ls_token);

    }

    /**
     * @param $page_size
     * @param $account_no
     * @return array|false|string[]
     *
     */
    public function get_member_information($page_size = 10, $account_no = '')
    {

        $url = $this->baseURL . $this->URI['get_member_info'] . '?PageSize=' . $page_size;

        if ($account_no) {
            $url = $url . '&AccountNo=' . $account_no;
        }

        return $this->sendRequestToLS($url, '', 'GET');

    }

    /*
     *
     *
     *
     *
     * **/

    public function get_member_history($account_no = '')
    {

        $url = $this->baseURL . $this->URI['get_member_his'] . '?AccountNo=' . $account_no;
        return $this->sendRequestToLS($url, '', 'GET');

    }


    /*
     *
     *
     *
     *
     * **/

    public function get_member_check($number_phone = '', $club_code = 'DAFC')
    {
        try {

            $url = $this->baseURL . $this->URI['get_member_check'] . '?Phone=' . $number_phone . '&ClubCode=' . $club_code;

            return $this->sendRequestToLS($url, '', 'GET');

        } catch (\Throwable $th) {

        }
    }

    /**
     * @return void
     */

    public function user_request()
    {

    }

    /**
     * @param $user_info
     * @return array|false|string[]
     */

    public function post_member_create($user_info = [])
    {
        try {

            $url = $this->baseURL . $this->URI['post_member_create'];

//            $data = array(
//                'LoginID'       =>  $user_info['LoginID'],
//                'Password'      =>  $user_info['Password'],
//                'FirstName'     =>  $user_info['FirstName'],
//                'MiddleName'    =>  $user_info['MiddleName'],
//                'LastName'      =>  $user_info['LastName'],
//                'DateOfBirth'   =>  $user_info['DateOfBirth'],
//                'Phone'         =>  $user_info['Phone'],
//                'Address'       =>  $user_info['Address'],
//                'PostCode'      =>  $user_info['PostCode'],
//                'Email'         =>  $user_info['Email'],
//                'Gender'        =>  $user_info['Gender'],
//                'City'          =>  $user_info['City'],
//                'Distrist'      =>  $user_info['District'],
//                'Ward'          =>  $user_info['Ward'],
//                'Country'       =>  $user_info['Country'],
//                'UserCreate'    =>  $user_info['UserCreate'],
//                'AgeGroup'      =>  $user_info['AgeGroup'],
//                'Passport'      =>  $user_info['Passport'],
//                'Floor'         =>  $user_info['Floor'],
//                'Block'         =>  $user_info['Block'],
//                'Name'          =>  $user_info['Name'],
//            );

            $data = array(
                'LoginID' => '',
                'Password' => '',
                'FirstName' => '',

            );

            return $this->sendRequestToLS($url, $data, 'POST');

        } catch (\Throwable $th) {

            write_log('Member'. $th->getMessage());
            return false;

        }
    }


}