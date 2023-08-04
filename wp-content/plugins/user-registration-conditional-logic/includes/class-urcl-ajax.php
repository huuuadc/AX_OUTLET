<?php
/**
 * UserRegistrationConditionalLogic URCL_AJAX
 *
 * AJAX Event Handler
 *
 * @class    URCL_AJAX
 * @version  1.3.6
 * @package  UserRegistrationConditionalLogic/AJAX
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * URCL_AJAX Class
 */
class URCL_AJAX {

	/**
	 * Initialization of ajax.
	 */
	public static function init() {
		self::add_ajax_events();
	}

	/**
	 * Hook in methods - uses WordPress ajax handlers (admin-ajax)
	 */
	public static function add_ajax_events() {
		$ajax_events = array(
			'fetch_conditional_block' => false,
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_urcl_' . $ajax_event, array( __CLASS__, $ajax_event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_urcl_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			}
		}
	}

	/**
	 * Triggered when clicking the allow usage notice allow or deny buttons.
	 */
	public static function fetch_conditional_block() {
		check_ajax_referer( 'urcl_condtional_logic_nonce', '_wpnonce' );

		if ( ! current_user_can( 'manage_options' ) || ! isset( $_POST['formID'] ) ) {
			wp_die( -1 );
		}

		$action_label = '';

		$form_id = absint( $_POST['formID'] );
		$fields  = get_conditional_fields_by_form_id( $form_id, '' );

		$template = URCL_Conditional_Logic_Submit::get_template( $action_label, ur_get_conditional_actions(), $fields, ur_get_conditional_operators() );
		$html     = URCL_Conditional_Logic_Submit::get_submit_conditions_list( $form_id, ur_get_conditional_actions(), $fields, ur_get_conditional_operators() );

		wp_send_json_success(
			array(
				'template' => $template,
				'html'     => $html
			)
		);
	}
}

URCL_AJAX::init();
