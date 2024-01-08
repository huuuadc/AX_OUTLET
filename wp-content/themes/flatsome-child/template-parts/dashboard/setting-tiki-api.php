<?php

$base_url_address       =   get_option('tiki_base_url_address') ?? '';
$base_url_tnsl          =   get_option('tiki_base_url_tnsl') ?? '';
$client_id              =   get_option('tiki_client_id') ?? '';
$secret_key             =   get_option('tiki_secret_key') ?? '';
$secret_client          =   get_option('tiki_secret_client') ?? '';
$access_token           =   get_option('tiki_access_token') ?? '';
$path_webhook           =   get_option('tiki_path_webhook') ?? '';
$shop_id                =   get_option('tiki_shop_id') ?? '';
$platform           =   get_option('tiki_platform') ?? '';

?>
<div class="card" id="card_setting_tiki_api">
    <div class="card-header">
        <h3>Tiki API</h3>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label for="base_url_address">Base url address</label>
            <input id="base_url_address" name="base_url_address" type="text" class="form-control" value="<?php echo $base_url_address?>"/>
        </div>
        <div class="form-group">
            <label for="base_url_tnsl">Base url TNSL</label>
            <input id="base_url_tnsl" name="base_url_tnsl" type="text" class="form-control" value="<?php echo $base_url_tnsl?>"/>
        </div>
        <div class="form-group">
            <label for="client_id">Client ID</label>
            <input id="client_id" name="client_id" type="text" class="form-control" value="<?php echo $client_id ?>"/>
        </div>
        <div class="form-group">
            <label for="secret_key">Secret Key</label>
            <input id="secret_key" name="secret_key" type="text" class="form-control" value="<?php echo $secret_key?>"/>
        </div>
        <div class="form-group">
            <label for="access_token">Access Token</label>
            <input id="access_token" name="access_token" type="password" class="form-control" value="<?php echo $access_token?>" disabled/>
        </div>
        <hr>
        <div class="form-group">
            <label for="shop_id">Shop ID</label>
            <input id="shop_id" name="shop_id" type="text" class="form-control" value="<?php echo $shop_id?>"/>
        </div>
        <div class="form-group">
            <label for="platform">Platform</label>
            <input id="platform" name="platform" type="text" class="form-control" value="<?php echo $platform?>"/>
        </div>
        <div class="form-group">
            <label for="secret_client">Secret Client</label>
            <input id="secret_client" name="secret_client" type="url" class="form-control" value="<?php echo $secret_client?>"/>
        </div>
        <div class="form-group">
            <label for="path_webhook">Path webhook</label>
            <input id="path_webhook" name="path_webhook" type="text" class="form-control" value="<?php echo $path_webhook?>"/>
        </div>
        <button onclick="tiki_action_ajax('register_webhook')" class="btn btn-primary">Register webhook</button>


    </div>
    <div class="card-footer">
        <button onclick="save_setting_tiki_api()" class="btn btn-primary">Save</button>
        <button onclick="tiki_action_ajax('get_token')" class="btn btn-info">Get Token</button>
    </div>
</div>