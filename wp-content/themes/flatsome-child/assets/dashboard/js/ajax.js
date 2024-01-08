
/**
 *
 */

function generate_database_address(){

    $.ajax({
        type: 'POST',
        url: '/wp-admin/admin-ajax.php',
        data:{
            action: 'generate_database_address'
        },
        beforeSend: function (){
            $('#card_create_table').append('<div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i></div>')

        },
        success: function (data){
            try {
                const rep = JSON.parse(data);
                $(document).Toasts('create', {
                    class: 'bg-success',
                    title: 'Success',
                    body: `Complete generate database and send info`,
                    icon: 'fas fa-info-circle',
                    autohide: true,
                    delay: 10000
                })
            } catch (e){
                $(document).Toasts('create', {
                    class: 'bg-danger',
                    title: 'Danger',
                    body: e,
                    icon: 'fas fa-info-circle',
                    autohide: true,
                    delay: 10000
                })
            }
        },
        complete: function (){
            $('#card_create_table>.overlay').remove()
        },
        error: function(errorThrown){

            console.log("ERROR",errorThrown)

        }
    })
}

/**
 *
 */

function save_setting_tiki_api(){

    let tiki_base_url_address = $("input[name='base_url_address']").val();
    let tiki_base_url_tnsl = $("input[name='base_url_tnsl']").val();
    let tiki_client_id = $("input[name='client_id']").val();
    let tiki_secret_key = $("input[name='secret_key']").val();
    let tiki_secret_client = $("input[name='secret_client']").val();
    let tiki_access_token = $("input[name='access_token']").val();
    let tiki_shop_id = $("input[name='shop_id']").val();
    let tiki_platform = $("input[name='platform']").val();
    let tiki_path_webhook = $("input[name='path_webhook']").val();

    $.ajax({
        type: 'POST',
        url: '/wp-admin/admin-ajax.php',
        data:{
            action: 'save_setting_tiki_api',
            tiki_base_url_address,
            tiki_base_url_tnsl,
            tiki_client_id,
            tiki_secret_key,
            tiki_secret_client,
            tiki_access_token,
            tiki_shop_id,
            tiki_platform,
            tiki_path_webhook,
        },
        beforeSend: function (){
            $('#card_setting_tiki_api').append('<div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i></div>')
        },
        success: function (data){
            const rep = JSON.parse(data);
            $(document).Toasts('create', {
                class: 'bg-success',
                title: 'Success',
                body: `update setting tiki success`,
                icon: 'fas fa-info-circle',
                autohide: true,
                delay: 10000
            })
        },
        complete: function (){
            $('#card_setting_tiki_api>.overlay').remove()
        },
        error: function(e){

            console.log("ERROR",e)

        }
    })
}

/**
 *
 */

function tiki_action_ajax(at = ''){

    if (!confirm('Are you sure create new token?')) return;

    $.ajax({
        type    :   'POST',
        url     :   '/wp-admin/admin-ajax.php',
        data    :   {
                    action  :  'get_access_token_tiki',
                    payload_action : at
        },
        beforeSend  :   function (){
            $('#card_setting_tiki_api').append('<div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i></div>')
        },
        success     :   function (data){
            const rep = JSON.parse(data);
            if(rep.data?.token){
                $("input[name='access_token']").val(rep.data);
                $(document).Toasts('create', {
                    class: 'bg-success',
                    title: 'Success',
                    body: `get access token success`,
                    icon: 'fas fa-info-circle',
                    autohide: true,
                    delay: 10000
                })
            }else {
                $(document).Toasts('create', {
                    class: 'bg-success',
                    title: 'Success',
                    body: `register webhook success`,
                    icon: 'fas fa-info-circle',
                    autohide: true,
                    delay: 10000
                })
            }

        },
        complete    :    function (){
            $('#card_setting_tiki_api>.overlay').remove()
        },
        error       :   function (e){
            console.log("ERROR",e)
        }
    })
}

