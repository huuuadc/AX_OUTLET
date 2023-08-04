<?php
/**
 * UserRegistrationAdvancedFields Admin.
 *
 * @class    UR_Form_Field_Profile_Picture
 * @since  1.3.0
 * @package  UserRegistrationAdvancedFields/Form
 * @category Admin
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * UR_Form_Field_Profile_Picture Class
 */
class UR_Form_Field_Profile_Picture extends UR_Form_Field {

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	private static $_instance;

	/**
	 * Get Instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Hook in tabs.
	 */
	public function __construct() {

		$this->id = 'user_registration_profile_picture';

		$this->form_id = 1;

		$this->registered_fields_config = array(

			'label' => __( 'Profile Picture', 'user-registration-advanced-fields' ),

			'icon'  => 'ur-icon ur-icon-user-display-name',
		);
		$this->field_defaults           = array(

			'default_label'      => __( 'Profile Picture', 'user-registration-advanced-fields' ),

			'default_field_name' => 'profile_pic_url',

		);

		add_filter( "{$this->id}_advance_class", array( $this, 'settings_override' ), 10, 1 );
	}

	public function settings_override( $file_path_override ) {
		$file_path_override['file_path'] = URAF_ABSPATH . 'includes' . UR_DS . 'form' . UR_DS . 'settings' . UR_DS . 'class-ur-setting-profile-picture.php';
		return $file_path_override;
	}

	/**
	 * Get registered admin fields
	 */
	public function get_registered_admin_fields() {
		if ( ! extension_loaded( 'gd' ) ) {
			$ur_gd_extension_class = 'ur-field-requirements-needed';
		} else {
			$ur_gd_extension_class = '';
		}

		return '<li id="' . $this->id . '_list "

				class="ur-registered-item draggable ' . $ur_gd_extension_class . '"

                data-field-id="' . $this->id . '"><span class="' . $this->registered_fields_config['icon'] . '"></span>' . $this->registered_fields_config['label'] . '</li>';
	}

	/**
	 * Validate Profile Picture field.
	 *
	 * @param mixed $single_form_field Single form field.
	 * @param mixed $form_data Form Data.
	 * @param mixed $filter_hook Filter hook.
	 * @param int   $form_id Form id.
	 */
	public function validation( $single_form_field, $form_data, $filter_hook, $form_id ) {
		$field_name       = isset( $single_form_field->general_setting->field_name ) ? $single_form_field->general_setting->field_name : '';
		$urcl_hide_fields = isset( $_POST['urcl_hide_fields'] ) ? (array) json_decode( stripslashes( $_POST['urcl_hide_fields'] ), true ) : array();
		$required         = isset( $single_form_field->general_setting->required ) ? $single_form_field->general_setting->required : 'no';
		$field_label      = isset( $form_data->label ) ? $form_data->label : '';
		$value            = isset( $form_data->value ) ? $form_data->value : '';

		// Do some Validation here.
	}
}

return UR_Form_Field_Profile_Picture::get_instance();
