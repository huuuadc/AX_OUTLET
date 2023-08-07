<?php
namespace Joomunited\WPSOL\Minification;

use Joomunited\WPSOL\Minification\External\CSSMin;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class Styles
 */
class Styles extends Base
{
    /**
     * Init minify css params
     *
     * @var boolean
     */
    private $minifyCSS = false;
    /**
     * Init css params
     *
     * @var array
     */
    private $css = array();
    /**
     * Init css code params
     *
     * @var array
     */
    private $csscode = array();
    /**
     * Init defer params
     *
     * @var boolean
     */
    private $defer = false;
    /**
     * Save defer css file path
     *
     * @var array
     */
    private $deferPath = array();
    /**
     * Init default exclude params
     *
     * @var array
     */
    private $defaultExcludeCSS = array();
    /**
     * Init css inline size params
     *
     * @var string
     */
    private $cssinlinesize = '';
    /**
     * Init google font params
     *
     * @var array
     */
    private $grfonts = array('fonts.googleapis.com');
    /**
     * Init group fonts params
     *
     * @var boolean
     */
    private $group_fonts = false;
    /**
     * Init include inline params
     *
     * @var boolean
     */
    private $excludeInlineStyle = false;
    /**
     * Init group css params
     *
     * @var boolean
     */
    private $group_css = false;
    /**
     * Init css min array params
     *
     * @var array
     */
    private $css_min_arr = array();
    /**
     * Init minified url params
     *
     * @var boolean
     */
    private $minified_url = array();
    /**
     * Init css exclude params
     *
     * @var array
     */
    private $css_exclude = array();
    /**
     * Exclude from defer css by customer
     *
     * @var array
     */
    private $exFDeferCSSList = array();
    /**
     * Styles
     *
     * @var array
     */
    private $styles = array();
    /**
     * Reads the page and collects style tags
     *
     * @param array $options Option of minify css
     *
     * @return boolean
     */
    public function read($options)
    {
        /**
         * Should we minify the specified inline css content
         *
         * @param true Default value
         * @param string Css content
         *
         * @return boolean
         */
        $dominify = apply_filters('wpsol_css_inline_do_minify', true, $this->content);
        if (!$dominify) {
            return false;
        }

        /**
         * Apply filter inline size of css
         *
         * @param string wpsol_css_inline_max_size
         * @param integer Default value
         *
         * @return integer
         */
        $this->cssinlinesize = apply_filters('wpsol_css_inline_max_size', 256);

        $this->minifyCSS = $options['minifyCSS'];

        // group css?
        $this->group_css = $options['groupcss'];

        $this->group_fonts = $options['groupfonts'];

        //custom css,font exclude
        $this->css_exclude = $options['exclude_css'];

        // what CSS shouldn't be exclude
        $this->defaultExcludeCSS = $options['css_exclude'];

        // should we defer css?
        $this->defer = $options['defer'];

        // Get exclude from defer css file list
        $this->exFDeferCSSList = get_option('wpsol_exclude_from_defer_css');

        // exclude inline?
        $this->excludeInlineStyle = $options['exclude_inline'];

        // exclude (no)script, as those may contain CSS which should be left as is
        if (strpos($this->content, '<script') !== false) {
            $this->content = preg_replace_callback(
                '#<(?:no)?script.*?<\/(?:no)?script>#is',
                function ($matches) {
                    return '%%SCRIPT' . WPSOL_HASH . '%%' . base64_encode($matches[0]) . '%%SCRIPT%%';
                },
                $this->content
            );
        }

        // Hide comments
        $this->content = $this->hideComments($this->content);
        // Hide IE hacks
        $this->content = $this->hideIEhacks($this->content);

        // Get <style> and <link>
        if (preg_match_all('#(<style[^>]*>.*</style>)|(<link[^>]*stylesheet[^>]*>)#Usmi', $this->content, $matches)) {
            foreach ($matches[0] as $tag) {
                if ($this->isMatch($tag, $this->defaultExcludeCSS)) {
                    continue;
                }

                $id_attr = '';
                if (preg_match('#id=("|\')(.*)("|\')#Usmi', $tag, $matches)) {
                    $id_attr = $matches[2];
                }
                $styles = array(
                    'id_attr' => $id_attr,
                    'tag' => $tag,
                    'type' => '', // inline, local or external, unknown
                    'url' => '', // in case of local or external, the script url
                    'path' => '', // in case of local or external, the script path
                    'content' => '', // inline script content
                    'media' => '',
                    'excluded' => false
                );

                // Get the media
                if (strpos($tag, 'media=') !== false) {
                    preg_match('#media=(?:"|\')([^>]*)(?:"|\')#Ui', $tag, $medias);
                    $medias = explode(',', $medias[1]);
                    $media = array();
                    foreach ($medias as $elem) {
                        if (empty($elem)) {
                            $elem = 'all';
                        }
                        $media[] = $elem;
                    }
                } else {
                    // No media specified - applies to all
                    $media = array('all');
                }
                $styles['media'] = $media;

                //Check exclude style with media from group css
                if ($this->group_css) {
                    if ((count($media) === 1 && !in_array('all', $media)) || (count($media) > 1)) {
                        continue;
                    }
                }

                if (preg_match('#<link.*href=("|\')(.*)("|\')#Usmi', $tag, $source)) {
                    if ($this->isMatchWildcard($source[2], $this->css_exclude)) {
                        //Exclude css by customer
                        continue;
                    }

                    if ($this->isMatch($tag, $this->grfonts)) {
                        // google font link
                        $this->css[] = array($media, 'GOOGLEFONT;'.$source[2] . '_TAGGOOGLEFONT;'.$tag);
                        $styles['url'] = $source[2];
                        $styles['path'] = $styles['url'];
                        $styles['type'] = 'google_font';
                        $this->styles[] = $styles;
                        continue;
                    }
                    // <link>
                    $url = current(explode('?', $source[2], 2));
                    $path = $this->getpath($url);
                    $styles['type'] = 'url';
                    $styles['url'] = $url;
                    $styles['path'] = $path;

                    if ($path !== false && preg_match('#\.css$#', $path)) {
                        // Good link
                        $this->css[] = array($media, $path);
                        // Remove the original style tag
                        if ($this->minifyCSS || $this->group_css) {
                            $this->content = str_replace($tag, '', $this->content);
                        }
                    } elseif (defined('SCCSS_FILE') && preg_match('#\?sccss=1#', $source[2])) {
                        // Proceed Simple Custom CSS
                        $options     = get_option('sccss_settings');
                        $raw_content = isset($options['sccss-content']) ? $options['sccss-content'] : '';
                        $content     = wp_kses($raw_content, array( '\'', '\"' ));
                        $content     = str_replace('&gt;', '>', $content);
                        $this->css[] = array($media, '', $content);
                        $styles['content'] = $content;
                        if ($this->group_css || $this->minifyCSS) {
                            $this->content = str_replace($tag, '', $this->content);
                        }
                    }
                } else {
                    // exclude inline style from minification
                    if ($this->excludeInlineStyle) {
                        continue;
                    }
                    // inline css in style tags can be wrapped
                    // Restore comment inline scripts
                    $styles['type'] = 'inline';
                    $tag = $this->restoreComments($tag);
                    if (preg_match('#<style.*>(.*)</style>#Usmi', $tag, $code)) {
                        $regex = '#^.*<!\[CDATA\[(?:\s*\*/)?(.*)(?://|/\*)\s*?\]\]>.*$#sm';
                        $code = preg_replace($regex, '$1', $code[1]);
                        // Font check
                        $font_face = array('@font-face');
                        if (empty($code) || (!$this->group_fonts && $this->isMatch($code, $font_face))) {
                            continue;
                        }
                        $this->css[] = array($media, 'INLINE;' . $code);
                        $styles['content'] = $code;
                        // Remove the original style tag
                        if ($this->minifyCSS || $this->group_css) {
                            $this->content = str_replace($tag, '', $this->content);
                        }
                    }
                }

                $this->styles[] = $styles;
            }
            return true;
        }
        // Really, no styles?
        return false;
    }