/**
 *
 * @param id
 * @param status
 */
function send_update_status(id = '', status = ''){

    let commit_note = prompt('Nhận ghi chú')

    if(!commit_note){
        alert("Bạn cần nhập ghi chú trước khi thực hiên thao tác")
        return
    }

    $.ajax({
        type: 'POST',
        url: '/wp-admin/admin-ajax.php',
        data:{
            action: 'post_order_update_status',
            payload_action: 'order_status_' + status,
            order_id: id,
            commit_note

        },
        beforeSend: function (){
            $('#card_orders').append('<div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i></div>')

        },
        success: function (data){
            console.log(data)
            if (isJsonString(data)){
                const rep = JSON.parse(data);
                if (rep.status){

                    let class_status = rep.data.class ?? 'muted';

                    if (rep.data.order_status)  $(`#order_status_${id}`).html(`<span class="badge badge-${class_status}">${rep.data.order_status}</span> `);
                    if (rep.data.tracking_id)   $(`#order_tracking_id_${id}`).html(`<a href="${rep.data.tracking_url}" >${rep.data.tracking_id}</a>`)
                    if (rep.data.shipment_status)   $(`#order_shipment_status_${id}`).html(`<span class="badge">${rep.data.shipment_status}</span>`)
                    if (rep.data.data_status)   $(`#content_order_status`).html(`${rep.data.data_status}`)
                    $(document).Toasts('create', {
                        class: 'bg-success',
                        title: 'Success',
                        body: `Update status: <span class="badge badge-${class_status}">${rep.data.order_status}</span>`,
                        icon: 'fas fa-info-circle',
                        autohide: true,
                        delay: 10000
                    })
                }else {
                    $(document).Toasts('create', {
                        class: 'bg-danger',
                        title: 'update false',
                        body: `${rep.messenger ?? '' }`,
                        icon: 'fas fa-info-circle',
                        autohide: true,
                        delay: 10000
                    })
                }
            }else {
                console.log(data)
                $(document).Toasts('create', {
                    class: 'bg-danger',
                    title: 'Error',
                    body: `${rep.data ?? ''}`,
                    icon: 'fas fa-info-circle',
                    autohide: true,
                    delay: 10000
                })
            }
        },
        complete: function (){
            $('#card_orders>.overlay').remove()
        },
        error: function(errorThrown){

            console.log("ERROR",errorThrown)

        }
    })
}

/**
 *
 * @param id
 * @param status
 */
function send_update_payment(id = ''){

    let commit_note = prompt('Nhận ghi chú')

    if(!commit_note){
        alert("Bạn cần nhập ghi chú trước khi thực hiên thao tác")
        return
    }

    $.ajax({
        type: 'POST',
        url: '/wp-admin/admin-ajax.php',
        data:{
            action: 'post_order_update_payment_status',
            payload_action: 'order_update_payment',
            order_id: id,
            commit_note

        },
        beforeSend: function (){
            $('#card_orders').append('<div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i></div>')

        },
        success: function (data){
            if (isJsonString(data)){
                const rep = JSON.parse(data);
                if (rep.status){

                    let class_status = rep.data.class ?? 'muted';

                    if (rep.data.order_payment_title)  $(`#order_payment_status_${id}`).html(`<span class="badge badge-${class_status}">${rep.data.order_payment_title}</span> `);
                    $(document).Toasts('create', {
                        class: 'bg-success',
                        title: 'Success',
                        body: `Update status: <span class="badge ${class_status}">${rep.data.order_payment_title}</span>`,
                        icon: 'fas fa-info-circle',
                        autohide: true,
                        delay: 10000
                    })
                }else {
                    $(document).Toasts('create', {
                        class: 'bg-danger',
                        title: 'update false',
                        body: `${rep.messenger} <br>${JSON.stringify(rep.data) ?? ''}`,
                        icon: 'fas fa-info-circle',
                        autohide: true,
                        delay: 10000
                    })
                }
            }else {
                $(document).Toasts('create', {
                    class: 'bg-danger',
                    title: 'Error',
                    body: `${rep.data ?? ''}`,
                    icon: 'fas fa-info-circle',
                    autohide: true,
                    delay: 10000
                })
            }
        },
        complete: function (){
            $('#card_orders>.overlay').remove()
        },
        error: function(errorThrown){

            console.log("ERROR",errorThrown)

        }
    })
}


