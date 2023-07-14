
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <?php if(  get_query_var('pagename') == 'order-list' && !isset($_GET['order_id'])) {?>
    <section class="content">
        <?php get_template_part('template-parts/dashboard/order','list') ?>
    </section>
    <?php }?>
    <?php if( get_query_var('pagename') == 'order-list' && isset($_GET['order_id']) ) {
        $order_id = $_GET['order_id'];
        ?>
        <section class="content">
            <?php get_template_part('template-parts/dashboard/order','detail',$order_id) ?>
        </section>
    <?php }?>
    <!-- ./main content -->


</div>