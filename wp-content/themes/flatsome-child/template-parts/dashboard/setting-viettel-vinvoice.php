<?php

$base_url       =   get_option('viettel_base_url') ?? '';
$username          =   get_option('viettel_username') ?? '';
$password              =   get_option('viettel_password') ?? '';
$access_token             =   get_option('viettel_access_token') ?? '';
$refresh_token           =   get_option('viettel_refresh_token') ?? '';

?>
<div class="card" id="card_setting_viettel_vinvoice">
    <div class="card-header">
        <h3>Viettel vinvoice setting</h3>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label for="viettel_base_url">Base url</label>
            <input id="viettel_base_url" name="viettel_base_url" type="text" class="form-control" value="<?php echo $base_url?>"/>
        </div>
        <div class="form-group">
            <label for="viettel_username">Username</label>
            <input id="viettel_username" name="viettel_username" type="text" class="form-control" value="<?php echo $username?>"/>
        </div>
        <div class="form-group">
            <label for="viettel_password">Password</label>
            <input id="viettel_password" name="viettel_password" type="password" class="form-control" value="<?php echo $password ?>"/>
        </div>

        <hr>

        <div class="form-group">
            <label for="viettel_access_token">Access token</label>
            <input disabled id="viettel_access_token" name="viettel_access_token" type="text" class="form-control" value="<?php echo $access_token?>"/>
        </div>
        <div class="form-group">
            <label for="viettel_refresh_token">Refresh token</label>
            <input disabled id="viettel_refresh_token" name="viettel_refresh_token" type="text" class="form-control" value="<?php echo $refresh_token ?>"/>
        </div>
        <button onclick="get_access_token_tiki()" class="btn btn-info">Get Token</button>
        <hr>
    </div>
    <div class="card-footer">
        <button onclick="save_setting_viettel_vinvoice()" class="btn btn-primary">Save setting</button>
    </div>
</div>