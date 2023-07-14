<footer class="main-footer">
    <strong>Copyright &copy; 2023 <a href="https://dafc.com.vn" target="_blank">IT Project DAFC</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> 1.0.1
    </div>
</footer>

</div>
<footer>
    <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri().'/assets/dashboard/jquery/jquery.min.js' ?>"></script>
    <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri().'/assets/dashboard/jquery-ui/jquery-ui.min.js' ?>"></script>
    <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri().'/assets/dashboard/bootstrap/js/bootstrap.bundle.min.js' ?>"></script>
    <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri().'/assets/dashboard/chart.js/Chart.min.js' ?>"></script>
    <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri().'/assets/dashboard/sparklines/sparkline.js' ?>"></script>
    <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri().'/assets/dashboard/jqvmap/jquery.vmap.min.js' ?>"></script>
    <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri().'/assets/dashboard/jqvmap/maps/jquery.vmap.usa.js' ?>"></script>
    <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri().'/assets/dashboard/jquery-knob/jquery.knob.min.js' ?>"></script>
    <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri().'/assets/dashboard/moment/moment.min.js' ?>"></script>
    <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri().'/assets/dashboard/daterangepicker/daterangepicker.js' ?>"></script>
    <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri().'/assets/dashboard/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js' ?>"></script>
    <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri().'/assets/dashboard/summernote/summernote-bs4.min.js' ?>"></script>
    <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri().'/assets/dashboard/overlayScrollbars/js/jquery.overlayScrollbars.min.js' ?>"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js"></script>
    <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri().'/assets/dashboard/bootstrap-slider/bootstrap-slider.min.js' ?>"></script>
    <script>
        let toggleSwitch = document.querySelector('.theme-switch input[type="checkbox"]');
        let currentTheme = localStorage.getItem('theme');
        let mainHeader = document.querySelector('.main-header');

        if (currentTheme) {
            if (currentTheme === 'dark') {
                if (!document.body.classList.contains('dark-mode')) {
                    document.body.classList.add("dark-mode");
                }
                if (mainHeader.classList.contains('navbar-light')) {
                    mainHeader.classList.add('navbar-dark');
                    mainHeader.classList.remove('navbar-light');
                }
                toggleSwitch.checked = true;
            }
        }

        function switchTheme(e) {
            if (e.target.checked) {
                if (!document.body.classList.contains('dark-mode')) {
                    document.body.classList.add("dark-mode");
                }
                if (mainHeader.classList.contains('navbar-light')) {
                    mainHeader.classList.add('navbar-dark');
                    mainHeader.classList.remove('navbar-light');
                }
                localStorage.setItem('theme', 'dark');
            } else {
                if (document.body.classList.contains('dark-mode')) {
                    document.body.classList.remove("dark-mode");
                }
                if (mainHeader.classList.contains('navbar-dark')) {
                    mainHeader.classList.add('navbar-light');
                    mainHeader.classList.remove('navbar-dark');
                }
                localStorage.setItem('theme', 'light');
            }
        }

        toggleSwitch.addEventListener('change', switchTheme, false);
    </script>
</footer>
</body>
</html>