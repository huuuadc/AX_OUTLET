<?php

global $wpdb;

$company_name = get_option('web_company_name') ?? '';
$company_code = get_option('web_company_code') ?? '';
$company_email = get_option('web_company_email') ?? '';
$company_phone = get_option('web_company_phone') ?? '';
$company_url_logo = get_option('web_company_url_logo') ?? '';
$company_country = get_option('web_company_country') ?? '';
$company_city = get_option('web_company_city') ?? '';
$company_district = get_option('web_company_district') ?? '';
$company_ward = get_option('web_company_ward') ?? '';
$company_address = get_option('web_company_address') ?? '';

$arg_city = '';
$arg_districts = '';
$arg_wards = '';

$data_city = $wpdb->get_results("Select province_id,tiki_code,province_name 
                                        from {$wpdb->prefix}woocommerce_province");
foreach ($data_city as $value){
    $selected = $company_city == $value->tiki_code ? 'selected':'';
    $arg_city = $arg_city."<option {$selected}  value='{$value->tiki_code}'>{$value->tiki_code} - {$value->province_name}</option>";
}

$company_district = $company_district ?? $data_city['0']->tiki_code;

$data_districts = $wpdb->get_results("Select district_id,tiki_code,district_name 
                                        from {$wpdb->prefix}woocommerce_district where left(tiki_code,5) = left('{$company_district}',5)");
foreach ($data_districts as $value){
    $selected = $company_district == $value->tiki_code ? 'selected':'';
    $arg_districts = $arg_districts."<option {$selected}  value='{$value->tiki_code}'>{$value->tiki_code} - {$value->district_name}</option>";
}

$company_ward = $company_ward ?? $data_districts['0']->tiki_code;

$data_wards = $wpdb->get_results("Select ward_id,tiki_code,ward_name 
                                        from {$wpdb->prefix}woocommerce_ward  where left(tiki_code,8) =  left('{$company_ward}',8)");
foreach ($data_wards as $value){
    $selected = $company_ward == $value->tiki_code ? 'selected':'';
    $arg_wards = $arg_wards."<option {$selected}  value='{$value->tiki_code}'>{$value->tiki_code} - {$value->ward_name}</option>";
}



?>

<div class="card" id="card_information_company">
    <div class="card-header">
        <h3>Information company</h3>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label for="company_name">Company name</label>
            <input type="text" name="company_name" class="form-control" id="company_name" placeholder="Enter company name" value="<?php echo $company_name?>">
        </div>
        <div class="form-group">
            <label for="company_code">Company Code</label>
            <input type="text" name="company_code" class="form-control" id="company_code" placeholder="Enter company code" value="<?php echo $company_code?>">
        </div>
        <div class="form-group">
            <label for="company_email">Email address</label>
            <input type="email" name="company_email" class="form-control" id="company_email" placeholder="Enter email" value="<?php echo $company_email?>">
        </div>
        <div class="form-group">
            <label for="company_phone">Company Phone</label>
            <input type="text" name="company_phone" class="form-control" id="company_phone" placeholder="Enter company phone" value="<?php echo $company_phone?>">
        </div>
        <div class="form-group">
            <label>Company country</label>
            <select id="company_country" class="form-control " style="width: 100%;">
                <option selected value="VN">VN - Việt Nam</option>
            </select>
        </div>
        <div class="form-group">
            <label for="company_city">company city</label>
            <select id="company_city" class="form-control " style="width: 100%;">
                <?php echo $arg_city?>
            </select>
        </div>
        <div class="form-group">
            <label for="company_district">company district</label>
            <select id="company_district" class="form-control " style="width: 100%;">
                <?php echo $arg_districts?>
            </select>
        </div>
        <div class="form-group">
            <label for="company_ward">company ward</label>
            <select id="company_ward" class="form-control " style="width: 100%;">
                <?php echo $arg_wards?>
            </select>
        </div>
        <div class="form-group">
            <label for="company_">company address</label>
            <input type="text" name="company_address" class="form-control" id="company_address" placeholder="Enter company address" value="<?php echo $company_address?>">
        </div>

    </div>
    <!-- /.card-body -->

    <div class="card-footer">
        <button type="button" onclick="save_company_info()" class="btn btn-primary">Save</button>
    </div>
</div>