    /**
     * Joins and optimizes CSS
     *
     * @return boolean
     */
    public function minify()
    {
        foreach ($this->css as $group) {
            if (isset($group[2])) {
                list($media, $css, $content) = $group;
            } else {
                list($media, $css) = $group;
            }
            $csscode = '';
            if (preg_match('#^INLINE;#', $css)) {
                // <INLINE style>
                $css = preg_replace('#^INLINE;#', '', $css);
                $csscode = $this->fixurls(ABSPATH . '/index.php', $css);
            } elseif (preg_match('#^GOOGLEFONT;#', $css)) {
                $css_link = preg_replace('#^GOOGLEFONT;#', '', substr($css, 0, strpos($css, '_TAGGOOGLEFONT;')));
                $css_tag = preg_replace('#^_TAGGOOGLEFONT;#', '', substr($css, strpos($css, '_TAGGOOGLEFONT;')));

                if (strpos($css_link, '//') === 0) {
                    if (is_ssl()) {
                        $http = 'https:';
                    } else {
                        $http = 'http:';
                    }
                    $css_link = $http . $css_link;
                }

                if ($this->group_css || $this->group_fonts) {
                    //get css from server
                    $csscode = '@import url('.$css_link.');';
                    $this->content = str_replace($css_tag, '', $this->content);
                }
            } else {
                /**
                 * Apply filter to allow or not minifying a css url
                 *
                 * @param boolean Default check minify value
                 * @param string  Style url
                 *
                 * @return boolean|string
                 */
                $minify_this_css = apply_filters('wpsol_css_url_do_minify', true, $css);
                if (!$minify_this_css) {
                    continue;
                }
                //<link>
                if (isset($content) && $content !== null) {
                    // Proceed Simple Custom CSS
                    $csscode = $this->fixurls($css, $content);
                    $csscode = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', $csscode);
                } elseif ($css !== false && file_exists($css) && is_readable($css)) {
                    $csscode = file_get_contents($css);
                    $csscode = $this->fixurls($css, $csscode);
                    $csscode = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', $csscode);

                    if (strpos($css, 'min.css') !== false) {
                        $csscode = '/*MINIFIED;*/'.$csscode;
                    }
                }
            }

            $this->csscode[] = array($media, $csscode, $css);
        }

        // If have css code then minify/group code
        if (!empty($this->csscode)) {
            // Do grouping
            if ($this->group_css) {
                $this->doGroup();
            } elseif ($this->minifyCSS) {
                foreach ($this->csscode as $group) {
                    list($media, $csscode, $cssPath) = $group;
                    // Check defer each file
                    if ($this->defer && !$this->checkExFDeferCSS($cssPath)) {
                        $checkDefer = 1;
                    } else {
                        $checkDefer = 0;
                    }
                    $hash = md5($csscode);
                    $cacheMinify = new Cache($hash, 'css');
                    if ($cacheMinify->exists()) {
                        $cssCacheExist = $cacheMinify->retrieve();
                        $this->css_min_arr[] = array($media, $hash, $cssCacheExist, $checkDefer);
                        continue;
                    }
                    unset($cacheMinify);

                    // Minify
                    if ($this->minifyCSS && !preg_match('#^MINIFIED;#', $csscode)) {
                        $csscode = $this->doMinifyCss($csscode);
                    }
                    $this->css_min_arr[] = array($media, $hash, $csscode, $checkDefer);
                }
            } elseif ($this->group_fonts) {
                $this->doGroupFont();
            }
        }

        return true;
    }

