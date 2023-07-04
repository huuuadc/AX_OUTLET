<?php

class Hm_Wp_Scss_Admin {

    private $plugin_name;
    private $version;

    /*
     * For easier overriding we declared the keys
     * here as well as our tabs array which is populated
     * when registering settings
     */
    private $settings_tab1_key = 'compilation';
    private $settings_tab2_key = 'import_export';
    private $settings_tab3_key = 'advanced_paths';
    private $settings_tab4_key = 'smart_compiler';
    private $title_icon = '<span class="dashicons dashicons-saved"></span>';
    private $tabs = [];

    /*
     * Fired during plugins_loaded (very very early),
     * so don't miss-use this, only actions and filters,
     * current ones speak for themselves.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_action( 'admin_init', array( &$this, 'register_tab1' ) );
        add_action( 'admin_init', array( &$this, 'register_tab3' ) );
        add_action( 'admin_init', array( &$this, 'register_tab4' ) );
        add_action( 'admin_init', array( &$this, 'register_tab2' ) );

        // Admin menu
        add_action( 'admin_menu', [$this,'add_admin_menus'] );

        // Advanced custom paths
        add_action( 'admin_post_advancedpaths', [$this,'advanced_paths'] );
        // Download settings
        add_action( 'admin_post_dljson', [$this,'dl_settings'] );
        // Smart compiler
        add_action( 'admin_post_smartcompiler', [$this,'smart_compiler'] );
        // Import settings
        add_action( 'admin_post_importsettings', [$this,'import_settings'] );

        // Check set folders
        if( !$this->scssFolderExists() )
            add_action( 'admin_notices', [$this,'admin_notice__error_scss'] );
        if( !$this->cssFolderExists() )
            add_action( 'admin_notices', [$this,'admin_notice__error_css'] );
        if( isset( $_GET['import'] ) )
        {
          switch( $_GET['import'] )
          {
            case 'yes':
              add_action( 'admin_notices', [$this,'admin_notice__import_ok'] );
              break;
            case 'no':
              add_action( 'admin_notices', [$this,'admin_notice__import_no'] );
              break;
            case 'ext':
              add_action( 'admin_notices', [$this,'admin_notice__import_ext'] );
              break;
          }
        }
    }

    /**
     * Register the stylesheets for the admin area.
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

        wp_enqueue_style( $this->plugin_name.'-highlight', plugin_dir_url( __FILE__ ) . '../includes/highlight/styles/ir-black.min.css', array(), $this->version, 'all' );
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/hm-wp-scss-admin.css', array(), $this->version, 'all' );

    }

    /**
     * Register the JavaScript for the admin area.
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

        wp_enqueue_script( $this->plugin_name.'-highlight', plugin_dir_url( __FILE__ ) . '../includes/highlight/highlight.min.js', [], $this->version, false );
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/hm-wp-scss-admin.js', [], $this->version, false );

    }

    /**
     * Declare admin pages.
     */
    public function add_admin_menus() {
        add_submenu_page( 'options-general.php', 'Happy WP SCSS', 'Happy WP SCSS', 'manage_options', 'happy-scss-compiler', [$this, 'happy_scss_compiler_main_settings'] );
    }

    /**
     * Admin page content.
     */
    public function happy_scss_compiler_main_settings() {
        $version = $this->version;
        require_once plugin_dir_path( __FILE__ ) . 'partials/hm_wp_scss_main_settings.php';
    }

    private function scssFolderExists()
    {
        return is_dir( get_stylesheet_directory() . get_option('hm_wp_scss__scss_location') );
    }
    private function cssFolderExists()
    {
        return is_dir( get_stylesheet_directory() . get_option('hm_wp_scss__css_location') );
    }

    /**
     * Form fields
     */

