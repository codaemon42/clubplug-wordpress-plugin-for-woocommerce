<?php
function clubplug_activate_plugin(){
    if(version_compare(get_bloginfo('version'), '5.0', '<')){
        wp_die('you must update wordpress to use this plugin');
    }  

    // new cron interval declaration
    add_filter( 'cron_schedules', 'test_example_add_cron_interval' );
    function test_example_add_cron_interval( $schedules ) { 
        $schedules['five_second'] = array(
            'interval' => 5,
            'display'  => esc_html__( 'Every Five Seconds' ), );
        return $schedules;
    }
    add_filter( 'cron_schedules', 'test_example_add_cron_interval_minute' );
    function test_example_add_cron_interval_minute( $schedules ) { 
        $schedules['minutely'] = array(
            'interval' => 60,
            'display'  => esc_html__( 'Every minutes' ), );
        return $schedules;
    }
 
    if ( ! wp_next_scheduled( 'bl_due_cron_hook2' ) ) {
        wp_schedule_event( time(), 'daily', 'bl_due_cron_hook2' );
    }

    // 1/1/21
    //mohaazon_lifetime_plan_product_upload();
    mohaazon_upload_product_per_month();
}

function mohaazon_upload_product_per_month(){
    //here
    global $wpdb;
        // Add Product
        $post_title = 'Due';
        $new_post = array(
            'post_title' => $post_title,
            'post_author' => '1',
            'post_type' => 'product',
            'post_status' => 'publish'
        );

        $rowcount = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}posts WHERE post_title = '$post_title' AND post_type = 'product' AND post_status = 'publish'");

        if($rowcount < 1){
            $product_id = wp_insert_post( $new_post );
            $metas = array(
               '_visibility' => 'visible',
               '_virtual' => 'yes',
               '_regular_price' => get_option('mohaazon-min-amount', 1000),
               '_price' => get_option('mohaazon-min-amount', 1000),
               '_sold_individually' => 'yes',
            );
            foreach ($metas as $key => $value) {
                update_post_meta($product_id, $key, $value);
            }
        }
        else if($rowcount > 0){

            return false;

        }
}


/**
 * 
 * f(mohaazon_lifetime_plan_product_upload)
 * 
 * upload Lifetime membership product on activation
 * 
 * $post_title = 'LifeTime-Membership'
 * 
 * once in a site
 * 
 */
// 1/1/21
// function mohaazon_lifetime_plan_product_upload(){
//     global $wpdb;
//     $post_title = 'LifeTime-Membership';
//     $rowcount = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}posts WHERE post_title = '$post_title' AND post_type = 'product' AND post_status = 'publish'");
//     if($rowcount < 1){
//         /**
//          * Add Product if it not exists
//          */ 
//         $new_post = array(
//             'post_title' => $post_title,
//             'post_author' => '1',
//             'post_type' => 'product',
//             'post_status' => 'publish'
//         );
//         $product_id = wp_insert_post( $new_post );
//         $GLOBALS['lifetime_member_id'] = $product_id;
//         $metas = array(
//            '_visibility' => 'visible',
//            '_virtual' => 'yes',
//            '_regular_price' => get_option('mohaazon-lifetime-membership-price'),
//            '_price' => get_option('mohaazon-lifetime-membership-price'),
//            '_sold_individually' => 'yes',
//         );
//         foreach ($metas as $key => $value) {
//             update_post_meta($product_id, $key, $value);
//         }
//     }
//     else if($rowcount > 0){
//         return false;
//     }
// }