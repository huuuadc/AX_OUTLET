<?php
/**
 * UserRegistrationConditionalLogic URCL_Redirection_Setting
 *
 * AJAX Event Handler
 *
 * @class    URCL_Redirection_Setting
 * @version  1.3.6
 * @package  UserRegistrationConditionalLogic/Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * URCL_Redirection_Setting class.
 */
class URCL_Redirection_Setting {

	/**
	 * Class Constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_filter( 'user_registration_get_form_settings', array( $this, 'add_setting' ), 2, 1 );
		add_action( 'user_registration_after_form_settings_save', array( $this, 'save' ), 10, 1 );
		add_filter( 'user_registration_success_params_before_send_json', array( $this, 'redirect_after_registration' ), 100, 4 );
	}

	/**
	 * Enqueue scripts.
	 */
	public function admin_scripts() {
		$suffix  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$form_id = 0;

		if ( isset( $_GET['edit-registration'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification
			$form_id = absint( $_GET['edit-registration'] ); //phpcs:ignore WordPress.Security.NonceVerification
			wp_enqueue_script( 'urcl-admin-form-settings-script', URCL()->plugin_url() . '/assets/js/admin/urcl-admin-form-settings' . $suffix . '.js', array(), URCL_VERSION, true );

			wp_localize_script(
				'urcl-admin-form-settings-script',
				'urcl_redirection_params',
				array(
					'ajax_url'                  => admin_url( 'admin-ajax.php' ),
					'nonce'                     => wp_create_nonce( 'urcl_condtional_logic_nonce' ),
					'select_option_placeholder' => esc_html__( '--Select Option--', 'user-registration-conditional-logic' ),
					'or_text'                   => esc_html__( 'OR', 'user-registration-conditional-logic' ),
					'templates'                 => array(
						'redirection_action' => $this->get_form_setting_redirection_setting_html( $form_id ),
					),
					'conditional_settings'      => array(
						'redirection' => get_post_meta( $form_id, 'user_registration_conditional_redirection_settings', array() ),
					),
				)
			);
		};
	}

	/**
	 * Conditional logic setting for submit button.
	 *
	 * @param  array $setting Form Settings.
	 * @return array $setting Form Settings.
	 */
	public function add_setting( $setting ) {

		$form_id      = isset( $_GET['edit-registration'] ) ? absint( $_GET['edit-registration'] ) : 0;
		$form_setting = array(
			array(
				'type'              => 'toggle',
				'label'             => __( 'Enable Conditional Redirection', 'user-registration-conditional-logic' ),
				'description'       => '',
				'required'          => false,
				'id'                => 'user_registration_form_setting_enable_conditional_redirection',
				'class'             => array( 'ur-enhanced-select', 'urcl-form-settings-conditional-redirection' ),
				'input_class'       => array(),
				'custom_attributes' => array(),
				'default'           => ur_get_single_post_meta( $form_id, 'user_registration_form_setting_enable_conditional_redirection', 'false' ),
				'tip'               => __( 'Redirect Users to specific page conditionally.', 'user-registration-conditional-logic' ),
			),
		);

		$setting['setting_data'] = array_merge( $setting['setting_data'], $form_setting );

		return $setting;
	}


	/**
	 * Returns html template for conditional redirection settings.
	 *
	 * @param integer $form_id Form Id.
	 * @return string
	 */
	public function get_form_setting_redirection_setting_html( $form_id = 0 ) {
		$redirection_options = array(
			'no_redirection' => __( 'No Redirection', 'user-registration-conditional-logic' ),
			'custom_page'    => __( 'Custom Page', 'user-registration-conditional-logic' ),
			'external_url'   => __( 'External Url', 'user-registration-conditional-logic' ),
		);

		ob_start();
		?>
		<div class="urcl-form-settings-container">
			<div class="urcl-form-settings-logic-wrap urcl-redirection-wrap" data-group="1">
				<div class="urcl-form-settings-action-wrapper">
					<p class="urcl-assign-label"><?php echo esc_html__( 'Redirect to', 'user-registration-conditional-logic' ); ?> </p>
					<select class="urcl-form-settings-field urcl-redirection-field" name="urcl_conditional_redirection_action_type" id="urcl_conditional_redirection_action_type">
						<?php
						foreach ( $redirection_options as $key => $value ) {
							echo '<option value="' . esc_attr( $key ) . '"> ' . esc_html( $value ) . ' </option>';
						}
						?>
					</select>
					<?php echo $this->get_custom_pages_html( $form_id ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php echo $this->get_external_url_html(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<p><?php echo esc_html__( '&nbsp;&nbsp;if following matches.', 'user-registration-conditional-logic' ); ?></p>
				</div>
				<button class="button button-secondary button-medium urcl-add-or-condition-btn"><?php echo esc_html( 'Add OR Condition' ); ?></button>
			</div>
		</div>
		<?php
		$html = ob_get_clean();

		return $html;
	}


	/**
	 * Returns html for custom page selection input.
	 *
	 * @param integer $form_id Form Id.
	 * @return string
	 */
	public function get_custom_pages_html( $form_id = 0 ) {
		$selected_roles_pages = get_option( 'ur_pro_settings_redirection_after_registration', array() );
		$selected_roles_pages = ur_get_single_post_meta( $form_id, 'user_registration_form_setting_role_based_redirection', $selected_roles_pages );

		$pages     = get_pages();
		$settings  = '';
		$key       = 'redirection-setting';
		$settings .= '<select name="urcl-redirection-custom-page" id="urcl-redirection-custom-page" >';
		$settings .= '<option value="" >---Select a page---</option>';

		foreach ( $pages as $page ) {

			if ( ! empty( $selected_roles_pages ) && false ) {
				$selected = 'selected=selected';
			} else {
				$selected = '';
			}
			$settings .= '<option value="' . $page->ID . '" ' . $selected . ' >' . $page->post_title . '</option>';
		}
		$settings .= '</select>';
		return $settings;
	}


	/**
	 * Returns html for external url input.
	 *
	 * @return string
	 */
	public function get_external_url_html() {
		$setting = '<input type="url" name="urcl-redirection-external-url" id="urcl-redirection-external-url" placeholder="https://example.com">';
		return $setting;
	}


	/**
	 * Save form conditional redirection settings.
	 *
	 * @param [obj] $post Post Object.
	 * @return void
	 */
	public function save( $post ) {
		$form_id                        = absint( $post['form_id'] );
		$condition_redirection_settings = isset( $post['conditional_redirection_settings'] ) ? wp_unslash( $post['conditional_redirection_settings'] ) : array();

		if ( ! empty( $condition_redirection_settings ) ) {
				update_post_meta( $form_id, 'user_registration_conditional_redirection_settings', $condition_redirection_settings );
		}
	}


	/**
	 * Add redirection url to response object if conditions match.
	 *
	 * @param [array] $success_params Success Params.
	 * @param [array] $valid_form_data Valid Form Data.
	 * @param [int]   $form_id Form Id.
	 * @param [int]   $user_id User Id.
	 * @return array
	 */
	public function redirect_after_registration( $success_params, $valid_form_data, $form_id, $user_id ) {

		$login_option      = ur_get_form_setting_by_key( $form_id, 'user_registration_form_setting_login_options' );
		$paypal_is_enabled = ur_string_to_bool( ur_get_single_post_meta( $form_id, 'user_registration_enable_paypal_standard', false ) );

		if ( 'auto_login' === $login_option || $paypal_is_enabled ) {
			return $success_params;
		}

		$enabled_conditional_redirection = get_post_meta( $form_id, 'user_registration_form_setting_enable_conditional_redirection', true );

		if ( ur_string_to_bool( $enabled_conditional_redirection ) ) {

			$conditional_redirection_settings = get_post_meta( $form_id, 'user_registration_conditional_redirection_settings', true );

			if ( ! empty( $conditional_redirection_settings ) ) {
				$conditions = $conditional_redirection_settings[0]['conditions'];
				$rule_match = $this->check_conditions( $conditions, $user_id );

				if ( $rule_match ) {
					$actions = $conditional_redirection_settings[0]['actions'];

					switch ( $actions['urcl_conditional_redirection_action_type'] ) {
						case 'no_redirection':
							$success_params['role_based_redirect_url'] = '';
							break;

						case 'custom_page':
							$page_id                                   = isset( $actions['urcl-redirection-custom-page'] ) ? absint( $actions['urcl-redirection-custom-page'] ) : 0;
							$redirect_url                              = get_permalink( $page_id );
							$success_params['role_based_redirect_url'] = $redirect_url;
							break;

						case 'external_url':
							$redirect_url                              = isset( $actions['urcl-redirection-external-url'] ) ? esc_url( $actions['urcl-redirection-external-url'] ) : 0;
							$success_params['role_based_redirect_url'] = $redirect_url;
							break;
					}
				}
			}
		}

		return $success_params;
	}


	/**
	 * Check if conditions match.
	 *
	 * @param array   $conditions Conditions Map array.
	 * @param integer $user_id User Id.
	 * @return boolean
	 */
	public function check_conditions( $conditions = array(), $user_id = 0 ) {

		$rule_match = false;
		foreach ( $conditions as $or_condition ) {
			$group_match = true;
			foreach ( $or_condition as $single_condition ) {
				$submit_value = '';
				$field_value  = '';
				$operator     = '';

				foreach ( $single_condition as $parameter ) {
					$key = explode( '[', $parameter['field_key'] )[0];
					switch ( $key ) {
						case 'user_registration_form_fields':
							$field_key = $parameter['field_value'];
							if ( in_array( $field_key, array( 'user_login', 'user_email', 'user_nicename', 'user_url', 'display_name' ), true ) ) {
								$user = get_user_by( 'ID', $user_id );
								if ( $user ) {
									$submit_value = isset( $user->data->$field_key ) ? $user->data->$field_key : '';
								}
							} else {
								$submit_value = get_user_meta( $user_id, 'user_registration_' . $field_key, true );

								// For default fields and woocommerce fields.
								if ( empty( $submit_value ) ) {
									$submit_value = get_user_meta( $user_id, $field_key, true );
								}
							}
							break;

						case 'user_registration_form_operator':
							$operator = $parameter['field_value'];
							break;

						case 'user_registration_form_value':
							$field_value = $parameter['field_value'];
							break;
					}
				}

				// Condition check for fields like checkbox that has value in array format.
				if ( is_array( $submit_value ) ) {
					if ( 'is' === $operator ) {
						$operator = 'in';
					} elseif ( 'is_not' === $operator ) {
						$operator = 'not_in';
					}
				};

				$match       = URCL_Frontend::logic_gates( $submit_value, $field_value, $operator );
				$group_match = $group_match && $match;
			}
			$rule_match = $rule_match || $group_match;
		}

		return $rule_match;
	}
}

new URCL_Redirection_Setting();
