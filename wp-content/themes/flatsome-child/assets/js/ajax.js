
jQuery(function ($){
    var checkDiscount = function() {
        $('.shop_table').each(function () {
            var hasDiscount = $(this).find('.cart-discount');
            if (hasDiscount.length) {
                $('.order-total').addClass('show');
            }
        });
    }
    checkDiscount();
    $('body').on('click','.woocommerce-remove-coupon',function(){
        $(document).on( "ajaxComplete", function( event, xhr, settings ) {
            checkDiscount();
        });
    });
    $('#billing_city').change(function (){
        let billing_city_id = $('#billing_city').val()
        $('#billing_district').html('');
        $('#billing_ward').html('');

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
                data.data.ward.map((e)=>{
                    $('#billing_ward').append(`<option value="${e.tiki_code}" >${e.ward_name}</option>`)
                })

            },
            error: function (e){
                console.log(e)
            }

        })

    })

    $('#billing_district').change(function (){
        let billing_district_id = $('#billing_district').val()
        $('#billing_ward').html('');

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
                })

            },
            error: function (e){
                console.log(e)
            }

        })

    })


    /**
     *
     */

    $('#billing_ward').change(function (){
        let billing_ward_id = $('#billing_ward').val()

        $.ajax({
            type: 'POST',
            url: '/wp-admin/admin-ajax.php',
            data : {
                action: 'get_address_shipping',
                action_payload : 'get_est_shipment',
                id: `${billing_ward_id}`
            },
            success: function (rep){
                let data = JSON.parse(rep);

                let fee = data.data.data.quotes['0'].fee.amount

                $('.shipping__table>tbody>tr').html('<ul id="shipping_method" class="shipping__list woocommerce-shipping-methods"><li class="shipping__list_item">' +
                    `<label class="shipping__list_label" for="shipping_method_0_flat_rate4">Tiki Now: <span class="woocommerce-Price-amount amount"><bdi>${fee}<span class="woocommerce-Price-currencySymbol">₫</span></bdi></span></label></li>` +
                    '</ul>')
            },
            error: function (e){
                console.log(e)
            }

        })

    })

})