<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * URCL_Conditional_Setting_Profile_Picture Class
 *
 * @version  1.0.0
 * @package  UserRegistrationContidtionalLogic/Form/Settings
 * @author   WPEverest
 */
class URCL_Conditional_Setting_Profile_Picture extends URCL_Field_Settings {

	public function __construct() {
		$this->field_id = 'profile_picture_advance_setting';
	}
}

return new URCL_Conditional_Setting_Profile_Picture();
