<?php
/**
 * User_Registration_Pro_Ajax
 *
 * AJAX Event Handler
 *
 * @class    User_Registration_Pro_Ajax
 * @version  1.0.0
 * @package  UserRegistrationPro/Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * User_Registration_Pro_Ajax Class
 */
class User_Registration_Pro_Ajax {

	/**
	 * Hooks in ajax handlers
	 */
	public static function init() {

		self::add_ajax_events();

	}

	/**
	 * Hook in methods - uses WordPress ajax handlers (admin-ajax)
	 */
	public static function add_ajax_events() {

		$ajax_events = array(
			'dashboard_analytics'             => true,
			'delete_account'                  => false,
			'send_email_logout'               => true,
			'extension_install'               => true,
			'get_db_columns_by_table'         => true,
			'get_form_fields_list_by_form_id' => true,
			'request_user_data'               => false,
			'get_license_expiry_count'        => false,
			'inactive_logout'                 => true,
		);
		foreach ( $ajax_events as $ajax_event => $nopriv ) {

			add_action( 'wp_ajax_user_registration_pro_' . $ajax_event, array( __CLASS__, $ajax_event ) );

			if ( $nopriv ) {

				add_action(
					'wp_ajax_nopriv_user_registration_pro_' . $ajax_event,
					array(
						__CLASS__,
						$ajax_event,
					)
				);
			}
		}
	}


	/**
	 * Get License expiry count
	 */

	public static function get_license_expiry_count() {
		check_ajax_referer( 'ur_pro_get_license_expiry_count_nonce', 'security' );
		$last_notice_count = get_option( 'user_registration_license_expiry_notice_last_notice_count', 0 );
		update_option( 'user_registration_license_expiry_notice_last_dismissed_time', date_i18n( 'Y-m-d H:i:s' ) );
		update_option( 'user_registration_license_expiry_notice_last_notice_count', $last_notice_count + 1 );
		wp_die();
	}

	/**
	 * Get Column list by table name
	 */
	public static function get_db_columns_by_table() {
		check_ajax_referer( 'ur_pro_get_db_columns_by_table_nonce', 'security' );

		$table = isset( $_POST['table'] ) ? sanitize_text_field( wp_unslash( $_POST['table'] ) ) : '';
		if ( ! empty( $table ) ) {
			$columns = user_registration_get_columns_by_table( $table );
			wp_send_json_success(
				array(
					'columns' => json_encode( $columns, true ),
				)
			);
		}
	}

	/**
	 * Get Form Fields list by form id.
	 */
	public static function get_form_fields_list_by_form_id() {
		check_ajax_referer( 'ur_pro_get_form_fields_by_form_id_nonce', 'security' );
		$form_id    = isset( $_POST['form_id'] ) ? sanitize_text_field( wp_unslash( $_POST['form_id'] ) ) : '';
		$field_list = array();
		if ( ! empty( $form_id ) ) {
			$fields = ur_pro_get_form_fields( $form_id );
			foreach ( $fields as $post_key => $post_data ) {

				$pos = strpos( $post_key, 'user_registration_' );

				if ( false !== $pos ) {
					$new_string = substr_replace( $post_key, '', $pos, strlen( 'user_registration_' ) );

					if ( ! empty( $new_string ) ) {
						$field_list[ $new_string ] = $post_data['label'];
					}
				}
			}
		}
		wp_send_json_success(
			array(
				'form_field_list' => json_encode( $field_list, true ),
			)
		);
	}