function save_company_info(){

    let web_company_name        =       $('#company_name').val()
    let web_company_code        =       $('#company_code').val()
    let web_company_email       =       $('#company_email').val()
    let web_company_phone       =       $('#company_phone').val()
    let web_company_country     =       $('#company_country').val()
    let web_company_city        =       $('#company_city').val()
    let web_company_district    =       $('#company_district').val()
    let web_company_ward        =       $('#company_ward').val()
    let web_company_address     =       $('#company_address').val()

    if (web_company_code.length > 2 || web_company_code.length < 2)
        return $(document).Toasts('create', {
        class: 'bg-warning',
        title: 'warning',
        body: `Mã công ty không được lớn hoặc nhỏ hơn 2`,
        icon: 'fas fa-info-circle',
        autohide: true,
        delay: 10000
    });

    $.ajax({
        type    :   'POST',
        url     :   '/wp-admin/admin-ajax.php',
        data    :   {
            action  :   'save_company_info',
            web_company_name,
            web_company_code,
            web_company_email,
            web_company_phone,
            web_company_country,
            web_company_city,
            web_company_district,
            web_company_ward,
            web_company_address,
        },
        beforeSend: function (){
            $('#card_information_company').append('<div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i></div>')
        },
        success :   function (data){
            const rep = JSON.parse(data);
            if (rep.status) {
                $(document).Toasts('create', {
                    class: 'bg-success',
                    title: 'Success',
                    body: `Update success`,
                    icon: 'fas fa-info-circle',
                    autohide: true,
                    delay: 10000
                })
            } else {
                $(document).Toasts('create', {
                    class: 'bg-danger',
                    title: 'Danger',
                    body: data,
                    icon: 'fas fa-info-circle',
                    autohide: true,
                    delay: 10000
                })
            }
        },
        complete :  function (){
            $('#card_information_company>.overlay').remove()
        },
        error   :   function (e){
            console.log("ERROR",e)
        }
    })
}


$('#company_city').change(function (){
    let company_city_id = $('#company_city').val()
    $('#company_district').html('');
    $('#company_ward').html('');

    $.ajax({
        type: 'POST',
        url: '/wp-admin/admin-ajax.php',
        data : {
            action: 'get_address_shipping',
            action_payload : 'get_district',
            id: `${company_city_id}`
        },
        success: function (rep){
            let data = JSON.parse(rep);

            $('#company_district').append(`<option value="" >Quận/Huyện</option>`)
            $('#company_ward').append(`<option value="" >Phường/Xã</option>`)

            data.data.district.map((e)=>{
                $('#company_district').append(`<option value="${e.tiki_code}" >${e.tiki_code} - ${e.district_name}</option>`)
            })
            data.data.ward.map((e)=>{
                $('#company_ward').append(`<option value="${e.tiki_code}" >${e.tiki_code} - ${e.ward_name}</option>`)
            })
        },
        error: function (e){
            console.log(e)
        }

    })

})


$('#company_district').change(function (){
    let company_district_id = $('#company_district').val()
    $('#company_ward').html('');

    $.ajax({
        type: 'POST',
        url: '/wp-admin/admin-ajax.php',
        data : {
            action: 'get_address_shipping',
            action_payload : 'get_ward',
            id: `${company_district_id}`
        },
        success: function (rep){
            let data = JSON.parse(rep);
            data.data.map((e)=>{
                $('#company_ward').append(`<option value="${e.tiki_code}" >${e.tiki_code} - ${e.ward_name}</option>`)
            });
        },
        error: function (e){
            console.log(e)
        }

    })

})


