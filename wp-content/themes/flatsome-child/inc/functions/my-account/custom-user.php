<?php

add_action('check_admin_referer', 'logout_without_confirm', 10, 2);
function logout_without_confirm($action, $result)
{
    /**
     * Allow logout without confirmation
     */
    if ($action == "log-out" && !isset($_GET['_wpnonce'])) {
        $redirect_to = wc_get_page_permalink( 'myaccount' );
        $location = str_replace('&amp;', '&', wp_logout_url($redirect_to));;
        header("Location: $location");
        die();
    }
}

add_action( 'wp_login', 'format_user_display_name_on_login' );
function format_user_display_name_on_login( $username ) {
    $user = get_user_by( 'login', $username );
    $first_name = get_user_meta( $user->ID, 'first_name', true );
    $last_name = get_user_meta( $user->ID, 'last_name', true );
    $full_name = trim( $first_name . ' ' . $last_name );
    if ( ! empty( $full_name ) && ( $user->data->display_name != $full_name ) ) {
        $userdata = array(
            'ID' => $user->ID,
            'display_name' => $full_name,
        );
        wp_update_user( $userdata );
    }
}