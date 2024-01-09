<?php

// function that registers new custom schedule

function list_custom_schedule( $schedules )
{
    $schedules[ 'every_five_minutes' ] = array(
        'interval' => 60*5,
        'display'  => 'Every 5 minutes',
    );

    $schedules[ 'every_one_hour' ] = array(
        'interval' => 60*60,
        'display'  => 'Every 60 minutes',
    );

    $schedules[ 'every_six_hour' ] = array(
        'interval' => 60*60*6,
        'display'  => 'Every 60 minutes',
    );

    $schedules[ 'every_half_day' ] = array(
        'interval' => 60*60*12,
        'display'  => 'Every 60 minutes',
    );

    $schedules[ 'every_one_day' ] = array(
        'interval' => 60*60*24,
        'display'  => 'Every 60 minutes',
    );


    return $schedules;
}

// function that schedules custom event

function register_schedule_custom_event()
{
    if (get_option('is_sync_platform') == 'checked') {
        // the actual hook to register new custom schedule
        add_filter('cron_schedules', 'list_custom_schedule');
        // schedule custom event
        if (!wp_next_scheduled('tiktok_sync_on_schedule_event')) {
            wp_schedule_event(time(), 'every_five_minutes', 'tiktok_sync_on_schedule_event');
        }
    }

    if (get_option('admin_dashboard_is_update_price') == 'checked') {
        // the actual hook to register new custom schedule
        add_filter('cron_schedules', 'list_custom_schedule');
        // schedule custom event
        if (!wp_next_scheduled('update_discount_price_event')) {
            wp_schedule_event(time(), 'every_one_hour', 'update_discount_price_event');
        }
    }
}
add_action( 'init', 'register_schedule_custom_event' );