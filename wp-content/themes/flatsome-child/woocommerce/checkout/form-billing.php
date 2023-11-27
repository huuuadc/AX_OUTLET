<?php
/**
 * Checkout billing information form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-billing.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 * @global WC_Checkout $checkout
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="woocommerce-billing-fields">
	<?php if ( wc_ship_to_billing_address_only() && WC()->cart->needs_shipping() ) : ?>

		<h3><?php esc_html_e( 'Billing &amp; Shipping', 'woocommerce' ); ?></h3>

	<?php else : ?>

		<h3><?php esc_html_e( 'Billing details', 'woocommerce' ); ?></h3>

	<?php endif; ?>

	<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>

	<div class="woocommerce-billing-fields__field-wrapper">
		<?php
		$fields = $checkout->get_checkout_fields( 'billing' );

		foreach ( $fields as $key => $field ) {
			woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
		}
		?>
	</div>

	<?php do_action( 'woocommerce_after_checkout_billing_form', $checkout ); ?>
</div>

<?php if ( ! is_user_logged_in() && $checkout->is_registration_enabled() ) : ?>
	<div class="woocommerce-account-fields">
		<?php if ( ! $checkout->is_registration_required() ) : ?>

			<p class="form-row form-row-wide create-account">
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
					<input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true ); ?> type="checkbox" name="createaccount" value="1" /> <span><?php esc_html_e( 'Create an account?', 'woocommerce' ); ?></span>
				</label>
			</p>

		<?php endif; ?>

		<?php do_action( 'woocommerce_before_checkout_registration_form', $checkout ); ?>

		<?php if ( $checkout->get_checkout_fields( 'account' ) ) : ?>
			<div class="create-account">
				<?php foreach ( $checkout->get_checkout_fields( 'account' ) as $key => $field ) : ?>
					<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
				<?php endforeach; ?>
				<div class="clear"></div>
			</div>

            <?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ): ?>
            <script>
                jQuery(document).ready(function($){
                    var billing_phone = $('input#billing_phone');
                    var account_username = $('input#account_username');
                    $(window).load(function(){
                        if(billing_phone.val()!==''){
                            account_username.val(billing_phone.val());
                        }
                    });
                    $('body').on('change',billing_phone,function(){
                        var get_billing_phone = billing_phone.val();
                        account_username.val(get_billing_phone);
                    });
                    $('form.checkout').on('submit',function(){
                        if(billing_phone.val()!==''){
                            account_username.val(billing_phone.val());
                        }
                    });
                });
            </script>
            <?php endif; ?>
		<?php endif; ?>

		<?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>
	</div>
<?php endif; ?>
<style>
    .woocommerce-message.message-wrapper, .woocommerce-error.message-wrapper{
        bottom:auto !important;
        z-index:3000;
    }
</style>

<p class="form-row form-row-wide create-account woocommerce-validated">
    <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
        <input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox"
               id="is_issue_vat"
               type="checkbox"
               name="is_issue_vat"
               >
        <span><?php echo __('Xuất hóa đơn công ty') ?></span>
    </label>
</p>
<div id="vat_company" class="woocommerce-billing-fields" style="display: none ; opacity: 0">
    <div class="woocommerce-billing-fields__field-wrapper">
        <div class="form-row form-row-first validate-required"
           id="vat_company_name_field">
            <span class="woocommerce-input-wrapper">
                <div class="fl-wrap fl-wrap-input">
                    <label for="vat_company_name" class="fl-label">Tên công ty
                        <abbr class="required" title="bắt buộc">*</abbr></label>
                    <input type="text"
                           class="input-text fl-input"
                           name="vat_company_name"
                           id="vat_company_name"
                           placeholder="Tên công ty"
                           value=""
                           autocomplete="company-name" />
                </div>
            </span>
        </div>
        <div class="form-row form-row-last validate-required"
           id="vat_company_tax_code_field">
            <span class="woocommerce-input-wrapper">
                <div class="fl-wrap fl-wrap-input">
                    <label for="vat_company_tax_code" class="fl-label">Mã số thuế
                        <abbr class="required" title="bắt buộc">*</abbr>
                    </label>
                    <input type="text"
                           class="input-text fl-input"
                           name="vat_company_tax_code"
                           id="vat_company_tax_code"
                           placeholder="Mã số thuế"
                           value=""
                           autocomplete="company-tax-code">
                </div>
            </span>
        </div>
         <div class="form-row form-row-first validate-required"
           id="vat_company_address_field">
            <span class="woocommerce-input-wrapper">
                <div class="fl-wrap fl-wrap-input">
                    <label for="vat_company_address" class="fl-label">Địa chỉ công ty
                        <abbr class="required" title="bắt buộc">*</abbr>
                    </label>
                    <input type="text"
                           class="input-text fl-input"
                           name="vat_company_address"
                           id="vat_company_address"
                           placeholder="Địa chỉ công ty"
                           value=""
                           autocomplete="company-tax-code">
                </div>
            </span>
        </div>
         <div class="form-row form-row-last validate-required"
           id="vat_company_email_field">
            <span class="woocommerce-input-wrapper">
                <div class="fl-wrap fl-wrap-input">
                    <label for="vat_company_email" class="fl-label">Email công ty
                        <abbr class="required" title="bắt buộc">*</abbr>
                    </label>
                    <input type="text"
                           class="input-text fl-input"
                           name="vat_company_email"
                           id="vat_company_email"
                           placeholder="Email công ty"
                           value=""
                           autocomplete="company-tax-code">
                </div>
            </span>
        </div>
    </div>
</div>

<script>
    jQuery(function ($){

        [   'vat_company_name',
            'vat_company_tax_code',
            'vat_company_address',
            'vat_company_email'].forEach((e)=> {
                $(`#${e}`).on('input', function (evt) {
                    let value = evt.target.value
                    const field_class = $(`#${e}_field>span>div`)
                    if (value.length === 0) {
                        field_class.removeClass('fl-is-active')
                    } else {
                        field_class.addClass('fl-is-active')
                    }
                })
            }
        )

        $('#is_issue_vat').change(function (){
           if( $('#is_issue_vat:checked').val() === 'on'){
               $('#vat_company').css('display','block').animate({opacity : 1},500)
           } else {
               $('#vat_company').animate({opacity : 0},500).css({display : 'none',opacity: 0})
           }
        })


    })
</script>