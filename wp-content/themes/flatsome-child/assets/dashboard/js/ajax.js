
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
            const rep = JSON.parse(data);
            $(document).Toasts('create', {
                title: 'Success',
                body: `Complete generate database and send info`,
                icon: 'fas fa-info-circle',
                autohide: true,
                delay: 5000
            })
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

    $.ajax({
        type: 'POST',
        url: '/wp-admin/admin-ajax.php',
        data:{
            action: 'post_order_update_status',
            payload_action: 'order_status_' + status,
            order_id: id

        },
        beforeSend: function (){
            $('#card_orders').append('<div class="overlay"><i class="fas fa-2x fa-sync-alt"></i></div>')

        },
        success: function (data){
            const rep = JSON.parse(data);
            $(`#order_status_${id}`).html(rep.data)
            $(document).Toasts('create', {
                title: 'Success',
                body: `Update status: ${rep.data}`,
                icon: 'fas fa-info-circle',
                autohide: true,
                delay: 5000
            })
        },
        complete: function (){
            $('#card_orders>.overlay').remove()
        },
        error: function(errorThrown){

            console.log("ERROR",errorThrown)

        }
    })
}