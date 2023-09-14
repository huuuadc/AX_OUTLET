<?php

function exist_option( $arg ) {

    global $wpdb;
    $prefix = $wpdb->prefix;
    $db_options = $prefix.'options';
    $sql_query = 'SELECT * FROM ' . $db_options . ' WHERE option_name LIKE "' . $arg . '"';

    $results = $wpdb->get_results( $sql_query, OBJECT );

    if ( count( $results ) === 0 ) {
        return false;
    } else {
        return true;
    }
}

function get_date_format(){
    return get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
}

function decode_post_data_in_post($post_data){

    // Details you want injected into WooCommerce session.
    $details = array('billing_first_name', 'billing_last_name', 'billing_company', 'billing_email', 'billing_phone');

    // Parsing data
    $post = array();
    $vars = explode('&', $post_data);
    foreach ($vars as $k => $value){
        $v = explode('=', urldecode($value));
        $post[$v[0]] = $v[1];
    }

    return $post;

}

function convert_string_to_range_date(string $date){

    $dates = explode(' - ',$date);

    $start_date = str_replace('/', '-', $dates['0']);
    $start_date = date('Y-m-d', strtotime($start_date.' - 1 days'));
    $end_date = str_replace('/', '-', $dates['1']);
    $end_date = date('Y-m-d', strtotime($end_date.' + 1 days'));

    return array(
                'start_date' => $start_date,
                'end_date' => $end_date,
                'text_date' => $date);
}

function convert_string_to_range_date_default(int $number = 0){
    $start_date = date('Y-m-d',( strtotime( date('Y-m-d').' - '. $number.' days')));
    $end_date = date('Y-m-d');
    $text_date = $start_date . ' - ' . $end_date;
    $start_date = date('Y-m-d',( strtotime( date('Y-m-d').' - '. ($number-1).' days')));
    $end_date = date('Y-m-d',(strtotime($end_date.'+ 1 days')));
    return array(
                'start_date'=>$start_date,
                'end_date'=>$end_date,
                'text_date' => $text_date);
}

function get_product_brand_list( $product_id, $sep = ', ', $before = '', $after = '' ) {
    return get_the_term_list( $product_id, 'brand', $before, $sep, $after );
}
function get_product_brand_name( $product_id, $sep = ', ', $before = '', $after = '' ) {

    $taxonomy = 'brand';

    $terms = get_the_terms( $product_id, $taxonomy );

    if ( is_wp_error( $terms ) ) {
        return $terms;
    }

    if ( empty( $terms ) ) {
        return false;
    }

    $links = array();

    foreach ( $terms as $term ) {
        $link = get_term_link( $term, $taxonomy );
        if ( is_wp_error( $link ) ) {
            return $link;
        }
        $links[] = $term->name ;
    }

    return $before . implode( $sep, $links ) . $after;
}

function format_number_default(float $number): string{
    return number_format($number,'0','.',',');
};

function response(bool $status = false, string $messenger = '', array $data = []){
    return json_encode(array(
        'status' => $status,
        'messenger' => $messenger,
        'data' => $data
    ));
}

function logo_login() { ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/assets/img/dashboard.png);
            background-repeat: no-repeat;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'logo_login' );


function isJson($string): bool {
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}