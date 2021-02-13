<?php

/**
 * update 2
 */

function disable_repeat_purchase( $purchasable, $product ) {
    if ( $product->is_type( 'variable' ) ) {
        return $purchasable;
    }

    // Get the ID for the current product
    $product_id = $product->is_type( 'variation' ) ? $product->variation_id : $product->id; 

    // return false statement if the customer has bought the product / variation
    if ( wc_customer_bought_product( wp_get_current_user()->user_email, get_current_user_id(), $product_id ) ) {
        $purchasable = false;
    }

    // Double-check for variations: if parent is not purchasable, then variation is not
    if ( $purchasable && $product->is_type( 'variation' ) ) {
        $purchasable = $product->parent->is_purchasable();
    }

    return $purchasable;
}
add_filter( 'woocommerce_is_purchasable', 'disable_repeat_purchase', 10, 2 );


/**
 * update 3
 */

function purchase_disabled_message() {

    global $product;

    if ( $product->is_type( 'variable' ) ) {

        foreach ( $product->get_children() as $variation_id ) {
            // Render the purchase restricted message if it has been purchased
            if ( wc_customer_bought_product( wp_get_current_user()->user_email, get_current_user_id(), $variation_id ) ) {
                render_variation_non_purchasable_message( $product, $variation_id );
            }
        }

    } else {
        if ( wc_customer_bought_product( wp_get_current_user()->user_email, get_current_user_id(), $product->id ) ) {
            echo '<div class="woocommerce"><div class="woocommerce-info wc-nonpurchasable-message">You\'ve already purchased this product! It can only be purchased once per customer.</div></div>';
        }
    }
}
add_action( 'woocommerce_single_product_summary', 'purchase_disabled_message', 31 );


function render_variation_non_purchasable_message( $product, $no_repeats_id ) {

    if ( $product->is_type( 'variable' ) && $product->has_child() ) {

        $variation_purchasable = true;

        foreach ( $product->get_available_variations() as $variation ) {

            if ( $no_repeats_id === $variation['variation_id'] ) {
                $variation_purchasable = false; 
                echo '<div class="woocommerce"><div class="woocommerce-info wc-nonpurchasable-message js-variation-' . sanitize_html_class( $variation['variation_id'] ) . '">You\'ve already purchased this product! It can only be purchased once per customer.</div></div>';
            }
        }
    }

    if ( ! $variation_purchasable ) {
        wc_enqueue_js("
            jQuery('.variations_form')
                .on( 'woocommerce_variation_select_change', function( event ) {
                    jQuery('.wc-nonpurchasable-message').hide();
                })
                .on( 'found_variation', function( event, variation ) {
                    jQuery('.wc-nonpurchasable-message').hide();
                    if ( ! variation.is_purchasable ) {
                        jQuery( '.wc-nonpurchasable-message.js-variation-' + variation.variation_id ).show();
                    }
                })
            .find( '.variations select' ).change();
        ");
    }
}