jQuery(document).ready(function ($) {
    // Check module is enable in system
    system_check_ajax();

    function system_check_ajax()
    {
        $.ajax({
            url : ajaxURL.define,
            dataType : 'json',
            method : 'POST',
            data : {
                action : 'wpsol_ajax_system_check',
                ajaxnonce : speedoptimizeNonce.ajaxnonce
            },
            success : function (res) {
                if (res.error) {
                    $('#message-systemcheck').html('<strong>'+res.error+'</strong>').show();
                }

                if (res.list_modules) {
                    $.each(res.list_modules, function ($k, $v) {
                        if ($v) {
                            $('.apache-container span.notification-icon.'+$k).html(icon.name.ok);
                        } else {
                            if ($k === 'mod_expires') {
                                $('.apache-container span.notification-icon.'+$k).html(icon.name.alert);
                            } else {
                                $('.apache-container span.notification-icon.'+$k).html(icon.name.info);
                            }
                            $('.apache-container p.notification.'+$k).show(1000);
                        }
                    });
                }
            }
        });
    }

    $("#minify_html").click(function () {
        val = $(this).val();
        if (val == 1) {
            $(this).val(0);
        } else {
            $(this).val(1);
        }
    });
});