<?php
    global $wp;
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
        ?>
        <section class="content">
            <?php get_template_part('template-parts/dashboard/order','detail',$order_id) ?>
        </section>
    <?php endif;?>
    <!-- ./main content -->



</div>