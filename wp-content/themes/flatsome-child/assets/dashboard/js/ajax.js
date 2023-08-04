
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
            $('#card_create_table').append('<div class="overlay"><i class="fas fa-2x fa-sync-alt"></i></div>')

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
                    delay: 5000
                })
            } catch (e){
                $(document).Toasts('create', {
                    class: 'bg-danger',
                    title: 'Danger',
                    body: e,
                    icon: 'fas fa-info-circle',
                    autohide: true,
                    delay: 5000
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
        },
        beforeSend: function (){
            $('#card_setting_tiki_api').append('<div class="overlay"><i class="fas fa-2x fa-sync-alt"></i></div>')
        },
        success: function (data){
            const rep = JSON.parse(data);
            $(document).Toasts('create', {
                class: 'bg-success',
                title: 'Success',
                body: `update setting tiki success`,
                icon: 'fas fa-info-circle',
                autohide: true,
                delay: 5000
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

function get_access_token_tiki(){

    if (!confirm('Are you sure create new token?')) return;

    $.ajax({
        type    :   'POST',
        url     :   '/wp-admin/admin-ajax.php',
        data    :   {
                    action  :   'get_access_token_tiki'
        },
        beforeSend  :   function (){
            $('#card_setting_tiki_api').append('<div class="overlay"><i class="fas fa-2x fa-sync-alt"></i></div>')
        },
        success     :   function (data){
            const rep = JSON.parse(data);
            $("input[name='access_token']").val(rep.data);
            $(document).Toasts('create', {
                class: 'bg-success',
                title: 'Success',
                body: `get access token success`,
                icon: 'fas fa-info-circle',
                autohide: true,
                delay: 5000
            })
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
            $('#card_orders').append('<div class="overlay"><i class="fas fa-2x fa-sync-alt"></i></div>')

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
                    $(document).Toasts('create', {
                        class: 'bg-success',
                        title: 'Success',
                        body: `Update status: <span class="badge badge-${class_status}">${rep.data.order_status}</span>`,
                        icon: 'fas fa-info-circle',
                        autohide: true,
                        delay: 5000
                    })
                }else {
                    $(document).Toasts('create', {
                        class: 'bg-info',
                        title: 'update false',
                        body: `${rep.messenger} <br>${JSON.stringify(rep.data) ?? ''}`,
                        icon: 'fas fa-info-circle',
                        autohide: true,
                        delay: 5000
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
                    delay: 5000
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
    let web_company_email       =       $('#company_email').val()
    let web_company_phone       =       $('#company_phone').val()
    let web_company_country     =       $('#company_country').val()
    let web_company_city        =       $('#company_city').val()
    let web_company_district    =       $('#company_district').val()
    let web_company_ward        =       $('#company_ward').val()
    let web_company_address     =       $('#company_address').val()

    $.ajax({
        type    :   'POST',
        url     :   '/wp-admin/admin-ajax.php',
        data    :   {
            action  :   'save_company_info',
            web_company_name,
            web_company_email,
            web_company_phone,
            web_company_country,
            web_company_city,
            web_company_district,
            web_company_ward,
            web_company_address,
        },
        beforeSend: function (){
            $('#card_information_company').append('<div class="overlay"><i class="fas fa-2x fa-sync-alt"></i></div>')
        },
        success :   function (data){
            const rep = JSON.parse(data);
            if (rep.success = '200') {
                $(document).Toasts('create', {
                    class: 'bg-success',
                    title: 'Success',
                    body: `Update success`,
                    icon: 'fas fa-info-circle',
                    autohide: true,
                    delay: 5000
                })
            } else {
                $(document).Toasts('create', {
                    class: 'bg-danger',
                    title: 'Danger',
                    body: data,
                    icon: 'fas fa-info-circle',
                    autohide: true,
                    delay: 5000
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
    let footer_print_shipment = $('#footer_print_shipment').val()

    $.ajax({
        type:   'POST',
        url:    '/wp-admin/admin-ajax.php',
        data:   {
            action: 'save_admin_dashboard_setting',
            item_in_page,
            footer_print_shipment
        },
        beforeSend: function (){
            $('#card_admin_dashboard').append('<div class="overlay"><i class="fas fa-2x fa-sync-alt"></i></div>')
        },
        success :   function (data){
            const rep = JSON.parse(data);
            if (rep.success = '200') {
                $(document).Toasts('create', {
                    class: 'bg-success',
                    title: 'Success',
                    body: `Update success`,
                    icon: 'fas fa-info-circle',
                    autohide: true,
                    delay: 5000
                })
            } else {
                $(document).Toasts('create', {
                    class: 'bg-danger',
                    title: 'Danger',
                    body: data,
                    icon: 'fas fa-info-circle',
                    autohide: true,
                    delay: 5000
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
            $('#card_admin_dashboard').append('<div class="overlay"><i class="fas fa-2x fa-sync-alt"></i></div>')
        },
        success :   function (data){
            const rep = JSON.parse(data);
            if (rep.success = '200') {
                $(document).Toasts('create', {
                    class: 'bg-success',
                    title: 'Success',
                    body: `Update success`,
                    icon: 'fas fa-info-circle',
                    autohide: true,
                    delay: 5000
                })
            } else {
                $(document).Toasts('create', {
                    class: 'bg-danger',
                    title: 'Danger',
                    body: data,
                    icon: 'fas fa-info-circle',
                    autohide: true,
                    delay: 5000
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