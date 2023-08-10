<?php
/**
 * @param $arrg(printType, bankName, accountNo, ?description,?amount=0 ,?width=300, ?height=300)
 *
 * @return void
 */
function render_qr_code_payment($arrg){

    $printTypes = array('compact','compact2','qr_only','print');

    $printType          = !(isset($arrg['printType']) && isset($printTypes[$arrg['printType']] )) ? 'compact' : $arrg['printType'] ;
    $bankName           = !isset($arrg['bankName']) ? 'VCB' : $arrg['bankName'] ;
    $accountNo          = !isset($arrg['accountNo']) ? 'NoAccountNo' : $arrg['accountNo'] ;
    $description        = !isset($arrg['description']) ? '' : $arrg['description'] ;
    $amount             = !(isset($arrg['amount']) && (int)$arrg['amount'] > 0)  ? 0 : $arrg['amount'] ;
    $width              = !isset($arrg['width']) ? 300 : $arrg['width'] ;
    $height             = !isset($arrg['height']) ? 300 : $arrg['height'] ;

    if (isset($arrg['description']) && $arrg['description'] != '') {
        echo "<img src='https://img.vietqr.io/image/{$bankName}-{$accountNo}-{$printType}.jpg?amount={$amount}&addInfo={$description}' alt='account-no-{$accountNo}' width='{$width}' height='{$height}'/>";
    }else{
        echo "<img src='https://img.vietqr.io/image/{$bankName}-{$accountNo}-{$printType}.jpg' alt='account-no-{$accountNo}' width='{$width}' height='{$height}'/>";
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
        'description'   =>  $description));

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