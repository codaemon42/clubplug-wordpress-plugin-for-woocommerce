<?php
function clubplug_deactivate_plugin(){
    $timestamp = wp_next_scheduled( 'bl_due_cron_hook2' );
    wp_unschedule_event( $timestamp, 'bl_due_cron_hook2' );

}