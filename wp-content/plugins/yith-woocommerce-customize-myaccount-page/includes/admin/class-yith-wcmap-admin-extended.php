<?php
/**
 * Admin class extended
 *
 * @author  YITH
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit;

if ( ! class_exists( 'YITH_WCMAP_Admin_Extended', false ) ) {
	/**
	 * Admin class extended.
	 * The class manage all the admin behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCMAP_Admin_Extended extends YITH_WCMAP_Admin {

		/**
		 * Class construct
		 *
		 * @since  3.12.0
		 * @author Francesco Licandro
		 * @return void
		 */
		public function __construct() {
			parent::__construct();

			add_filter( 'yith_wcmap_admin_tabs', array( $this, 'add_extended_tabs' ), 10, 1 );
			// Add action links.
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );
			add_filter( 'yith_plugin_row_meta_documentation_url', array( $this, 'filter_extended_documentation_link' ), 10, 4 );
			add_filter( 'yith_wcmap_admin_panel_args', array( $this, 'filter_doc_url' ), 10, 1 );
		}

		/**
		 * Add extended tabs to plugin panel tabs
		 *
		 * @since  3.12.0
		 * @author Francesco Licandro
		 * @param array $tabs The panel admin tabs.
		 * @return array
		 */
		public function add_extended_tabs( $tabs ) {
			$tabs = array_merge(
				$tabs,
				array(
					'general'  => __( 'General Settings', 'yith-woocommerce-customize-myaccount-page' ),
					'style'    => __( 'Style Options', 'yith-woocommerce-customize-myaccount-page' ),
					'security' => __( 'Security Options', 'yith-woocommerce-customize-myaccount-page' ),
				)
			);
			return $tabs;
		}

		/**
		 * Filter extended version documentation link for row meta
		 *
		 * @since 3.12.0
		 * @author Francesco Licandro
		 * @param string $url Current url.
		 * @param string $field Current printing field.
		 * @param string $slug Current product slug.
		 * @param array  $base_uri The row meta base uri array.
		 * @return string
		 */
		public function filter_extended_documentation_link( $url, $field, $slug, $base_uri ) {
			if ( YITH_WCMAP_SLUG === $slug ) {
				$url = $base_uri[ $field ] . 'yith-woocommerce-customize-my-account-page';
			}
			return $url;
		}

		/**
		 * Filter doc url in plugin panel args
		 *
		 * @since 3.12.0
		 * @param array $args The panel arguments array.
		 * @author Francesco Lciandro
		 * @return array
		 */
		public function filter_doc_url( $args ) {
			if ( isset( $args['help_tab'] ) ) {
				$args['help_tab']['doc_url'] = 'https://www.bluehost.com/help/article/yith-woocommerce-customize-my-account-page';
			}
			return $args;
		}

		/**
		 * Plugin row meta. Add the action links to plugin admin page
		 *
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use      plugin_row_meta
		 * @param array    $new_row_meta_args An array of plugin row meta.
		 * @param string[] $plugin_meta       An array of the plugin's metadata,
		 *                                    including the version, author,
		 *                                    author URI, and plugin URI.
		 * @param string   $plugin_file       Path to the plugin file relative to the plugins directory.
		 * @param array    $plugin_data       An array of plugin data.
		 * @param string   $status            Status of the plugin. Defaults are 'All', 'Active',
		 *                                    'Inactive', 'Recently Activated', 'Upgrade', 'Must-Use',
		 *                                    'Drop-ins', 'Search', 'Paused'.
		 * @return   Array
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status ) {
			if ( defined( 'YITH_WCMAP_EXTENDED_INIT' ) && YITH_WCMAP_EXTENDED_INIT === $plugin_file ) {
				$new_row_meta_args['slug']        = YITH_WCMAP_SLUG;
				$new_row_meta_args['is_extended'] = true;
			}

			return $new_row_meta_args;
		}
	}
}
