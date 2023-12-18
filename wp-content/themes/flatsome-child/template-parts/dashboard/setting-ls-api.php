<?php

$config_1                   =   get_option('wc_settings_tab_config_name') ?? '';
$base_url_1                 =   get_option('wc_settings_tab_ls_api_url') ?? '';
$username_1                 =   get_option('wc_settings_tab_ls_api_username') ?? '';
$password_1                 =   get_option('wc_settings_tab_ls_api_password') ?? '';
$location_from_1            =   get_option('wc_settings_tab_ls_location_code') ?? '';
$location_to_1              =   get_option('wc_settings_tab_ls_location_code2') ?? '';
$access_token_1             =   get_option('wc_settings_tab_ls_access_token') ?? '';

$config_2                   =   get_option('wc_settings_tab_config_name_2') ?? '';
$base_url_2                 =   get_option('wc_settings_tab_ls_api_url_2') ?? '';
$username_2                 =   get_option('wc_settings_tab_ls_api_username_2') ?? '';
$password_2                 =   get_option('wc_settings_tab_ls_api_password_2') ?? '';
$location_from_2            =   get_option('wc_settings_tab_ls_location_code_2') ?? '';
$location_to_2              =   get_option('wc_settings_tab_ls_location_code2_2') ?? '';
$access_token_2             =   get_option('wc_settings_tab_ls_access_token_2') ?? '';

?>
<div class="card" id="save_setting_ls_retail">
    <div class="card-header">
        <h3>LS Retail API</h3>
    </div>
    <div class="card-body">
        <h4>Config 1</h4>
        <div class="form-group">
            <label for="wc_settings_tab_config_name">Config name</label>
            <input id="wc_settings_tab_config_name" name="wc_settings_tab_config_name" type="text" class="form-control" value="<?php echo $config_1?>"/>
        </div>
        <div class="form-group">
            <label for="wc_settings_tab_ls_api_url">Base url 1</label>
            <input id="wc_settings_tab_ls_api_url" name="wc_settings_tab_ls_api_url" type="text" class="form-control" value="<?php echo $base_url_1?>"/>
        </div>
        <div class="form-group">
            <label for="wc_settings_tab_ls_api_username">Username 1</label>
            <input id="wc_settings_tab_ls_api_username" name="wc_settings_tab_ls_api_username" type="text" class="form-control" value="<?php echo $username_1?>"/>
        </div>
        <div class="form-group">
            <label for="wc_settings_tab_ls_api_password">Password 1</label>
            <input id="wc_settings_tab_ls_api_password" name="wc_settings_tab_ls_api_password" type="text" class="form-control" value="<?php echo $password_1?>"/>
        </div>
        <div class="form-group">
            <label for="wc_settings_tab_ls_location_code">Location code 1</label>
            <input id="wc_settings_tab_ls_location_code" name="wc_settings_tab_ls_location_code" type="text" class="form-control" value="<?php echo $location_from_1?>"/>
        </div>
        <div class="form-group">
            <label for="wc_settings_tab_ls_location_code2">Location code 2 1</label>
            <input id="wc_settings_tab_ls_location_code2" name="wc_settings_tab_ls_location_code2" type="text" class="form-control" value="<?php echo $location_to_1?>"/>
        </div>

        <hr>
        <h4>Config 2</h4>
        <div class="form-group">
            <label for="wc_settings_tab_config_name_2">Config name</label>
            <input id="wc_settings_tab_config_name_2" name="wc_settings_tab_config_name_2" type="text" class="form-control" value="<?php echo $config_2?>"/>
        </div>
        <div class="form-group">
            <label for="wc_settings_tab_ls_api_url_2">Base url 2</label>
            <input id="wc_settings_tab_ls_api_url_2" name="wc_settings_tab_ls_api_url_2" type="text" class="form-control" value="<?php echo $base_url_2?>"/>
        </div>
        <div class="form-group">
            <label for="wc_settings_tab_ls_api_username_2">Username 2</label>
            <input id="wc_settings_tab_ls_api_username_2" name="wc_settings_tab_ls_api_username_2" type="text" class="form-control" value="<?php echo $username_2?>"/>
        </div>
        <div class="form-group">
            <label for="wc_settings_tab_ls_api_password_2">Password 2</label>
            <input id="wc_settings_tab_ls_api_password_2" name="wc_settings_tab_ls_api_password_2" type="text" class="form-control" value="<?php echo $password_2?>"/>
        </div>
        <div class="form-group">
            <label for="wc_settings_tab_ls_location_code_2">Location code 2</label>
            <input id="wc_settings_tab_ls_location_code_2" name="wc_settings_tab_ls_location_code_2" type="text" class="form-control" value="<?php echo $location_from_2?>"/>
        </div>
        <div class="form-group">
            <label for="wc_settings_tab_ls_location_code2_2">Location code 2 2</label>
            <input id="wc_settings_tab_ls_location_code2_2" name="wc_settings_tab_ls_location_code2_2" type="text" class="form-control" value="<?php echo $location_to_2?>"/>
        </div>

    </div>
    <div class="card-footer">
        <button onclick="save_setting_ls_retail()" class="btn btn-primary">Save config</button>
    </div>
</div>