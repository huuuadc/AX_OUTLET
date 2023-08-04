<?php
/**
 * Form View: Input Type User First Name
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="ur-input-type-billing-first-name ur-admin-template">
	
	<div class="ur-label">
		<label><?php echo esc_html($this->get_general_setting_data( 'label' )); ?></label>

	</div>
	<div class="ur-field" data-field-key="billing_first_name">

		<input type="text" id="ur-input-type-billing-first-name"
			   placeholder="<?php echo esc_attr($this->get_general_setting_data( 'placeholder' )); ?>" disabled/>

	</div>
	<?php

	UR_Form_Field_Billing_First_Name::get_instance()->get_setting();

	?>
</div>

