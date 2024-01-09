<?php
use \OMS\Tiktok_Api;


// fire custom event

/**
 * @throws WC_Data_Exception
 */
function tiktok_sync_on_schedule()
{
    write_log('Running..............');

    $tiktok_api = new Tiktok_Api();

    $response = $tiktok_api->sync_orders_v_202309();
    write_log('Done');
}
add_action( 'tiktok_sync_on_schedule_event', 'tiktok_sync_on_schedule' );