<?php

    $company_name = 'AX Outlet';
    $company_email = 'huuuadc@gmail.com';
    $company_phone = '0326473067';
    $company_url_logo = 'https://devdafc.com.vn/logo.png';
    $company_address = '72-74 Nguyễn Thị Minh Khai, Phường Võ Thị Sáu, Quận 3, Tp Hồ Chí Minh';

?>

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
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Information company</h3>
                    </div>
                    <div class="card-body">
                        <form action="/">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="companyName">Company name</label>
                                    <input type="text" name="companyName" class="form-control" id="companyName" placeholder="Enter company name" value="<?php echo $company_name?>">
                                </div>
                                <div class="form-group">
                                    <label for="companyEmail">Email address</label>
                                    <input type="email" name="companyEmail" class="form-control" id="companyEmail" placeholder="Enter email" value="<?php echo $company_email?>">
                                </div>
                                <div class="form-group">
                                    <label for="companyPhone">Company Phone</label>
                                    <input type="text" name="companyPhone" class="form-control" id="companyPhone" placeholder="Enter company phone" value="<?php echo $company_phone?>">
                                </div>
                                <div class="form-group">
                                    <label for="companyAddress">Company Address</label>
                                    <input type="text" name="companyAddress" class="form-control" id="companyAddress" placeholder="Enter company address" value="<?php echo $company_address?>">
                                </div>
                                <div class="form-group">
                                    <label for="companyLogo">Logo Company</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="companyLogo">
                                            <label class="custom-file-label" for="companyLogo">Choose file</label>
                                        </div>
                                        <div class="input-group-append">
                                            <span class="input-group-text">Upload</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="exampleCheck1">
                                    <label class="form-check-label" for="exampleCheck1">Check me out</label>
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="save change" class="btn btn-primary">Save change</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card" id="card_create_table" >
                    <div class="card-header">
                        <h3>Create Table</h3>
                    </div>
                    <div class="card-body">
                        <button onclick="generate_database_address()" class="btn btn-primary">General Database Address</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    function generate_database_address(){
        $.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php');?>',
            data:{
                action: 'generate_database_address'
            },
            beforeSend: function (){
                $('#card_create_table').append('<div class="overlay"><i class="fas fa-2x fa-sync-alt"></i></div>')

            },
            success: function (data){
                const rep = JSON.parse(data);
                console.log(rep);
            },
            complete: function (){
                $('#card_create_table>.overlay').remove()
            },
            error: function(errorThrown){

                console.log("ERROR",errorThrown)

            }
        })
    }
</script>