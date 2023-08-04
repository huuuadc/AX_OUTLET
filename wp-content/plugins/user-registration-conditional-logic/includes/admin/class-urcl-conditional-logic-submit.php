<?php
/**
 * UserRegistrationConditionalLogic URCL_Conditional_Logic_Submit
 *
 * AJAX Event Handler
 *
 * @class    URCL_Conditional_Logic_Submit
 * @version  1.3.6
 * @package  UserRegistrationConditionalLogic/Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * URCL_Conditional_Logic_Submit class.
 */
class URCL_Conditional_Logic_Submit {

	/**
	 * Class Constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_filter( 'user_registration_get_form_settings', array( $this, 'conditional_logic_submit_setting' ), 10, 1 );
		add_action( 'user_registration_after_form_settings_save', array( $this, 'save_conditional_submit_settings' ), 10, 1 );
	}

	/**
	 * Enqueue scripts.
	 */
	public function admin_scripts() {
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';
		$suffix    = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'urcl-conditional-logic-submit-script', URCL()->plugin_url() . '/assets/js/admin/conditional-logic-submit' . $suffix . '.js', array( 'jquery' ), URCL_VERSION, true );
		wp_localize_script(
			'urcl-conditional-logic-submit-script',
			'urcl_params',
			array(
				'ajax_url'                  => admin_url( 'admin-ajax.php' ),
				'nonce'                     => wp_create_nonce( 'urcl_condtional_logic_nonce' ),
				'select_option_placeholder' => esc_html__( '--Select Option--', 'user-registration-conditional-logic' ),
				'or_text'                   => esc_html__( 'OR', 'user-registration-conditional-logic' ),
			)
		);
		wp_enqueue_script( 'urcl-conditional-logic-submit-script' );
	}

	/**
	 * Conditional logic setting for submit button.
	 *
	 * @param  array $setting Form Settings.
	 * @return array $setting Form Settings.
	 */
	public function conditional_logic_submit_setting( $setting ) {

		$form_id      = isset( $_GET['edit-registration'] ) ? absint( $_GET['edit-registration'] ) : 0;
		$form_setting = array(
			array(
				'type'              => 'toggle',
				'label'             => __( 'Enable Conditional Logic for Submit Button', 'user-registration-conditional-logic' ),
				'description'       => '',
				'required'          => false,
				'id'                => 'user_registration_form_setting_enable_submit_conditional_logic',
				'input_class'       => array( 'ur-enable-submit-conditional_logic ur-enable-conditional-logic' ),
				'custom_attributes' => array(),
				'default'           => ur_get_single_post_meta( $form_id, 'user_registration_form_setting_enable_submit_conditional_logic', 'false' ),
				'tip'               => __( 'Check this option to enable condition logic for submit button.', 'user-registration-conditional-logic' ),
			),
		);

		$setting['setting_data'] = array_merge( $setting['setting_data'], $form_setting );
		return $setting;
	}

	/**
	 * Get the HTML template.
	 *
	 * @param string $action_label Action label.
	 * @param array  $actions Actions array.
	 * @param array  $fields $fields.
	 * @param array  $operators $operators.
	 * @param string $name Conditional Logic name.

	 * @return string
	 */
	public static function get_template( $action_label = '', $actions = array(), $fields = array(), $operators = array(), $name = 'submit' ) {
		ob_start();
		?>
			<div class="urcl-form-settings-logic-wrap urcl-submit-logic-wrap" data-group="condition_1">
				<div class="urcl-form-settings-assign urcl-assign-role">
					<p class="urcl-assign-label"><?php echo esc_html( $action_label ); ?></p>
					<select class="urcl-form-settings-field urcl-submit-field" name="user_registration_form_conditional_<?php echo esc_attr( $name ); ?>[condition_1]">
						<?php
						foreach ( $actions as $key => $value ) {
							echo '<option value="' . esc_attr( $key ) . '"> ' . esc_html( $value ) . ' </option>';
						}
						?>
					</select>
					<p><?php echo esc_html__( 'only if following matches.', 'user-registration-conditional-logic' ); ?></p>
				</div>
				<ul class="urcl-form-settings-logic-box urcl-role-logic-box" data-group="condition_1" data-last-key="1">
					<li class="urcl-conditional-group" data-key="1">
						<div class="urcl-form-group">
							<select class="urcl-form-settings-field urcl-submit-field urcl-field-conditional-field-select" name="user_registration_form_fields[condition_1][1]">
								<option value=""><?php echo esc_html( '-- Select Field --' ); ?></option>
								<?php
								foreach ( $fields as $key => $value ) {
									echo '<option value="' . esc_attr( $key ) . '" data-type="' . esc_attr( $value['field_key'] ) . '"> ' . esc_html( $value['label'] ) . ' </option>';
								}
								?>
							</select>
						</div>
						<div class="urcl-operator">
							<select class="urcl-form-settings-field urcl-submit-field" name="user_registration_form_operator[condition_1][1]">
								<?php
								foreach ( $operators as $key => $value ) {
									echo '<option value="' . esc_attr( $key ) . '"> ' . esc_html( $value ) . ' </option>';
								}
								?>
							</select>
						</div>
						<div class="urcl-value">
							<input name="user_registration_form_value[condition_1][1]" value="" class="urcl-form-settings-field urcl-submit-field" type="text">
						</div><span class="add"><?php echo esc_html( 'AND' ); ?></span><span class="remove"><i class="dashicons dashicons-minus"></i></span></li>
				</ul>
				<button class="button button-secondary button-medium urcl-add-or-condition"><?php echo esc_html( 'Add OR Condition' ); ?></button>
				<button class="urcl-remove-condition"></button>
			</div>
		<?php
		$html = ob_get_clean();

		return $html;
	}

	/**
	 *  Get the form setting html with conditional for submit.
	 *
	 * @param int    $form_id Form Id.
	 * @param array  $actions Actions.
	 * @param array  $fields Form Fields.
	 * @param array  $operators $operators.
	 * @param string $name Conditional Logic name.
	 * @param string $action_label Action label text.
	 *
	 * @return string $output
	 */
	public static function get_submit_conditions_list( $form_id, $actions, $fields, $operators, $name = 'submit', $action_label = '' ) {
		$condition_submit_settings = maybe_unserialize( get_post_meta( $form_id, 'user_registration_submit_condition', true ) );

		if ( is_array( $condition_submit_settings ) && ! empty( $condition_submit_settings ) ) {
			$output  = '<div class="urcl-form-settings-container urcl-conditional-logic-container">';
			$output .= '<div class="urcl-submit-conditional-container">';

			$condition_id = 1;

			foreach ( $condition_submit_settings as $conditions ) {
				$output .= '<div class="urcl-form-settings-logic-wrap urcl-submit-logic-wrap" data-group ="condition_' . $condition_id . '">';

				$output .= '<div class="urcl-form-settings-assign urcl-assign-role">';
				$output .= '<p class="urcl-assign-label">' . esc_html( $action_label ) . '</p>';
				$output .= '<select class="urcl-form-settings-field urcl-submit-field" name="user_registration_form_conditional_' . esc_attr( $name ) . '[condition_' . $condition_id . ']">';

				foreach ( $actions as $key => $action ) {
					$output .= '<option value="' . esc_attr( $key ) . '"' . ( $conditions['action'] === $key ? 'selected' : '' ) . '> ' . $action . ' </option>';
				}
				$output .= '</select>';
				$output .= '<p>' . esc_html__( 'only if following matches.', 'user-registration-conditional-logic' ) . '</p>';
				$output .= '</div>';

				if ( isset( $conditions['conditions'] ) && isset( $conditions['conditions'] ) ) {
					$output .= '<ul class="urcl-form-settings-logic-box urcl-role-logic-box" data-group ="condition_' . $condition_id . '" data-last-key="' . count( $conditions['conditions'] ) . '">';

					$data_key = 1;

					foreach ( $conditions['conditions'] as $condition ) {
						$output .= '<li class="urcl-conditional-group" data-key="' . $data_key . '">';
						$output .= '<div class="urcl-form-group">';

						$fields_data = wp_list_pluck( $condition, 'field_value' );

						$output .= '<select class="urcl-form-settings-field urcl-submit-field urcl-field-conditional-field-select" name="user_registration_form_fields[condition_' . $condition_id . '][' . $data_key . ']">';
						$output .= '<option value="">' . esc_html__( '-- Select Field --', 'user-registration-conditional-logic' ) . '</option>';

						$selected_urcl_field_type = '';
						$selected_urcl_field_key  = '';
						foreach ( $fields as $ind_field_key => $ind_field_value ) {
							$selectedField = $fields_data[0] === $ind_field_key ? 'selected="selected"' : '';

							if ( $fields_data[0] === $ind_field_key ) {
								$selected_urcl_field_type = $ind_field_value['field_key'];
								$selected_urcl_field_key  = $ind_field_key;
							}
							$output .= '<option value="' . esc_attr( $ind_field_key ) . '" data-type="' . esc_attr( $ind_field_value['field_key'] ) . '" ' . $selectedField . '> ' . $ind_field_value['label'] . ' </option>';
						}
						$output .= '</select></div>';
						$output .= '<div class="urcl-operator"><select class="urcl-form-settings-field urcl-submit-field" name="user_registration_form_operator[condition_' . $condition_id . '][' . $data_key . ']">';

						foreach ( $operators as $key => $operator ) {
							$output .= '<option value="' . esc_attr( $key ) . '"' . ( $fields_data[1] === $key ? 'selected' : '' ) . '> ' . esc_html( $operator ) . ' </option>';
						}

						$output .= '</select></div>';
						$output .= '<div class="urcl-value">';

						if ( 'checkbox' === $selected_urcl_field_type || 'radio' === $selected_urcl_field_type || 'select' === $selected_urcl_field_type || 'country' === $selected_urcl_field_type || 'billing_country' === $selected_urcl_field_type || 'shipping_country' === $selected_urcl_field_type || 'select2' === $selected_urcl_field_type || 'multi_select2' === $selected_urcl_field_type ) {
							$choices = get_checkbox_choices( $form_id, $selected_urcl_field_key );
							$output .= '<select name="user_registration_form_value[condition_' . $condition_id . '][' . $data_key . ']" class="urcl-form-settings-field urcl-submit-field">';

							if ( is_array( $choices ) && array_filter( $choices ) ) {
								$output .= '<option>' . esc_html__( '-- Select Option --', 'user-registration-conditional-logic' ) . '</option>';
								foreach ( $choices as $key => $choice ) {
									$key            = 'country' === $selected_urcl_field_type ? $key : $choice;
									$selected_value = $fields_data[2] === $key ? 'selected="selected"' : '';
									$output        .= '<option ' . $selected_value . ' value="' . $key . '">' . esc_html( $choice ) . '</option>';
								}
							} else {
								$selected = isset( $fields_data[2] ) ? $fields_data[2] : 0;
								$output  .= '<option value="1" ' . ( ur_string_to_bool( $selected ) ? 'selected="selected"' : '' ) . ' >' . __( 'Checked', 'user-registration-conditional-logic' ) . '</option>';
							}
							$output .= '</select>';
						} else {
								$output .= '<input name="user_registration_form_value[condition_' . $condition_id . '][' . $data_key . ']" value="' . esc_attr( $fields_data[2] ) . '" class="urcl-form-settings-field urcl-submit-field" type="text" />';
						}
						$output .= '</div>';
						$output .= '<span class="add">';
						$output .= esc_html__( 'AND', 'user-registration-conditional-logic' );
						$output .= '</span>';
						$output .= '<span class="remove">';
						$output .= '<i class="dashicons dashicons-minus"></i>';
						$output .= '</span></li>';
						$data_key++;
					}
					$output .= '</ul>';
				}

				if ( isset( $conditions['or_conditions'] ) && isset( $conditions['or_conditions'] ) ) {
					foreach ( $conditions['or_conditions'] as $condition ) {
						$output .= '<p class="urcl-or-label">' . esc_html__( 'OR', 'user-registration-conditional-logic' ) . '</p>';
						$output .= '<ul class="urcl-form-settings-logic-box urcl-or-groups urcl-role-logic-box" data-group ="condition_' . $condition_id . '" data-last-or-key="' . count( $conditions['or_conditions'] ) . '">';

						$data_key = 1;

						foreach ( $condition as $condition_field ) {
							$output .= '<li class="urcl-conditional-or-group" data-key="' . $data_key . '">';
							$output .= '<div class="urcl-form-group">';

							$fields_data = wp_list_pluck( $condition_field, 'field_value' );

							$output .= '<select class="urcl-form-settings-field urcl-submit-field urcl-field-conditional-field-select" name="user_registration_form_fields[condition_' . $condition_id . '][' . $data_key . ']">';
							$output .= '<option value="">' . esc_html__( '-- Select Field --', 'user-registration-conditional-logic' ) . '</option>';

							$selected_urcl_field_type = '';
							$selected_urcl_field_key  = '';

							foreach ( $fields as $ind_field_key => $ind_field_value ) {
								$selectedField = $fields_data[0] === $ind_field_key ? 'selected="selected"' : '';

								if ( $fields_data[0] === $ind_field_key ) {
									$selected_urcl_field_type = $ind_field_value['field_key'];
									$selected_urcl_field_key  = $ind_field_key;
								}
								$output .= '<option value="' . esc_attr( $ind_field_key ) . '" data-type="' . esc_attr( $ind_field_value['field_key'] ) . '" ' . $selectedField . '> ' . esc_html( $ind_field_value['label'] ) . ' </option>';
							}
							$output .= '</select></div>';
							$output .= '<div class="urcl-operator"><select class="urcl-form-settings-field urcl-submit-field" name="user_registration_form_operator[condition_' . $condition_id . '][' . $data_key . ']">';

							foreach ( $operators as $key => $operator ) {
								$output .= '<option value="' . esc_attr( $key ) . '"' . ( $fields_data[1] === $key ? 'selected' : '' ) . '> ' . esc_html( $operator ) . ' </option>';
							}

							$output .= '</select></div>';
							$output .= '<div class="urcl-value">';

							if ( 'checkbox' === $selected_urcl_field_type || 'radio' === $selected_urcl_field_type || 'select' === $selected_urcl_field_type || 'country' === $selected_urcl_field_type || 'billing_country' === $selected_urcl_field_type || 'shipping_country' === $selected_urcl_field_type ) {
								$choices = get_checkbox_choices( $form_id, $selected_urcl_field_key );
								$output .= '<select name="user_registration_form_value[condition_' . $condition_id . '][' . $data_key . ']" class="urcl-form-settings-field urcl-submit-field">';

								if ( is_array( $choices ) && array_filter( $choices ) ) {
									$output .= '<option>' . esc_html__( '-- Select Option --', 'user-registration-conditional-logic' ) . '</option>';
									foreach ( $choices as $key => $choice ) {
										$key            = 'country' === $selected_urcl_field_type ? $key : $choice;
										$selected_value = $fields_data[2] === $key ? 'selected="selected"' : '';
										$output        .= '<option ' . $selected_value . ' value="' . $key . '">' . esc_html( $choice ) . '</option>';
									}
								} else {
									$selected = isset( $fields_data[2] ) ? $fields_data[2] : 0;
									$output  .= '<option value="1" ' . ( ur_string_to_bool( $selected ) ? 'selected="selected"' : '' ) . ' >' . __( 'Checked', 'user-registration-conditional-logic' ) . '</option>';
								}
								$output .= '</select>';
							} else {
									$output .= '<input name="user_registration_form_value[condition_' . $condition_id . '][' . $data_key . ']" value="' . esc_attr( $fields_data[2] ) . '" class="urcl-form-settings-field urcl-submit-field" type="text" />';
							}
							$output .= '</div>';
							$output .= '<span class="add">';
							$output .= esc_html__( 'AND', 'user-registration-conditional-logic' );
							$output .= '</span>';
							$output .= '<span class="remove">';
							$output .= '<i class="dashicons dashicons-minus"></i>';
							$output .= '</span></li>';
							$data_key++;
						}
						$output .= '</ul>';
					}
				}
				$output .= '<button class="button button-secondary button-medium urcl-add-or-condition">Add OR Condition</button>';

				if ( count( $condition_submit_settings ) > 1 ) {
					$output .= '<button class="urcl-remove-condition"></button>';
				}
				$output .= '</div>';
				$condition_id++;
			}
			$output .= '</div>';
			$output .= '<button class="button button-secondary button-medium urcl-add-new-condition" data-last-conditionid="' . $condition_id . '">Add New Condition</button></div>';
			return $output;
		}
	}

	/**
	 * Save the conditional logic form settings for submit.
	 *
	 * @param array $post Form settings data.
	 * @return void
	 */
	public function save_conditional_submit_settings( $post ) {
		$form_id                   = absint( $post['form_id'] );
		$condition_submit_settings = isset( $post['conditional_submit_settings_data'] ) ? wp_unslash( $post['conditional_submit_settings_data'] ) : array();

		if ( ! empty( $condition_submit_settings ) ) {
				update_post_meta( $form_id, 'user_registration_submit_condition', $condition_submit_settings );
		}
	}
}

new URCL_Conditional_Logic_Submit();
