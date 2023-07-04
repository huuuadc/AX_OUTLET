<?php

/**
 * Fired during plugin activation
 *
 * @link       https://mkey.fr
 * @since      1.0.0
 *
 * @package    Hm_Wp_Scss
 * @subpackage Hm_Wp_Scss/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Hm_Wp_Scss
 * @subpackage Hm_Wp_Scss/includes
 * @author     Happy Monkey <contact@mkey.fr>
 */
class Hm_Wp_Scss_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

        // Default settings
        add_option( 'hm_wp_scss__scss_location', '' );
        add_option( 'hm_wp_scss__css_location', '' );
        add_option( 'hm_wp_scss__adv_path_scss', '' );
        add_option( 'hm_wp_scss__adv_path_css', '' );
        add_option( 'hm_wp_scss__compilation_time', 'When SCSS has changed' );
        add_option( 'hm_wp_scss__compilation_mode', 'Compac' );
        add_option( 'hm_wp_scss__source_map_file', '1' );
        add_option( 'hm_wp_scss__errors_display', 'If Admin Logged In' );
        add_option( 'hm_wp_scss__no_compilation_underscore', '1' );
        add_option( 'hm_wp_scss__enqueuing', '1' );
	}

}