    /**
     * Do group css code
     *
     * @return boolean|void
     */
    protected function doGroup()
    {
        // Ready minified css
        $media_group = array('all');
        $md5hash_defer = '';
        $md5hash_exF_defer = '';
        /*
         * When grouping css files, if have $this->defer_css = true and have exclude from defer css path (by customer)
         * We would then have a css file in the head loaded at first with the content
         * And one css file for defer css, this file will load after
         */
        foreach ($this->csscode as $group) {
            list($media, $csscode, $cssPath) = $group;
            // Check exclude from defer css path
            if ($this->defer && !$this->checkExFDeferCSS($cssPath)) {
                // grouping files defer, this file will load after the content
                $md5hash_defer .= $csscode;
            } else {
                // this file will load with the content
                $md5hash_exF_defer .= $csscode;
            }
        }
        // hash css code for hashed path
        $hash_defer = md5($md5hash_defer);
        $hash_exF_defer = md5($md5hash_exF_defer);

        // Check for already-minified code
        $cacheMinify_defer = new Cache($hash_defer, 'css');
        $cacheMinify_not_defer = new Cache($hash_exF_defer, 'css');

        if ($this->defer) {
            // Defer all file
            if (empty($this->exFDeferCSSList) && $cacheMinify_defer->exists() && !$cacheMinify_not_defer->exists()) {
                $cacheDeferExist = $cacheMinify_defer->retrieve();
                if (!empty($cacheDeferExist)) {
                    $checkDefer = 1;
                    $this->css_min_arr[] = array($media_group, $hash_defer, $cacheDeferExist, $checkDefer);
                }
                return true;
            } elseif (!empty($this->exFDeferCSSList) && $cacheMinify_defer->exists() && $cacheMinify_not_defer->exists()) {
                // Defer have exclude file
                $cacheDeferExist = $cacheMinify_defer->retrieve();
                $cacheNotDeferExist = $cacheMinify_not_defer->retrieve();
                if (!empty($cacheDeferExist) && !empty($cacheNotDeferExist)) {
                    $this->css_min_arr[] = array($media_group, $hash_defer, $cacheDeferExist, 1); // checkDefer = 1
                    $this->css_min_arr[] = array($media_group, $hash_exF_defer, $cacheNotDeferExist, 0); // checkDefer = 0
                    return true;
                }
            }
        } else {
            if ($cacheMinify_not_defer->exists() && !$cacheMinify_defer->exists()) { // not defer
                $cacheNotDeferExist = $cacheMinify_not_defer->retrieve();
                if (!empty($cacheNotDeferExist)) {
                    $checkDefer = 0;
                    $this->css_min_arr[] = array($media_group, $hash_exF_defer, $cacheNotDeferExist, $checkDefer);
                }
                return true;
            }
        }
        // Cache not exist
        unset($cacheMinify_defer);
        unset($cacheMinify_not_defer);

        // Minify
        $groupCodeDefer = '';
        $groupCodeNotDefer = '';

        foreach ($this->csscode as $group) {
            list($media, $csscode, $cssPath) = $group;
            // Minify
            if ($this->minifyCSS && !preg_match('#^MINIFIED;#', $csscode)) {
                $csscode = $this->doMinifyCss($csscode);
            }
            if ($this->defer && !$this->checkExFDeferCSS($cssPath)) {
                $groupCodeDefer .= '/*WPSOL_GROUP_CSS*/'. $csscode . "\n";
            } else {
                $groupCodeNotDefer .= '/*WPSOL_GROUP_CSS*/'. $csscode . "\n";
            }
        }

        if (!empty($groupCodeDefer)) {
            $this->css_min_arr[] = array($media_group, $hash_defer, $groupCodeDefer, 1);
        }
        if (!empty($groupCodeNotDefer)) {
            $this->css_min_arr[] = array($media_group, $hash_exF_defer, $groupCodeNotDefer, 0);
        }

        $this->css_min_arr = $this->manageImport($this->css_min_arr);
    }

