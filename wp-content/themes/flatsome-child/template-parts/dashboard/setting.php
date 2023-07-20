<?php

    $company_name           =   get_option('web_company_name') ?? '';
    $company_email          =   get_option('web_company_email') ?? '';
    $company_phone          =   get_option('web_company_phone') ?? '';
    $company_url_logo       =   get_option('web_company_url_logo') ?? '';
    $company_address        =   get_option('web_company_address') ?? '';

    $base_url_address       =   get_option('tiki_base_url_address') ?? '';
    $base_url_tnsl          =   get_option('tiki_base_url_tnsl') ?? '';
    $client_id              =   get_option('tiki_client_id') ?? '';
    $secret_key             =   get_option('tiki_secret_key') ?? '';
    $secret_client          =   get_option('tiki_secret_client') ?? '';
    $access_token           =   get_option('tiki_access_token') ?? '';

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
                                <button type="button" class="btn btn-primary">Save change</button>
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
                    </div>
                    <div class="card-footer">
                        <button onclick="generate_database_address()" class="btn btn-primary">General Database Address</button>
                    </div>
                </div>
                <div class="card" id="card_setting_tiki_api">
                    <div class="card-header">
                        <h3>Tiki API</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Base url address</label>
                            <input id="base_url_address" name="base_url_address" type="text" class="form-control" value="<?php echo $base_url_address?>"/>
                        </div>
                        <div class="form-group">
                            <label>Base url TNSL</label>
                            <input id="base_url_tnsl" name="base_url_tnsl" type="text" class="form-control" value="<?php echo $base_url_tnsl?>"/>
                        </div>
                        <div class="form-group">
                            <label>Client ID</label>
                            <input id="client_id" name="client_id" type="text" class="form-control" value="<?php echo $client_id ?>"/>
                        </div>
                        <div class="form-group">
                            <label>Secret Key</label>
                            <input id="secret_key" name="secret_key" type="text" class="form-control" value="<?php echo $secret_key?>"/>
                        </div>
                        <div class="form-group">
                            <label>Secret Client</label>
                            <input id="secret_client" name="secret_client" type="url" class="form-control" value="<?php echo $secret_client?>"/>
                        </div>
                        <div class="form-group">
                            <label>Access Token</label>
                            <input id="access_token" name="access_token" type="password" class="form-control" value="<?php echo $access_token?>" disabled/>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button onclick="save_setting_tiki_api()" class="btn btn-primary">Save</button>
                        <button onclick="get_access_token_tiki()" class="btn btn-info">Get Token</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>