<?php

add_filter( 'login_url' , 'login_url', 10, 3 );

/**
 *
 * Update url redirect : wp-admin/options.php
 *
 * @param $login_url
 * @param $redirect
 * @param $force_reauth
 *
 * @return string
 */
function login_url( $login_url, $redirect, $force_reauth ) {
    if ( is_404() ) {
        return '#';
    }

    if ( $force_reauth === false ) {
        return $login_url;
    }

    if ( empty( $redirect ) ) {
        return $login_url;
    }

    $redirect = explode( '?', $redirect );

    if ( $redirect[0] === admin_url( 'options.php' ) ) {
        $login_url = admin_url();
    }

    return $login_url;
}