    /**
     * Do single group font feature
     *
     * @return boolean
     */
    protected function doGroupFont()
    {
        $groupCode = '';
        foreach ($this->csscode as $group) {
            list($media, $csscode, $cssPath) = $group;
            if (preg_match('#^GOOGLEFONT;#', $cssPath)) {
                $groupCode .= '/*WPSOL_GROUP_CSS*/'. $csscode . "\n";
            }
        }

        $hash = md5($groupCode);
        $cache = new Cache($hash, 'css');
        if ($cache->exists()) {
            $cacheExists = $cache->retrieve();
            if ($this->defer) {
                $this->css_min_arr[] = array(array('all'), $hash, $cacheExists, 1);
            } else {
                $this->css_min_arr[] = array(array('all'), $hash, $cacheExists, 0);
            }

            return true;
        }

        if ($this->defer) {
            $this->css_min_arr[] = array(array('all'), $hash, $groupCode, 1);
        } else {
            $this->css_min_arr[] = array(array('all'), $hash, $groupCode, 0);
        }

        return true;
    }
    /**
     *  Caches the CSS in uncompressed, deflated and gzipped form.
     *
     * @return void
     */
    public function cache()
    {
        if (!empty($this->css_min_arr)) {
            foreach ($this->css_min_arr as $group) {
                list($media, $hash, $csscode, $checkDefer) = $group;

                $cacheMinify = new Cache($hash, 'css');
                if (!$cacheMinify->exists()) {
                    // Cache our code
                    $cacheMinify->cache($csscode, 'text/css');
                }

                $url = WPSOL_CACHE_URL . $cacheMinify->getname();

                $this->minified_url[] = array($media, $hash, $url, $checkDefer);
            }
        }
    }

