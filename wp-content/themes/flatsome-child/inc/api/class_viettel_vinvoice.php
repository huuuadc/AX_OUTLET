<?php

namespace OMS;
Class Viettel_Invoice
{
    private string $username = '';
    private string $password = '';
    private string $base_url = '';
    private string $access_token = '';
    private string $refresh_token = '';
    private string $template_code = '';
    private string $invoice_series = '';
    private string $transaction_uu_id = '';
    private string $company_tax_code = '';
    public array $buyer_info = [
        "buyerAddressLine" => "người mua không cung cấp",
        "buyerEmail"=> "huuuadc@gmail.com",
        "buyerIdNo"=> "",
        "buyerIdType"=> "",
        "buyerLegalName"=> "",
        "buyerName"=> "Khách hàng test",
        "buyerPhoneNumber"=> "0326473067",
        "buyerTaxCode"=> ""
    ];
    public array $general_invoice_tnfo = [
        "adjustmentType"=> "1",
        "currencyCode"=> "VND",
        "cusGetInvoiceRight"=> true,
        "invoiceIssuedDate"=> "",
        "invoiceSeries"=> "AB/20E",
        "invoiceType"=> "1",
        "originalInvoiceIssueDate"=> 0,
        "paymentStatus"=> true,
        "paymentType"=> "TM/CK",
        "paymentTypeName"=> "TM/CK",
        "templateCode"=> "02GTTT0/060",
//        "transactionUuid"=> "2bbfc858-5af5-4837-a116-bfebd02a50bE"
    ];
    public array $item_info = [
        "discount"=> 0.0,
        "itemCode"=> "TEST00001",
        "itemDiscount"=> 0.0,
        "itemName"=> "TEST00001-sản phẩm test",
        "itemTotalAmountWithoutTax"=> 12876898.0,
        "lineNumber"=> 16,
        "quantity"=> 1,
        "taxAmount"=> 1030152.0,
        "taxPercentage"=> 8,
        "unitName"=> "CAI",
        "unitPrice"=> 12876898.0
    ];
    public array $seller_info = [
        "sellerAddressLine"=> "",
        "sellerBankAccount"=> "",
        "sellerBankName"=> "",
        "sellerCode"=> "",
        "sellerEmail"=> "",
        "sellerLegalName"=> "",
        "sellerPhoneNumber"=> "",
        "sellerTaxCode"=> ""
    ];
    public array $summarize_info = [
        "discountAmount"=> 0.0,
        "isDiscountAmtPos"=> true,
        "isTotalAmountPos"=> true,
        "isTotalAmtWithoutTaxPos"=> true,
        "isTotalTaxAmountPos"=> true,
        "sumOfTotalLineAmountWithoutTax"=> 12876898.0,
        "totalAmountWithoutTax"=> 12876898.0,
        "totalAmountWithTax"=> 13907050.0,
        "totalAmountWithTaxInWords"=> "Hai trăm chín mươi tư triệu sáu trăm bảy mươi nghìn đồng chẵn",
        "totalTaxAmount"=> 1030152.0
    ];

    public array $tax_breakdowns = [
        [
            "taxableAmount"=> 0.0,
            "taxAmount"=> 0.0,
            "taxPercentage"=> 10.0
        ],
        [
            "taxableAmount"=> 0.0,
            "taxAmount"=> 0.0,
            "taxPercentage"=> 8.0
        ],
        [
            "taxableAmount"=> 0.0,
            "taxAmount"=> 0.0,
            "taxPercentage"=> 5.0
        ],
        [
            "taxableAmount"=> 0.0,
            "taxAmount"=> 0.0,
            "taxPercentage"=> 0.0
        ],
    ];

    private string $SOURCE = 'VIETTEL_VINVOICE';
    protected \WP_REST_API_Log_DB $log;


    public function __construct()
    {

        $this->base_url = get_option('viettel_base_url');
        $this->username = get_option('viettel_username');
        $this->password = get_option('viettel_password');
        $this->company_tax_code = '0100109106-503';

        $this->log = new \WP_REST_API_Log_DB();

    }

    /**
     * @param $url
     * @param $data
     * @param $method
     * @return false|mixed|string[]
     */

    public function sendRequestToServer($url,array $data = [],string $method = 'GET')
    {

        try {

            $data_request = json_encode($data);


            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Connection: keep-alive',
                    'Cookie: access_token='.$this->access_token
                )
            );

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_request);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $rep = curl_exec($ch);
            $result = json_decode($rep );

            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            //Begin Write log in to WP rect log;
            $headers = [
                'method'            =>  $method,
                'Content-Type'      =>  'application/json',
                'Content-Length'    =>  strlen($data_request)
            ];
            $arg = [
                'route'         =>  $url,
                'source'        =>  $this->SOURCE,
                'method'        =>  $method,
                'status'        =>  $http_status,
                'request'       =>  [
                    'headers'    =>  $headers,
                    'query_params'    =>  [],
                    'body_params'    =>  $data,
                    'body'      =>  $data_request,
                ],
                'response'      =>  [
                    'headers'    =>  [],
                    'body'      =>  $result
                ]

            ];
            $this->log->insert($arg);
            //End write log in to WP rect log;

            if ($http_status != 200) {
                return (object)array(
                    'Responcode' => $http_status,
                    'messenger' => 'error http status code: ' . $http_status
                );
            }

            return $result;
        } catch (\Throwable $th) {

            write_log( $th->getMessage());
            return false;
        }
    }

    function get_access_token()
    {
        $url = $this->base_url . '/auth/login';
        $body = [
            'username' => $this->username,
            'password' => $this->password
        ];

        $response = $this->sendRequestToServer($url,$body,'POST');

        if(isset($response->scope) && $response->scope === 'openid')
        {
            $this->access_token = $response->access_token ?? '';
            $this->refresh_token = $response->refresh_token ?? '';
            $this->transaction_uu_id = $response->jti ?? '';
            if(!add_option('viettel_access_token',$response->access_token ?? '' , '','no')){
                update_option('viettel_access_token',$response->access_token ?? '' , '');
            }
            if(!add_option('viettel_refresh_token',$response->refresh_token ?? '' , '','no')){
                update_option('viettel_refresh_token',$response->refresh_token ?? '' , '');
            }
        }

        return $response;

    }

    public function create_invoice_by_order_id(int $order_id = 0)
    {
        $this->get_access_token();

        $this->general_invoice_tnfo['transactionUuid'] = $this->transaction_uu_id;
        $this->general_invoice_tnfo['templateCode'] = '02GTTT0/060';
        $this->general_invoice_tnfo['invoiceSeries'] = 'AB/20E';
        $this->general_invoice_tnfo['paymentType'] = 'TM/CK';
        $this->general_invoice_tnfo['paymentTypeName'] = 'TM/CK';
        $this->general_invoice_tnfo['invoiceType'] = '1';

        $this->buyer_info['buyerAddressLine'] = 'người mua không cung cấp';
        $this->buyer_info['buyerEmail'] = 'huu.tran@dafc.com.vn';
        $this->buyer_info['buyerIdNo'] = '0326473067';
        $this->buyer_info['buyerName'] = 'khánh lẽ';
        $this->buyer_info['buyerPhoneNumber'] = '0326473067';

        $order = new \OMS_ORDER($order_id);


        $items = $order->get_items();

        $itemInfo = [];

        $line = 0;

        foreach ($items as $item)
        {
            $this->item_info['discount'] = 0.0;
            $this->item_info['itemCode'] = $item->get_id();
            $this->item_info['itemDiscount'] = 0.0;
            $this->item_info['itemName'] = $item->get_name();
            $this->item_info['itemTotalAmountWithoutTax'] = 12876898.0;
            $this->item_info['lineNumber'] = $line;
            $this->item_info['quantity'] = $item->get_quantity();
            $this->item_info['taxAmount'] = 1030152.0;
            $this->item_info['taxPercentage'] = 8;
            $this->item_info['unitName'] = 'CAI';
            $this->item_info['unitPrice'] = 12876898.0;

            $itemInfo[] = $this->item_info;

            $line++;
        }

        $this->summarize_info['sumOfTotalLineAmountWithoutTax'] = 12876898.0;
        $this->summarize_info['totalAmountWithoutTax'] = 12876898.0;
        $this->summarize_info['totalAmountWithTax'] = 13907050.0;
        $this->summarize_info['totalAmountWithTaxInWords'] = numberInVietnameseCurrency(13907050) . ' chẵn';
        $this->summarize_info['totalTaxAmount'] = 1030152.0;

        $this->tax_breakdowns[1]['taxableAmount'] = 12876898.0;
        $this->tax_breakdowns[1]['taxAmount'] = 1030152.0;

        $body = [
            'generalInvoiceInfo'    =>  $this->general_invoice_tnfo,
            'buyerInfo'             =>  $this->buyer_info,
            'sellerInfo'            =>  $this->seller_info,
            'payments'              =>  [
                ["paymentMethodName" => "TM/CK"]
            ],
            'itemInfo'              =>  $itemInfo,
            'metadata'              =>  [],
            'summarizeInfo'         =>  $this->summarize_info,
            'taxBreakdowns'         =>  $this->tax_breakdowns
        ];

        write_log(json_encode($body));

        $url = $this->base_url.'/services/einvoiceapplication/api/InvoiceAPI/InvoiceWS/createInvoice/'.$this->company_tax_code;

        $response = $this->sendRequestToServer($url,$body,'POST');

        write_log($response);

        return $response;

    }


}