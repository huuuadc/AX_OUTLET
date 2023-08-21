<?php
    global $wp;
    $main_sidebar = [
            'dashboard' => [
                    'title'     => 'Dashboard',
                    'slug'      =>  '/admin-dashboard',
                    'active'    =>  false,
                    'icon'      =>  'fas fa-tachometer-alt',
                    'permission' => 'admin_dashboard',
                    'option'    =>  []
            ],
            'customer' => [
                    'title'     => 'Khách hàng',
                    'slug'      =>  '/customers',
                    'active'    =>  false,
                    'icon'      =>  'fas fa-address-book',
                    'permission' => 'admin_dashboard_customer',
                    'option'    =>  [
                            [
                                    'title'     =>  'Danh sách',
                                    'slug'      =>  '/customers',
                                    'icon'      =>  'fas fa-clipboard-list',
                                    'active'    =>  false
                            ],
                            [
                                'title'     =>  'Yêu thích',
                                'slug'      =>  '/witchlist',
                                'icon'      =>  'fa fa-heart',
                                'active'    =>  false
                            ]
                    ]
            ],
            'order' => [
                'title'     => 'Đơn hàng',
                'slug'      =>  '/orders',
                'icon'      =>  'fas fa-boxes',
                'active'    =>  false,
                'permission' => 'admin_dashboard_order',
                'option'    =>  [
                    'new' => [
                        'title'     =>  'Thêm mới',
                        'slug'      =>  '/admin-dashboard/order-new',
                        'icon'      =>  'fa fa-list-alt',
                        'active'    =>  false
                    ],
                    'list' => [
                        'title'     =>  'Danh sách',
                        'slug'      =>  '/admin-dashboard/order-list',
                        'icon'      =>  'fas fa-clipboard-list',
                        'active'    =>  false
                    ],
                    'report' => [
                        'title'     =>  'Báo cáo',
                        'slug'      =>  '/admin-dashboard/order-report',
                        'icon'      =>  'fas fa-clipboard-list',
                        'active'    =>  false
                    ]
                ]
            ],
            'inventory' => [
                'title'     => 'Tồn kho',
                'slug'      =>  '/admin-dashboard/inventory',
                'active'    =>  false,
                'icon'      =>  'fas fa-address-book',
                'permission' => 'admin_dashboard_stock',
                'option'    =>  [
                    'to' => [
                        'title'     =>  'TO',
                        'slug'      =>  '/admin-dashboard/order-list',
                        'icon'      =>  'fas fa-clipboard-list',
                        'active'    =>  false
                    ],
                    'so' => [
                        'title'     =>  'SO',
                        'slug'      =>  '/admin-dashboard/order-report',
                        'icon'      =>  'fas fa-clipboard-list',
                        'active'    =>  false
                    ],
                    'report' => [
                        'title'     =>  'Báo cáo',
                        'slug'      =>  '/admin-dashboard/inventory-report',
                        'icon'      =>  'fas fa-clipboard-list',
                        'active'    =>  false
                    ]
                ]
            ]
    ]
?>

<!-- Navbar header -->
<?php get_template_part('template-parts/dashboard/navbar','header') ?>
<!-- /.navbar header-->

<!-- Main content -->

<!--//=====================================================================-->
<?php if (get_query_var('pagename') == 'admin-dashboard' && isset($wp->query_vars['page'])) :?>
    <?php $main_sidebar['dashboard']['active'] = true?>
<?php get_template_part('template-parts/dashboard/main','sidebar', $main_sidebar) ?>
    <div class="content-wrapper">
        <section class="content">
            <?php get_template_part('template-parts/dashboard/dashboard','') ?>
        </section>
    </div>
<?php endif; ?>



<!--//=====================================================================-->
<?php if  (get_query_var('pagename') == 'order-new' || isset($wp->query_vars['order-new'])) :?>
    <?php $main_sidebar['order']['option']['new']['active'] = true?>
    <?php $main_sidebar['order']['active'] = true?>
    <?php if ( $main_sidebar['order']['option']['new']['active']) $main_sidebar['order']['option']['new']['slug'] = '/wp-admin/post-new.php?post_type=shop_order'; ?>
    <?php get_template_part('template-parts/dashboard/main','sidebar', $main_sidebar) ?>

            <?php get_template_part('template-parts/dashboard/order','new') ?>
<?php endif;?>


<!--//=====================================================================-->
<?php if(  (get_query_var('pagename') == 'admin-dashboard' && isset($wp->query_vars['order-list'])) && !isset($_GET['order_id'])) :?>
<?php $main_sidebar['order']['option']['list']['active'] = true?>
<?php $main_sidebar['order']['active'] = true?>
<?php get_template_part('template-parts/dashboard/main','sidebar', $main_sidebar) ?>
    <div class="content-wrapper">
            <section class="content">
                <?php get_template_part('template-parts/dashboard/order','list') ?>
            </section>
    </div>
<?php endif;?>


<!--//====================================================================-->
<?php if( (get_query_var('pagename') == 'admin-dashboard' && isset($wp->query_vars['order-list'])) && isset($_GET['order_id']) ) :

    $main_sidebar['order']['active'] = true;
    get_template_part('template-parts/dashboard/main', 'sidebar', $main_sidebar);
    echo '<div class="content-wrapper">';
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

</div>
<?php endif;?>


<!--//============================================================-->
<?php if (get_query_var('pagename') == 'admin-dashboard' && isset($wp->query_vars['order-report'])) :
    $main_sidebar['order']['active'] = true;
    $main_sidebar['order']['option']['report']['active'] = true;
    get_template_part('template-parts/dashboard/main', 'sidebar', $main_sidebar);
    echo '<div class="content-wrapper">';
    ?>
    <section class="content">
        <?php get_template_part('template-parts/dashboard/order','report') ?>
    </section>
<?php echo '</div>'; endif; ?>



<!--//=====================================================================-->
<?php if( get_query_var('pagename') == 'setting' || isset($wp->query_vars['setting']) ) :
        get_template_part('template-parts/dashboard/main', 'sidebar', $main_sidebar);
        echo '<div class="content-wrapper">';
        ?>
        <section class="content">
            <?php get_template_part('template-parts/dashboard/setting') ?>
        </section>
    <?php echo '</div>'; endif;?>

<!--//=====================================================================-->
<?php if (get_query_var('pagename') == 'inventory-report' || isset($wp->query_vars['inventory-report'])) :
        $main_sidebar['inventory']['active'] = true;
        $main_sidebar['inventory']['option']['report']['active'] = true;
        get_template_part('template-parts/dashboard/main', 'sidebar', $main_sidebar);
        echo '<div class="content-wrapper">';
        ?>
        <section class="content">
            <?php get_template_part('template-parts/dashboard/inventory','report') ?>
        </section>
    <?php echo '</div>'; endif; ?>
    <!-- ./main content -->

