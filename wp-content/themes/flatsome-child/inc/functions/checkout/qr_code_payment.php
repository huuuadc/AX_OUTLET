<?php
/**
 * @param $arrg(printType, bankName, accountNo, ?description,?amount=0 ,?width=300, ?height=300)
 *
 * @return void
 */
function render_qr_code_payment($arg){

    $printTypes = ['compact'=>'compact','compact2'=>'compact2','qr_only'=>'qr_only','print'=>'print'];

    $printType          = !(isset($arg['printType']) && isset($printTypes[$arg['printType']] )) ? 'compact' : $arg['printType'] ;
    $bankName           = !isset($arg['bankName']) ? 'VCB' : $arg['bankName'] ;
    $accountNo          = !isset($arg['accountNo']) ? 'NoAccountNo' : $arg['accountNo'] ;
    $description        = !isset($arg['description']) ? '' : $arg['description'] ;
    $amount             = !(isset($arg['amount']) && (int)$arg['amount'] > 0)  ? 0 : $arg['amount'] ;
    $width              = !isset($arg['width']) ? 300 : $arg['width'] ;
    $height             = !isset($arg['height']) ? 300 : $arg['height'] ;

    if (isset($arg['description']) && $arg['description'] != '') {
        echo '<div class="text-center">';
        echo "<img src='https://img.vietqr.io/image/{$bankName}-{$accountNo}-{$printType}.jpg?amount={$amount}&addInfo={$description}' alt='account-no-{$accountNo}' width='{$width}' height='{$height}'/></div>";
    }else{
        echo '<div class="text-center">';
        echo "<img src='https://img.vietqr.io/image/{$bankName}-{$accountNo}-{$printType}.jpg' alt='account-no-{$accountNo}' width='{$width}' height='{$height}'/></div>";
    }

}

function get_viet_qr_code($amount,$description){
    $payment_gateways   = WC_Payment_Gateways::instance();
    $gateway = $payment_gateways->payment_gateways()['bacs'];

    $accountNo = '';
    $bankName = '';

    if(count($gateway->account_details) > 0) {
        $accountNo = $gateway->account_details[0]['account_name'] ?? '';
        $bankName = $gateway->account_details[0]['bank_name'] ?? '';
    }

    render_qr_code_payment(array('printType'=> 'qr_only',
        'bankName'      =>  $bankName,
        'accountNo'     =>  $accountNo,
        'amount'        =>  $amount,
        'description'   =>  $description,
        'width'         =>  200,
        'height'        =>  200));

    if ( is_checkout()) :
        ?>
        <script type="text/javascript">
            jQuery( function($){
                $('form.checkout').on('change', 'input[name="payment_method"],input[id="billing_first_name"],input[id="billing_last_name"]', function(){
                    $(document.body).trigger('update_checkout');
                });
            });
        </script>
    <?php
    endif;

}