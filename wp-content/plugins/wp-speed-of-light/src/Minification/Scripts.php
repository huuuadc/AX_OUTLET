<?php
namespace Joomunited\WPSOL\Minification;

use Joomunited\WPSOL\Minification\External\JSMin;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class Scripts
 */
class Scripts extends Base
{
    /**
     * Init minify javascript params
     *
     * @var boolean
     */
    private $minifyJS = false;

    /**
     * List of all script in the current page
     *
     * @var array
     */
    private $all_scripts = array();

    /**
     * Init dont move params
     *
     * @var array
     */
    private $default_exclude = array();

    /**
     * Content exclude script
     *
     * @var array
     */
    private $content_excluded = array();

    /**
     * Array URL excluded
     *
     * @var string[]
     */
    private $url_excluded = array('wpforo');

    /**
     * Init minified url params
     *
     * @var string
     */
    private $minified_url = '';
    /**
     * Init md5hash params
     *
     * @var string
     */
    private $md5hash = '';
    /**
     * Init group js params
     *
     * @var boolean
     */
    private $group_js = false;
    /**
     * Init javascript min  params
     *
     * @var array
     */
    private $js_min_arr = array();
    /**
     * Init url group params
     *
     * @var array
     */
    private $url_arr = array();
    /**
     * Init javascript exclude params
     *
     * @var array
     */
    private $js_exclude = array();
    /**
     * Init external scripts params
     *
     * @var array
     */
    private $external_scripts = array();
    /**
     * Init external local path params
     *
     * @var array
     */
    private $external_local_url = array();
    /**
     * Init js after group params
     *
     * @var string
     */
    private $js_after_group = '';
    /**
     * Init cache external params
     *
     * @var boolean
     */
    private $cache_external = false;
    /**
     * Init move to footer params
     *
     * @var boolean
     */
    private $move_to_footer = false;
    /**
     * Init all script compare params
     *
     * @var array
     */
    private $script_to_footer = array();
    /**
     * Init exclude move to footer params
     *
     * @var array
     */
    private $exclude_move_to_footer = array();
    /**
     * Defer option
     *
     * @var boolean
     */
    private $defer = false;
    /**
     * Reads the page and collects script tags
     *
     * @param array $options Option of minify js
     *
     * @return boolean
     */
    public function read($options)
    {
        //turn on minification
        $this->minifyJS = $options['minify_js'];
        // group js?
        $this->group_js = $options['group_js'];
        $this->defer = $options['defer'];
        //cache external js
        $this->cache_external = $options['cache_external'];
        //exclude inline script
        $this->move_to_footer = $options['move_to_script'];
        //exclude inline script
        $this->exclude_move_to_footer = $options['exclude_move_to_script'];
        // get extra exclusions settings or filter
        $this->js_exclude = array_merge($options['exclude_js'], $this->default_exclude);

        // Hide comments and ie hack to avoid handling content in these parts
        $this->content = $this->hideComments($this->content);
        $this->content = $this->hideIEhacks($this->content);

        //Get script files
        if (preg_match_all('#<script.*</script>#Usmi', $this->content, $matches)) {
            foreach ($matches[0] as $id => $tag) {
                // Determines whether a <script> $tag should be aggregated or not.
                if (!$this->isJavascriptType($tag)) {
                    continue;
                }

                $script = array(
                    'id' => $id,
                    'tag' => $tag, // full html
                    'type' => '', // inline, local or external, unknown
                    'url' => '', // in case of local or external, the script url
                    'path' => '', // in case of local or external, the script path
                    'content' => '', // Inline script content
                    'minify' => false, // should this script be minified
                    'group' => false, // should this script be grouped with others
                    'excluded' => false, // in case the script has been excluded by customer
                );

                // Check for scr attribute
                if (preg_match('#src=("|\')(.*)("|\')#Usmi', $tag, $source)) {
                    // Local or external script file
                    $url = current(explode('?', $source[2], 2));
                    $path = $this->getpath($url);
                    $script['url'] = $url;
                    if ($path !== false && $this->isMatchWildcard($source[2], $this->js_exclude)) {
                        //Exclude js by customer
                        if (!$this->group_js) {
                            continue;
                        }
                        $script['excluded'] = true;
                    }

                    if ($path !== false && preg_match('#\.js$#', $path)) {
                        $script['type'] = 'local';
                        $script['path'] = $path;

                        // Set url to compare for move to footer
                        $this->script_to_footer[$tag] = $url;
                    } else {
                        if (preg_match('#<script.*>(.*)</script>#Usmi', $tag)) {
                            // We should keep inline script for external
                            continue;
                        }

                        $script['type'] = 'external';
                        $script['path'] = $url;

                        //External script (example: google analytics)
                        if ($this->cache_external) {
                            $this->external_scripts[$tag] = $url;
                        }
                    }
                } else {
                    // Inline script
                    // Restore comment inline scripts
                    $tag = $this->restoreComments($tag);
                    if (preg_match('#<script.*>(.*)</script>#Usmi', $tag, $match)) {
                        $code = preg_replace('#.*<!\[CDATA\[(?:\s*\*/)?(.*)(?://|/\*)\s*?\]\]>.*#sm', '$1', $match[1]);
                        $code = preg_replace('/(?:^\\s*<!--\\s*|\\s*(?:\\/\\/)?\\s*-->\\s*$)/', '', $code);
                        if (empty($code)) {
                            continue;
                        }

                        $script['type'] = 'inline';
                        $script['content'] = $code;

                        // Set inline to compare for move to footer
                        $this->script_to_footer[$tag] =  $code;
                    }
                }

                $this->all_scripts[] = $script;
            }

            return true;
        }

        // No script files, great ;-)
        return false;
    }