    /**
     * Returns the content
     *
     * @return mixed|string
     */
    public function getcontent()
    {
        // restore (no)script
        if (strpos($this->content, '%%SCRIPT%%') !== false) {
            // set this to avoid error "Backtrack limit exhausted" in some cases
            ini_set('pcre.backtrack_limit', '-1');
            $this->content = preg_replace_callback(
                '#%%SCRIPT' . WPSOL_HASH . '%%(.*?)%%SCRIPT%%#is',
                function ($matches) {
                    return base64_decode($matches[1]);
                },
                $this->content
            );
        }


        if (!empty($this->minified_url)) {
            foreach ($this->minified_url as $group) {
                list($media, $hash, $url, $checkDefer) = $group;

                $media_ele = '';
                if (!empty($media)) {
                    $media_ele = implode(',', $media);
                }

                $cacheMinify = new Cache($hash, 'css');
                if ($cacheMinify->exists()) {
                    $csscode = $cacheMinify->retrieve();
                }

                //Add the stylesheet either deferred (import at bottom) or normal links in head
                if ($this->defer && $checkDefer) {
                    // phpcs:disable WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet -- This style for lazyload
                        $deferredCSSBlock = '<link rel="preload" href="'.$url. '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'" media="'. $media_ele .'">
<noscript><link rel="stylesheet" href="'.$url. '" media="'. $media_ele.'"></noscript>';
                    // phpcs:enable
                    $replaceTag = array('</title>', 'after');
                    $this->injectInHtml($deferredCSSBlock, $replaceTag);
                } else {
                    $replaceTag = array('<title', 'before');
                    if (strlen($csscode) > $this->cssinlinesize) {
                        $minified = '<link type="text/css" media="' . $media_ele . '" href="';
                        // phpcs:ignore WordPress.WP.EnqueuedResources -- This is writing direct style to the content
                        $minified .= $url . '" rel="stylesheet" />';
                    } elseif (strlen($csscode) > 0) {
                        $minified = '<style type="text/css" media="' . $media_ele . '">' . $csscode . '</style>';
                    }
                    if (isset($minified)) {
                        $this->injectInHtml($minified, $replaceTag);
                    }
                }
            }
        }

        // Some option on our addon
        if (class_exists('\Joomunited\WPSOLADDON\SpeedOptimization')) {
            if ($this->defer && !$this->minifyCSS && !$this->group_css) {
                // single defer css
                if (is_array($this->styles) && !empty($this->styles)) {
                    $groupInline = '';
                    foreach ($this->styles as $css) {
                        if ($this->group_fonts && $css['type'] === 'google_font') {
                            continue; // we deferred group font file
                        }
                        // check exclude form defer css
                        if ($css['type'] === 'url' || $css['type'] === 'google_font') {
                            if ($this->checkExFDeferCSS($css['url'])) {
                                continue;
                            }
                        }
                        /**
                         * WPSOL ADDON do defer inline/local css
                         *
                         * @internal
                         */
                        $replaceTag = $css['tag'];
                        if ($css['type'] === 'url' || $css['type'] === 'google_font') {
                            $css = apply_filters('wpsol_addon_do_single_defer_css', $css);
                            $this->content = str_replace($replaceTag, $css['tag'], $this->content);
                        } elseif ($css['type'] === 'inline') {
                            if (!empty($css['media'])) {
                                $media_css = implode(',', $css['media']);
                                $groupInline .= '/*wpsol_defer_inline_css*/ @media ' . $media_css  . " { \r" . $css['content'] . "\r} \n\r";
                            } else {
                                $groupInline .= '/*wpsol_defer_inline_css*/' . "\r" . $css['content'] . "\n\r";
                            }
                            // remove inline css in content
                            $this->content = str_replace($replaceTag, '', $this->content);
                        }
                    }

                    if (!empty($groupInline)) {
                        // we should group and defer inline css in file
                        $hash = md5($groupInline);
                        $cache = new Cache($hash, 'css');
                        if (!$cache->exists()) {
                            // set cache
                            $cache->cache($groupInline, 'text/css');
                        }

                        $url = WPSOL_CACHE_URL . $cache->getname();
                        $inlineCss = array(
                            'id_attr' => 'wpsol-defer-inline-css',
                            'tag' => '',
                            'type' => 'url',
                            'url' => $url,
                            'path' => '',
                            'content' => $groupInline,
                            'media' => array('all'),
                            'excluded' => false
                        );

                        $inlineCss = apply_filters('wpsol_addon_do_single_defer_css', $inlineCss);
                        if (!empty($inlineCss['tag'])) {
                            $replaceTag = array('</title>', 'after');
                            $this->injectInHtml($inlineCss['tag'], $replaceTag);
                        }
                    }
                }
            }
        }

        // Restore Comments
        $this->content = $this->restoreComments($this->content);
        // Restore IE hacks
        $this->content = $this->restoreIEhacks($this->content);

        //Return the modified stylesheet
        return $this->content;
    }


