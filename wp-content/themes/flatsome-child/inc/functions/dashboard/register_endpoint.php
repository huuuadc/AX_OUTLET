<?php

add_action( 'query_vars', 'dashboard_register_query_vars' );


function dashboard_register_query_vars( $vars ) {
    $vars[] = 'order-list';
    $vars[] = 'order-report';
    $vars[] = 'order-new';
    $vars[] = 'customer-wishlist';
    $vars[] = 'customer-report';
    $vars[] = 'customer-list';
    $vars[] = 'customer-cart';
    $vars[] = 'setting';
    $vars[] = 'inventory-report';
    $vars[] = 'inventory-adjustment';
    $vars[] = 'docs';

    return $vars;
}

add_action('init','dashboard_register_page_endpoint');
function dashboard_register_page_endpoint(): void
{

    add_rewrite_endpoint('order-list', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint('order-report', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint('order-new', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint('customer-wishlist', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint('customer-report', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint('customer-list', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint('customer-cart', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint('setting', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint('inventory-report', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint('inventory-adjustment', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint('docs', EP_ROOT | EP_PAGES );

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
    if(get_query_var('pagename') == 'order-new'  || isset($wp->query_vars['order-new'])) {
        return get_template_part('page','dashboard') ;
    }
    if(get_query_var('pagename') == 'customer-list'  || isset($wp->query_vars['customer-list'])) {
        return get_template_part('page','dashboard') ;
    }
    if(get_query_var('pagename') == 'customer-cart'  || isset($wp->query_vars['customer-list'])) {
        return get_template_part('page','dashboard') ;
    }
    if(get_query_var('pagename') == 'customer-wishlist'  || isset($wp->query_vars['customer-wishlist'])) {
        return get_template_part('page','dashboard') ;
    }
    if(get_query_var('pagename') == 'customer-report'  || isset($wp->query_vars['customer-report'])) {
        return get_template_part('page','dashboard') ;
    }
    if(get_query_var('pagename') == 'setting' || isset($wp->query_vars['setting'])) {
        return get_template_part('page','dashboard') ;
    }
    if(get_query_var('pagename') == 'inventory-report' || isset($wp->query_vars['inventory-report'])) {
        return get_template_part('page','dashboard') ;
    }
    if(get_query_var('pagename') == 'inventory-adjustment' || isset($wp->query_vars['inventory-adjustment'])) {
        return get_template_part('page','dashboard') ;
    }
    if(get_query_var('pagename') == 'docs' || isset($wp->query_vars['docs'])) {
        return get_template_part('page-api','docs') ;
    }
    return $template;
}
