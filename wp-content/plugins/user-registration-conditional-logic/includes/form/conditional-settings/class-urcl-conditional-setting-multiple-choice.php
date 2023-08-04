<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * URCL_Conditional_Setting_Multiple_Choice Class
 *
 * @version  1.2.7
 * @package  UserRegistrationContidtionalLogic/Form/Settings
 * @author   WPEverest
 */
class URCL_Conditional_Setting_Multiple_Choice extends URCL_Field_Settings {

	public function __construct() {
		$this->field_id = 'multiple_choice_advance_setting';
	}
}

return new URCL_Conditional_Setting_Multiple_Choice();