function save_admin_dashboard_setting(){

    let item_in_page = $('#item_in_page').val()
    let item_fee_ship = $('#item_fee_ship').val()
    let footer_print_shipment = $('#footer_print_shipment').val()
    let product_return_policy = $('#product_return_policy').val()
    // let footer_print_shipment = ''
    // let product_return_policy = ''
    let member_card_guest = $('#member_card_guest').val()
    let is_check_stock = $('input[name="is_check_stock"]').is(':checked') ? 'checked': 'nocheck'
    let is_issue_vat = $('input[name="is_issue_vat"]').is(':checked') ? 'checked': 'nocheck'
    let is_sync_platform = $('input[name="is_sync_platform"]').is(':checked') ? 'checked': 'nocheck'

    $.ajax({
        type:   'POST',
        url:    '/wp-admin/admin-ajax.php',
        data:   {
            action: 'save_admin_dashboard_setting',
            item_in_page,
            footer_print_shipment,
            product_return_policy,
            item_fee_ship,
            member_card_guest,
            is_check_stock,
            is_issue_vat,
            is_sync_platform
        },
        beforeSend: function (){
            $('#card_admin_dashboard').append('<div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i></div>')
        },
        success :   function (data){
            const rep = JSON.parse(data);
            if (rep.status) {
                $(document).Toasts('create', {
                    class: 'bg-success',
                    title: 'Success',
                    body: `Update success`,
                    icon: 'fas fa-info-circle',
                    autohide: true,
                    delay: 10000
                })
            } else {
                $(document).Toasts('create', {
                    class: 'bg-danger',
                    title: 'Danger',
                    body: data,
                    icon: 'fas fa-info-circle',
                    autohide: true,
                    delay: 10000
                })
            }
        },
        complete :  function (){
            $('#card_admin_dashboard>.overlay').remove()
        },
        error   :   function (e){
            console.log("ERROR",e)
        }
    })


}


function post_create_shipment(){

    let order_id = $('#item_in_page').val()

    $.ajax({
        type:   'POST',
        url:    '/wp-admin/admin-ajax.php',
        data:   {
            action: 'post_create_shipment',
            action_payload: 'post_create_shipment_to_tiki',
            order_id
        },
        beforeSend: function (){
            $('#card_admin_dashboard').append('<div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i></div>')
        },
        success :   function (data){
            const rep = JSON.parse(data);
            if (rep.status === '200') {
                $(document).Toasts('create', {
                    class: 'bg-success',
                    title: 'Success',
                    body: `Update success`,
                    icon: 'fas fa-info-circle',
                    autohide: true,
                    delay: 10000
                })
            } else {
                $(document).Toasts('create', {
                    class: 'bg-danger',
                    title: 'Danger',
                    body: data,
                    icon: 'fas fa-info-circle',
                    autohide: true,
                    delay: 10000
                })
            }
        },
        complete :  function (){
            $('#card_admin_dashboard>.overlay').remove()
        },
        error   :   function (e){
            console.log("ERROR",e)
        }
    })


}


