<?php
use \OMS\Tiktok_Api;
// function that registers new custom schedule

function bf_add_custom_schedule( $schedules )
{
    $schedules[ 'every_five_minutes' ] = array(
        'interval' => 300,
        'display'  => 'Every 5 minutes',
    );

    return $schedules;
}

// function that schedules custom event

function bf_schedule_custom_event()
{
    // the actual hook to register new custom schedule

    add_filter( 'cron_schedules', 'bf_add_custom_schedule' );

    // schedule custom event

    if( !wp_next_scheduled( 'bf_your_custom_event' ) )
    {
        wp_schedule_event( time(), 'every_five_minutes', 'bf_your_custom_event' );
    }
}
add_action( 'init', 'bf_schedule_custom_event' );

// fire custom event

/**
 * @throws WC_Data_Exception
 */
function bf_do_something_on_schedule()
{
    write_log('Running..............');

    $tiktok_api = new Tiktok_Api();

    $response = $tiktok_api->sync_orders_v_202309();
    write_log('Done');
}
add_action( 'bf_your_custom_event', 'bf_do_something_on_schedule' );