<?php

function wishlist_add_end_point()
{
    add_rewrite_endpoint('wish-list', EP_ROOT | EP_PAGES);
    // flush_rewrite_rules();
}
add_action('init', 'wishlist_add_end_point');
// ------------------
// 2. Add new query var
function wishlist_query_vars($vars)
{
    $vars[] = 'wish-list';
    return $vars;
}
add_filter('query_vars', 'wishlist_query_vars', 0);
// ------------------
// 3. Insert the new endpoint into the My Account menu
function wishlist_add_link_my_account($items)
{
    $items['wish-list'] = 'Wishlist';
    return $items;
}
add_filter('woocommerce_account_menu_items', 'wishlist_add_link_my_account', 1);
// ------------------
// 4. Add content to the new tab
function wishlist_add_tab_content()
{
    get_template_part('woocommerce/myaccount/wish-list');
}
add_action('woocommerce_account_wish-list_endpoint', 'wishlist_add_tab_content');


// Rename, re-order my account menu items
function woo_reorder_my_account_menu()
{
    $neworder = array(
        'dashboard'          => __(__('Dashboard', 'DGFC'), 'woocommerce'),
        'edit-account'       => __(__('Account Details', 'DGFC'), 'woocommerce'),
        'edit-address'       => __(__('Addresses', 'DGFC'), 'woocommerce'),
        'orders'             => __(__('Orders History', 'DGFC'), 'woocommerce'),
        'wish-list'    => __(__('Wishlist', 'DGFC'), 'woocommerce'),
        'support'            => __(__('Support', 'DGFC'), 'woocommerce'),
        'customer-logout'    => __(__('Log out', 'DGFC'), 'woocommerce'),
    );
    return $neworder;
}
add_filter('woocommerce_account_menu_items', 'woo_reorder_my_account_menu');