<?php
/**
 * Admin class premium
 *
 * @author  YITH
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit;

if ( ! class_exists( 'YITH_WCMAP_Admin_Premium', false ) ) {
	/**
	 * Admin class.
	 * The class manage all the admin behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCMAP_Admin_Premium extends YITH_WCMAP_Admin_Extended {

		/**
		 * Class construct
		 *
		 * @since  3.12.0
		 * @author Francesco Licandro
		 * @return void
		 */
		public function __construct() {
			parent::__construct();

			remove_filter( 'yith_plugin_row_meta_documentation_url', array( $this, 'filter_extended_documentation_link' ), 10 );
			remove_filter( 'yith_wcmap_admin_panel_args', array( $this, 'filter_doc_url' ), 10 );

			add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCMAP_DIR . '/' . basename( YITH_WCMAP_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_wcmap_admin_tabs', array( $this, 'add_premium_tabs' ), 15, 1 );
			add_filter( 'yith_wcmap_admin_panel_args', array( $this, 'customize_premium_panel' ), 10, 1 );
			// Filter endpoint template args.
			add_filter( 'yith_wcmap_admin_endpoints_template', array( $this, 'filter_endpoint_template_args' ), 10, 1 );
			add_action( 'yith_wcmap_admin_after_single_item_content', array( $this, 'print_grouped_endpoint' ), 10, 3 );
		}

		/**
		 * Add premium tabs to plugin panel tabs
		 *
		 * @since  3.12.0
		 * @author Francesco Licandro
		 * @param array $tabs The panel admin tabs.
		 * @return array
		 */
		public function add_premium_tabs( $tabs ) {
			$tabs = array_merge( $tabs, array( 'banners' => __( 'Banners', 'yith-woocommerce-customize-myaccount-page' ) ) );
			return $tabs;
		}

		/**
		 * Add help tab to plugin panel tabs
		 *
		 * @since  3.12.0
		 * @author Francesco Licandro
		 * @param array $args The panel admin arguments.
		 * @return array
		 */
		public function customize_premium_panel( $args ) {
			unset( $args['premium_tab'] );
			return $args;
		}

		/**
		 * Filter endpoint template args.
		 * Add buttons link and group.
		 *
		 * @since  3.12.0
		 * @author Francesco Licandro
		 * @param array $args An array of template arguments.
		 * @return array
		 */
		public function filter_endpoint_template_args( $args ) {
			$args['actions'] = array_merge(
				$args['actions'],
				array(
					'link'  => array(
						'label'     => __( 'Add link', 'yith-woocommerce-customize-myaccount-page' ),
						'alt-label' => __( 'Close new link', 'yith-woocommerce-customize-myaccount-page' ),
					),
					'group' => array(
						'label'     => __( 'Add group', 'yith-woocommerce-customize-myaccount-page' ),
						'alt-label' => __( 'Close new group', 'yith-woocommerce-customize-myaccount-page' ),
					),
				)
			);

			return $args;
		}

		/**
		 * Print grouped endpoint in admin endpoint list
		 *
		 * @since  3.12.0
		 * @author Francesco Licandro
		 * @param string $item_key The parent item key.
		 * @param array  $options  The parent item options array.
		 * @param string $type     The parent type.
		 * @return void
		 */
		public function print_grouped_endpoint( $item_key, $options, $type ) {
			if ( empty( $options['children'] ) ) {
				return;
			}

			echo '<ol class="dd-list items">';
			foreach ( (array) $options['children'] as $key => $single_options ) {
				$args = array(
					'item_key' => $key,
					'options'  => $single_options,
					'type'     => isset( $single_options['url'] ) ? 'link' : 'endpoint',
				);

				call_user_func( 'yith_wcmap_admin_print_single_item', $args );
			}
			echo '</ol>';
		}

		/**
		 * Action Links
		 * add the action links to plugin admin page
		 *
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @param array $links Links plugin array.
		 * @return mixed
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, self::PANEL_PAGE, true, YITH_WCMAP_SLUG );
			return $links;
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
			if ( defined( 'YITH_WCMAP_INIT' ) && YITH_WCMAP_INIT === $plugin_file ) {
				$new_row_meta_args['slug']       = YITH_WCMAP_SLUG;
				$new_row_meta_args['live_demo']  = array( 'url' => 'https://plugins.yithemes.com/yith-woocommerce-customize-my-account-page/' );
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;
		}

		/**
		 * Enqueue scripts
		 * Premium add icons list as script data.
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 */
		public function enqueue_scripts() {
			// phpcs:disable WordPress.Security.NonceVerification
			parent::enqueue_scripts();
			if ( isset( $_GET['page'] ) && sanitize_text_field( wp_unslash( $_GET['page'] ) ) === self::PANEL_PAGE ) {
				// font awesome.
				wp_enqueue_style( 'font-awesome', YITH_WCMAP_ASSETS_URL . '/css/font-awesome.min.css', array(), YITH_WCMAP_VERSION );

				if ( empty( $_GET['tab'] ) || in_array( sanitize_text_field( wp_unslash( $_GET['tab'] ) ), array( 'items', 'banners' ), true ) ) {
					wp_localize_script( 'yith_wcmap', 'ywcmap_icons', yith_wcmap_get_icon_list() );
				}
			}
			// phpcs:enable WordPress.Security.NonceVerification
		}
	}
}
