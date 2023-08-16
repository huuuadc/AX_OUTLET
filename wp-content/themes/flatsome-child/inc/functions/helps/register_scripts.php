<?php

function custom_js():void {
    wp_enqueue_script( 'toast-js', get_stylesheet_directory_uri() . '/assets/js/toast.js', array( 'jquery' ),'',true );
    wp_enqueue_script( 'validation-js', get_stylesheet_directory_uri() . '/assets/js/validations.js', array( 'jquery' ),'',true );
    wp_enqueue_script( 'help-js', get_stylesheet_directory_uri() . '/assets/js/helps.js', array( 'jquery' ),'',true );
    wp_enqueue_script( 'sticky-js', get_stylesheet_directory_uri() . '/assets/js/jquery.sticky.js', array( 'jquery' ),'',true );
    wp_enqueue_script( 'ajax-js', get_stylesheet_directory_uri() . '/assets/js/ajax.js', array( 'jquery' ),'',true );
}
add_action( 'wp_enqueue_scripts', 'custom_js' );
add_action('admin_enqueue_scripts', 'custom_js');

//Add fontsome and css admin page wordpress
function admin_css_custom() {
    wp_enqueue_style('toast-css', get_stylesheet_directory_uri().'/assets/css/toast.css', array(),'','all');
    echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">   ';
}

add_action('admin_head', 'admin_css_custom');


function yith_raq_image_fix() {
    ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/2.0.5/waypoints.min.js" defer></script>
    <?php
}
add_action( 'wp_footer', 'yith_raq_image_fix', 999999 );
