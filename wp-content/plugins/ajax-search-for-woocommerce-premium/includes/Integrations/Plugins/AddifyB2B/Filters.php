<?php
/**
 * @dgwt_wcas_premium_only
 */

namespace DgoraWcas\Integrations\Plugins\AddifyB2B;

use DgoraWcas\Engines\TNTSearchMySQL\Config;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Filters {
	public $plugin_names = array(
		'b2b/addify_b2b.php',
	);

	private $applied_products = array();
	private $appied_categories = array();
	private $action = '';

	public function init() {
		foreach ( $this->plugin_names as $plugin_name ) {
			if ( Config::isPluginActive( $plugin_name ) ) {
				$this->setData();
				$this->excludeOrShowProductsAndCategories();

				break;
			}
		}
	}

	/**
	 * Set applied products from PHP Session
	 *
	 * @return void
	 */
	private function setData() {
		if ( ! session_id() ) {
			session_start();
		}

		if ( ! empty( $_SESSION['dgwt-wcas-addify-b2b-applied-products'] ) ) {
			$this->applied_products = $_SESSION['dgwt-wcas-addify-b2b-applied-products'];
		}
		if ( ! empty( $_SESSION['dgwt-wcas-addify-b2b-applied-categories'] ) ) {
			$this->appied_categories = $_SESSION['dgwt-wcas-addify-b2b-applied-categories'];
		}
		if ( ! empty( $_SESSION['dgwt-wcas-addify-b2b-action'] ) ) {
			$this->action = $_SESSION['dgwt-wcas-addify-b2b-action'];
		}
	}

	/**
	 * Show/hide products and categories returned by plugin
	 */
	private function excludeOrShowProductsAndCategories() {
		add_filter( 'dgwt/wcas/tnt/search_results/ids', function ( $ids ) {
			// Exclude hidden products.
			if ( $this->action === 'hide' && ! empty( $this->applied_products ) && is_array( $this->applied_products ) ) {
				$ids = array_diff( $ids, $this->applied_products );
			}
			// Show visible products.
			if ( $this->action === 'show' && ! empty( $this->applied_products ) && is_array( $this->applied_products ) ) {
				$ids = array_intersect( $ids, $this->applied_products );
			}

			return $ids;
		} );

		add_filter( 'dgwt/wcas/search_results/term_ids', function ( $ids ) {
			ray($ids);
			// Exclude hidden categories.
			if ( $this->action === 'hide' && ! empty( $this->appied_categories ) && is_array( $this->appied_categories ) ) {
				$ids = array_diff( $ids, $this->appied_categories );
			}
			// Show visible categories.
			if ( $this->action === 'show' && ! empty( $this->appied_categories ) && is_array( $this->appied_categories ) ) {
				$ids = array_intersect( $ids, $this->appied_categories );
			}

			return $ids;
		} );
	}
}
