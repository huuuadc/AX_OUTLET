<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://mkey.fr
 * @since      1.0.0
 *
 * @package    Hm_Wp_Scss
 * @subpackage Hm_Wp_Scss/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Hm_Wp_Scss
 * @subpackage Hm_Wp_Scss/includes
 * @author     Happy Monkey <contact@mkey.fr>
 */
class Hm_Wp_Scss_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'hm-wp-scss',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
