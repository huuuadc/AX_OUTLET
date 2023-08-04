<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract URCL Setting Custom_Url Class
 *
 * @version  1.0.0
 * @package  UserRegistrationContidtionalLogic/Form/Settings
 * @author   WPEverest
 */
class URCL_Conditional_Setting_Custom_Url extends URCL_Field_Settings {
	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->field_id = 'custom_url_advance_setting';
	}
}

return new URCL_Conditional_Setting_Custom_Url();
