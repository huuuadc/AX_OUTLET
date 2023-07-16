<?php
    global $wp;
?>
<div class="content-wrapper">
    <!-- Main content -->
    <?php if(  (get_query_var('pagename') == 'order-list' || isset($wp->query_vars['order-list'])) && !isset($_GET['order_id'])) {?>
    <section class="content">
        <?php get_template_part('template-parts/dashboard/order','list') ?>
    </section>
    <?php }?>
    <?php if( (get_query_var('pagename') == 'order-list' || isset($wp->query_vars['order-list'])) && isset($_GET['order_id']) ) {
        $order_id = $_GET['order_id'];
        ?>
        <section class="content">
            <?php get_template_part('template-parts/dashboard/order','detail',$order_id) ?>
        </section>
    <?php }?>
    <!-- ./main content -->


</div>