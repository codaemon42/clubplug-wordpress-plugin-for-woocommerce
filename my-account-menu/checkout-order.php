<?php

add_filter( 'woocommerce_order_status_completed', 'mohaazon_club_new_payment');
function mohaazon_club_new_payment($order_id){
    global $wpdb;
    $order = new WC_Order( $order_id );
    $user_id = $order->get_user_id();
    $due_id = get_due_product_id();
    $has_due = false;
    //;
//check if ordered item is not due
    $items = $order->get_items();
    foreach ( $order->get_items() as $item_id => $item ) {
        $product_id = $item->get_product_id();
        if($product_id == $due_id){
            $has_due = true;
            break;
        }
    }

    if($has_due){
        $paid = $order->get_total();
        $pre_due = intval(get_user_meta($user_id, 'total_due', true));
        $due = $pre_due - $paid;
        update_user_meta($user_id, 'total_due', $due);
    }
    else{
        
    }
}

add_action('woocommerce_review_order_before_payment', 'mohaazon_add_payment_form_input');
function mohaazon_add_payment_form_input(){
    
    $is_due = get_if_due_in_cart();

    if($is_due){

    
    $minimum = intval(get_option('mohaazon-min-amount', 1000));
        ?>
            <div style="background:#1d8d2b; color:#fff; padding:10px; border-radius:5px; box-shadow: 2px 2px 6px 2px #66d356; margin:10px; text-align:center" class="payment_section">
                <h3>Enter the amount you want to pay Now : </h3>
                <label for="naims_checkout_amount">Amount : </label>
                <input style="width:200px; background:#fff; color:#666; padding:5px; border-radius:5px;border: 1px solid #3330;box-shadow: 2px 2px 5px 2px #10640d;" type="number" name="naims_checkout_amount" placeholder="1000" id="naims_checkout_amount">
                <button  style="background:#b22214; color:#fff; padding:8px; border-radius:5px; box-shadow: 1px 1px 3px 2px #6660; border: 1px solid #3330; margin:10px; text-align:center;box-shadow: 2px 2px 5px 2px #10640d;letter-spacing: 1.5px;" type="button" name="naims_confirm_amount_btn"> CONFIRM </button>
                <h5><?php echo "Minimum payable amount ".$minimum." tk"; ?></h5>
            </div>

        <?php
        
            }
        
}

add_action( 'woocommerce_before_calculate_totals', 'before_calculate_totals', 10, 1 );
function before_calculate_totals( $cart_obj ) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
        return;
    }
    // Iterate through each cart item
    $product_id = get_due_product_id();
    foreach( $cart_obj->get_cart() as $key=>$value ) {
        if($value['data']->get_id() == $product_id ){
            if( isset( $value['new_price'] ) ) {
                $price = $value['new_price'];
                $value['data']->set_price( ( $price ) );
                if(isset($_POST['naims_checkout_amount'])){
                    $minimum = intval(get_option('mohaazon-min-amount', 1000));
                    if( $_POST['naims_checkout_amount'] >= $minimum ){
                        $value['data']->set_price( intval( $_POST['naims_checkout_amount'] ) );
                    }
                    else{
                        $value['data']->set_price( $minimum );
                        wc_add_notice("Minimum payable amount ".$minimum." tk", 'error');
                    }
                }

            }
        }
    }
}

add_filter( 'woocommerce_add_cart_item_data', 'add_cart_item_data_test', 10, 4 );

function add_cart_item_data_test( $cart_item_data, $product_id, $variation_id, $quantity ) {  
            global $woocommerce;
            $has_item = false;
            $due_item_id = get_due_product_id(); // due_id

            if( $due_item_id == $product_id ){ // have only one item and that is due_id
                $woocommerce->cart->empty_cart();
                $due = intval(get_user_meta(get_current_user_id(), 'total_due', true));
                if($due <= 0 || $due == null || $due == ''){
                    $due = 1000;
                }
                $cart_item_data['new_price'] = $due;
            }

            else { // added item is not due_id      //if($due_item_id !== $product_id )
                $items = $woocommerce->cart->get_cart();       
                    foreach($items as $item => $values) { 
                    if($values['data']->get_id() == $due_item_id){                                
                        $has_item = true;
                        $key_to_remove = $item;
                        break;
                    }
                }
            }
            
            if($has_item){
                $woocommerce->cart->empty_cart();
            }

            return $cart_item_data;
}

function get_if_due_in_cart(){
    global $woocommerce;
    
    $productsInCart = array(); 
    
    $items = $woocommerce->cart->get_cart(); 
    
    foreach($items as $item => $values) { 
        $product_id = $values['data']->get_id();
        
        array_push( $productsInCart, $product_id);
    }
   $due_product = get_due_product_id();
   if(in_array($due_product, $productsInCart)){  
       return true;
     }
     else{
         return false;
     }
}

function get_due_product_id(){
    global $wpdb;
    $title = 'Due';
    $query = "SELECT ID FROM {$wpdb->prefix}posts WHERE post_title = '$title' AND `post_status` = 'publish' AND `post_type` = 'product'";
    return $product_id = $wpdb->get_var($query);
}