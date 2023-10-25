<?php

use \OMS\Tiktok_Api;
global $wpdb;

//check_permission admin dashboard order
if (!current_user_can('admin_dashboard_setting')):
    user_permission_failed_content();
else:
    $tiktok_api = new Tiktok_Api();

    $tiktok_client_secret   = $tiktok_api->get_client_secret();

    //Set code auth
    if(isset( $_GET['code']) && isset( $_GET['state']) && $_GET['state'] === $tiktok_client_secret)
    {
        if(!add_option('tiktok_code_auth',$_GET['code'] , '','no')){
            update_option('tiktok_code_auth',$_GET['code'] , 'no');
        }
    }

    $tiktok_auth_url        = $tiktok_api->get_auth_url();
    $tiktok_token_url       = $tiktok_api->get_token_url();
    $tiktok_api_url         = $tiktok_api->get_api_url();
    $tiktok_code_auth       = $tiktok_api->get_code_auth();
    $tiktok_app_key         = $tiktok_api->get_app_key();
    $tiktok_app_secret      = $tiktok_api->get_app_secret();
    $tiktok_access_token    = $tiktok_api->get_access_token();
    $tiktok_refresh_token   = $tiktok_api->get_refresh_token();
    $tiktok_version         = $tiktok_api->get_version();
    $tiktok_shop_id         = $tiktok_api->get_shop_id();
    $tiktok_shop_cipher     = $tiktok_api->get_shop_cipher();

?>

<div class="card" id="card_tiktok_api">
    <div class="card-header">
        <h3>Tiktok api setting</h3>
    </div>
    <!-- .card-body -->
    <div class="card-body">

        <div class="form-group">
            <label for="tiktok_version">Version</label>
            <input type="text" name="tiktok_version" class="form-control" id="tiktok_version" placeholder="Version" value="<?php echo $tiktok_version?>"/>
        </div>

        <hr>

        <div class="form-group">
            <label for="tiktok_auth_url">Auth url</label>
            <input type="text" name="tiktok_auth_url" class="form-control" id="tiktok_auth_url" placeholder="Auth url" value="<?php echo $tiktok_auth_url?>"/>
        </div>

        <div class="form-group">
            <label for="tiktok_client_secret">Client secret</label>
            <input type="text" name="tiktok_client_secret" class="form-control" id="tiktok_client_secret" placeholder="Client secret" value="<?php echo $tiktok_client_secret?>"/>
        </div>

        <div class="form-group">
            <label for="tiktok_app_key">App Key</label>
            <input type="text" name="tiktok_app_key" class="form-control" id="tiktok_app_key" placeholder="App key" value="<?php echo $tiktok_app_key?>"/>
        </div>

        <div class="form-group">
            <label for="tiktok_app_secret">App Secret</label>
            <input type="text" name="tiktok_app_secret" class="form-control" id="tiktok_app_secret" placeholder="App secret" value="<?php echo $tiktok_app_secret?>"/>
        </div>

        <div class="form-group">
            <label for="tiktok_code_auth">Code auth</label>
            <input type="text" name="tiktok_code_auth" class="form-control" id="tiktok_code_auth" placeholder="Code auth" value="<?php echo $tiktok_code_auth?>" disabled/>
        </div>

        <a type="button" href="<?php echo $tiktok_auth_url."/oauth/authorize?app_key=".$tiktok_app_key."&state=".$tiktok_client_secret ?>" class="btn btn-primary">Get Code Auth App</a>

        <hr>

        <div class="form-group">
            <label for="tiktok_token_url">Token url</label>
            <input type="text" name="tiktok_token_url" class="form-control" id="tiktok_token_url" placeholder="Token url" value="<?php echo $tiktok_token_url?>"/>
        </div>
        <div class="form-group">
            <label for="tiktok_access_token">Access token</label>
            <input disabled type="text" name="tiktok_access_token" class="form-control" id="tiktok_access_token" placeholder="Token url" value="<?php echo $tiktok_access_token?>"/>
        </div>
        <div class="form-group">
            <label for="tiktok_refresh_token">Refresh token</label>
            <input disabled type="text" name="tiktok_refresh_token" class="form-control" id="tiktok_refresh_token" placeholder="Token url" value="<?php echo $tiktok_refresh_token?>"/>
        </div>

        <button type="button" onclick="get_tiktok_token('by_auth_code')" class="btn btn-primary">Get token</button>
        <button type="button" onclick="get_tiktok_token('by_refresh_token')" class="btn btn-primary">Refresh token</button>

        <hr>

        <div class="form-group">
            <label for="tiktok_api_url">Api url</label>
            <input type="text" name="tiktok_api_url" class="form-control" id="tiktok_api_url" placeholder="Api url" value="<?php echo $tiktok_api_url?>"/>
        </div>
        <div class="form-group">
            <label for="tiktok_shop_id">Shop id</label>
            <input disabled type="text" name="tiktok_shop_id" class="form-control" id="tiktok_shop_id" placeholder="Shop id" value="<?php echo $tiktok_shop_id?>"/>
        </div>
        <div class="form-group">
            <label for="tiktok_shop_cipher">Shop cipher</label>
            <input disabled type="text" name="tiktok_shop_cipher" class="form-control" id="tiktok_shop_cipher" placeholder="Shop cipher" value="<?php echo $tiktok_shop_cipher?>"/>
        </div>
        <button type="button" onclick="get_tiktok_authorized_shop()" class="btn btn-primary">Authorized shop</button>

    </div>
    <!-- /.card-body -->

    <div class="card-footer">
        <button type="button" onclick="save_tiktok_api_setting()" class="btn btn-primary">Save change</button>
    </div>
</div>

<?php endif;