    /**
     * Joins and optimizes JS
     *
     * @return boolean
     */
    public function minify()
    {
        foreach ($this->all_scripts as &$script) {
            $check_allow_minify = apply_filters('wpsol_js_url_do_minify', true, $script);
            //Check allow minify
            if (!$check_allow_minify) {
                continue;
            }

            if ($script['type'] === 'local' || $script['type'] === 'external') {
                if (file_exists($script['path']) && is_readable($script['path'])) {
                    $script['content'] = file_get_contents($script['path']);
                    $script['content'] = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', $script['content']);
                    $script['content'] = rtrim($script['content'], ";\n\t\r") . ';';
                }
            }
        }

        if ($this->group_js) {
            $this->doGroup();
        } elseif ($this->minifyJS) {
            $this->doSingle();
        }

        return true;
    }

    /**
     * Start group js
     *
     * @return boolean
     */
    public function doGroup()
    {
        $this->md5hash = md5(serialize(array_values($this->all_scripts)));
        $currentUrl = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $contentMd5 = md5($currentUrl . 'wpsol_content_after_group_script');

        //Check for already-minified code
        $cache = new Cache($this->md5hash, 'js');
        $cacheContent = new Cache($contentMd5, 'php');

        if ($cache->exists() && $cacheContent->exists()) {
            $this->content = str_replace('<?php die("Access Denied"); ?>', '', $cacheContent->retrieve());
            $this->content = str_replace('</body>', '<!-- Cache content after group script by WPSOL --></body>', $this->content);
            $this->js_after_group = $cache->retrieve();
            return true;
        }
        unset($cache);

        $groupCode = '';
        foreach ($this->all_scripts as $script) {
            if ($script['content'] !== '' && ($script['type'] !== 'inline')) {
                foreach ($this->url_excluded as $url_exclusion) {
                    if (preg_match('/' . str_replace('/', '\\/', $url_exclusion) . '/', $script['url'])) {
                        // This script contains url which can't be grouped
                        $tag = preg_replace('#^(<script.*type=["\'])(.*)(["\'].*>)#Usmi', '$1wpsol' . $script['id'] . '-$2$3', $script['tag'], 1, $count);
                        if (!$count) {
                            // no type was present in tag
                            $tag = preg_replace('#^<script(.*?)>#Usmi', '<script$1 type="wpsol' . $script['id'] . '-text/javascript>', $script['tag'], 1, $count);
                        }

                        if (!$count) {
                            // We failed to replace type
                            continue 2;
                        }

                        // fixme: should not be replaced here
                        $this->content = str_replace($script['tag'], $tag . '<script>window.addEventListener("wpsolr", function(){wpsolr(' . $script['id'] . ', "excluded");});</script>', $this->content);

                        continue 2;
                    }
                }

                foreach ($this->content_excluded as $exclusion) {
                    if (preg_match('/' . str_replace('/', '\\/', $exclusion) . '/', $script['content'])) {
                        // This script contains content which can't be grouped
                        $tag = preg_replace('#^(<script.*type=["\'])(.*)(["\'].*>)#Usmi', '$1wpsol' . $script['id'] . '-$2$3', $script['tag'], 1, $count);
                        if (!$count) {
                            // no type was present in tag
                            $tag = preg_replace('#^<script(.*?)>#Usmi', '<script$1 type="wpsol' . $script['id'] . '-text/javascript>', $script['tag'], 1, $count);
                        }

                        if (!$count) {
                            // We failed to replace type
                            continue 2;
                        }

                        // fixme: should not be replaced here
                        $this->content = str_replace($script['tag'], $tag . '<script>window.addEventListener("wpsolr", function(){wpsolr(' . $script['id'] . ', "excluded");});</script>', $this->content);

                        continue 2;
                    }
                }
            }

            // Replace in content the script by our event call
            if ($script['type'] === 'inline') {
                $script_type = 'inline';
                $script_url = 'null';
            } elseif ($script['excluded'] === true) {
                $script_type = 'injected';
                $script_url = '"' . $script['url'] . '"';
            } else {
                $script_type = 'url';
                $script_url = '"' . $script['url'] . '"';
            };
            $this->content = str_replace($script['tag'], '<script id="wpsolr-' . $script['id'] . '">window.addEventListener("wpsolr", function(){wpsolr(' . $script['id'] . ', "' . $script_type . '", ' . $script_url . ')});</script>', $this->content);

            if ($script['excluded'] === true) {
                continue;
            }

            // Remove Strict Mode to fix error js when group
            $code = str_replace('use strict', '', $script['content']);

            $fullcode = '/****************************** WPSOL ' . $script['id'] . "******************************/\r\n";
            if ($script['url']) {
                $fullcode = '/*************** ' . $script['url'] . " ***************/\r\n";
            }
            $fullcode .= 'var wpsolr' . $script['id'] . "=function(_wpsol0){\r\n";
            if ($script_type === 'url' && strpos($code, 'webpack') !== false) {
                $fullcode .= 'if(_wpsol0!==undefined && _wpsol0){wpsolw(_wpsol0, 1)};';
            }
            $fullcode .= $code . "\r\n";
            preg_match_all('/(var|let|function)\s+([a-zA-Z_$][^;\/]*)[;]*/mx', $code, $variables); // find all variables declaration location
            if (is_array($variables) && !empty($variables[2])) {
                $tmp_variables = array();
                foreach ($variables[2] as $variable) { // a = 1, b, c = 3
                    $variable = explode(',', $variable);
                    foreach ($variable as $v) { // a = 1
                        $v = explode('=', $v);
                        $v = trim($v[0]); // $v = a
                        // check variable name
                        if (preg_match('/^[a-zA-Z_$][a-zA-Z_$0-9]*$/', $v)) {
                            $tmp_variables[] = $v;
                        }
                    }
                }
                // Remove duplicates
                $variables = array_unique($tmp_variables);

                // Remove declaration or instructions
                $variables = array_diff($variables, array('abstract','arguments','await*','boolean','break','byte','case','catch','char','class*','const','continue','debugger','default','delete','do','double','else','enum*','eval','export*','extends*','false','final','finally','float','for','function','goto','if','implements','import*','in','instanceof','int','interface','let*','long','native','new','null','package','private','protected','public','return','short','static','super*','switch','synchronized','this','throw','throws','transient','true','try','typeof','var','void','volatile','while','with','yield','abstract','boolean','byte','char','double','final','float','goto','int','long','native','short','synchronized','throws','transient','volatile','Array','Date','eval','function','hasOwnProperty','Infinity','isFinite','isNaN','isPrototypeOf','length','Math','NaN','name','Number','Object','prototype','String','toString','undefined','valueOf','alert','all','anchor','anchors','area','assign','blur','button','checkbox','clearInterval','clearTimeout','clientInformation','close','closed','confirm','constructor','crypto','decodeURI','decodeURIComponent','defaultStatus','document','element','elements','embed','embeds','encodeURI','encodeURIComponent','escape','event','fileUpload','focus','form','forms','frame','innerHeight','innerWidth','layer','layers','link','location','mimeTypes','navigate','navigator','frames','frameRate','hidden','history','image','images','offscreenBuffering','open','opener','option','outerHeight','outerWidth','packages','pageXOffset','pageYOffset','parent','parseFloat','parseInt','password','pkcs11','plugin','prompt','propertyIsEnum','radio','reset','screenX','screenY','scroll','secure','select','self','setInterval','setTimeout','status','submit','taint','text','textarea','top','unescape','untaint','window','onblur','onclick','onerror','onfocus','onkeydown','onkeypress','onkeyup','onmouseover','onload','onmouseup','onmousedown','onsubmit'));

                $vars_code = 'var wpsolv = [';
                foreach ($variables as $variable_name) {
                    $vars_code .= '"' . $variable_name . '",';
                }
                $vars_code .= '];';
                $vars_code .= "for(var wpsolp in wpsolv){eval('if(typeof '+wpsolv[wpsolp]+'!==\"undefined\"){window[\"'+wpsolv[wpsolp]+'\"]='+wpsolv[wpsolp]+';}');}\r\n";
                $fullcode .= $vars_code;
            }
            $fullcode .= '};';

            $groupCode .=  $fullcode . "\r\n";
        }

        // Content after replaced for group js
        if (!$cacheContent->exists()) {
            $cacheContent->cache('<?php die("Access Denied"); ?>' . $this->content, 'text/php');
        }

        if ($this->minifyJS) {
            $groupCode = $this->doMinify($groupCode);
        }

        //Unset use strict mode with group file
        $this->js_after_group = "var wpsol_unset_use_strict = 1;\r\n";
        $this->js_after_group .= $groupCode . "\r\n";
        $runner_code = file_get_contents(WPSOL_PLUGIN_DIR . 'assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'script-runner.js');
        if ($this->minifyJS) {
            $runner_code = $this->doMinify($runner_code) . "\r\n";
        }
        $this->js_after_group .= $runner_code;
        return true;
    }

    /**
     * Do minify for single script.
     *
     * @return void
     */
    public function doSingle()
    {
        foreach ($this->all_scripts as $script) {
            // Check if file minified
            if (strpos($script['path'], '.min.js') !== false) {
                continue;
            }
            $hash = md5($script['content']);
            //Check for already-minified code
            $cacheMinify = new Cache($hash, 'js');
            if ($cacheMinify->exists()) {
                $jscodeExist = $cacheMinify->retrieve();
                $this->js_min_arr[] = array($hash, $script['tag'], $jscodeExist);
                continue;
            }
            unset($cacheMinify);

            $jscode = $this->doMinify($script['content']);
            $this->js_min_arr[] = array($hash, $script['tag'], $jscode);
        }
    }

    /**
     * Caches the JS in uncompressed, deflated and gzipped form.
     *
     * @return void
     */
    public function cache()
    {
        if ($this->group_js) {
            if (!empty($this->js_after_group)) {
                $cache = new Cache($this->md5hash, 'js');
                if (!$cache->exists()) {
                    //Cache our code
                    $cache->cache($this->js_after_group, 'text/javascript');
                }

                $this->minified_url = WPSOL_CACHE_URL . $cache->getname();
            }
        } else {
            if (!empty($this->js_min_arr)) {
                foreach ($this->js_min_arr as $group) {
                    list($hash, $tag, $js_minified) = $group;

                    $cacheMinify = new Cache($hash, 'js');
                    if (!$cacheMinify->exists()) {
                        //Cache our code
                        $cacheMinify->cache($js_minified, 'text/javascript');
                    }
                    $url = WPSOL_CACHE_URL . $cacheMinify->getname();

                    $this->url_arr[] = array($hash, $tag, $url);
                }
            }
        }

        if ($this->cache_external) {
            // Cache external script
            if (!empty($this->external_scripts)) {
                foreach ($this->external_scripts as $tag => $url) {
                    $script = $this->getExternalData($url);
                    if (empty($script) || strpos($script, 'get content remotely failed') !== false) {
                        continue;
                    }
                    $hash = md5($script);

                    $cacheMinify = new Cache($hash, 'js');
                    if (!$cacheMinify->exists()) {
                        //Cache external code
                        $cacheMinify->cache($script, 'text/javascript');
                    }

                    $cache_external_url = WPSOL_CACHE_URL . $cacheMinify->getname();

                    $this->external_local_url[] = array($tag, $cache_external_url);
                }
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
        // Add the scripts taking forcehead/ deferred (default) into account
        $defer = '';
        if ($this->move_to_footer) {
            $replaceTag = array('</body>', 'before');
        } else {
            $replaceTag = array('</head>', 'before');
        }

        if ($this->defer) {
            $defer = ' defer ';
        }

        if ($this->group_js) {
            $minified = '<script type="text/javascript" ' . $defer ;
            $minified .= 'src="' . $this->minified_url . '"></script>';
            $this->injectInHtml($minified, $replaceTag);
        } elseif ($this->minifyJS) {
            $groupInline = '';
            if ($this->move_to_footer) {
                foreach ($this->url_arr as $group) {
                    list($hash, $tag, $url) = $group;

                    if (array_key_exists($tag, $this->script_to_footer)) {
                        $this->script_to_footer[$tag] = $hash.'_wpsolmtf_'.$url;
                    }
                }

                // injected flag
                $this->injectInHtml('<script type="text/javascript" id="wpsol-inline-script">/* inline script should be replace here */</script>', $replaceTag);
                foreach ($this->script_to_footer as $tag => $script) {
                    if (strpos($script, '_wpsolmtf_') !== false) {
                        $hash = substr($script, 0, strpos($script, '_wpsolmtf_'));
                        $url = substr($script, strpos($script, '_wpsolmtf_') + strlen('_wpsolmtf_'));

                        if (preg_match('#src=("|\')(.*)("|\')#Usmi', $tag, $source)) {
                            $script_to_footer = '<script type="text/javascript" '.$defer . 'src="' . $url . '"></script>';
                        } else {
                            $inline_script = '';
                            $cacheMinify = new Cache($hash, 'js');
                            if ($cacheMinify->exists()) {
                                $inline_script = $cacheMinify->retrieve();
                            }

                            if (strlen($inline_script) > 0) {
                                if ($this->defer) {
                                    // defer inline script
                                    $groupInline .= $inline_script . "\r\n" . '/***********\/inline script***********/' . "\r\n";
                                    $script_to_footer = '';
                                } else {
                                    $script_to_footer = '<script type="text/javascript">'.$inline_script.'</script>';
                                }
                            } else {
                                $script_to_footer = '<script type="text/javascript" '. $defer.'src="'.$url.'"></script>';
                            }
                        }
                    } else {
                        if (preg_match('#src=("|\')(.*)("|\')#Usmi', $tag, $source)) {
                            $script_to_footer = '<script type="text/javascript" '.$defer . 'src="' . $script . '"></script>';
                        } else {
                            // inline script
                            if ($this->defer) {
                                $groupInline .= $script . "\r\n" . '/***********\/inline script***********/' . "\r\n";
                                $script_to_footer = '';
                            } else {
                                $script_to_footer = '<script type="text/javascript">'.$script.'</script>';
                            }
                        }
                    }
                    // Exclude script from "move to footer"
                    if ($this->isMatch($tag, $this->exclude_move_to_footer)) {
                        $this->content = str_replace($tag, $script_to_footer, $this->content);
                    } else {
                        // Remove old script
                        $this->content = str_replace($tag, '', $this->content);
                        // Inject script to footer
                        if (!empty($script_to_footer)) {
                            $this->injectInHtml($script_to_footer, $replaceTag);
                        }
                    }
                }
                // inject inline script
                if (!empty($groupInline)) {
                    /**
                     * WPSOL defer inline script
                     */
                    $replaced = apply_filters('wpsol_addon_do_defer_inline_script', $groupInline);
                    if (!$replaced) {
                        $replaced = ''; // empty to remove injected flag
                    }
                    $this->content = str_replace('<script type="text/javascript" id="wpsol-inline-script">/* inline script should be replace here */</script>', $replaced, $this->content);
                }
            } else {
                foreach ($this->url_arr as $group) {
                    list($hash, $tag, $url) = $group;

                    if (preg_match('#src=("|\')(.*)("|\')#Usmi', $tag, $source)) {
                        $script = '<script type="text/javascript" ' . $defer . 'src="' . $url . '"></script>';
                    } else {
                        $inline_script = '';

                        $cacheMinify = new Cache($hash, 'js');
                        if ($cacheMinify->exists()) {
                            $inline_script = $cacheMinify->retrieve();
                        }
                        if (strlen($inline_script) > 0) {
                            if ($this->defer) {
                                // defer inline script in file
                                $groupInline .= $inline_script . "\r\n" . '/***********\/inline script***********/' . "\r\n";
                                $script = ''; // remove inline script
                            } else {
                                $script = '<script type="text/javascript">'.$inline_script.'</script>';
                            }
                        } else {
                            $script = '<script type="text/javascript" ' . $defer . 'src="' . $url . '"></script>';
                        }
                    }
                    $this->content = str_replace($tag, $script, $this->content);
                }
                if (!empty($groupInline)) {
                    /**
                     * WPSOL defer inline script
                     */
                    $replaced = apply_filters('wpsol_addon_do_defer_inline_script', $groupInline);
                    if ($replaced) {
                        $this->injectInHtml($replaced, array('</title>', 'after'));
                    }
                }
            }
        } else {
            // Optimization without group and minification
            // Some option in our addon
            if (class_exists('\Joomunited\WPSOLADDON\SpeedOptimization')) {
                if ($this->defer) {
                    /**
                     * WPSOL ADDON do defer js without grouping and minification
                     *
                     * @internal
                     */
                    $singleDeferJs = apply_filters('wpsol_addon_do_single_defer_js', $this->all_scripts);
                    if ($singleDeferJs) {
                        // Defer js & move script to footer
                        if ($this->move_to_footer) {
                            // inject inline script first
                            $this->injectInHtml($singleDeferJs['wpsol_defer_inline_script_tag'], $replaceTag);
                            foreach ($this->all_scripts as $script) {
                                if ($script['type'] === 'inline') {
                                    // we will group and defer inline script in file
                                    $this->content = str_replace($script['tag'], '', $this->content); // remove inline script
                                    continue;
                                }
                                if (isset($singleDeferJs[$script['tag']]) && !empty($singleDeferJs[$script['tag']])) {
                                    if ($this->isMatch($script['tag'], $this->exclude_move_to_footer)) {
                                        // Replace by defer tag
                                        $this->content = str_replace($script['tag'], $singleDeferJs[$script['tag']], $this->content);
                                    } else {
                                        // Remove old script
                                        $this->content = str_replace($script['tag'], '', $this->content);
                                        // Inject script to footer
                                        $this->injectInHtml($singleDeferJs[$script['tag']], $replaceTag);
                                    }
                                }
                            }
                        } else {
                            // Single defer js
                            // inject inline script first, after title tag
                            $this->injectInHtml($singleDeferJs['wpsol_defer_inline_script_tag'], array('</title>', 'after'));
                            foreach ($this->all_scripts as $script) {
                                if ($script['type'] === 'inline') {
                                    // we will group and defer inline script in file
                                    $this->content = str_replace($script['tag'], '', $this->content); // remove inline script
                                    continue;
                                }
                                if (isset($singleDeferJs[$script['tag']]) && !empty($singleDeferJs[$script['tag']])) {
                                    $this->content = str_replace($script['tag'], $singleDeferJs[$script['tag']], $this->content);
                                }
                            }
                        }
                    }
                } elseif ($this->move_to_footer) {
                    // Only Move scripts to footer option is active
                    foreach ($this->all_scripts as $script) {
                        if (!$this->isMatch($script['tag'], $this->exclude_move_to_footer)) {
                            // Remove old script
                            $this->content = str_replace($script['tag'], '', $this->content);
                            // Inject script to footer
                            $this->injectInHtml($script['tag'], $replaceTag);
                        }
                    }
                }
            }
        }

        //Inject External script
        if (!empty($this->external_local_url)) {
            foreach ($this->external_local_url as $group) {
                list($tag, $url) = $group;

                $script = '<script type="text/javascript" ' . $defer . 'src="' . $url . '"></script>';

                $this->content = str_replace($tag, $script, $this->content);
            }
        }


        // Restore Comments
        $this->content = $this->restoreComments($this->content);
        // Restore IE hacks
        $this->content = $this->restoreIEhacks($this->content);

        // Return the modified HTML
        return $this->content;
    }


    /**
     * Determines whether a <script> $tag should be considered as javascript or not.
     *
     * We consider these as "aggregation-safe" currently:
     * - script tags without a `type` attribute
     * - script tags with an explicit `type` of `text/javascript`, 'text/ecmascript',
     *   'application/javascript' or 'application/ecmascript'
     * Everything else should return false.
     *
     * @param string $tag Tag to aggregate
     *
     * @return boolean
     *
     * original function by https://github.com/zytzagoo/ on his AO fork, thanks Tomas!
     */
    public function isJavascriptType($tag)
    {
        preg_match('#<(script[^>]*)>#i', $tag, $scripttag);
        if (strpos($scripttag[1], 'type=') === false) {
            // No defined type, it's javascript
            return true;
        } elseif (preg_match('/type=["\']?(?:text|application)\/(?:javascript|ecmascript)["\']?/i', $scripttag[1])) {
            // type is set and is javascript compatible
            return true;
        } else {
            // This type is not javascript
            return false;
        }
    }

    /**
     * Execute script to minify
     *
     * @param string $jscode Script string
     *
     * @return string
     */
    public function doMinify($jscode)
    {
        $tmp_jscode = trim(JSMin::minify($jscode));

        if (!empty($tmp_jscode)) {
            $jscode = $tmp_jscode;
            unset($tmp_jscode);
        }

        return $jscode;
    }

    /**
     * Get content of external script
     *
     * @param string $url External url
     *
     * @return mixed
     */
    public function getExternalData($url)
    {
        if (strpos($url, '//') === 0) {
            if (is_ssl()) {
                $http = 'https:';
            } else {
                $http = 'http:';
            }
            $url = $http . $url;
        }

        $data = '';
        $args = array(
            'httpversion' => '1.1',
        );
        //Start preload
        $response = wp_remote_get($url, $args);
        if (is_wp_error($response)) {
            $data = 'WPSOL_MINIFICATION; get content remotely failed!';
        }
        if (is_array($response)) {
            $data = $response['body'];
        }

        return $data;
    }
}
