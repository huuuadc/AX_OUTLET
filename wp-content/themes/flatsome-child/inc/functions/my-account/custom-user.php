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

/*function action_woocommerce_edit_account_form() {
    woocommerce_form_field( 'user_registration_user_birthday', array(
        'type'        => 'text',
        'label'       => __( 'Ngày sinh', 'woocommerce' ),
        'placeholder' => __( 'ngày/tháng/năm', 'woocommerce' ),
        'required'    => true,
    ), get_user_meta( get_current_user_id(), 'user_registration_user_birthday', true ));
}
add_action( 'woocommerce_edit_account_form', 'action_woocommerce_edit_account_form' );*/

// Validate - my account
function action_woocommerce_save_account_details_errors( $args ){
    if ( isset($_POST['user_registration_user_birthday']) && empty($_POST['user_registration_user_birthday']) ) {
        $args->add( 'error', __( 'Vui lòng nhập ngày sinh nhật của bạn', 'woocommerce' ) );
    }
}
add_action( 'woocommerce_save_account_details_errors','action_woocommerce_save_account_details_errors', 10, 1 );

// Save - my account
function action_woocommerce_save_account_details( $user_id ) {
    if( isset($_POST['user_registration_user_birthday']) && ! empty($_POST['user_registration_user_birthday']) ) {
        update_user_meta( $user_id, 'user_registration_user_birthday', sanitize_text_field($_POST['user_registration_user_birthday']) );
    }
}
add_action( 'woocommerce_save_account_details', 'action_woocommerce_save_account_details', 10, 1 );
