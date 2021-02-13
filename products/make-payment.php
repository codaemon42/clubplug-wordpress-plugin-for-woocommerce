<?php
/* Create Buy Now Button dynamically after Add To Cart button */
add_action( 'woocommerce_after_add_to_cart_button', 'add_content_after_addtocart' );
function add_content_after_addtocart() {
    
    // get the current post/product ID
    $current_product_id = get_the_ID();

    // get the product based on the ID
    $product = wc_get_product( $current_product_id );

    // get the "Checkout Page" URL
    $checkout_url = wc_get_checkout_url();

    // run only on simple products
    if( $product->is_type( 'simple' ) ){
        echo '<a href="'.$checkout_url.'?add-to-cart='.$current_product_id.'" class="buy-now button">Make Payment</a>';
        //echo '<a href="'.$checkout_url.'" class="buy-now button">Buy Now</a>';
    }
}

/* Forcefully Redirect to checkout page if it goes to cart page */
add_filter('woocommerce_add_to_cart_redirect', 'naims_add_to_cart_redirect');
function naims_add_to_cart_redirect() {
 global $woocommerce;
 $checkout_url = wc_get_checkout_url();
 if(get_if_due_in_cart()){
     return $checkout_url;
 }

}