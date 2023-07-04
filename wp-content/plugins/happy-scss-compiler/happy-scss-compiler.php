<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://mkey.fr
 * @since             1.0.0
 * @package           Hm_Wp_Scss
 *
 * @wordpress-plugin
 * Plugin Name:       Happy SCSS Compiler - Compile SCSS to CSS automatically
 * Plugin URI:
 * Description:       Compile your SCSS code to CSS automatically, and choose when and how to compile.
 * Version:           1.3.10
 * Author:            Happy Monkey
 * Author URI:        https://mkey.fr
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       happy-scss-compiler
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'HM_WP_SCSS_VERSION', '1.3.10' );

// Adding Settings link in plugins page of WP administration
$basename = plugin_basename( __FILE__ );
$prefix = is_network_admin() ? 'network_admin_' : '';
add_filter(
    "{$prefix}plugin_action_links_$basename",
    'plugin_action_links',
    10, // priority
    4  // parameters
);
function plugin_action_links( $links, $plugin_file, $plugin_data, $context )
{
    $links[] = '<a href="' .
    admin_url( 'options-general.php?page=happy-scss-compiler' ) .
    '">' . __('Settings') . '</a>';
    return $links;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-hm-wp-scss-activator.php
 */
function activate_hm_wp_scss() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-hm-wp-scss-activator.php';
	Hm_Wp_Scss_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-hm-wp-scss-deactivator.php
 */
function deactivate_hm_wp_scss() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-hm-wp-scss-deactivator.php';
	Hm_Wp_Scss_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_hm_wp_scss' );
register_deactivation_hook( __FILE__, 'deactivate_hm_wp_scss' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-hm-wp-scss.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_hm_wp_scss() {

	$plugin = new Hm_Wp_Scss();
	$plugin->run();

}
run_hm_wp_scss();

function happy_scss_compiler_activation_redirect( $plugin ) {
    if( $plugin == plugin_basename( __FILE__ ) ) {
        exit( wp_redirect( admin_url( 'options-general.php?page=happy-scss-compiler' ) ) );
    }
}
add_action( 'activated_plugin', 'happy_scss_compiler_activation_redirect' );
