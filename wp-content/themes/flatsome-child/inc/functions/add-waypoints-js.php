<?php
function yith_raq_image_fix() {
    ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/2.0.5/waypoints.min.js" defer></script>
    <?php
}
add_action( 'wp_footer', 'yith_raq_image_fix', 999999 );