    public function register_tab3()
    {
      $this->plugin_settings_tabs[$this->settings_tab3_key] = 'Advanced Paths';
    }
    public function register_tab2()
    {
        $this->plugin_settings_tabs[$this->settings_tab2_key] = 'Import / Export';
    }
    public function register_tab4()
    {
        //$this->plugin_settings_tabs[$this->settings_tab4_key] = 'Smart Compiler';
    }
    public function register_tab1()
    {
      $this->plugin_settings_tabs[$this->settings_tab1_key] = 'Compilation';
      $form = ['hm_wp_scss_sections' =>
          [
              'title' 		=> $this->title_icon . ' Folder Paths',
              'description'	=> 'folderPathsDesc',
              'fields' 		=>
                  [
                      'hm_wp_scss__scss_location' => [
                          'title'				=> 'SCSS Folder Path',
                          'type'				=> 'input',
                          'subtype'			=> 'text',
                          'required' 			=> 'required',
                          'get_options_list' 	=> '',
                          'value_type'		=> 'normal',
                          'wp_data' 			=> 'option'
                      ],
                      'hm_wp_scss__css_location' => [
                          'title'				=> 'CSS Folder Path',
                          'type'				=> 'input',
                          'subtype'			=> 'text',
                          'required' 			=> 'required',
                          'get_options_list' 	=> '',
                          'value_type'		=> 'normal',
                          'wp_data' 			=> 'option'
                      ],
                  ],
          ],
          [
              'title' 		=> $this->title_icon . ' Compilation',
              'description'	=> 'compilationDesc',
              'fields' 		=>
                  [
                      'hm_wp_scss__compilation_time' => [
                          'title'				=> 'Compilation Time',
                          'type'				=> 'select',
                          'subtype'			=> '',
                          'required' 			=> 'required',
                          'get_options_list' 	=> 'Always|When SCSS has changed|If Logged In|If Admin Logged In',
                          'value_type'		=> 'normal',
                          'wp_data' 			=> 'option'
                      ],
                      'hm_wp_scss__compilation_mode' => [
                          'title'				=> 'Minification Mode',
                          'type'				=> 'select',
                          'subtype'			=> '',
                          'required' 			=> 'required',
                          'get_options_list' 	=> 'Expanded|Compressed',
                          'value_type'		=> 'normal',
                          'wp_data' 			=> 'option',
                          'help'		=> 'minification',
                      ],
                      'hm_wp_scss__source_map_file' => [
                          'title'				=> 'Enable Source Map File',
                          'type'				=> 'input',
                          'subtype'			=> 'checkbox',
                          'required' 			=> 'required',
                          'get_options_list' 	=> '',
                          'value_type'		=> 'normal',
                          'wp_data' 			=> 'option'
                      ],
                      'hm_wp_scss__errors_display' => [
                          'title'				=> 'Display Errors in Front Office',
                          'type'				=> 'select',
                          'subtype'			=> '',
                          'required' 			=> '',
                          'get_options_list' 	=> 'Always|If Debug Mode of Wordpress is true|If Logged In|If Admin Logged In|Never|Never, even in Back Office',
                          'value_type'		=> 'normal',
                          'wp_data' 			=> 'option'
                      ],
                      'hm_wp_scss__no_compilation_underscore' => [
                          'title'				=> "Files starting with an <code>_</code> won't generate CSS files",
                          'type'				=> 'input',
                          'subtype'			=> 'checkbox',
                          'required' 			=> 'required',
                          'get_options_list' 	=> '',
                          'value_type'		=> 'normal',
                          'wp_data' 			=> 'option'
                      ],
                  ]
          ],
          [
              'title'		 	=> $this->title_icon . ' Enqueuing CSS files',
              'description'	=> 'enqueuingCssFilesDesc',
              'fields' 		=>
                  [
                      'hm_wp_scss__enqueuing' => [
                          'title'				=> 'Auto Enqueue CSS files',
                          'type'				=> 'input',
                          'subtype'			=> 'checkbox',
                          'required' 			=> 'required',
                          'get_options_list' 	=> '',
                          'value_type'		=> 'normal',
                          'wp_data' 			=> 'option'
                      ],
                  ]
          ],
      ];

      foreach( $form as $section_name => $section_data )
      {
          add_settings_section(
              $section_name,				// ID used to identify this section and with which to register options
              $section_data['title'],		// Title to be displayed on the administration page
              [$this,$section_data['description']],// Callback used to render the description of the section
              $this->settings_tab1_key						// Page on which to add this section of options
          );
          foreach( $section_data['fields'] as $field_name => $field_data )
          {
              unset($args);
              $args = $field_data;
              $args['id'] = $field_name;
              $args['name'] = $field_name;

              add_settings_field(
                  $field_name,
                  $field_data['title'],
                  [$this, 'plugin_name_render_settings_field'],
                  $this->settings_tab1_key,
                  $section_name,
                  $args
              );

              register_setting(
                  $this->settings_tab1_key,
                  $field_name
              );
          }
      }

    }
    /**
     * Section descriptions
     */
     // Main Settings
    public function folderPathsDesc()
    {
        echo '<p>Set the path of the SCSS and CSS folders from your active (child) theme path.<br />The CSS folder will be automatically filled with generated files according to your SCSS folder.<br />Examples: <code>/assets/scss/</code> and <code>/assets/css/</code></p>';
    }
    public function compilationDesc()
    {
        echo '<p>Choose when you want to generate files, the minification level of your CSS, if you want to also generate Soure Map Files (for debugging), and when to display errors in front office to debug your SCSS code (errors will always be displayed in BO).</p>';
    }
    public function enqueuingCssFilesDesc()
    {
        echo '<p>Check below to automatically include all the generated CSS files in your header.</p>';
    }

