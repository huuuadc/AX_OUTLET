
    function  send_test_connect_ls() {
        let username = jQuery("#wc_settings_tab_ls_api_username").val();
        let password = jQuery("#wc_settings_tab_ls_api_password").val();
        let base_url = jQuery("#wc_settings_tab_ls_api_url").val();

            jQuery.ajax({
            type: 'POST',
            url: '/wp-admin/admin-ajax.php',
            data: {
                action: "post_check_connect_ls",
                username,
                password,
                base_url
            },
            beforeSend: function (){
            },
            success: function (rep){
                try {
                    if (!isJsonString(rep)) return console.log(rep)
                    let {status, messenger, data} = JSON.parse(rep)
                    if (status == '200'){
                        toast({title:"Success", message: messenger, type: 'success', duration: 5000});
                    } else {
                        toast({title:"Error", message: messenger + data, type: 'error', duration: 5000});
                    }

                } catch (e) {
                    toast({title:"Error Catch", message: e, type: 'error', duration: 5000});
                    console.log(e)
                }
            },
            error: function (){
                alert("error");
            }

        });
}