    /**
     * Fix urls to avoid breaking URLs
     *
     * @param string $file Url of file
     * @param string $code Css code
     *
     * @return mixed
     */
    public static function fixurls($file, $code)
    {
        $file = str_replace(WPSOL_ROOT_DIR, '/', $file);
        $dir = dirname($file); //Like /wp-content

        // quick fix for import-troubles in e.g. arras theme
        $code = preg_replace('#@import ("|\')(.+?)\.css("|\')#', '@import url("${2}.css")', $code);

        if (preg_match_all('#url\((?!data)(?!\#)(?!"\#)(.*)\)#Usi', $code, $matches)) {
            $replace = array();
            foreach ($matches[1] as $k => $url) {
                // Remove quotes
                $url = trim($url, " \t\n\r\0\x0B\"'");
                $noQurl = trim($url, "\"'");
                if ($url !== $noQurl) {
                    $removedQuotes = true;
                } else {
                    $removedQuotes = false;
                }
                $url = $noQurl;
                if (substr($url, 0, 1) === '/' || preg_match('#^(https?://|ftp://|data:)#i', $url)) {
                    //URL is absolute
                    continue;
                } else {
                    // relative URL
                    $str_replace = str_replace('//', '/', $dir . '/' . $url);
                    $subject = str_replace(' ', '%20', WPSOL_WP_ROOT_URL . $str_replace);
                    $newurl = preg_replace('/https?:/', '', $subject);

                    $hash = md5($url);
                    $code = str_replace($matches[0][$k], $hash, $code);

                    if (!empty($removedQuotes)) {
                        $replace[$hash] = 'url(\'' . $newurl . '\')';
                    } else {
                        $replace[$hash] = 'url(' . $newurl . ')';
                    }
                }
            }
            //Do the replacing here to avoid breaking URLs
            $code = str_replace(array_keys($replace), array_values($replace), $code);
        }
        return $code;
    }