	/**
	 * Ajax call when user clicks on delete account menu/tab.
	 */
	public static function delete_account() {

		$delete_account_option = get_option( 'user_registration_pro_general_setting_delete_account', 'disable' );

		if ( 'disable' === $delete_account_option ) {
			return;
		}
		$user     = new stdClass();
		$user->ID = (int) get_current_user_id();

		$form_id   = ur_get_form_id_by_userid( $user->ID );
		$form_data = user_registration_form_data( $user->ID, $form_id );

		$user_extra_fields = ur_get_user_extra_fields( $user->ID );
		$user_data         = array_merge( (array) get_userdata( $user->ID )->data, $user_extra_fields );

		// Get form data as per need by the {{all_fields}} smart tag.
		$valid_form_data = array();
		foreach ( $form_data as $key => $value ) {
			$new_key = trim( str_replace( 'user_registration_', '', $key ) );

			if ( isset( $user_data[ $new_key ] ) ) {
				$valid_form_data[ $new_key ] = (object) array(
					'field_type'   => $value['type'],
					'label'        => $value['label'],
					'field_name'   => $value['field_key'],
					'value'        => $user_data[ $new_key ],
					'extra_params' => array(
						'label'     => $value['label'],
						'field_key' => $value['field_key'],
					),
				);
			}
		}

		$current_user = get_user_by( 'id', get_current_user_id() );

		if ( $user->ID <= 0 ) {
			return;
		}

		$delete_account_flag = false;

		if ( isset( $_POST['password'] ) && ! empty( $_POST['password'] ) && 'prompt_password' === $delete_account_option ) {

			// Authenticate Current User.
			if ( ! wp_check_password( $_POST['password'], $current_user->user_pass, $current_user->ID ) ) {
				wp_send_json_error(
					array(
						'message' => __( 'Your current password is incorrect.', 'user-registration' ),
					)
				);
			}
			$delete_account_flag = true;

		} elseif ( 'direct_delete' === $delete_account_option ) {
			$delete_account_flag = true;
		}

		if ( $delete_account_flag ) {

			do_action( 'user_registration_pro_before_delete_account', $user );

			self::send_delete_account_email( $user->ID, $form_id, $valid_form_data );
			self::send_delete_account_admin_email( $user->ID, $form_id, $valid_form_data );

			if ( is_multisite() ) {

				if ( ! function_exists( 'wpmu_delete_user' ) ) {
					require_once ABSPATH . 'wp-admin/includes/ms.php';
				}

				wpmu_delete_user( $user->ID );

			} else {

				if ( ! function_exists( 'wp_delete_user' ) ) {
					require_once ABSPATH . 'wp-admin/includes/user.php';
				}

				wp_delete_user( $user->ID );

			}
			// TODO : Remove uploaded Files.
			do_action( 'user_registration_pro_after_delete_account', $user );
			wp_logout();
			wp_send_json_success(
				array(
					'message' => 'Deleted',
				)
			);
		}
	}

	/**
	 * Ajax call when user clicks force logout link .
	 *
	 * @since 3.0.0
	 */
	public static function send_email_logout() {

		include dirname( __FILE__ ) . '/admin/settings/emails/class-ur-settings-prevent-concurrent-login-email.php';

		$email   = sanitize_email( isset( $_POST['user_email'] ) ? $_POST['user_email'] : '' );
		$user_id = intval( $_POST['user_id'] );
		$header  = "Reply-To: {{email}} \r\n";
		$header .= 'Content-Type: text/html; charset=UTF-8';

		$subject                   = get_option( 'user_registration_prevent_concurrent_login_email_subject', 'Force logout' );
		$values                    = array(
			'email'   => $email,
			'user_id' => $user_id,
		);
		$settings                  = new UR_Settings_Prevent_Concurrent_Login_Email();
		$message                   = $settings->user_registration_get_prevent_concurrent_login_email();
		$message                   = get_option( 'user_registration_prevent_concurrent_login_email_content', $message );
		$form_id                   = ur_get_form_id_by_userid( $user_id );
		list( $message, $subject ) = user_registration_email_content_overrider( $form_id, $settings, $message, $subject );

		$message = UR_Emailer::parse_smart_tags( $message, $values );
		$subject = UR_Emailer::parse_smart_tags( $subject, $values );

		// Get selected email template id for specific form.
		$template_id = ur_get_single_post_meta( $form_id, 'user_registration_select_email_template' );

		if ( ur_option_checked( 'user_registration_enable_prevent_concurrent_login_email', true ) ) {
				UR_Emailer::user_registration_process_and_send_email( $email, $subject, $message, $header, '', $template_id );
		}

	}


