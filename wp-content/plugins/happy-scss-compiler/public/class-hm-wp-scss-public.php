<?php

require_once plugin_dir_path( __DIR__ ) . 'includes/scssphp/scss.inc.php';
use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\Formatter;
use ScssPhp\ScssPhp\SourceMap\SourceMapGenerator;

class Hm_Wp_Scss_Public {

    private $error;
    private $errorTitle;
    private $errorString;

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->error = false;
        $this->errorTitle = 'WP SCSS Compiling Error';
        $this->errorString = " -- FILE: ";
        if(!function_exists('wp_get_current_user'))
        {
            // Prevents errors with Plugabble.php constants
            if ( !defined('AUTH_COOKIE') ) define('AUTH_COOKIE', "C1");
            if ( !defined('SECURE_AUTH_COOKIE') ) define('SECURE_AUTH_COOKIE', "C2");
            if ( !defined('LOGGED_IN_COOKIE') ) define('LOGGED_IN_COOKIE', "C3");

            require_once(ABSPATH . "wp-includes/pluggable.php");
        }

        // Launch compilation
        if(
            !get_option('hm_wp_scss__compilation_time') ||
            get_option('hm_wp_scss__compilation_time') == 'Always' ||
            ( get_option('hm_wp_scss__compilation_time') == 'When SCSS has changed' && $this->scssHasChanged() ) ||
            ( get_option('hm_wp_scss__compilation_time') == 'If Logged In' && is_user_logged_in() ) ||
            ( get_option('hm_wp_scss__compilation_time') == 'If Admin Logged In' && current_user_can( 'edit_pages' ) )
        )
        {
            $this->compile();
        }

    }

    function admin_notice__error() {
        if( get_option('hm_wp_scss__errors_display') != 'Never, even in Back Office' ):
            $class = 'notice notice-error';
            printf( '<div class="%1$s"><p><strong>%2$s</strong>%3$s</p></div>', esc_attr( $class ), esc_html( $this->errorTitle ), esc_html( $this->error ) );
        endif;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Hm_Wp_Scss_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Hm_Wp_Scss_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        //wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/hm-wp-scss-public.css', array(), $this->version, 'all' );

        // Automatic Enqueue stylesheets
        if( get_option('hm_wp_scss__enqueuing') == '1' )
        {
	        $paths = $this->getAllPaths();
			if( is_array($paths) ):

				$i=0;
				foreach( $paths as $path_css ):

					$path_css = $path_css[1];

			        if( is_dir( $path_css ) )
			        {
						$url_css = $this->getFileUrlFromPath($path_css);
				        foreach( scandir( $path_css ) as $file )
				        {

					        if( $this->getFileExtension($file) == 'css' )
					        {
						        $i++;
						        wp_enqueue_style( $this->getFilename($file, 'css') . "-$i", $url_css . $file, array(), filemtime($path_css.$file), 'all' );
					        }
				        }
			        }

		        endforeach;

			endif;
        }
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Hm_Wp_Scss_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Hm_Wp_Scss_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        //wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/hm-wp-scss-public.js', array( 'jquery' ), $this->version, false );

    }

    private function compile()
    {
        if( is_array( $r = $this->getAllPaths() ) && count($r) ):
            
            foreach( $r as $paths_couple ):
                
                // Variables
                $path_scss 	= $paths_couple[0];
                $path_css	= $paths_couple[1];
    
                // Class initialization
                $compiler = new Compiler();
                $formatter = get_option('hm_wp_scss__compilation_mode');
                if( $formatter == "" )
                    $formatter = 'Compressed';
                $formatterClass = "ScssPhp\ScssPhp\Formatter\\$formatter";
                $compiler->setFormatter(new $formatterClass());
    
                $filesScssList = $this->findAllFiles( $path_scss );
                
    
                //$this->v($filesScssList, '$filesScssList');
    
                if( get_option('hm_wp_scss__compilation_time') == 'When SCSS has changed' ):
    
                    // Arrays creation
                    $filesLinked = [];
                    $filesIncluded = [];
                    $filesToCompile = [];
                    foreach( $filesScssList as $filePath )
                    {
                        if( $this->getFileExtension( $filePath ) == 'scss' ):
    
                            preg_match_all('%@import.*[\'"]{1}([^://;]+)[\'"]{1}.*;%', file_get_contents( $filePath ), $matches);
                            
                            $filesLinked[$filePath] = [
                                'includes'  => $matches[1],
                                'changed'   => $this->fileHasChanged($filePath, $this->getCssPathFromScssPath($filePath, $path_scss, $path_css)),
                            ];
                            if( count($matches[1]) )
                            {
                                foreach( $matches[1] as $pathIncluded )
                                {
                                    $filesIncluded[$pathIncluded][] = $filePath;
                                }
                            }
    
                        endif;
                    }
    
                    //$this->v($filesLinked, '$filesLinked');
    
                    foreach( $filesLinked as $file_Path => $fileData )
                    {
                        if( $fileData['changed'] === true )
                        {
                            $filesToCompile[] = $file_Path;
    
                            // Included files detection
                            foreach( $filesIncluded as $fileIncluded => $filesWhichIncludePath )
                            {
                                // Directly included files
                                if( $fileIncluded == $this->getFilename($file_Path, 'scss') )
                                {
                                    foreach( $filesWhichIncludePath as $fileIncludedPath )
                                    {
                                        $filesToCompile[] = $fileIncludedPath;
                                        // Indirectly included files
                                        foreach( $filesIncluded as $fileIncludedIndirectly => $filesWhichIncludePathIndirectly )
                                        {
                                            if( $this->getFilename($fileIncludedPath, 'scss') == $fileIncludedIndirectly )
                                            {
                                                foreach( $filesWhichIncludePathIndirectly as $fileWhichIncludePathIndirectly )
                                                {
                                                    $filesToCompile[] = $fileWhichIncludePathIndirectly;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $filesToCompile = array_unique($filesToCompile);
    
                    //$this->v($filesLinked, '$filesLinked');
                    //$this->v($filesIncluded, '$filesIncluded');
    
                else:
                    $filesToCompile = $filesScssList;
    
                endif;
    
                //$this->v($filesToCompile, '$filesToCompile');
    
                // Course
                if( is_array($filesToCompile) ):

                    foreach( $filesToCompile as $file_path )
                    {
                        $ext = $this->getFileExtension( $file_path );             //      scss
                        $filename = $this->getFilename( $file_path, $ext );       //      c_footer

                        if( $ext == 'scss' )
                        {
                            if(
                                get_option('hm_wp_scss__no_compilation_underscore') != '1' ||
                                ( get_option('hm_wp_scss__no_compilation_underscore') == '1' && substr($filename, 0, 1) != '_' )
                            ):

                                // CSS Folders creation recursively
                                $fileFolders = $this->getFilePath($file_path);     //       /root/to/theme/assets/scss
                                $fileFoldersCss = str_replace(substr($path_scss, 0, -1), substr($path_css, 0, -1), $fileFolders );  //      /root/to/theme/assets/css
                                if( !is_dir($fileFoldersCss) )
                                    mkdir( $fileFoldersCss, 0755, true );

                                // CSS file creation
                                $fileCssPath = $this->getCssPathFromScssPath($file_path, $path_scss, $path_css);    //      /root/to/theme/assets/css/c_footer.css

                                // Source Map File Option
                                if( get_option('hm_wp_scss__source_map_file') == '1' )
                                {
                                    $fileMapPath = $fileCssPath . '.map';
                                    $compiler->setSourceMap(Compiler::SOURCE_MAP_FILE);
                                    $compiler->setSourceMapOptions([
                                        'sourceMapWriteTo' => $fileMapPath,
                                        'sourceMapFilename' => $fileCssPath,
                                        'sourceMapBasepath' => ABSPATH,
                                        'sourceRoot' => '/',
                                    ]);
                                }

                                // Saving in file
                                try
                                {
                                    $compiler->setImportPaths(dirname($file_path));
                                    $result = $compiler->compile(file_get_contents($file_path));
                                    file_put_contents($fileCssPath, $result);
                                }
                                catch( Exception $e )
                                {
                                    $this->error = $this->errorString . $filename . ' -- ERROR: ' . $e->getMessage();
                                    add_action( 'admin_notices', [$this,'admin_notice__error'] );

                                    // Admin Error
                                    if(
                                        get_option('hm_wp_scss__errors_display') == 'Always' ||
                                        ( get_option('hm_wp_scss__errors_display') == 'If Debug Mode of Wordpress is true' && WP_DEBUG ) ||
                                        ( get_option('hm_wp_scss__errors_display') ==  'If Logged In' && is_user_logged_in() ) ||
                                        ( get_option('hm_wp_scss__errors_display') == 'If Admin Logged In' && current_user_can( 'edit_pages' ) )
                                    ) {
                                        $errorTitle = $this->errorTitle;
                                        $error = $this->error;

                                        add_action( 'wp_head', function () use ( $errorTitle, $error ) {
                                            echo '<style type="text/css">'.
                                                'body::before {
                                              font-family: Helvetica, monospace;
                                              font-size: 14px;
                                              white-space: pre;
                                              display: block;
                                              padding: 1em;
                                              margin-bottom: 1em;
                                              border-bottom: 2px solid black;
                                              font-weight: bold;
                                              content: \'' . esc_html($errorTitle) . esc_html($error) . '\'; }' .
                                                '</style>';
                                        });
                                    }
                                }

                            endif;
                        }
                    }
                endif;

            endforeach;
            
        endif;
    }
    private function getAllPaths()
    {
        // Will contains all paths couples
        $paths = [];
        
        // Simple paths
        if(
            ( $option_scss = get_option('hm_wp_scss__scss_location') ) && $option_scss != ""
            && 
            ( $option_css = get_option('hm_wp_scss__css_location') ) && $option_css != ""
        ){
            if(
                is_dir( $simple_path_scss = get_stylesheet_directory() . $option_scss ) &&
                is_dir( $simple_path_css = get_stylesheet_directory() . $option_css )
            ){
                $paths[] = [$simple_path_scss, $simple_path_css];
            }
        }
        // Advanced paths
        if(
            ( $option_adv_scss = get_option('hm_wp_scss__adv_path_scss') ) && $option_adv_scss != ""
            && 
            ( $option_adv_css = get_option('hm_wp_scss__adv_path_css') ) && $option_adv_css != ""
        ){
            $option_scss_paths  = explode("\n", trim( $option_adv_scss ));
            $option_css_paths   = explode("\n", trim( $option_adv_css ));

            for( $i=0; $i<count($option_scss_paths); $i++ )
            {
                if(
                    is_dir( $adv_path_scss = substr(ABSPATH, 0, -1) . trim( $option_scss_paths[$i] ) ) &&
                    is_dir( $adv_path_css = substr(ABSPATH, 0, -1) . trim( $option_css_paths[$i] ) )
                ){
                    $paths[] = [$adv_path_scss, $adv_path_css];
                }
            }
        }
        return $paths;
    }
    private function scssHasChanged()
    {
        if( is_array( $r = $this->getAllPaths() ) && count($r) ):
            
            foreach( $r as $paths_couple ):
                // Variables
                $path_scss 	= $paths_couple[0];
                $path_css	= $paths_couple[1];
                
                if( is_dir( $path_scss ) && is_dir( $path_css ) )
                {
                    foreach( scandir( $path_scss ) as $file )
                    {
                        $ext = $this->getFileExtension($file);
        
                        if( $ext == 'scss' )
                        {
                            $filename = basename( $file, $ext );
                            $fileCssPath = $path_css . $filename . 'css';
        
                            if( !file_exists($path_css . $filename . 'css') )
                                return true;
                            elseif( filemtime($path_scss . $filename . 'scss') > filemtime($path_css . $filename . 'css' ) )
                                return true;
                        }
                    }
                }
            endforeach;
            
        endif;
        return false;
    }
    private function fileHasChanged($_fileScss, $_fileCss)
    {
        if( !file_exists($_fileCss) )
            return true;
        elseif( filemtime($_fileScss) > filemtime($_fileCss) )
            return true;
        return false;
    }
    private function getCssPathFromScssPath( $_scssPath, $_scssFolderPath, $_cssFolderPath )
    {
        return preg_replace('%\.scss$%', '.css', str_replace($_scssFolderPath, $_cssFolderPath, $_scssPath) );
    }
    private function findAllFiles($dir)
    {
        $root = scandir($dir);
        foreach($root as $value)
        {
            if($value === '.' || $value === '..') {continue;}
            if( is_file("$dir/$value") )
            {
                $result[] = str_replace('//', '/', "$dir/$value");
                continue;
            }

            if( is_array( $folders = $this->findAllFiles("$dir/$value") ) && count( $folders ) )
            {
                foreach($folders as $value)
                {
                    $result[]= $value;
                }
            }
        }
        return $result;
    }
    private function getFileExtension( $_filePath )
    {
        $extension = pathinfo($_filePath, PATHINFO_EXTENSION);
        return $extension;
    }
    private function getFilePath( $_filePath )
    {
        $path_parts = pathinfo( $_filePath );
        return $path_parts['dirname'];
    }
	private function getFileUrlFromPath( $_filePath )
	{
		$paths = explode('wp-content/', $_filePath);
		return '/wp-content/' . $paths[1];
	}
    private function getFilename( $_filePath, $_ext )
    {
        return substr(basename( $_filePath, $_ext ), 0, -1);
    }
    private function v($_v, $_t=null)
    {
        if($_t)
            echo "$_t<br />____________<br />";
        echo "<pre>";
        var_dump($_v);
        echo "</pre>";
    }

}
