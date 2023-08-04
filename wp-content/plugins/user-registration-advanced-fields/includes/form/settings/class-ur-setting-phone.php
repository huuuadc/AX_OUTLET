<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * URAF_Setting_Phone Class
 *
 * @package  UserRegistrationAdvancedFields/Form/Settings
 * @category Abstract Class
 * @author   WPEverest
 */
class UR_Setting_Phone extends UR_Field_Settings {


	public function __construct() {
		$this->field_id = 'phone_advance_setting';
	}

	public function output( $field_data = array() ) {

		$this->field_data = $field_data;
		$this->register_fields();
		$field_html = $this->fields_html;

		return $field_html;
	}

	public function register_fields() {
		$fields = array(

			'custom_class' => array(
				'label'       => esc_html__( 'Custom Class', 'user-registration-advanced-fields' ),
				'data-id'     => $this->field_id . '_custom_class',
				'name'        => $this->field_id . '[custom_class]',
				'class'       => $this->default_class . ' ur-settings-custom-class',
				'type'        => 'text',
				'required'    => false,
				'default'     => '',
				'placeholder' => esc_html__( 'Custom Class', 'user-registration' ),
				'tip'         => __( 'Custom CSS class to embed in this field.', 'user-registration-advanced-fields' ),
			),
		);

		$fields = apply_filters( 'user_registration_phone_field_advance_settings', $fields, $this->field_id, $this->default_class );
		$this->render_html( $fields );
	}
}

return new UR_Setting_Phone();