	/**
	 * Dashboard Analytics.
	 */
	public static function dashboard_analytics() {
		$form_id       = isset( $_POST['form_id'] ) ? $_POST['form_id'] : 'all';
		$selected_date = isset( $_POST['selected_date'] ) ? $_POST['selected_date'] : 'Week';

		$user_registration_pro_dashboard = new User_Registration_Pro_Dashboard_Analytics();
		$message                         = $user_registration_pro_dashboard->output( $form_id, $selected_date );

		wp_send_json_success(
			$message
		);
	}

	/**
	 * Extenstion Install.
	 */
	public static function extension_install() {
		check_ajax_referer( 'ur_pro_install_extension_nonce', 'security' );

		$name = isset( $_POST['name'] ) ? $_POST['name'] : '';
		$slug = isset( $_POST['slug'] ) ? $_POST['slug'] : '';

		$status = ur_install_extensions( $name, $slug );

		wp_send_json( $status );
	}

	/**
	 * Send email to user when user deleted thier own account.
	 *
	 * @param int   $user_id ID of the user.
	 * @param int   $form_id Form ID.
	 * @param array $form_data Form Data.
	 */
	public static function send_delete_account_email( $user_id, $form_id, $form_data ) {

		include dirname( __FILE__ ) . '/admin/settings/emails/class-ur-settings-delete-account-email.php';

		$user     = get_user_by( 'ID', $user_id );
		$username = $user->data->user_login;
		$email    = $user->data->user_email;

		list( $name_value, $data_html ) = ur_parse_name_values_for_smart_tags( $user_id, $form_id, $form_data );
		$values                         = array(
			'username'   => $username,
			'email'      => $email,
			'all_fields' => $data_html,
		);

		$header  = 'From: ' . UR_Emailer::ur_sender_name() . ' <' . UR_Emailer::ur_sender_email() . ">\r\n";
		$header .= 'Reply-To: ' . UR_Emailer::ur_sender_email() . "\r\n";
		$header .= "Content-Type: text/html\r\n; charset=UTF-8";

		$subject = get_option( 'user_registration_pro_delete_account_email_subject', 'Your account has been deleted' );

		$settings                  = new UR_Settings_Delete_Account_Email();
		$message                   = $settings->user_registration_get_delete_account_email();
		$message                   = get_option( 'user_registration_pro_delete_account_email_content', $message );
		$form_id                   = ur_get_form_id_by_userid( $user_id );
		list( $message, $subject ) = user_registration_email_content_overrider( $form_id, $settings, $message, $subject );

		$message = UR_Emailer::parse_smart_tags( $message, $values, $name_value );
		$subject = UR_Emailer::parse_smart_tags( $subject, $values, $name_value );

		// Get selected email template id for specific form.
		$template_id = ur_get_single_post_meta( $form_id, 'user_registration_select_email_template' );

		if ( ur_option_checked( 'user_registration_pro_enable_delete_account_email', true ) ) {
			UR_Emailer::user_registration_process_and_send_email( $email, $subject, $message, $header, '', $template_id );
		}
	}
	/**
	 * Trigger the admin email after email verified.
	 *
	 * @param  string $user_email Email of the user.
	 * @param  string $username   Username of the user.
	 * @param  int    $user_id       User id.
	 * @param  string $data_html  String replaced with {{all_fields}} smart tag.
	 * @param  array  $name_value Array to replace with extra fields smart tag.
	 * @param  array  $attachments Email Attachement.
	 * @param  int    $template_id Template ID.
	 * @return void
	 */
	public static function send_mail_to_admin_after_email_verified( $user_email, $username, $user_id, $data_html, $name_value, $attachments, $template_id ) {

		include dirname( __FILE__ ) . '/admin/settings/emails/class-ur-settings-email-verified-admin-email.php';

		$header  = "Reply-To: {{email}} \r\n";
		$header .= 'Content-Type: text/html; charset=UTF-8';

		$attachment  = isset( $attachments['admin'] ) ? $attachments['admin'] : '';
		$admin_email = get_option( 'user_registration_pro_email_verified_admin_email_receipents', get_option( 'admin_email' ) );
		$admin_email = explode( ',', $admin_email );
		$admin_email = array_map( 'trim', $admin_email );

		$subject  = get_option( 'user_registration_pro_email_verified_admin_email_subject', __( 'A User Confirmed Email Address', 'user-registration' ) );
		$settings = new UR_Settings_Email_Verified_Admin_Email();
		$message  = $settings->ur_get_email_verified_admin_email();
		$message  = get_option( 'user_registration_pro_email_verified_admin_email', $message );

		$values                    = array(
			'username'   => $username,
			'email'      => $user_email,
			'all_fields' => $data_html,
		);
		list( $message, $subject ) = user_registration_email_content_overrider( ur_get_form_id_by_userid( $user_id ), $settings, $message, $subject );
		$message                   = UR_Emailer::parse_smart_tags( $message, $values, $name_value );
		$subject                   = UR_Emailer::parse_smart_tags( $subject, $values, $name_value );
		$header                    = UR_Emailer::parse_smart_tags( $header, $values, $name_value );

		if ( ur_option_checked( 'user_registration_enable_email_verified_admin_email', true ) ) {
			foreach ( $admin_email as $email ) {
				UR_Emailer::user_registration_process_and_send_email( $email, $subject, $message, $header, $attachment, $template_id );
			}
		}
	}