    /**
     * Manage @import from group feature
     *
     * @param array $csscode_arr List css code
     *
     * @return mixed
     */
    public function manageImport($csscode_arr)
    {
        // Check empty css
        if (empty($csscode_arr)) {
            return false;
        }

        // Manage @imports, while is for recursive import management.
        foreach ($csscode_arr as &$group) {
            $thiscss = &$group[2];

            // Flag to trigger import reconstitution and var to hold external imports.
            $fiximports = false;
            $external_imports = '';
            // remove comments to avoid importing commented-out imports.
            $thiscss_nocomments = preg_replace('#/\*.*\*/#Us', '', $thiscss);

            while (preg_match_all('#@import +(?:url)?(?:(?:\((["\']?)(?:[^"\')]+)\1\)|(["\'])(?:[^"\']+)\2)(?:[^,;"\']+(?:,[^,;"\']+)*)?)(?:;)#mi', $thiscss_nocomments, $matches)) {
                foreach ($matches[0] as $import) {
                    $url = trim(preg_replace('#^.*((?:https?:|ftp:)?//.*\.css).*$#', '$1', trim($import)), " \t\n\r\0\x0B\"'");
                    $path = $this->getpath($url);

                    $import_ok = false;
                    if (file_exists($path) && is_readable($path)) {
                        $code = addcslashes(self::fixurls($path, file_get_contents($path)), '\\');
                        $code = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', $code);

                        if (!empty($code)) {
                            $tmp_thiscss = preg_replace('#(.*)' . preg_quote($import, '#') . '#Us', $code . '$1', $thiscss);

                            if (!empty($tmp_thiscss)) {
                                $thiscss = $tmp_thiscss;
                                $import_ok = true;
                                unset($tmp_thiscss);
                            }
                        }
                        unset($code);
                    }

                    if (!$import_ok) {
                        // External imports and general fall-back.
                        $external_imports .= $import;
                        $thiscss = str_replace($import, '', $thiscss);
                        $fiximports = true;
                    }
                }

                // and update $thiscss_nocomments before going into next iteration in while loop.
                $thiscss_nocomments = preg_replace('#/\*.*\*/#Us', '', $thiscss);
            }
            unset($thiscss_nocomments);

            // Add external imports to top of aggregated CSS.
            if ($fiximports) {
                $thiscss = $external_imports . $thiscss;
            }
        }
        unset($thiscss);

        // Return current minify css
        return $csscode_arr;
    }

    /**
     * Do minify for style string
     *
     * @param string $csscode Css string
     *
     * @return string
     */
    public function doMinifyCss($csscode)
    {
        $cssmin = new CSSMin();
        $tmp_code = trim($cssmin->run($csscode));

        if (!empty($tmp_code)) {
            $csscode = $tmp_code;
            unset($tmp_code);
        }

        return $csscode;
    }

    /**
     * Get data of external url
     *
     * @param string $url URL
     *
     * @return mixed
     */
    public function getExternalData($url)
    {
        $data = false;

        if (false !== $url && file_exists($url) && is_readable($url)) {
            $data = file_get_contents($url);

            if (!strpos($http_response_header[0], '200')) {
                return false;
            }

            if (!empty($data)) {
                return $data;
            }
        }
        // try to use CURL for URLs
        if (function_exists('curl_init')) {
            $ch = curl_init();
            $options = array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_AUTOREFERER => true,
                CURLOPT_CONNECTTIMEOUT => 120,
                CURLOPT_TIMEOUT => 120,
                CURLOPT_SSL_VERIFYPEER => false,
            );
            curl_setopt_array($ch, $options);
            $response = curl_exec($ch);

            // Then, after your curl_exec call:
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $headers = substr($response, 0, $header_size);
            $data = substr($response, $header_size);
            curl_close($ch);

            $headers = (explode("\r\n", $headers));
            if (isset($headers[0]) && !strpos($headers[0], '200')) {
                return false;
            }
        }

        return $data;
    }

    /**
     * Check exclude path from defer css
     *
     * @param string $path Path to css file
     *
     * @return boolean
     */
    public function checkExFDeferCSS($path)
    {
        if (!is_array($this->exFDeferCSSList) || empty($this->exFDeferCSSList) || strlen($path) > 2048) {
            return false;
        }
        if (!empty($path)) {
            // Check $path in exclude from defer css list
            foreach ($this->exFDeferCSSList as $exPath) {
                if (strlen(trim($exPath)) > 2048) {
                    continue; // url have max length 2048 character
                }
                // Check with wildcard
                $exPath = '*' . $exPath . '*';
                if (fnmatch($exPath, $path, 0)) {
                    return true;
                }
            }
        }

        return false;
    }
}