function run_product_shop_by(action){
    let action_payload = 'action_' + action;
    let last_piece_qty = parseInt($('input[name="last_piece_qty"]').val())
    let present_discount = parseInt($('input[name="present_discount"]').val())
    let checkbox_remove = $('input[name="checkbox_remove"]').is(":checked")
    let product_skus_str = ($('input[name="product_skus"]').val()).replace(/\s+/g, '')
    let product_skus = product_skus_str ?
        product_skus_str.split(",") : []

    if (action === 'sales_special' && ( !present_discount || present_discount <= 0 || present_discount > 99))
        return $(document).Toasts('create', {
            class: 'bg-info',
            title: 'Lỗi',
            body: 'Giá trị phần trăm giảm giá chưa đúng. Giá trị từ 1 đến 99',
            icon: 'fas fa-info-circle',
            autohide: true,
            delay: 10000
        })

    if (action === 'update_check_stock_manager' && product_skus.length <= 0 )
        return $(document).Toasts('create', {
            class: 'bg-info',
            title: 'Lỗi',
            body: 'Bạn phải để mã sku hoặc danh sách sku vào!',
            icon: 'fas fa-info-circle',
            autohide: true,
            delay: 10000
        })

    $.ajax({
        type: 'POST',
        url:  '/wp-admin/admin-ajax.php',
        data:   {
            action: 'run_product_shop_by',
            action_payload,
            last_piece_qty,
            present_discount,
            checkbox_remove,
            product_skus
        },
        beforeSend: function (){
            $('#card_task_scheduler').append('<div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i></div>')
        },
        success :   function (data){
            const rep = JSON.parse(data);
            if (rep.status) {
                $(document).Toasts('create', {
                    class: 'bg-success',
                    title: 'Success',
                    body: `Update success`,
                    icon: 'fas fa-info-circle',
                    autohide: true,
                    delay: 10000
                })
            } else {
                $(document).Toasts('create', {
                    class: 'bg-danger',
                    title: 'Danger',
                    body: data,
                    icon: 'fas fa-info-circle',
                    autohide: true,
                    delay: 10000
                })
            }
        },
        complete :  function (){
            $('#card_task_scheduler>.overlay').remove()
        },
        error   :   function (e){
            console.log("ERROR",e)
        }

    })
}


function post_invoice_ls_retail( orderId = ''){
    let commit_note = prompt('Nhận ghi chú')

    if(!commit_note){
        alert("Bạn cần nhập ghi chú trước khi thực hiên thao tác")
        return
    }

    $.ajax({
        type: 'POST',
        url: '/wp-admin/admin-ajax.php',
        data:{
            action: 'post_invoice_ls_retail',
            payload_action: 'post_invoice_ls_retail',
            order_id: orderId,
            commit_note

        },
        beforeSend: function (){
            $('#card_orders').append('<div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i></div>')

        },
        success: function (data){
            if (isJsonString(data)){
                const rep = JSON.parse(data);
                if (rep.status){
                    if (rep.data.status)  $(`#order_ls_status_${orderId}`).html(`<span class="badge badge-${rep.data.class_status}">${rep.data.status}</span> `);
                    $(document).Toasts('create', {
                        class: 'bg-success',
                        title: 'Success',
                        body: `Posted invoice ls retail`,
                        icon: 'fas fa-info-circle',
                        autohide: true,
                        delay: 10000
                    })
                }else {
                    $(document).Toasts('create', {
                        class: 'bg-danger',
                        title: 'update false',
                        body: `${rep.messenger} <br>${JSON.stringify(rep.data) ?? ''}`,
                        icon: 'fas fa-info-circle',
                        autohide: true,
                        delay: 10000
                    })
                }
            }else {
                $(document).Toasts('create', {
                    class: 'bg-danger',
                    title: 'Error',
                    body: `${rep.data ?? ''}`,
                    icon: 'fas fa-info-circle',
                    autohide: true,
                    delay: 10000
                })
            }
        },
        complete: function (){
            $('#card_orders>.overlay').remove()
        },
        error: function(errorThrown){

            console.log("ERROR",errorThrown)

        }
    })
}


function change_transfer_order(transfer_id = '', payload_action = '',item_id = ''){

    $.ajax({
        type: 'POST',
        url: '/wp-admin/admin-ajax.php',
        data:{
            action: 'change_transfer_order',
            payload_action: payload_action,
            transfer_id,
            item_id
        },
        beforeSend: function (){

            $('#card_table_line').append('<div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i></div>')

        },
        success: function (data){
                $(`#inventory_card_line`).html(`${data}`);
        },
        complete: function (){
            $('#card_table_line>.overlay').remove()
            $('.table_simple_non_line').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": false,
                "info": false,
                "autoWidth": true,
                "responsive": true,
                "language": {
                    "url": '//cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json',
                },
            });
        },
        error: function(errorThrown){
            console.log("ERROR",errorThrown)
        }
    })
}