	/**
	 * Send email to admin when user deleted thier own account.
	 *
	 * @param int   $user_id ID of the user.
	 * @param int   $form_id ID of the user.
	 * @param array $form_data Form Data.
	 */
	public static function send_delete_account_admin_email( $user_id, $form_id, $form_data ) {

		include dirname( __FILE__ ) . '/admin/settings/emails/class-ur-settings-delete-account-admin-email.php';

		$user     = get_user_by( 'ID', $user_id );
		$username = $user->data->user_login;
		$email    = $user->data->user_email;

		list( $name_value, $data_html ) = ur_parse_name_values_for_smart_tags( $user_id, $form_id, $form_data );
		$values                         = array(
			'username'   => $username,
			'email'      => $email,
			'all_fields' => $data_html,
		);

		$header  = "Reply-To: {{admin_email}} \r\n";
		$header .= 'Content-Type: text/html; charset=UTF-8';

		$admin_email = get_option( 'user_registration_pro_delete_account_email_receipents', get_option( 'admin_email' ) );
		$admin_email = explode( ',', $admin_email );
		$admin_email = array_map( 'trim', $admin_email );

		$subject = get_option( 'user_registration_pro_delete_account_admin_email_subject', '{{blog_info}} Account deleted.' );

		$settings                  = new UR_Settings_Delete_Account_Admin_Email();
		$message                   = $settings->user_registration_get_delete_account_admin_email();
		$message                   = get_option( 'user_registration_pro_delete_account_admin_email_content', $message );
		$form_id                   = ur_get_form_id_by_userid( $user_id );
		list( $message, $subject ) = user_registration_email_content_overrider( $form_id, $settings, $message, $subject );

		$message = UR_Emailer::parse_smart_tags( $message, $values, $name_value );
		$subject = UR_Emailer::parse_smart_tags( $subject, $values, $name_value );

		// Get selected email template id for specific form.
		$template_id = ur_get_single_post_meta( $form_id, 'user_registration_select_email_template' );

		if ( ur_option_checked( 'user_registration_pro_enable_delete_account_admin_email', true ) ) {
			foreach ( $admin_email as $email ) {
				UR_Emailer::user_registration_process_and_send_email( $email, $subject, $message, $header, '', $template_id );
			}
		}
	}
	/**
	 * Privacy request.
	 */
	public static function request_user_data() {
		$security = isset( $_POST['security'] ) ? sanitize_text_field( wp_unslash( $_POST['security'] ) ) : '';
		if ( '' === $security || ! wp_verify_nonce( $security, 'user_data_nonce' ) ) {
			wp_send_json_error( 'Nonce verification failed' );
			return;
		}
		if ( ! isset( $_POST['request_action'] ) ) {
			wp_send_json_error( __( 'Wrong request.', 'user-registration' ) );
		}

		$user_id        = get_current_user_id();
		$password       = ! empty( $_POST['password'] ) ? sanitize_text_field( wp_unslash( $_POST['password'] ) ) : '';
		$user           = get_userdata( $user_id );
		$hash           = $user->data->user_pass;
		$request_action = sanitize_key( $_POST['request_action'] );

		if ( ! wp_check_password( $password, $hash ) ) {
			$answer = sprintf( '<div class="ur-field-error ur-erase-data"><span class="ur-privacy-password-error"><i class="ur-faicon-caret-up"></i>%s</span></div>', esc_html__( 'The password you entered is incorrect.', 'user-registration' ) );
			wp_send_json_success(
				array(
					'success' => 0,
					'answer'  => $answer,
				)
			);
		}

		if ( 'ur-export-data' === $request_action ) {
			$request_id   = wp_create_user_request( $user->data->user_email, 'export_personal_data', array(), 'confirmed' );
			$request_name = __( 'Export Personal Data', 'user-registration' );
		} elseif ( 'ur-erase-data' === $request_action ) {
			$request_id   = wp_create_user_request( $user->data->user_email, 'remove_personal_data', array(), 'confirmed' );
			$request_name = __( 'Export Erase Data', 'user-registration' );
		}

		if ( ! isset( $request_id ) || empty( $request_id ) ) {
			wp_send_json_error( __( 'Wrong request.', 'user-registration' ) );
		}

		if ( is_wp_error( $request_id ) ) {
			$answer = esc_html( $request_id->get_error_message() );
		} else {
			if ( 'ur-export-data' === $request_action ) {
				$visit_url = admin_url() . 'export-personal-data.php';
				$answer    = sprintf( '<h3>%s</h3> %s', __( 'Download your Data', 'user-registration' ), esc_html__( 'The administrator has not yet approved downloading the data. Pleas wait for approval.', 'user-registration' ) );
			} elseif ( 'ur-erase-data' === $request_action ) {
				$visit_url = admin_url() . 'erase-personal-data.php';
				$answer    = sprintf( '<h3>%s</h3> %s', __( 'Erase of your Data', 'user-registration' ), esc_html__( 'The administrator has not yet approved deleting your data. Pleas wait for approval.', 'user-registration' ) );
			}
			$subject    = sprintf( '%s %s', __( 'Approval Action:', 'user-registration' ), $request_name );
			$request    = wp_get_user_request( $request_id );
			$user_email = $request->email;
			$headers    = array(
				'From: ' . get_bloginfo( 'name' ) . ' <' . get_bloginfo( 'admin_email' ) . '>',
				'Reply-To: ' . $user_email,
			);
			$message    = sprintf(
				'%s  %s %s %s %s %s',
				__( 'Hi,', 'user-registration' ),
				__(
					'A user data privacy request has been confirmed:',
					'user-registration'
				),
				__( 'user:', 'user-registration' ),
				$user_email,
				__( 'You can view and manage these data privacy requests here:', 'user-registration' ),
				$visit_url
			);
			wp_mail( get_bloginfo( 'admin_email' ), $subject, $message, $headers );
		}

		wp_send_json_success(
			array(
				'success' => 1,
				'answer'  => $answer,
			)
		);
	}

	/**
	 * Auto logout if the certain inactive time is over.
	 */
	public static function inactive_logout() {
		$security = isset( $_POST['security'] ) ? sanitize_text_field( wp_unslash( $_POST['security'] ) ) : '';
		if ( '' === $security || ! wp_verify_nonce( $security, 'inactive_logout_nonce' ) ) {
			wp_send_json_error( __( 'Nonce verification failed', 'user-registration' ) );
			return;
		}
		$user_id = get_current_user_id();
		// logout the current login user.
		wp_logout( $user_id );
		wp_send_json_success( __( 'Logout successfully', 'user-registration' ) );
	}

}

User_Registration_Pro_Ajax::init();
