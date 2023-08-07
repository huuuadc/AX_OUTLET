<?php

namespace DgoraWcas;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Shortcode {

	public static function register() {
		add_shortcode( 'wcas-search-form', array( __CLASS__, 'addBody' ) );
		add_shortcode( 'fibosearch', array( __CLASS__, 'addBody' ) );
		if ( dgoraAsfwFs()->is__premium_only() ) {
			add_shortcode( 'fibosearch_posts_results', array( __CLASS__, 'fibosearchPostsResultsBody__premium_only' ) );
		}
	}

	/**
	 * Register Woo Ajax Search shortcode
	 *
	 * @param array $atts bool show_details_box
	 */
	public static function addBody( $atts, $content, $tag ) {
		$layout = Helpers::getLayoutSettings();

		$searchArgs = shortcode_atts( array(
			'class'                     => '',
			'style'                     => $layout->style,
			'icon'                      => $layout->icon,
			'layout'                    => $layout->layout,
			'layout_breakpoint'         => '',
			'mobile_overlay'            => $layout->mobile_overlay,
			'mobile_overlay_breakpoint' => '',
			'darken_bg'                 => $layout->darken_background,
			'submit_btn'                => null,
			'submit_text'               => null,
			'icon_color'                => ''
		), $atts, $tag );

		$searchArgs['class'] .= empty( $searchArgs['class'] ) ? 'woocommerce' : ' woocommerce';

		$args = apply_filters( 'dgwt/wcas/shortcode/args', $searchArgs );

		return self::getForm( $args );
	}

	/**
	 * Display search form
	 *
	 * @param array args
	 *
	 * @return string
	 */

	public static function getForm( $args ) {
		// Enqueue required scripts (only if AMP is not active)
		if ( ! Helpers::isAMPEndpoint() ) {
			wp_enqueue_script( 'jquery-dgwt-wcas' );
			if ( DGWT_WCAS()->settings->getOption( 'show_details_box' ) === 'on' ) {
				wp_enqueue_script( 'woocommerce-general' );
			}
		}

		$args = self::mapAlternativeFormArgs( $args );

		$args = self::applyCondtitionalFormArgs( $args );

		$filename = apply_filters( 'dgwt/wcas/form/partial_path', DGWT_WCAS_DIR . 'partials/search-form.php' );

		$html = self::getTemplatePart( $filename, $args );

		return apply_filters( 'dgwt/wcas/form/html', $html, $args );
	}

	/**
	 * Display search results for post types
	 *
	 * @param $atts
	 * @param $content
	 * @param $tag
	 *
	 * @return string
	 */
	public static function fibosearchPostsResultsBody__premium_only( $atts, $content, $tag ) {
		// Break early when we are not on the search page.
		if ( ! Helpers::isProductSearchPage() ) {
			return '';
		}

		$shortcodeArgs = shortcode_atts( array(
			'post_type'  => 'post',
			'headline'   => '',
			'no_results' => __( 'Nothing found', 'ajax-search-for-woocommerce' ),
			'layout'     => 'list', // supported layouts: grid, list
			'limit'      => '12',
			'show_image' => '1',
		), $atts, $tag );

		if ( ! in_array( $shortcodeArgs['post_type'], Helpers::getAllowedPostTypes() ) ) {
			return '';
		}

		$pluralPostTypeLabel = mb_strtolower( Helpers::getPostTypeLabel( $shortcodeArgs['post_type'], 'name' ) );

		if ( empty( $shortcodeArgs['headline'] ) ) {
			/* translators: %s: Post type name in plural form e.g. Posts, Pages */
			$shortcodeArgs['headline'] = sprintf( __( 'Search results for %s:', 'ajax-search-for-woocommerce' ), $pluralPostTypeLabel );
		}

		$args = apply_filters( 'dgwt/wcas/shortcode/fibosearch_posts_results/args', $shortcodeArgs );

		if ( ! in_array( $args['layout'], array( 'list', 'grid' ) ) ) {
			return '';
		}

		$phrase = $_GET['s'] ?? ''; // FiboSearch takes care of the security of the phrase on its side.

		$results = DGWT_WCAS()->searchPosts( $phrase, array(
			'post_type' => $args['post_type'],
			'per_page'  => $args['limit'],
			'fields'    => 'all',
		) );

		if ( is_wp_error( $results ) ) {
			_doing_it_wrong( __FUNCTION__, join( ' ', $results->get_error_messages() ), '1.25.0' );

			return '';
		}

		$args['results'] = $results;

		$filename = apply_filters( 'dgwt/wcas/shortcode/fibosearch_posts_results/partial_path', DGWT_WCAS_DIR . 'partials/search-results-page/posts-results.php' );

		return self::getTemplatePart( $filename, $args );
	}

	/**
	 * Map alternative form of shortcode params values
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public static function mapAlternativeFormArgs( $args ) {

		// Show submit button
		if ( isset( $args['submit_btn'] ) ) {
			if ( in_array( $args['submit_btn'], array( '1', 'yes', 'show' ) ) ) {
				$args['submit_btn'] = 'on';
			}

			if ( in_array( $args['submit_btn'], array( '0', 'no', 'hide' ) ) ) {
				$args['submit_btn'] = 'off';
			}
		}

		// Style: solaris, pirx
		if ( ! empty( $args['style'] ) ) {
			if ( in_array( $args['style'], array( 'default', 'classic' ) ) ) {
				$args['style'] = 'solaris';
			}
			if ( in_array( $args['style'], array( 'bean', 'rounded' ) ) ) {
				$args['style'] = 'pirx';
			}
		}

		// Layout: classic, icon, icon-flexible, icon-flexible-inv
		if ( ! empty( $args['layout'] ) ) {
			if ( in_array( $args['layout'], array( 'search-bar', 'default' ) ) ) {
				$args['layout'] = 'classic';
			}
			if ( in_array( $args['layout'], array( 'flex-icon-on-mobile', 'flex-icon-mob', ) ) ) {
				$args['layout'] = 'icon-flexible';
			}
			if ( in_array( $args['layout'], array( 'flex-icon-on-desktop', 'flex-icon-desktop', ) ) ) {
				$args['layout'] = 'icon-flexible-inv';
			}
		}

		return $args;
	}

	/**
	 * Apply some conditions before pass shortcode args forward
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public static function applyCondtitionalFormArgs( $args ) {

		// Force requires options for Pirx style
		if ( ! empty( $args['style'] ) && $args['style'] === 'pirx' ) {
			$args['submit_btn']  = 'on';
			$args['submit_text'] = '';
		}

		return $args;
	}

	public static function getTemplatePart( $filename, $args ) {
		ob_start();
		if ( file_exists( $filename ) ) {
			include $filename;

			/** @see wp_opcache_invalidate() */
			if (
				function_exists( 'opcache_invalidate' ) &&
				( ! ini_get( 'opcache.restrict_api' ) || stripos( realpath( $_SERVER['SCRIPT_FILENAME'] ), ini_get( 'opcache.restrict_api' ) ) === 0 )
			) {
				@opcache_invalidate( $filename, true );
			}
		}

		return (string) ob_get_clean();
	}
}