function transfer_order_add_new(){

    $.ajax({
        type: 'POST',
        url: '/wp-admin/admin-ajax.php',
        data:{
            action: 'transfer_order_add_new',
            payload_action: 'transfer_order_add_new',
        },
        beforeSend: function (){
            $('#card_table_header').append('<div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i></div>')
        },
        success: function (data){
            $(`#inventory_adjustment`).html(`${data}`);
        },
        complete: function (){
            $('.table_simple_non_btn').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": false,
                "info": false,
                "autoWidth": true,
                "responsive": true,
                "language": {
                    "url": '//cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json',
                },
            });
        },
        error: function(errorThrown){
            console.log("ERROR",errorThrown)
        }
    })
}

function transfer_order_import_product(transfer_order_id = ''){
    let file_input = document.getElementById('importProduct')
    if (!file_input)           return  alert("Không thấy được tập tinh!")
    if (!file_input.files)     return  alert("File bạn chọn không đúng định dạng");
    if (!file_input.files[0])  return  alert("Vui lòng chọn file excel để import");
    if (!transfer_order_id)    return  alert("Không có số phiếu");

    let file_import = file_input.files[0]
    let reader = new FileReader();
    reader.onload = function (e){
        let data = e.target.result;
        let workbook = XLSX.read(data,{
            type: 'binary'
        });

        workbook.SheetNames.forEach(function(sheetName) {

            let XL_row_object = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[sheetName]);
            let json_object = JSON.stringify(XL_row_object);
            let data_products = JSON.parse(json_object)
            $.ajax({
                type: 'POST',
                url: '/wp-admin/admin-ajax.php',
                data:{
                    action: 'transfer_order_import_product',
                    payload_action: 'transfer_order_import_product',
                    transfer_id: transfer_order_id,
                    data: data_products
                },
                beforeSend: function (){
                    $('#card_table_line').append('<div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i></div>')
                },
                success: function (data){
                    $(`#inventory_card_line`).html(`${data}`);
                },
                complete: function (){
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                    $('.table_simple_non_line').DataTable({
                        "paging": true,
                        "lengthChange": false,
                        "searching": true,
                        "ordering": false,
                        "info": false,
                        "autoWidth": true,
                        "responsive": true,
                        "language": {
                            "url": '//cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json',
                        },
                    });
                },
                error: function(errorThrown){
                    console.log("ERROR",errorThrown)
                }
            })
        })
    }

    reader.onerror = function(ex) {
        console.log(ex);
    };
    reader.readAsBinaryString(file_import);

}


function get_tiktok_token(action_payload = 'by_auth_code'){

    $.ajax({
        type:   'POST',
        url:    '/wp-admin/admin-ajax.php',
        data:   {
            action: 'get_tiktok_token',
            action_payload,
        },
        beforeSend: function (){
            $('#card_tiktok_api').append('<div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i></div>')
        },
        success :   function (data){
            const rep = JSON.parse(data);
            if (rep.status) {
                $('#tiktok_access_token').val(rep.data.access_token)
                $('#tiktok_refresh_token').val(rep.data.refresh_token)
                $(document).Toasts('create', {
                    class: 'bg-success',
                    title: 'Success',
                    body: `Update success`,
                    icon: 'fas fa-info-circle',
                    autohide: true,
                    delay: 10000
                })
            } else {
                $(document).Toasts('create', {
                    class: 'bg-danger',
                    title: 'Danger',
                    body: data,
                    icon: 'fas fa-info-circle',
                    autohide: true,
                    delay: 10000
                })
            }
        },
        complete :  function (){
            $('#card_tiktok_api>.overlay').remove()
        },
        error   :   function (e){
            console.log("ERROR",e)
        }
    })


}

