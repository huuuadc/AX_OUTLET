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

/**
 *
 */
$(function () {
    // Summernote
    $('#footer_print_shipment').summernote()

    //Initialize Select2 Elements
    $('.select2').select2()

    $('#filter_order_status').change(
        function (){
            let filter_order_status = $('#filter_order_status').val()
            let param = ''
            console.log(filter_order_status)
            if (filter_order_status.length > 0){
                param = 'filter_status='+ filter_order_status.toString()
                window.location.href = '/admin-dashboard/order-list/?'+param;
            }else {
                window.location.href = '/admin-dashboard/order-list';
            }
        }
    )

    $('#reservation').daterangepicker()

    $('#daterange-btn').daterangepicker(
        {
            ranges   : {
                'Hôm nay'       : [moment(), moment()],
                'Hôm qua'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '7 ngày trước' : [moment().subtract(6, 'days'), moment()],
                '30 ngày trước': [moment().subtract(29, 'days'), moment()],
                'Tháng này'  : [moment().startOf('month'), moment().endOf('month')],
                'Tháng trước'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            startDate: moment().subtract(29, 'days'),
            endDate  : moment()
        },
        function (start, end) {
            $('#reportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'))
            window.location.href = '/admin-dashboard/order-list/?filter_range_date='+start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY');
        }
    )


})

