<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Setting</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/admin-dashboard">Dashboard</a></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-5 col-sm-3">
                <div class="nav flex-column nav-tabs h-100" id="vert-tabs-tab" role="tablist" aria-orientation="vertical">
                <a class="nav-link active" id="setting_company-tab" data-toggle="pill" href="#setting_company" role="tab" aria-controls="setting_company" aria-selected="true">Company info</a>
                <a class="nav-link" id="setting_tiki-tab" data-toggle="pill" href="#setting_tiki" role="tab" aria-controls="setting_tiki" aria-selected="false">Tiki now</a>
                <a class="nav-link" id="setting_create_table-tab" data-toggle="pill" href="#setting_create_table" role="tab" aria-controls="setting_create_table" aria-selected="false">Create table</a>
                <a class="nav-link" id="setting_admin-dashboard-tab" data-toggle="pill" href="#setting_admin_dashboard" role="tab" aria-controls="setting_admin_dashboard" aria-selected="false">Admin dashboard</a>
                </div>
            </div>
            <div class="col-7 col-sm-9">
                <div class="tab-content" id="vert-tabs-tabContent">
                    <div class="tab-pane text-left fade active show" id="setting_company" role="tabpanel" aria-labelledby="setting_company-tab">
                        <?php get_template_part('template-parts/dashboard/setting','company')?>
                    </div>
                    <div class="tab-pane text-left fade" id="setting_tiki" role="tabpanel" aria-labelledby="setting_tiki-tab">
                        <?php get_template_part('template-parts/dashboard/setting-tiki','api')?>
                    </div>
                    <div class="tab-pane text-left fade" id="setting_create_table" role="tabpanel" aria-labelledby="setting_create_table-tab">
                        <?php get_template_part('template-parts/dashboard/setting-create-table','address')?>
                    </div>
                    <div class="tab-pane text-left fade" id="setting_admin_dashboard" role="tabpanel" aria-labelledby="setting_admin_dashboard-tab">
                        <?php get_template_part('template-parts/dashboard/setting-admin','dashboard')?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>