    /**
     * Notice an error
     */
    function admin_notice__error( $_string ) {
        $class = 'notice notice-error';
        printf( '<div class="%1$s"><p><strong>%2$s</strong>%3$s</p></div>', esc_attr( $class ), esc_html( $_string ), esc_html( $this->error ) );
    }
    function admin_notice__error_scss( $_string ) {
        $class = 'notice notice-error';
        printf( '<div class="%1$s"><p><strong>%2$s</strong>%3$s</p></div>', esc_attr( $class ), 'Your SCSS folder doesn\'t exist in your active theme.', esc_html( $this->error ) );
    }
    function admin_notice__error_css( $_string ) {
        $class = 'notice notice-error';
        printf( '<div class="%1$s"><p><strong>%2$s</strong>%3$s</p></div>', esc_attr( $class ), 'Your CSS folder doesn\'t exist in your active theme.', esc_html( $this->error ) );
    }
    function admin_notice__import_ok( $_string ) {
        $class = 'notice notice-success';
        printf( '<div class="%1$s"><p><strong>%2$s</strong>%3$s</p></div>', esc_attr( $class ), 'Settings have been successfully imported.', '' );
    }
    function admin_notice__import_no( $_string ) {
        $class = 'notice notice-error';
        printf( '<div class="%1$s"><p><strong>%2$s</strong>%3$s</p></div>', esc_attr( $class ), 'Settings have not been imported. Check your file and try again.', esc_html( $this->error ) );
    }
    function admin_notice__import_ext( $_string ) {
        $class = 'notice notice-error';
        printf( '<div class="%1$s"><p><strong>%2$s</strong>%3$s</p></div>', esc_attr( $class ), 'Settings file must be a .json file downloaded previously from this plugin.', esc_html( $this->error ) );
    }

    /**
     * Save advanced paths
    **/
    public function advanced_paths()
    {
      $options = ["hm_wp_scss__adv_path_scss", "hm_wp_scss__adv_path_css"];
      foreach( $options as $optionName )
      {
        if( get_option( $optionName ) === false )
        {
          add_option($optionName, $_POST[$optionName], '', 1 );
        }
        else
        {
          update_option( $optionName, $_POST[$optionName], 1 );
        }
      }
      wp_redirect( admin_url( 'options-general.php?page=happy-scss-compiler&tab=advanced_paths' ) );
    }

    /**
     * Smart Compiler
     **/
    public function smart_compiler()
    {
        $optionName = "hm_wp_scss__smart_compiler";
        if( get_option( $optionName ) === false )
            add_option($optionName, $_POST[$optionName], '', 1 );
        else
            update_option( $optionName, $_POST[$optionName], 1 );

        wp_redirect( admin_url( 'options-general.php?page=happy-scss-compiler&tab=smart_compiler' ) );
    }

