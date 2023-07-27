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
                            Khách hàng
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/admin-dashboard/customer-list" class="nav-link <?php echo get_query_var('pagename') == 'customer-list' ? 'active' : ''; ?>">
                                <i class="fas fa-clipboard-list nav-icon"></i>
                                <p>Danh sách</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/admin-dashboard/customer-witchlist" class="nav-link <?php echo get_query_var('pagename') == 'customer-witchlist' ? 'active' : ''; ?>">
                                <i class="fa fa-heart nav-icon"></i>
                                <p>Yêu thích</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item <?php echo  substr(get_query_var('pagename'),0,5) == 'order' ? 'menu-open menu-is-opening' : ''; ?>">
                    <a href="#" class="nav-link <?php echo  substr(get_query_var('pagename'),0,5) == 'order' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-boxes "></i>
                        <p>
                            Đơn hàng
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/admin-dashboard/order-report" class="nav-link <?php echo get_query_var('pagename') == 'order-report' ? 'active' : ''; ?>">
                                <i class="fa fa-list-alt nav-icon"></i>
                                <p>Thêm đơn hàng mới</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/admin-dashboard/order-list" class="nav-link <?php echo get_query_var('pagename') == 'order-list' ? 'active' : ''; ?>">
                                <i class="fas fa-clipboard-list nav-icon"></i>
                                <p>Danh sách đơn hàng</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/admin-dashboard/order-report" class="nav-link <?php echo get_query_var('pagename') == 'order-report' ? 'active' : ''; ?>">
                                <i class="fa fa-list-alt nav-icon"></i>
                                <p>Báo cáo</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item <?php echo  substr(get_query_var('pagename'),0,8) == 'customer' ? 'menu-open menu-is-opening' : ''; ?>">
                    <a href="#" class="nav-link <?php echo substr(get_query_var('pagename'),0,8) == 'customer' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-address-book"></i>
                        <p>
                            Tồn kho
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/admin-dashboard/customer-list" class="nav-link <?php echo get_query_var('pagename') == 'customer-list' ? 'active' : ''; ?>">
                                <i class="fas fa-clipboard-list nav-icon"></i>
                                <p>TO</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/admin-dashboard/customer-witchlist" class="nav-link <?php echo get_query_var('pagename') == 'customer-witchlist' ? 'active' : ''; ?>">
                                <i class="fa fa-heart nav-icon"></i>
                                <p>SO</p>
                            </a>
                        </li>
                    </ul>
                </li>
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