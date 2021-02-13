<?php
/**
 * Plugin Name:       Club Plug
 * Plugin URI:        https://woopeaal.com/
 * Description:       A dedicated plugin developed for IBAACL by Mohaazon IT Solution, the most demanded Web-developing company from August, 2020.
 * Version:           1.1.2
 * Requires at least: 5.0
 * Requires PHP:      7.1
 * Author:            Naim-Ul-Hassan
 * Author URI:        https://woopearl.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       clubplug
 * Domain Path:       /languages
 */

defined('ABSPATH') or die('Only a foolish person try to access directly to see this white page. :-) ');

/**
 * Plugin language
 */
load_plugin_textdomain( 'clubplug', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );



 // Setup
 define('CLUBPLUG', __FILE__ );



 // Includes
 include('includes/activate.php');
 include('includes/deactivate.php');
 include('includes/membership-options.php');
 include('uninstall.php');
 include('products/make-payment.php');
 //include('products/purchasable.php');
 //include('products/upload-product.php');

 //include('users/create.php');
 include('users/due.php');

 include('my-account-menu/club-menu.php');
 include('my-account-menu/checkout-order.php');

 include('admin/user.php');
 include('admin/sms-system.php');



// Hooks
 register_activation_hook( __FILE__, 'clubplug_activate_plugin');
 register_deactivation_hook( __FILE__, 'clubplug_deactivate_plugin');
 register_uninstall_hook( __FILE__, 'clubplug_uninstall_plugin');




// 1/1/21
//  add_filter('woocommerce_add_to_cart_redirect', 'themeprefix_add_to_cart_redirect');
//  function themeprefix_add_to_cart_redirect() {
//   global $woocommerce;
//   $checkout_url = wc_get_checkout_url();
//   return $checkout_url;
//  }

add_action('wp_dashboard_setup', 'naims_custom_dashboard_widgets');
    function naims_custom_dashboard_widgets() {
    global $wp_meta_boxes;
    
    wp_add_dashboard_widget('naims_payment_report_widget', 'Payment Reports', 'naims_payment_report_widget');
}
 
function naims_payment_report_widget() {
    echo '<p>Get all report here : <a style="background: #0aa9ce;padding: 8px;border-radius: 5px;text-decoration: none;color: #fff;" href="'.site_url().'/wp-admin/admin.php?page=wc-reports&tab=orders">All Reports<a/></p>';
}


add_filter( 'gettext', 'mohaazon_translate_woocommerce_strings', 999, 3 );
function mohaazon_translate_woocommerce_strings( $translated, $untranslated, $domain ) {
   if ( ! is_admin() && 'woocommerce' === $domain ) {
      switch ( $translated ) {
         case 'Place order':
            $translated = 'Pay Now';
            break;      
      }
   }   
   return $translated;
}

add_filter( 'woocommerce_login_redirect', 'bbloomer_customer_login_redirect', 9999, 2 );
 
function bbloomer_customer_login_redirect( $redirect, $user ) {
     
    if ( wc_user_has_role( $user, 'customer' )) {
        $redirect = site_url('/my-account/club-payment'); // homepage
        //$redirect = wc_get_page_permalink( 'shop' ); // shop page
        //$redirect = '/custom_url'; // custom URL same site
        //$redirect = 'https://custom.url'; // custom URL other site
        //$redirect = add_query_arg( 'password-reset', 'true', wc_get_page_permalink( 'myaccount' ) ); // custom My Account tab
    }
  
    return $redirect;
}

function naims_delete_user($id){
    require_once( ABSPATH.'wp-admin/includes/user.php' );
    wp_delete_user( $id );
}


// ======================================================================================================

