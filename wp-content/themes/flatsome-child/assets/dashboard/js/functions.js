let toggleSwitch = document.querySelector('.theme-switch input[type="checkbox"]');
let currentTheme = localStorage.getItem('theme');
let mainHeader = document.querySelector('.main-header');

if (currentTheme) {
    if (currentTheme === 'dark') {
        if (!document.body.classList.contains('dark-mode')) {
            document.body.classList.add("dark-mode");
        }
        if (mainHeader.classList.contains('navbar-light')) {
            mainHeader.classList.add('navbar-dark');
            mainHeader.classList.remove('navbar-light');
        }
        toggleSwitch.checked = true;
    }
}

/**
 *
 * @param e
 */

function switchTheme(e) {
    if (e.target.checked) {
        if (!document.body.classList.contains('dark-mode')) {
            document.body.classList.add("dark-mode");
        }
        if (mainHeader.classList.contains('navbar-light')) {
            mainHeader.classList.add('navbar-dark');
            mainHeader.classList.remove('navbar-light');
        }
        localStorage.setItem('theme', 'dark');
    } else {
        if (document.body.classList.contains('dark-mode')) {
            document.body.classList.remove("dark-mode");
        }
        if (mainHeader.classList.contains('navbar-dark')) {
            mainHeader.classList.add('navbar-light');
            mainHeader.classList.remove('navbar-dark');
        }
        localStorage.setItem('theme', 'light');
    }
}

toggleSwitch.addEventListener('change', switchTheme, false);


/**
 * check isJson
 * @param str
 * @returns {boolean}
 */
const isJsonString = (str) => {
    try {
        JSON.stringify(str);
        return true;
    } catch (e) {
        return false;
    }
}

function QueryParamsToJSON() {
    var list = location.search.slice(1).split('&'),
        result = {};

    list.forEach(function(keyval) {
        keyval = keyval.split('=');
        var key = keyval[0];
        if (/\[[0-9]*\]/.test(key) === true) {
            var pkey = key.split(/\[[0-9]*\]/)[0];
            if (typeof result[pkey] === 'undefined') {
                result[pkey] = [];
            }
            result[pkey].push(decodeURIComponent(keyval[1] || ''));
        } else {
            result[key] = decodeURIComponent(keyval[1] || '');
        }
    });

    return JSON.parse(JSON.stringify(result));
}

function changeValueUrlParam(uri, param, value){
    let paramObj = {}
    if(window.location.search){
        paramObj = QueryParamsToJSON(window.location.search)
    }

    paramObj['offset'] = 1;

    paramObj[param] = value
    console.log(paramObj)
    return uri+'/?'+$.param(paramObj);
}

/**
 *
 */
$(function () {

    // Summernote
    $('#footer_print_shipment').summernote()

    $('#product_return_policy').summernote()

    //Initialize Select2 Elements
    $('.select2').select2()

    $('#reservation_order,#reservation_inventory').daterangepicker()

    $('#filter_order_status').change(
        function (){
            let filter_order_status = $('#filter_order_status').val()
            console.log(changeValueUrlParam('.','status',filter_order_status.toString()));
            window.location.href = changeValueUrlParam('.','status',filter_order_status.toString())
        }
    )

    $('#reservation').daterangepicker()

    let defaultMoment = parseInt($('#reservation_order').attr('default-moment'))

    $('#reservation_order').daterangepicker(
        {
            ranges   : {
                'Hôm nay'       : [moment(), moment()],
                '30 ngày trước': [moment().subtract(29, 'days'), moment()]
            },
            startDate: moment().subtract(defaultMoment, 'days'),
            endDate  : moment()
        },
        function (start, end) {
            let range_date = start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY')
            $('#reservation_title span').html(range_date)
            window.location.href = changeValueUrlParam('/admin-dashboard/order-list','range_date',range_date);
        }
    )


    $('#reservation_inventory').on('change',function (){
        let range_date = $('#reservation_inventory').val();
        $('#post_inventory').attr('href',`./?range_date=${range_date}`)
    })


})

