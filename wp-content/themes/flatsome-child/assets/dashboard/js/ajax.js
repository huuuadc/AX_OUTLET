
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
            console.log(rep);
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
            console.log(rep);
            $("input[name='access_token']").val(rep.data);
        },
        complete    :    function (){
            $('#card_setting_tiki_api>.overlay').remove()
        },
        error       :   function (e){
            console.log("ERROR",e)
        }
    })
}