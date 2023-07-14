
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Sidebar -->
    <div class="sidebar">
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

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="#" class="nav-link <?php echo get_query_var('pagename') == 'admin-dashboard' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                <li class="nav-item <?php echo  substr(get_query_var('pagename'),0,8) == 'customer' ? 'menu-open menu-is-opening' : ''; ?>">
                    <a href="#" class="nav-link <?php echo substr(get_query_var('pagename'),0,8) == 'customer' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-address-book"></i>
                        <p>
                            Customers
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/admin-dashboard/customer-list" class="nav-link <?php echo get_query_var('pagename') == 'customer-list' ? 'active' : ''; ?>">
                                <i class="fas fa-clipboard-list nav-icon"></i>
                                <p>List</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/admin-dashboard/customer-witchlist" class="nav-link <?php echo get_query_var('pagename') == 'customer-witchlist' ? 'active' : ''; ?>">
                                <i class="fa fa-heart nav-icon"></i>
                                <p>Witchlist</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item <?php echo  substr(get_query_var('pagename'),0,5) == 'order' ? 'menu-open menu-is-opening' : ''; ?>">
                    <a href="#" class="nav-link <?php echo  substr(get_query_var('pagename'),0,5) == 'order' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-boxes "></i>
                        <p>
                            Orders
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/admin-dashboard/order-list" class="nav-link <?php echo get_query_var('pagename') == 'order-list' ? 'active' : ''; ?>">
                                <i class="fas fa-clipboard-list nav-icon"></i>
                                <p>List Orders</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/admin-dashboard/order-report" class="nav-link <?php echo get_query_var('pagename') == 'order-report' ? 'active' : ''; ?>">
                                <i class="fa fa-list-alt nav-icon"></i>
                                <p>Report Order</p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>