<?php
namespace Joomunited\WPSOL\Minification;

use Joomunited\WPSOL\Minification\External\MinifyHtml;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class Html
 */
class Html extends Base
{
    /**
     * Init Minify HTML params
     *
     * @var string
     */
    private $minifyHTML = false;
    /**
     * Init exclude params
     * Default: exclude from nextgen-gallery
     *
     * @var array
     */
    private $exclude = array('<!-- ngg_resource_manager_marker -->');

    /**
     * Read and filter from content html
     *
     * @param array $options Option to minify
     *
     * @return boolean
     */
    public function read($options)
    {
        $this->minifyHTML = $options['minifyHTML'];

        /**
         * Apply filter to add strings to be excluded from HTML minification
         *
         * @param array Default value
         *
         * @return array
         */
        $this->exclude = apply_filters('wpsol_html_minification_exclude_string', $this->exclude);

        // Nothing else for HTML
        return true;
    }

    /**
     * Joins and optimizes CSS
     *
     * @return boolean
     */
    public function minify()
    {
        /**
         * Should we minify the specified inline html content
         *
         * @param true Minify by default the content
         * @param string Html content
         *
         * @return boolean
         */
        $dominify = apply_filters('wpsol_html_do_minify', true, $this->content);
        if (!$dominify) {
            return false;
        }

        if (class_exists('Joomunited\\WPSOL\\Minification\\External\\MinifyHtml')) {
            // wrap the to-be-excluded strings
            foreach ($this->exclude as $ex) {
                if (strpos($this->content, $ex) !== false) {
                    $ex_string = '<!--wpsol_exclude-->' . $ex . '<!--/wpsol_exclude-->';
                    $this->content = str_replace($ex, $ex_string, $this->content);
                }
            }

            // Minify html
            if (method_exists('Joomunited\\WPSOL\\Minification\\External\\MinifyHtml', 'minify')) {
                $tmp_content = MinifyHtml::minify($this->content);
                if (!empty($tmp_content)) {
                    $this->content = $tmp_content;
                    unset($tmp_content);
                }
            }

            // remove the exclude-wrapper from around the excluded strings
            foreach ($this->exclude as $ex) {
                $ex_string = '<!--wpsol_exclude-->' . $ex . '<!--/wpsol_exclude-->';
                if (strpos($this->content, $ex_string) !== false) {
                    $this->content = str_replace($ex_string, $ex, $this->content);
                }
            }

            return true;
        }

        // Didn't minify :(
        return false;
    }

    /**
     * Does nothing
     *
     * @return boolean
     */
    public function cache()
    {
        //No cache for HTML
        return true;
    }

    /**
     * Returns the content
     *
     * @return string
     */
    public function getcontent()
    {
        return $this->content;
    }
}