function save_tiktok_api_setting(){

    let tiktok_auth_url         = $('#tiktok_auth_url').val()
    let tiktok_token_url        = $('#tiktok_token_url').val()
    let tiktok_api_url          = $('#tiktok_api_url').val()
    let tiktok_client_secret    = $('#tiktok_client_secret').val()
    let tiktok_app_key          = $('#tiktok_app_key').val()
    let tiktok_app_secret       = $('#tiktok_app_secret').val()
    let tiktok_version          = $('#tiktok_version').val()

    $.ajax({
        type:   'POST',
        url:    '/wp-admin/admin-ajax.php',
        data:   {
            action: 'save_tiktok_api_setting',
            tiktok_auth_url,
            tiktok_token_url,
            tiktok_api_url,
            tiktok_client_secret,
            tiktok_app_key,
            tiktok_app_secret,
            tiktok_version,
        },
        beforeSend: function (){
            $('#card_tiktok_api').append('<div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i></div>')
        },
        success :   function (data){
            const rep = JSON.parse(data);
            if (rep.status) {
                $(document).Toasts('create', {
                    class: 'bg-success',
                    title: 'Success',
                    body: `Update success`,
                    icon: 'fas fa-info-circle',
                    autohide: true,
                    delay: 10000
                })
            } else {
                $(document).Toasts('create', {
                    class: 'bg-danger',
                    title: 'Danger',
                    body: data,
                    icon: 'fas fa-info-circle',
                    autohide: true,
                    delay: 10000
                })
            }
        },
        complete :  function (){
            $('#card_tiktok_api>.overlay').remove()
        },
        error   :   function (e){
            console.log("ERROR",e)
        }
    })


}


function get_tiktok_authorized_shop(){

    $.ajax({
        type:   'POST',
        url:    '/wp-admin/admin-ajax.php',
        data:   {
            action: 'get_tiktok_authorized_shop',
        },
        beforeSend: function (){
            $('#card_tiktok_api').append('<div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i></div>')
        },
        success :   function (data){
            const rep = JSON.parse(data);
            if (rep.status) {
                $('#tiktok_shop_id').val(rep.data.tiktok_shop_id)
                $('#tiktok_shop_cipher').val(rep.data.tiktok_shop_cipher)
                $(document).Toasts('create', {
                    class: 'bg-success',
                    title: 'Success',
                    body: `Update success`,
                    icon: 'fas fa-info-circle',
                    autohide: true,
                    delay: 10000
                })
            } else {
                $(document).Toasts('create', {
                    class: 'bg-danger',
                    title: 'Danger',
                    body: data,
                    icon: 'fas fa-info-circle',
                    autohide: true,
                    delay: 10000
                })
            }
        },
        complete :  function (){
            $('#card_tiktok_api>.overlay').remove()
        },
        error   :   function (e){
            console.log("ERROR",e)
        }
    })


}


function sync_e_commerce_platform(action_payload = 'all'){

    let order_platform_ids = $('input[name="order_platform_id"]').val()

    $.ajax({
        type:   'POST',
        url:    '/wp-admin/admin-ajax.php',
        data:   {
            action: 'sync_e_commerce_platform',
            action_payload,
            order_platform_ids,
        },
        beforeSend: function (){
            $('#card_task_scheduler').append('<div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i></div>')
        },
        success :   function (data){
            const rep = JSON.parse(data);
            if (rep.status) {
                $(document).Toasts('create', {
                    class: 'bg-success',
                    title: 'Success',
                    body: `Update success`,
                    icon: 'fas fa-info-circle',
                    autohide: true,
                    delay: 10000
                })
            } else {
                $(document).Toasts('create', {
                    class: 'bg-danger',
                    title: 'Danger',
                    body: data,
                    icon: 'fas fa-info-circle',
                    autohide: true,
                    delay: 10000
                })
            }
        },
        complete :  function (){
            $('#card_task_scheduler>.overlay').remove()
        },
        error   :   function (e){
            console.log("ERROR",e)
        }
    })


}


