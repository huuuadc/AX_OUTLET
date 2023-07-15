<?php

add_action( 'query_vars', 'dashboard_register_query_vars' );


function dashboard_register_query_vars( $vars ) {
    $vars[] = 'order-list';
    $vars[] = 'order-report';
    $vars[] = 'customer-witchlist';
    $vars[] = 'customer-report';
    $vars[] = 'customer-list';

    return $vars;
}

add_action('init','dashboard_register_page_endpoint');
function dashboard_register_page_endpoint(): void
{

    add_rewrite_endpoint('order-list', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint('order-report', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint('customer-witchlist', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint('customer-report', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint('customer-list', EP_ROOT | EP_PAGES );

}

add_action('template_include','dashboard_register_template');
function dashboard_register_template($template){
    global $wp;

   if(get_query_var('pagename') == 'order-list' || isset($wp->query_vars['order-list'])) {
      return get_template_part('page','dashboard') ;
    }
    if(get_query_var('pagename') == 'order-report'  || isset($wp->query_vars['order-report'])) {
        return get_template_part('page','dashboard') ;
    }
    if(get_query_var('pagename') == 'customer-list'  || isset($wp->query_vars['customer-list'])) {
        return get_template_part('page','dashboard') ;
    }
    if(get_query_var('pagename') == 'customer-witchlist'  || isset($wp->query_vars['customer-witchlist'])) {
        return get_template_part('page','dashboard') ;
    }
    if(get_query_var('pagename') == 'customer-report'  || isset($wp->query_vars['customer-report'])) {
        return get_template_part('page','dashboard') ;
    }
    return $template;
}