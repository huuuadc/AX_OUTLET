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

function logo_login() {
    $site_logo_id        = flatsome_option( 'site_logo' );
    $site_logo           = wp_get_attachment_image_src( $site_logo_id, 'large' );
    $src = $site_logo[0] ?? get_stylesheet_directory_uri() . '/assets/img/dashboard.png';
    $width               = get_theme_mod( 'logo_width', 200 );
    ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(<?php echo $src; ?>);
            background-repeat: no-repeat;
            background-size: <?php echo $width; ?>px;
            height: unset;
            width: unset;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'logo_login' );


function isJson($string): bool {
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}


function numInWords($num)
{
    $nwords = array(
        0                   => 'không',
        1                   => 'một',
        2                   => 'hai',
        3                   => 'ba',
        4                   => 'bốn',
        5                   => 'năm',
        6                   => 'sáu',
        7                   => 'bảy',
        8                   => 'tám',
        9                   => 'chín',
        10                  => 'mười',
        11                  => 'mười một',
        12                  => 'mười hai',
        13                  => 'mười ba',
        14                  => 'mười bốn',
        15                  => 'mười lăm',
        16                  => 'mười sáu',
        17                  => 'mười bảy',
        18                  => 'mười tám',
        19                  => 'mười chín',
        20                  => 'hai mươi',
        30                  => 'ba mươi',
        40                  => 'bốn mươi',
        50                  => 'năm mươi',
        60                  => 'sáu mươi',
        70                  => 'bảy mươi',
        80                  => 'tám mươi',
        90                  => 'chín mươi',
        100                 => 'trăm',
        1000                => 'nghìn',
        1000000             => 'triệu',
        1000000000          => 'tỷ',
        1000000000000       => 'nghìn tỷ',
        1000000000000000    => 'ngàn triệu triệu',
        1000000000000000000 => 'tỷ tỷ',
    );
    $separate = ' ';
    $negative = ' âm ';
    $rltTen   = ' linh ';
    $decimal  = ' phẩy ';
    if (!is_numeric($num)) {
        $w = '#';
    } else if ($num < 0) {
        $w = $negative . numInWords(abs($num));
    } else {
        if (fmod($num, 1) != 0) {
            $numInstr    = strval($num);
            $numInstrArr = explode(".", $numInstr);
            $w           = numInWords(intval($numInstrArr[0])) . $decimal . numInWords(intval($numInstrArr[1]));
        } else {
            $w = '';
            if ($num < 21) // 0 to 20
            {
                $w .= $nwords[$num];
            } else if ($num < 100) {
                // 21 to 99
                $w .= $nwords[10 * floor($num / 10)];
                $r = fmod($num, 10);
                if ($r > 0) {
                    $w .= $separate . $nwords[$r];
                }

            } else if ($num < 1000) {
                // 100 to 999
                $w .= $nwords[floor($num / 100)] . $separate . $nwords[100];
                $r = fmod($num, 100);
                if ($r > 0) {
                    if ($r < 10) {
                        $w .= $rltTen . $separate . numInWords($r);
                    } else {
                        $w .= $separate . numInWords($r);
                    }
                }
            } else {
                $baseUnit     = pow(1000, floor(log($num, 1000)));
                $numBaseUnits = (int) ($num / $baseUnit);
                $r            = fmod($num, $baseUnit);
                if ($r == 0) {
                    $w = numInWords($numBaseUnits) . $separate . $nwords[$baseUnit];
                } else {
                    if ($r < 100) {
                        if ($r >= 10) {
                            $w = numInWords($numBaseUnits) . $separate . $nwords[$baseUnit] . ' không trăm ' . numInWords($r);
                        }
                        else{
                            $w = numInWords($numBaseUnits) . $separate . $nwords[$baseUnit] . ' không trăm linh ' . numInWords($r);
                        }
                    } else {
                        $baseUnitInstr      = strval($baseUnit);
                        $rInstr             = strval($r);
                        $lenOfBaseUnitInstr = strlen($baseUnitInstr);
                        $lenOfRInstr        = strlen($rInstr);
                        if (($lenOfBaseUnitInstr - 1) != $lenOfRInstr) {
                            $numberOfZero = $lenOfBaseUnitInstr - $lenOfRInstr - 1;
                            if ($numberOfZero == 2) {
                                $w = numInWords($numBaseUnits) . $separate . $nwords[$baseUnit] . ' không trăm linh ' . numInWords($r);
                            } else if ($numberOfZero == 1) {
                                $w = numInWords($numBaseUnits) . $separate . $nwords[$baseUnit] . ' không trăm ' . numInWords($r);
                            } else {
                                $w = numInWords($numBaseUnits) . $separate . $nwords[$baseUnit] . $separate . numInWords($r);
                            }
                        } else {
                            $w = numInWords($numBaseUnits) . $separate . $nwords[$baseUnit] . $separate . numInWords($r);
                        }
                    }
                }
            }
        }
    }
    return $w;
}

function numberInVietnameseWords($num)
{
    return str_replace("mươi năm", "mươi lăm", str_replace("mươi một", "mươi mốt", numInWords($num)));
}

function numberInVietnameseCurrency($num)
{
    $rs    = numberInVietnameseWords($num);
    $rs[0] = strtoupper($rs[0]);
    return $rs . ' đồng';
}