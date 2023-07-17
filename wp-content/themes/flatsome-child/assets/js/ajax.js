
jQuery(function ($){

    $('#billing_city').change(function (){
        let billing_city_id = $('#billing_city').val()
        $('#billing_district').html('');

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
                data.data.map((e)=>{
                    $('#billing_district').append(`<option value="${e.tiki_code}" >${e.district_name}</option>`)
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

})