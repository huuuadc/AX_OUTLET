<?php
/**
 * Template name: Page - Admin Dashboard
 *
 * @package          IT Project
 * @admin_lte 3.2
 */

date_default_timezone_set('Asia/Ho_Chi_Minh');

?>


    <!-- Dashboard header -->
    <?php get_template_part('template-parts/dashboard/header') ?>
    <!-- ./dashboard header -->

    <?php
        //Check user login
        global $wp;
        if (!is_user_logged_in()){
            get_header();
            echo '<div class="flex-grow justify-center text-center">';
            echo '<a class="w-25 btn btn-primary m-lg-5 m-sm-2 no-wrap" href="/wp-login.php?redirect_to='. home_url( $wp->request ) .'">Đăng nhập ngay</a>';
            echo '</div>';
            get_footer();
            die;
        }

        //Check permission user login
        if (!current_user_can('admin_dashboard')){
            get_header();
            echo '<div class="flex-grow justify-center text-center" >';
            echo '<h3 class="text-danger pt-3">Cảnh báo!. Bạn không có quyền truy cập trang này.</h3>';
            echo '<h3 class="text-danger pb-3">Xin liên hệ quản lý trang web</h3>';
            echo '<a class="w-25 btn btn-primary m-lg-5 m-sm-2 no-wrap" href="/">Trang Chủ</a>';
            echo '</div>';
            get_footer();
            die;
        }
    ?>

    <!-- Content Wrapper. Contains page content -->
    <?php get_template_part('template-parts/dashboard/routers') ?>
    <!-- /.content-wrapper -->

    <!-- Dashboard footer -->
    <?php get_template_part('template-parts/dashboard/footer') ?>
    <!-- ./dashboard footer -->