    /**
     * Download settings
     **/
    public function dl_settings()
    {
      global $wpdb;
      $options = $wpdb->get_results( "SELECT * FROM $wpdb->options WHERE option_name LIKE 'hm_wp_scss_%'" );
      $filename = "happywpscss-settings-" . sanitize_title(get_bloginfo('name')) . ".json";
      header("Content-disposition: attachment; filename=$filename");
      header('Content-type: application/json');
      header('Pragma: no-cache');
      echo json_encode($options);
    }
    /**
     * Import settings
     **/
    public function import_settings()
    {
      $redirect_urls = [
        'yes' => '/options-general.php?page=happy-scss-compiler&import=yes',
        'no' => '/options-general.php?page=happy-scss-compiler&tab=import_export&import=no',
        'ext' => '/options-general.php?page=happy-scss-compiler&tab=import_export&import=ext',
      ];
      $changed = 'no';
      if(!empty($_FILES['import_field']["tmp_name"]) && is_array( $options = json_decode(file_get_contents($_FILES['import_field']['tmp_name'])) ) )
      {
        foreach( $options as $option )
        {
          // Check if this is a real plugin option
          if( preg_match( '#^hm_wp_scss_(.+)#', $option->option_name ) )
          {
            if( get_option( $option->option_name ) === false )
            {
              add_option( $option->option_name, $option->option_value, '', $option->autoload );
            }
            else
            {
              update_option( $option->option_name, $option->option_value, $option->autoload );
            }
            $changed = 'yes';
          }
        }
      }
      else
      {
        if( pathinfo($_FILES['import_field']["tmp_name"], PATHINFO_EXTENSION) != 'json' )
          $changed = 'ext';
      }
      wp_redirect( admin_url( $redirect_urls[$changed] ) );
      return $changed;
    }

    /**
     * Original function from https://blog.wplauncher.com/create-wordpress-plugin-settings-page/
     */
    public function plugin_name_render_settings_field($args)
    {
        if($args['wp_data'] == 'option'){
            $wp_data_value = get_option($args['name']);
        } elseif($args['wp_data'] == 'post_meta'){
            $wp_data_value = get_post_meta($args['post_id'], $args['name'], true );
        }

        switch ($args['type']) {

            case 'input':
                $value = ($args['value_type'] == 'serialized') ? serialize($wp_data_value) : $wp_data_value;
                if($args['subtype'] != 'checkbox'){
                    $prependStart = (isset($args['prepend_value'])) ? '<div class="input-prepend"> <span class="add-on">'.$args['prepend_value'].'</span>' : '';
                    $prependEnd = (isset($args['prepend_value'])) ? '</div>' : '';
                    echo '<div class="input-prepend"><span class="add-on"><input type="' . esc_attr($args['subtype']) . '" id="' . esc_attr($args['id']) . '" "' . esc_attr($args['required']) . '" name="' . esc_attr($args['name']) . '" size="40" value="' . esc_attr($value) . '" /></div>';

                } else {
                    $checked = ($value) ? 'checked' : '';
                    echo '<input type="'. esc_attr($args['subtype']) . '" id="' . esc_attr($args['id']) . '" "' . esc_attr($args['required']) . '" name="' . esc_attr($args['name']) . '" size="40" value="1" ' . esc_html($checked).' />';
                }
                break;
            case 'select':
                $value = ($args['value_type'] == 'serialized') ? serialize($wp_data_value) : $wp_data_value;
                echo '<select id="'.esc_attr($args['id']).'" name="'.esc_attr($args['name']).'">';
                foreach( explode('|',$args['get_options_list']) as $option )
                {
                    echo '<option value="' . esc_html($option) . '"' . ( strtolower($value) == strtolower($option) ? ' selected="selected"' : '' ) . '>' . esc_html($option) . '</option>';
                }
                echo '</select>';

                if( isset( $args['help'] ) && $args['help'] == 'minification' ): ?>
                    <div id="minification-demonstration">
                        <p><em>Minification preview:</em></p>


                        <div id="minification-expanded" class="minification_example">
<pre><code>/* An important comment */
body {
  background-color: #be0808;
}
body a {
  text-decoration: none;
  font-size: 14px;
  color: black;
}
body a:hover {
  color: #be0808;
}</code></pre>
                        </div>

                        <div id="minification-compressed" class="minification_example">
<pre><code>body{background-color:#be0808}body a{text-decoration:none;font-size:14px;color:black}body a:hover{color:#be0808}</code></pre>
</div>

                    </div>

                <?php endif;

                break;
            default:
                break;
        }
    }
    /*
     * Renders our tabs in the plugin options page,
     * walks through the object's tabs array and prints
     * them one by one. Provides the heading for the
     * plugin_options_page method.
     */
    public function plugin_options_tabs() {
      $current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->settings_tab1_key;

      screen_icon();
      echo '<h2 class="nav-tab-wrapper">';
      foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
        $active = $current_tab == $tab_key ? 'nav-tab-active' : '';
        echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->plugin_name . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
      }
      echo '</h2>';
    }
}
