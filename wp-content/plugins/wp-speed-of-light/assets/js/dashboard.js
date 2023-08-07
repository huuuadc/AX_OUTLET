jQuery(document).ready(function ($) {
    var success_icon = '<i class="material-icons success size-24 icon-vertical-mid">check_circle</i>';
    var warning_icon = '<i class="material-icons info size-24 icon-vertical-mid">info</i>';
    $.ajax({
        url : ajaxurl,
        method : "POST",
        dataType : "json",
        data:{
            action : 'wpsol_check_response_dashboard'
        },success : function(res){
            if (res.gzip) {
                $(".dashboard-info-right .gzip-compression").find('.right-checkbox').html(success_icon);
            } else {
                $(".dashboard-info-right .gzip-compression").find('.right-checkbox').html(warning_icon);
            }

            if (res.expires) {
                $(".dashboard-info-right .expires-header").find('.right-checkbox').html(success_icon);
            } else {
                $(".dashboard-info-right .expires-header").find('.right-checkbox').html(warning_icon);
            }
        }
    });

    $(".wpsol-dashboard .hover-section").hover(function(){
        $(this).find('.link-info').addClass('show-link');
    }, function () {
        $(this).find('.link-info').removeClass('show-link');
    });
});