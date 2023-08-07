<?php
namespace Joomunited\WPSOL\Minification;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class Base
 */
abstract class Base
{
    /**
     * Init content params
     *
     * @var string
     */
    protected $content = '';
    /**
     * Init tag warning params
     *
     * @var boolean
     */
    protected $tagWarning = false;

    /**
     * Base constructor.
     *
     * @param string $content Content of page
     */
    public function __construct($content)
    {
        $this->content = $content;
    }


    /**
     * Reads the page and collects tags
     *
     * @param array $justhead Just header of page
     *
     * @return mixed
     */
    abstract public function read($justhead);

    /**
     * Joins and optimizes collected things
     *
     * @return mixed
     */
    abstract public function minify();

    /**
     * Caches the things
     *
     * @return mixed
     */
    abstract public function cache();

    /**
     * Returns the content
     *
     * @return mixed
     */
    abstract public function getcontent();

    /**
     * Converts an URL to a full path
     *
     * @param string $url Url to get path
     *
     * @return boolean|mixed
     */
    protected function getpath($url)
    {
        if (strpos($url, '%') !== false) {
            $url = urldecode($url);
        }

        // normalize
        if (strpos($url, '//') === 0) {
            if (is_ssl()) {
                $url = 'https:' . $url;
            } else {
                $url = 'http:' . $url;
            }
        } elseif ((strpos($url, '//') === false)
            && (strpos($url, parse_url(WPSOL_WP_SITE_URL, PHP_URL_HOST)) === false)) {
            $url = WPSOL_WP_SITE_URL . $url;
        }

        // first check; hostname wp site should be hostname of url
        $thisHost = parse_url($url, PHP_URL_HOST);
        if ($thisHost !== parse_url(WPSOL_WP_SITE_URL, PHP_URL_HOST)) {
            /*
            * first try to get all domains from WPML (if available)
            * then apply own filter takes an array of hostnames
            * each item in that array will be considered part of the same WP multisite installation
            */
            $multidomains = array();

            $multidomainsWPML = apply_filters('wpml_setting', array(), 'language_domains');
            if (!empty($multidomainsWPML)) {
                $multidomains = array_map(array($this, 'wpsolMinifyGetDomain'), $multidomainsWPML);
            }

            if (!empty($multidomains)) {
                if (in_array($thisHost, $multidomains)) {
                    $url = str_replace($thisHost, parse_url(WPSOL_WP_SITE_URL, PHP_URL_HOST), $url);
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        // try to remove "wp root url" from url while not minding http<>https
        $tmp_ao_root = preg_replace('/https?/', '', WPSOL_WP_ROOT_URL);
        $tmp_url = preg_replace('/https?/', '', $url);
        $path = str_replace($tmp_ao_root, '', $tmp_url);

        // final check; if path starts with :// or //,
        // this is not a URL in the WP context and we have to assume we can't aggregate
        if (preg_match('#^:?//#', $path)) {
            /**
             * External script/css (adsense, etc)
             */
            return false;
        }

        $path = str_replace('//', '/', WPSOL_ROOT_DIR . $path);
        return $path;
    }

    /**
     * Needed for WPML-filter
     *
     * @param string $in Input to get domain
     *
     * @return mixed|string
     */
    protected function wpsolMinifyGetDomain($in)
    {
        // make sure the url starts with something vaguely resembling a protocol
        if ((strpos($in, 'http') !== 0) && (strpos($in, '//') !== 0)) {
            $in = 'http://' . $in;
        }

        // do the actual parse_url
        $out = parse_url($in, PHP_URL_HOST);

        // fallback if parse_url does not understand the url is in fact a url
        if (empty($out)) {
            $out = $in;
        }

        return $out;
    }

    /**
     * Inject already HTML code in optimized JS/CSS
     *
     * @param string $element    Element to inject
     * @param array  $replaceTag Position in html
     *
     * @return void
     */
    protected function injectInHtml($element, $replaceTag)
    {
        if (strpos($this->content, $replaceTag[0]) !== false) {
            if ($replaceTag[1] === 'after') {
                $replaceBlock = $replaceTag[0] . $element;
            } elseif ($replaceTag[1] === 'replace') {
                $replaceBlock = $element;
            } else {
                $replaceBlock = $element . $replaceTag[0];
            }
            $strpos = strpos($this->content, $replaceTag[0]);
            $strlen = strlen($replaceTag[0]);
            $this->content = substr_replace($this->content, $replaceBlock, $strpos, $strlen);
        } else {
            $this->content .= $element;
            if (!$this->tagWarning) {
                $this->content .= '<!--wpsolminification--><!-- WPSOL found a problem with the HTML in your Theme,';
                $this->content .= 'tag ' . $replaceTag[0] . ' missing --><!--/wpsolminification-->';
                $this->tagWarning = true;
            }
        }
    }

    /**
     * Check match from content
     *
     * @param string $tag   Tag from content
     * @param array  $array List check
     *
     * @return boolean
     */
    protected function isMatch($tag, $array)
    {
        foreach ($array as $match) {
            if (strpos($tag, $match) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Custom isMatch() function for allow to exclude URL with wildcard
     *
     * @param string $tag       Tag from content
     * @param array  $listCheck List check
     *
     * @return boolean
     */
    protected function isMatchWildcard($tag, $listCheck)
    {
        if (empty($tag) || empty($listCheck) || strlen(trim($tag)) > 4096) {
            return false;
        }
        if (!empty($tag) && !empty($listCheck)) {
            foreach ($listCheck as $match) {
                $match = '*' . $match . '*';
                if (strlen(trim($match)) > 2048) {
                    continue;
                }
                if (fnmatch($match, $tag, 0)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check to exclude url from group
     *
     * @param string $url      Url to check
     * @param array  $excludes Element check url
     *
     * @return boolean
     */
    protected function checkExcludeFile($url, $excludes)
    {
        if (!empty($excludes)) {
            foreach ($excludes as $ex) {
                if (empty($ex)) {
                    continue;
                }
                if (strpos($ex, '/') === 0) {
                    $ex = ltrim($ex, '/');
                }
                preg_match_all('@' . $ex . '@', $url, $matches);

                if (!empty($matches[0])) {
                    return true;
                }
            }
        }
        return false;
    }


    /**
     * Hide ie hacks
     *
     * @param string $iehacks_in Ie hack input
     *
     * @return mixed
     */
    protected function hideIEhacks($iehacks_in)
    {
        if (strpos($iehacks_in, '<!--[if') !== false) {
            $iehacks_out = preg_replace_callback(
                '#<!--\[if.*?\[endif\]-->#is',
                function ($matches) {
                    return '%%IEHACK'.WPSOL_HASH.'%%'.base64_encode($matches[0]).'%%IEHACK%%';
                },
                $iehacks_in
            );
        } else {
            $iehacks_out = $iehacks_in;
        }
        return $iehacks_out;
    }

    /**
     * Restore ie hacks
     *
     * @param string $iehacks_in Ie hack input
     *
     * @return mixed
     */
    protected function restoreIEhacks($iehacks_in)
    {
        if (strpos($iehacks_in, '%%IEHACK%%') !== false) {
            $iehacks_out = preg_replace_callback(
                '#%%IEHACK' . WPSOL_HASH . '%%(.*?)%%IEHACK%%#is',
                function ($matches) {
                    return base64_decode($matches[1]);
                },
                $iehacks_in
            );
        } else {
            $iehacks_out = $iehacks_in;
        }
        return $iehacks_out;
    }

    /**
     * Hide comment in file
     *
     * @param string $comments_in Comment input
     *
     * @return mixed
     */
    protected function hideComments($comments_in)
    {
        if (strpos($comments_in, '<!--') !== false) {
            $comments_out = preg_replace_callback(
                '#<!--.*?-->#is',
                function ($matches) {
                    return '%%COMMENTS'.WPSOL_HASH.'%%'.base64_encode($matches[0]).'%%COMMENTS%%';
                },
                $comments_in
            );
        } else {
            $comments_out = $comments_in;
        }
        return $comments_out;
    }

    /**
     * Restore comments
     *
     * @param string $comments_in Comment input
     *
     * @return mixed
     */
    protected function restoreComments($comments_in)
    {
        if (strpos($comments_in, '%%COMMENTS%%') !== false) {
            $comments_out = preg_replace_callback(
                '#%%COMMENTS' . WPSOL_HASH . '%%(.*?)%%COMMENTS%%#is',
                function ($matches) {
                    return base64_decode($matches[1]);
                },
                $comments_in
            );
        } else {
            $comments_out = $comments_in;
        }
        return $comments_out;
    }
}
