<?php
    /**
     * clean
     * f(1)->register extra fields (load,validate,save) => fname, lname, phone, membership_selection
     * f(2)->on create customer update usermeta naims_membership & total_due
     * 
     * usermeta naims_membership
     * @param monthly
     * @param premium_monthly
     * @param lifetime
     * 
     * usermeta total_due = naims_membership pricing
     * @param $monthly_cost
     * @param $premium_monthly
     * @param $lifetime_cost
     * 
     */
    add_action('bl_due_cron_hook2', 'bl_due_cron_hook2_exec');
    function bl_due_cron_hook2_exec(){
        global $wpdb;
    
        if(date('j') == get_option('mohaazon-due-date')){
    
            $user_query = get_users( array(
                'role' => 'customer',
                'number' => -1
            ) );
            foreach($user_query as $user){
                $user_id = $user->ID;
    
                $due = intval(get_user_meta($user_id, 'total_due', true));
                $membership = get_user_meta($user_id, 'naims_membership', true);
                $due_charge = intval(get_option('mohaazon-due-fee-monthly'));
    
                if($membership == 'monthly'){
                    $membership_cost = intval(get_option('mohaazon-monthly-membership-price'));             
                }
                else if($membership == 'premium_monthly'){
                    $membership_cost = intval(get_option('mohaazon-premium-monthly-membership-price'));
                }
                else if($membership == 'lifetime' && $total_due > 0){
                    $membership_cost = intval(get_option('mohaazon-lifetime-membership-price'));
                        // delete user if not paid the full lifetime membership cost
                        // naims_delete_user($user_id);
                }
                else{
                    //do nothing
                    $membership_cost = intval(get_option('mohaazon-monthly-membership-price'));
                }



                if($due > 0){
                    // has due
                    $total_due = (($membership_cost + $due)*((100+$due_charge)/100));
                    
                }
                else if($due < 0){
                    // has advance
                    $total_due = $due + $membership_cost;
                    naims_auto_pay_function($user_id, $membership_cost);// creates an order for customer with id $user_id
                }
                else if($due == 0){
                    // cleared... newly add fresh due
                    $total_due = $due + $membership_cost;
                }
                else{
                    // nothing left
                    $total_due = $due + $membership_cost;
                }
                update_user_meta($user_id, 'total_due', $total_due );
                
            }
        }
        else{
             return false;
        }
    }


function naims_auto_pay_function($id, $mem_cost){
    global $wpdb;
        $address = array(
            'first_name' => get_user_meta($id, 'billing_first_name', true),
            'last_name'  => get_user_meta($id, 'billing_first_name', true),
            'company'    => '',
            'email'      => get_user_meta($id, 'billing_email', true),
            'phone'      => get_user_meta($id, 'billing_phone', true),
            'address_1'  => get_user_meta($id, 'billing_address_1', true),
            'address_2'  => '', 
            'city'       => get_user_meta($id, 'billing_city', true),
            'state'      => get_user_meta($id, 'billing_state', true),
            'postcode'   => get_user_meta($id, 'billing_postcode', true),
            'country'    => get_user_meta($id, 'billing_country', true)
        );
        
    
        $title = 'Due';
        $query = "SELECT ID FROM {$wpdb->prefix}posts WHERE post_title = '$title' AND post_status = 'publish' AND post_type = 'product'";
        $product_id_2 = $wpdb->get_var($query);
    
        $order = wc_create_order();
        $order->add_product( wc_get_product( $product_id_2 ), 1,[
            'subtotal'     => $mem_cost, // e.g. 32.95
            'total'        => $mem_cost, // e.g. 32.95
        ]  ); 
        $order->set_address( $address, 'billing' );
        $order->set_address( $address, 'shipping' );
        $order->update_status('completed', 'From advance', true);
        $order->set_customer_id($id); 
        $order->calculate_totals();

}
