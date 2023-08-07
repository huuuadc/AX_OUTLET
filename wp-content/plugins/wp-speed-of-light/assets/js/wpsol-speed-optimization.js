jQuery(document).ready(function ($) {
    jQuery('.speedoflight_tool').qtip({
        content: {
            attr: 'alt'
        },
        position: {
            my: 'bottom left',
            at: 'top top'
        },
        style: {
            tip: {
                corner: true
            },
            classes: 'speedoflight-qtip qtip-rounded speedoflight-qtip-dashboard'
        },
        hide: {
            fixed: true,
            delay: 10
        }

    });

    $('input[name="all_control"]').click(function(){
        var checked = $(this).is(':checked');
        if(checked){
            $(".clean-data:enabled").prop("checked",true);
        }else{
            $(".clean-data:enabled").prop("checked",false);
        }
    });
    $(".clean-data").click(function(){
        var checked = $(this).is(':checked');
        if(!checked){
            $('input[name="all_control"]').prop('checked',false);
        }
    });

    // change value 
    $(".wpsol-optimization").change(function () {
        var clean_cache = $("#clean-cache");
        var active_cache = $("#active-cache");
        var add_expires = $("#add-expires");
        var query_strings = $("#query-strings");
        var cache_preload = $("#cache-preload");
        var remove_rest = $("#remove-rest");
        var remove_rss = $("#remove-rss");
        var disabloe_role = $("#config1");
        var val = clean_cache.val();
        (active_cache.is(':checked')) ? active_cache.attr("value", "1") : active_cache.attr("value", "0");
        (add_expires.is(':checked')) ? add_expires.attr("value", "1") : add_expires.attr("value", "0");
        (query_strings.is(':checked')) ? query_strings.attr("value", "1") : query_strings.attr("value", "0");
        (cache_preload.is(':checked')) ? cache_preload.attr("value", "1") : cache_preload.attr("value", "0");
        (remove_rest.is(':checked')) ? remove_rest.attr("value", "1") : remove_rest.attr("value", "0");
        (remove_rss.is(':checked')) ? remove_rss.attr("value", "1") : remove_rss.attr("value", "0");
        (disabloe_role.is(':checked')) ? disabloe_role.attr("value", "1") : disabloe_role.attr("value", "0");
        clean_cache.attr("value", val);
    });
    // change value
    $(".wpsol-minification").change(function () {
        var html = $("#html-minification");
        var css = $("#css-minification");
        var js = $("#js-minification");
        var cssGroup = $("#cssgroup-minification");
        var jsGroup = $("#jsgroup-minification");
        var fontGroup = $("#fontGroup-minification");
        var excludeFiles = $("#excludeFiles-minification");
        (html.is(':checked')) ? html.attr("value", "1") : html.attr("value", "0");
        (css.is(':checked')) ? css.attr("value", "1") : css.attr("value", "0");
        (js.is(':checked')) ? js.attr("value", "1") : js.attr("value", "0");
        (cssGroup.is(':checked')) ? cssGroup.attr("value", "1") : cssGroup.attr("value", "0");
        (jsGroup.is(':checked')) ? jsGroup.attr("value", "1") : jsGroup.attr("value", "0");
        (fontGroup.is(':checked')) ? fontGroup.attr("value", "1") : fontGroup.attr("value", "0");
        (excludeFiles.is(':checked')) ? excludeFiles.attr("value", "1") : excludeFiles.attr("value", "0");
    });


    $('#speed-optimization-form').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if(e.target.tagName != 'TEXTAREA') {
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        }
    });


    //   Display popup when active minify
    if ($("#wpsol_check_minify_modal").length > 0) {
        $("#jsgroup-minification,#cssgroup-minification").on("change",function(){
            if($(this).is(':checked')) {
                $("#wpsol_check_minify_modal").dialog("open");
                $(this).prop("checked", false);
                $(this).attr("value","0");
                var name = $(this).attr('name');
                $("#wpsol_check_minify_modal .check-minify-sucess #agree").attr('name', name);
            }
        });
    }

    $("#wpsol_check_minify_modal .check-minify-sucess #agree").click(function(){
        var name = $(this).attr('name');
        // Set checked for type
            $("ul").find('#'+name).prop("checked",true).attr("value","1");
        // Close dialog
            $("#wpsol_check_minify_modal").dialog("close");

    });

    $("#wpsol_check_minify_modal .check-minify-sucess .cancel").click(function(){
        $("#wpsol_check_minify_modal").dialog("close");
    });

    $("#wpsol_check_minify_modal").dialog({
        width: 600,
        height: 400,
        autoOpen: false,
        closeOnEscape: true,
        draggable: false,
        resizable: false,
        modal : true,
        dialogClass: 'noTitle',
        show: {
            effect: "fade",
            duration: 500
        },
        hide: {
            effect: "fade",
            duration: 300
        }
     });

    $(".woocommerce-clearup").on('click', function (){
        $('.woocommerce-ajax-message .ajax-loader-icon').show();
        $('.woocommerce-ajax-message .woocommerce-ajax-result').removeClass('ju-notice-success ju-notice-error').hide();

        var type = $(this).attr('data-type');

        $.ajax({
            url : ajaxurl,
            dataType : 'json',
            method : 'POST',
            data : {
                action : 'wpsol_ajax_'+type,
                ajaxnonce : speedoptimizeNonce.ajaxnonce
            },
            success : function (res) {
                $('.woocommerce-ajax-message .ajax-loader-icon').hide();
                if (res.status) {
                    $('.woocommerce-ajax-message .woocommerce-ajax-result').addClass('ju-notice-success').html('<strong>'+res.message+'</strong>').show();
                } else {
                    $('.woocommerce-ajax-message .woocommerce-ajax-result').addClass('ju-notice-error').html('<strong>'+res.message+'</strong>').show();
                }
            }
        });
    });

    // Check module is enable in system
    system_check_ajax();

    function system_check_ajax()
    {
        $.ajax({
            url : ajaxurl,
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
                            $('.ju-menu-tabs .tab .wpsol-system-warning-icon').show();
                        }
                    });
                }
            }
        });
    }

    if ($('.clean-data').length > 0) {
        $.ajax({
            url : ajaxurl,
            dataType : 'json',
            method : 'POST',
            data : {
                action : 'wpsol_ajax_load_database_element',
                ajaxnonce : speedoptimizeNonce.ajaxnonce
            },
            success : function (res) {
                $.each(res.list_elements, function (i, v) {
                    $('.'+i).html('('+v+')');
                });

            }
        });
    }
});