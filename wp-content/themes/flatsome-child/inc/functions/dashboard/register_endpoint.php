<?php

add_action('init','dashboard_register_page_endpoint');
function dashboard_register_page_endpoint(){

    add_rewrite_endpoint('order-list', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint('order-report', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint('customer-witchlist', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint('customer-report', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint('customer-list', EP_ROOT | EP_PAGES );

    wc_get_endpoint_url('view-order','15559','http://devdafc.com.vn/admin-dashboard');

}

add_action('template_include','dashboard_register_template');
function dashboard_register_template($template){

   if(get_query_var('pagename') == 'order-list') {
      return get_template_part('page','dashboard') ;
    }
    if(get_query_var('pagename') == 'order-report') {
        return get_template_part('page','dashboard') ;
    }
    if(get_query_var('pagename') == 'customer-list') {
        return get_template_part('page','dashboard') ;
    }
    if(get_query_var('pagename') == 'customer-witchlist') {
        return get_template_part('page','dashboard') ;
    }
    if(get_query_var('pagename') == 'customer-report') {
        return get_template_part('page','dashboard') ;
    }
    return $template;
}