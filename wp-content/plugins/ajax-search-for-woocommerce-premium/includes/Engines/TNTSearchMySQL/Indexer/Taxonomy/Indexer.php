<?php

namespace DgoraWcas\Engines\TNTSearchMySQL\Indexer\Taxonomy;

use DgoraWcas\Engines\TNTSearchMySQL\Config;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Builder;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Logger;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\Searchable\Cache;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\SynonymsHandler;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\WPDB;
use DgoraWcas\Engines\TNTSearchMySQL\Indexer\WPDBException;
use DgoraWcas\Engines\TNTSearchMySQL\Support\Stemmer\NoStemmer;
use DgoraWcas\Engines\TNTSearchMySQL\Support\Stemmer\StemmerInterface;
use DgoraWcas\Engines\TNTSearchMySQL\Support\Tokenizer\Tokenizer;
use DgoraWcas\Engines\TNTSearchMySQL\Support\Tokenizer\TokenizerInterface;
use DgoraWcas\Helpers;
use DgoraWcas\Multilingual;
use DgoraWcas\Term;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Indexer {
	/** @var string */
	private $indexRole;

	/** @var Cache */
	private $cache;

	/** @var null|StemmerInterface */
	private $stemmer = null;

	/** @var null|TokenizerInterface */
	private $tokenizer = null;

	/** @var null|SynonymsHandler */
	private $synonymsHandler = null;

	public function __construct() {
		$this->setIndexRole( Config::getIndexRole() );
		$this->cache = new Cache();
	}

	/**
	 * @param string $indexRole
	 */
	public function setIndexRole( string $indexRole ) {
		$this->indexRole = $indexRole;
	}

	public function getIndexRole() {
		return $this->indexRole;
	}

	public function prepareTools() {
		$this->setStemmer( new NoStemmer );
		$this->tokenizer = new Tokenizer;
		$this->tokenizer->setContext( 'indexer' );
		$this->synonymsHandler = new SynonymsHandler;
	}

	/**
	 * @param array $args
	 *
	 * @return Document
	 */
	public function getDocument( $args ) {
		if ( $this->stemmer === null ) {
			$this->prepareTools();
		}
		$document = new Document( $args, array() );
		$document->setStemmer( $this->stemmer );
		$document->setTokenizer( $this->tokenizer );
		$document->setSynonymsHandler( $this->synonymsHandler );
		$document->setIndexRole( $this->indexRole );

		return $document;
	}

	private function setStemmer( StemmerInterface $stemmer ) {
		$this->stemmer = $stemmer;
	}

	/**
	 * Insert term to the index
	 *
	 * @param int $termID
	 * @param string $taxonomy
	 *
	 * @return bool true on success
	 * @throws WPDBException
	 */
	public function index( $termID, $taxonomy ) {
		global $wpdb;

		$success = false;

		if ( ! Helpers::isTableExists( $wpdb->dgwt_wcas_tax_index . ( $this->indexRole === 'tmp' ? '_tmp' : '' ) ) ) {
			return $success;
		}

		$termLang = Multilingual::getTermLang( $termID, $taxonomy );

		if ( Multilingual::isMultilingual() ) {
			$term = Multilingual::getTerm( $termID, $taxonomy, $termLang );
			// Switch language to compatibility with other plugins.
			// Our plugin don't need this switch, but some plugins use the active language as the term language
			if ( Multilingual::getCurrentLanguage() !== $termLang ) {
				Multilingual::switchLanguage( $termLang );
			}
		} else {
			$term = get_term( $termID, $taxonomy );
		}

		$data = array();

		$termObj              = new Term( $term );
		$taxonomiesWithImages = apply_filters( 'dgwt/wcas/taxonomies_with_images', array() );

		if ( is_object( $term ) && ! is_wp_error( $term ) ) {

			$data = array(
				'term_id'        => $termID,
				'term_name'      => html_entity_decode( $term->name ),
				'term_link'      => get_term_link( $term, $taxonomy ),
				'image'          => in_array( $taxonomy, $taxonomiesWithImages ) ? $termObj->getThumbnailSrc() : '',
				'breadcrumbs'    => '',
				'total_products' => $term->count,
				'taxonomy'       => $taxonomy,
				'lang'           => $termLang
			);

			if ( $term->taxonomy === 'product_cat' ) {
				$breadcrumbs = Helpers::getTermBreadcrumbs( $termID, 'product_cat', array(), $termLang, array( $termID ) );

				// Fix: Remove last separator
				if ( ! empty( $breadcrumbs ) ) {
					$breadcrumbs = mb_substr( $breadcrumbs, 0, - 3 );
				}
				$data['breadcrumbs'] = $breadcrumbs;
			}

			$rows = WPDB::get_instance()->insert(
				$wpdb->dgwt_wcas_tax_index . ( $this->indexRole === 'tmp' ? '_tmp' : '' ),
				$data,
				array(
					'%d',
					'%s',
					'%s',
					'%s',
					'%s',
					'%d',
					'%s',
					'%s',
				)
			);

			if ( is_numeric( $rows ) ) {
				$success = true;
			}


			$documentData = array(
				'ID'        => $termID,
				'term_name' => html_entity_decode( $term->name ),
				'taxonomy'  => $taxonomy,
				'lang'      => Multilingual::isMultilingual() ? $termLang : '',
			);

			$documentData = apply_filters( 'dgwt/wcas/indexer/taxonomy/document_data', $documentData, $term );

			$document = $this->getDocument( $documentData );

			$doIndex = true;

			if ( Multilingual::isMultilingual() ) {
				$lang = $document->getLang();
				// Abort if the object hasn't the language.
				if ( empty( $lang ) ) {
					$doIndex = false;
				} // Abort if the object has a language that is not present in the settings.
				else if ( ! in_array( $lang, Multilingual::getLanguages() ) ) {
					$doIndex = false;
				}
			}

			if ( $doIndex ) {
				$document->save();
			}
		}

		do_action( 'dgwt/wcas/taxonomy_index/after_insert', $data, $termID, $taxonomy, $success, $this );

		return $success;
	}

	/**
	 * Update term
	 *
	 * @param int $termID
	 * @param string $taxonomy
	 *
	 * @return void
	 */
	public function update( $termID, $taxonomy ) {
		try {
			$this->delete( $termID, $taxonomy );
			$this->index( $termID, $taxonomy );
		} catch ( \Error $e ) {
			Logger::handleUpdaterThrowableError( $e, '[Taxonomy index] ' );
		} catch ( \Exception $e ) {
			Logger::handleUpdaterThrowableError( $e, '[Taxonomy index] ' );
		}
	}

	/**
	 * Remove term from the index
	 *
	 * @param int $termID
	 * @param string $taxonomy
	 *
	 * @return bool true on success
	 * @throws WPDBException
	 */
	public function delete( $termID, $taxonomy ) {
		global $wpdb;

		if ( Helpers::isTableExists( $wpdb->dgwt_wcas_tax_index ) ) {
			WPDB::get_instance()->delete(
				$wpdb->dgwt_wcas_tax_index,
				array( 'term_id' => $termID ),
				array( '%d' )
			);
		}

		try {
			$document = new Document( array(
				'ID'       => $termID,
				'taxonomy' => $taxonomy,
			), array() );
			$document->setIndexRole( $this->indexRole );
			$document->delete();
		} catch ( \Error $e ) {
			Logger::handleUpdaterThrowableError( $e, '[Taxonomy index] ' );
		} catch ( \Exception $e ) {
			Logger::handleUpdaterThrowableError( $e, '[Taxonomy index] ' );
		}

		$this->cache->setType( 'tax_' . $taxonomy );
		$this->cache->deleteByValue( $termID );

		return true;
	}

	/**
	 * Wipe index
	 *
	 * @return bool
	 */
	public function wipe( $indexRoleSuffix = '' ) {
		Database::remove( $indexRoleSuffix );
		Builder::log( '[Taxonomy index] Cleared' );

		return true;
	}

	/**
	 * Remove DB table
	 *
	 * @return void
	 */
	public static function remove() {
		global $wpdb;

		$wpdb->hide_errors();

		$wpdb->query( "DROP TABLE IF EXISTS $wpdb->dgwt_wcas_tax_index" );
	}
}
