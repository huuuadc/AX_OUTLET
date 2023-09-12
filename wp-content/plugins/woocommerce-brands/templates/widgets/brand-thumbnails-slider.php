<?php
/**
 * Brand slider
 */
?>
<style>

</style>
<div class="slider brand_slider">
    <?php foreach ( $brands as $index => $brand ) :
        $thumbnail = get_brand_thumbnail_url( $brand->term_id, apply_filters( 'woocommerce_brand_thumbnail_size', 'shop_catalog' ) );

        if ( ! $thumbnail )
            $thumbnail = wc_placeholder_img_src();

        $class = '';

        if ( $index == 0 || $index % $columns == 0 )
            $class = 'first';
        elseif ( ( $index + 1 ) % $columns == 0 )
            $class = 'last';

        $width = floor( ( ( 100 - ( ( $columns - 1 ) * 2 ) ) / $columns ) * 100 ) / 100;
        ?>
        <div class="slide <?php echo esc_attr( $class ); ?>" style="width: <?php echo esc_attr( $width ); ?>%;">
            <a class="item" href="<?php echo esc_url( get_term_link( $brand->slug, 'brand' ) ); ?>" title="<?php echo esc_attr( $brand->name ); ?>">
                <span class="term-thumbnail">
                    <img src="<?php echo esc_url( $thumbnail ); ?>" alt="<?php echo esc_attr( $brand->name ); ?>" />
                </span>
                <h3 class="brand_name"><?php echo $brand->name; ?></h3>
            </a>
        </div>
    <?php endforeach; ?>
</div>
<script>
    jQuery(function ($){
        $('.brand_slider').slick({
            slidesToShow: 4,
            slidesToScroll: 1,
            centerMode: true,
            arrows: true,
            dots: false,
            speed: 300,
            centerPadding: '20px',
            infinite: true,
            autoplay: false,
            variableWidth: true,
            responsive: [
                {
                    breakpoint: 768,
                    settings: {
                        arrows: false,
                        centerMode: true,
                        centerPadding: '20px',
                        slidesToShow: 3
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        arrows: false,
                        centerMode: true,
                        centerPadding: '20px',
                        slidesToShow: 1
                    }
                }
            ]
        });
    });
</script>