/**
 *
 */

function save_setting_viettel_vinvoice(){

    let viettel_base_url = $("input[name='viettel_base_url']").val();
    let viettel_username = $("input[name='viettel_username']").val();
    let viettel_password = $("input[name='viettel_password']").val();

    $.ajax({
        type: 'POST',
        url: '/wp-admin/admin-ajax.php',
        data:{
            action: 'save_setting_viettel_vinvoice',
            viettel_base_url,
            viettel_username,
            viettel_password,
        },
        beforeSend: function (){
            $('#card_setting_viettel_vinvoice').append('<div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i></div>')
        },
        success: function (data){
            const rep = JSON.parse(data);
            $(document).Toasts('create', {
                class: 'bg-success',
                title: 'Success',
                body: `update setting viettel invoice success`,
                icon: 'fas fa-info-circle',
                autohide: true,
                delay: 10000
            })
        },
        complete: function (){
            $('#card_setting_viettel_vinvoice>.overlay').remove()
        },
        error: function(e){

            console.log("ERROR",e)

        }
    })
}


/**
 *
 */

function save_setting_ls_retail(){

    let wc_settings_tab_config_name         = $("input[name='wc_settings_tab_config_name']").val()
    let wc_settings_tab_ls_api_url          = $("input[name='wc_settings_tab_ls_api_url']").val()
    let wc_settings_tab_ls_api_username     = $("input[name='wc_settings_tab_ls_api_username']").val()
    let wc_settings_tab_ls_api_password     = $("input[name='wc_settings_tab_ls_api_password']").val()
    let wc_settings_tab_ls_location_code    = $("input[name='wc_settings_tab_ls_location_code']").val()
    let wc_settings_tab_ls_location_code2   = $("input[name='wc_settings_tab_ls_location_code2']").val()
    let wc_settings_tab_config_name_2       = $("input[name='wc_settings_tab_config_name_2']").val()
    let wc_settings_tab_ls_api_url_2        = $("input[name='wc_settings_tab_ls_api_url_2']").val()
    let wc_settings_tab_ls_api_username_2   = $("input[name='wc_settings_tab_ls_api_username_2']").val()
    let wc_settings_tab_ls_api_password_2   = $("input[name='wc_settings_tab_ls_api_password_2']").val()
    let wc_settings_tab_ls_location_code_2  = $("input[name='wc_settings_tab_ls_location_code_2']").val()
    let wc_settings_tab_ls_location_code2_2 = $("input[name='wc_settings_tab_ls_location_code2_2']").val()

    $.ajax({
        type: 'POST',
        url: '/wp-admin/admin-ajax.php',
        data:{
            action: 'save_setting_ls_retail',
            wc_settings_tab_config_name,
            wc_settings_tab_ls_api_url,
            wc_settings_tab_ls_api_username,
            wc_settings_tab_ls_api_password,
            wc_settings_tab_ls_location_code,
            wc_settings_tab_ls_location_code2,
            wc_settings_tab_config_name_2,
            wc_settings_tab_ls_api_url_2,
            wc_settings_tab_ls_api_username_2,
            wc_settings_tab_ls_api_password_2,
            wc_settings_tab_ls_location_code_2,
            wc_settings_tab_ls_location_code2_2
        },
        beforeSend: function (){
            $('#save_setting_ls_retail').append('<div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i></div>')
        },
        success: function (data){
            const rep = JSON.parse(data);
            $(document).Toasts('create', {
                class: 'bg-success',
                title: 'Success',
                body: `update setting viettel invoice success`,
                icon: 'fas fa-info-circle',
                autohide: true,
                delay: 10000
            })
        },
        complete: function (){
            $('#save_setting_ls_retail>.overlay').remove()
        },
        error: function(e){

            console.log("ERROR",e)

        }
    })
}

