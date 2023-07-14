<?php
/**
 * Template name: Page - Admin Dashboard
 *
 * @package          IT Project
 * @admin_lte 3.2
 */
?>


    <!-- Dashboard header -->
    <?php get_template_part('template-parts/dashboard/header') ?>
    <!-- ./dashboard header -->

    <?php
        //Check login
        global $wp;
        if (!is_user_logged_in()){
            get_header();
            echo '<div style="
                        height: 20vh;
                        display: flex;
                        text-align: center;
                        justify-content: center;
                        align-items: center;">';
            echo '<a style="width: 20%" href="/wp-login.php?redirect_to='. home_url( $wp->request ) .'"><button type="button" class="btn btn-block btn-primary">Login Now</button></a>';
            echo '</div>';
            get_footer();
            die;
        }
    ?>

    <!-- Main Sidebar Container -->
    <?php get_template_part('template-parts/dashboard/main','sidebar') ?>
    <!-- ./main Sidebar Container -->

    <!-- Navbar header -->
    <?php get_template_part('template-parts/dashboard/navbar','header') ?>
    <!-- /.navbar header-->

    <!-- Content Wrapper. Contains page content -->
    <?php get_template_part('template-parts/dashboard/container','wrapper') ?>
    <!-- /.content-wrapper -->

    <!-- Dashboard footer -->
    <?php get_template_part('template-parts/dashboard/footer') ?>
    <!-- ./dashboard footer -->



