<?php
/**
 * @dgwt_wcas_premium_only
 */

namespace DgoraWcas\Integrations\Plugins\AddifyB2B;

use DgoraWcas\Helpers;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Integration with B2B for WooCommerce
 *
 * Plugin URL: https://woocommerce.com/products/b2b-for-woocommerce/
 * Author: Addify
 */
class AddifyB2B {
	public function init() {
		if ( ! dgoraAsfwFs()->is_premium() ) {
			return;
		}
		if ( ! class_exists( 'Addify_B2B_Plugin' ) ) {
			return;
		}

		add_action( 'init', array( $this, 'storeInSessionProductsAndTerms' ), 20 );

		add_filter( 'dgwt/wcas/troubleshooting/renamed_plugins', array( $this, 'getFolderRenameInfo' ) );
	}

	/**
	 * Store disallowed product and term ids in session
	 */
	public function storeInSessionProductsAndTerms() {
		/*
		 * This part of code is from \Addify_Products_Visibility_Front::afpvu_custom_pre_get_posts_query().
		 */
		$afpvu_enable_global = get_option( 'afpvu_enable_global' );
		$curr_role           = is_user_logged_in() ? current( wp_get_current_user()->roles ) : 'guest';
		$role_selected_data  = (array) get_option( 'afpvu_user_role_visibility' );

		if ( empty( $role_selected_data ) && 'yes' !== $afpvu_enable_global ) {
			return;
		}

		$role_data = isset( $role_selected_data[ $curr_role ]['afpvu_enable_role'] ) ? $role_selected_data[ $curr_role ]['afpvu_enable_role'] : 'no';

		if ( 'yes' === $afpvu_enable_global ) {
			$afpvu_show_hide          = get_option( 'afpvu_show_hide' );
			$afpvu_applied_products   = (array) get_option( 'afpvu_applied_products' );
			$afpvu_applied_categories = (array) get_option( 'afpvu_applied_categories' );
		}

		if ( 'yes' === $role_data ) {
			$_data                    = $role_selected_data[ $curr_role ];
			$afpvu_show_hide          = isset( $_data['afpvu_show_hide_role'] ) ? $_data['afpvu_show_hide_role'] : 'hide';
			$afpvu_applied_products   = isset( $_data['afpvu_applied_products_role'] ) ? (array) $_data['afpvu_applied_products_role'] : array();
			$afpvu_applied_categories = isset( $_data['afpvu_applied_categories_role'] ) ? (array) $_data['afpvu_applied_categories_role'] : array();
		}

		if ( empty( $afpvu_applied_products ) && empty( $afpvu_applied_categories ) ) {
			return;
		}

		$products_ids = array();

		if ( ! empty( $afpvu_applied_categories ) ) {
			$product_args = array(
				'numberposts' => - 1,
				'post_status' => array( 'publish' ),
				'post_type'   => array( 'product' ), //skip types
				'fields'      => 'ids'
			);

			$product_args['tax_query'] = array(
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'id',
					'terms'    => $afpvu_applied_categories,
					'operator' => 'IN',
				)
			);

			$products_ids = (array) get_posts( $product_args );
		}

		$afpvu_applied_products = array_merge( (array) $afpvu_applied_products, (array) $products_ids );

		$newSession = false;
		if ( ! session_id() ) {
			session_start();
			$newSession = true;
		}

		if ( ! empty( $afpvu_show_hide ) && 'hide' == $afpvu_show_hide ) {
			$_SESSION['dgwt-wcas-addify-b2b-applied-products']   = $afpvu_applied_products;
			$_SESSION['dgwt-wcas-addify-b2b-applied-categories'] = $afpvu_applied_categories;
			$_SESSION['dgwt-wcas-addify-b2b-action']             = 'hide';
		} elseif ( ! empty( $afpvu_show_hide ) && 'show' == $afpvu_show_hide ) {
			$_SESSION['dgwt-wcas-addify-b2b-applied-products']   = $afpvu_applied_products;
			$_SESSION['dgwt-wcas-addify-b2b-applied-categories'] = $afpvu_applied_categories;
			$_SESSION['dgwt-wcas-addify-b2b-action']             = 'show';
		}

		if ( $newSession && function_exists( 'session_status' ) && session_status() === PHP_SESSION_ACTIVE ) {
			session_write_close();
		}
	}

	/**
	 * Get info about renamed plugin folder
	 *
	 * @param array $plugins
	 *
	 * @return array
	 */
	public function getFolderRenameInfo( $plugins ) {
		$filters = new Filters();

		$result = Helpers::getFolderRenameInfo__premium_only( 'B2B for WooCommerce', $filters->plugin_names );
		if ( $result ) {
			$plugins[] = $result;
		}

		return $plugins;
	}
}
