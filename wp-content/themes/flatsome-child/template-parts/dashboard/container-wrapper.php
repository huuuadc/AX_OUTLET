<?php
    global $wp;
    date_default_timezone_set('Asia/Ho_Chi_Minh');
?>
<div class="content-wrapper">
    <?php if (get_query_var('pagename') == 'admin-dashboard' && isset($wp->query_vars['page'])) :?>
        <section class="content">
            <?php get_template_part('template-parts/dashboard/dashboard','') ?>
        </section>
    <?php endif; ?>

    <!-- Main content -->
    <?php if(  (get_query_var('pagename') == 'admin-dashboard' && isset($wp->query_vars['order-list'])) && !isset($_GET['order_id'])) :?>
    <section class="content">
        <?php get_template_part('template-parts/dashboard/order','list') ?>
    </section>
    <?php endif;?>

    <?php if( (get_query_var('pagename') == 'admin-dashboard' && isset($wp->query_vars['order-list'])) && isset($_GET['order_id']) ) :
        $order_id = $_GET['order_id'];
        $print = isset($_GET['print']) ?? false;
        if (!$print):
    ?>
        <section class="content">
            <?php get_template_part('template-parts/dashboard/order','detail',$order_id) ?>
        </section>
        <?php endif;?>

    <?php if ($print && $_GET['print'] == 'shipment' ):?>
        <section class="content">
            <?php get_template_part('template-parts/dashboard/order-print','shipment',$order_id) ?>
        </section>
    <?php endif;?>

        <?php if ($print && $_GET['print'] == 'invoice' ):?>
        <section class="content">
            <?php get_template_part('template-parts/dashboard/order-print','invoice',$order_id) ?>
        </section>
    <?php endif;?>


    <?php endif;?>

    <?php if( get_query_var('pagename') == 'setting' || isset($wp->query_vars['setting']) ) :
        ?>
        <section class="content">
            <?php get_template_part('template-parts/dashboard/setting') ?>
        </section>
    <?php endif;?>

    <?php write_log(get_query_var('pagename'));?>
    <?php if (get_query_var('pagename') == 'admin-dashboard' && isset($wp->query_vars['order-report'])) :?>
        <section class="content">
            <?php get_template_part('template-parts/dashboard/order','report') ?>
        </section>
    <?php endif; ?>
    <!-- ./main content -->



</div>