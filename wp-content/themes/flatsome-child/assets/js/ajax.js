
jQuery(function ($){
    $(".category-page-row .is-sticky-column").sticky({topSpacing: 70});
    $('body').on('click','.header-search > a',function(){
        if($('.header-search').is('.current-dropdown')){
            $('.header-search').removeClass('current-dropdown');
        }else{
            $('.header-search').addClass('current-dropdown');
        }
    });
    $(document).on('click', function (e) {
        var container = $('.header-search,.dgwt-wcas-suggestions-wrapp');
        if (!container.is(e.target) && container.has(e.target).length === 0)
        {
            $('.header-search').removeClass('current-dropdown');
        }
    });

    var checkDiscount = function() {
        $('.shop_table').each(function () {
            var hasDiscount = $(this).find('.cart-discount');
            if (hasDiscount.length) {
                $('.order-total').addClass('show');
            }
        });
    }

    checkDiscount();

    function checkQty(){
        $('.quantity input.qty').each(function(){
            var max_item = parseInt($(this).attr('max'));
            var current = parseInt($(this).val());
            if(current==max_item){
                $(this).next('.button').removeClass('plus').addClass('plus_disable');
/*
                $(this).next('.plus_disable').click(function(e){
                    e.preventDefault();
                    alert('Chỉ còn lại ' + max_item + ' sản phẩm');
                });*/
            }
            //console.log('max: '+max_item);
            //console.log('current: '+current);
        });
    }
    checkQty();

    $('body').on('click','.plus_disable',function(e){
        e.preventDefault();
        var max_item = $(this).prev('input.qty').attr('max');
        alert('Chỉ còn lại ' + max_item + ' sản phẩm');
    });

    function checkShipping(){
        $('.shipping__table').each(function(){
            if($(this).find('.shipping__list_item .amount').length || $(this).find('.shipping__list_item input[value="wdr_free_shipping"]').length){
                $(this).removeClass('hidden');
            }else{
                $(this).addClass('hidden');
            }
        });
    }
    $(document).ajaxStop(function() {
        checkShipping();
    });

    $('body').on('click','.woocommerce-remove-coupon',function(){
        $(document).ajaxStop(function() {
            checkDiscount();
        });
    });

    $('body').on('change','.quantity .input-text.qty', function (){
        $(document).ajaxStop(function() {
            checkDiscount();
            checkQty();
        });
    });

    $('#billing_city').change(function (){
        let billing_city_id = $('#billing_city').val()

        $('#billing_district').html('<option value="" >Quận/Huyện</option>');
        $('#billing_ward').html('<option value="" >Phường/Xã</option>');

        $.ajax({
            type: 'POST',
            url: '/wp-admin/admin-ajax.php',
            data : {
                action: 'get_address_shipping',
                action_payload : 'get_district',
                id: `${billing_city_id}`
            },
            success: function (rep){
                let data = JSON.parse(rep);

                data.data.district.map((e)=>{
                        $('#billing_district').append(`<option value="${e.tiki_code}" >${e.district_name}</option>`)
                })
            },
            error: function (e){
                console.log(e)
            }

        })
        $(document).ajaxStop(function() {
            checkShipping();
        });
    })

    $('#billing_district').change(function (){
        let billing_district_id = $('#billing_district').val()
        $('#billing_ward').html('<option value="" >Phường/Xã</option>');

        $.ajax({
            type: 'POST',
            url: '/wp-admin/admin-ajax.php',
            data : {
                action: 'get_address_shipping',
                action_payload : 'get_ward',
                id: `${billing_district_id}`
            },
            success: function (rep){
                let data = JSON.parse(rep);

                data.data.map((e)=>{
                    $('#billing_ward').append(`<option value="${e.tiki_code}" >${e.ward_name}</option>`)
                });

                $('body').trigger('update_checkout');

            },
            error: function (e){
                console.log(e)
            }

        })
        $(document).ajaxStop(function() {
            checkShipping();
        });
    })

    $('#billing_ward').change(function (){

        $('body').trigger('update_checkout');
        $(document).ajaxStop(function() {
            checkShipping();
        });
    })

})


function auto_load_district($) {
    let billing_city_id = $('#billing_city').val()
    $('#billing_district').html('<option value="" >Quận/Huyện</option>');
    $('#billing_ward').html('<option value="" >Phường/Xã</option>');

    $.ajax({
        type: 'POST',
        url: '/wp-admin/admin-ajax.php',
        data : {
            action: 'get_address_shipping',
            action_payload : 'get_district',
            id: `${billing_city_id}`
        },
        success: function (rep){
            let data = JSON.parse(rep);
            data.data.district.map((e)=>{
                $('#billing_district').append(`<option value="${e.tiki_code}" >${e.district_name}</option>`)
            })
        },
        error: function (e){
            console.log(e)
        }

    })
}

jQuery(auto_load_district)

