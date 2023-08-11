<?php

if ( ! $args ) {
    return;
}

?>

<!-- Main Sidebar Container -->
<aside class="main-sidebar main-sidebar-custom sidebar-dark-primary elevation-4">
    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="https://png.pngtree.com/png-clipart/20210311/original/pngtree-cute-ninja-mascot-logo-png-image_6051280.png" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">
                    <?php global $current_user;
                    wp_get_current_user();
                    echo 'Xin chào ' . $current_user->display_name . "\n";
                    ?>
                </a>
            </div>
        </div>
        <!-- Sidebar Menu -->
        <nav class="mt-2 flex-fill">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->

<?php

foreach ($args as $item):

    if (count($item['option']) > 0):
        ?>
        <li class="nav-item <?php echo  $item['active'] ? 'menu-open menu-is-opening' : ''; ?>">
            <a href="#" class="nav-link <?php echo $item['active'] ? 'active' : ''; ?>">
                <i class="nav-icon <?php echo $item['icon']?>"></i>
                <p><?php echo $item['title']?><i class="fas fa-angle-left right"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <?php foreach ($item['option'] as $sub_item): ?>
                <li class="nav-item">
                    <a href="<?php echo $sub_item['slug']?>" class="nav-link <?php echo $sub_item['active'] ? 'active' : ''; ?>">
                        <i class="nav-icon <?php echo $sub_item['icon']?>"></i>
                        <p><?php echo $sub_item['title']?></p>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </li>

<?php

    else:

        ?>
        <li class="nav-item">
            <a href="<?php echo $item['slug']?>" class="nav-link <?php echo $item['active'] ? 'active' : ''; ?>">
                <i class="nav-icon <?php echo $item['icon']?>"></i><p><?php echo $item['title']?></p>
            </a>
        </li>

<?php
    endif;
endforeach;
?>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
    <div class="sidebar-custom p-3">
        <a href="/admin-dashboard/setting" class="btn btn-link"><i class="fas fa-cogs"></i></a>
        <a href="#" class="btn btn-secondary hide-on-collapse pos-right">Cứu tôi với!</a>
    </div>
</aside>