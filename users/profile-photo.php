<?php

// Add field
add_action( 'woocommerce_edit_account_form_start', 'action_woocommerce_edit_account_form_start' );
function action_woocommerce_edit_account_form_start() {
    ?>
        <div>
            <span class="naims-photo-append"></span>
        </div>
    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="image"><?php esc_html_e( 'Profile Photo', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>

        <div class='file-input'>
            <input type='file' class="woocommerce-Input" name="image" accept="image/x-png,image/gif,image/jpeg" >
            <span class='naims-button'>Choose</span>
            <span class='label' data-js-label>No photo selected yet</span>
        </div>
    </p>
    <?php
}


// Validate
add_action( 'woocommerce_save_account_details_errors','action_woocommerce_save_account_details_errors', 10, 1 );
function action_woocommerce_save_account_details_errors( $args ){
    if ( isset($_POST['image']) && empty($_POST['image']) ) {
        $args->add( 'image_error', __( 'Please provide a valid image', 'woocommerce' ) );
    }
}


// Save
add_action( 'woocommerce_save_account_details', 'action_woocommerce_save_account_details', 10, 1 );
function action_woocommerce_save_account_details( $user_id ) {  
    if ( isset( $_FILES['image'] ) ) {
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/media.php' );

        $attachment_id = media_handle_upload( 'image', 0 );

        if ( is_wp_error( $attachment_id ) ) {
            update_user_meta( $user_id, 'image', $_FILES['image'] . ": " . $attachment_id->get_error_message() );
        } else {
            update_user_meta( $user_id, 'image', $attachment_id );
        }
   }
}


// Add enctype to form to allow image upload
add_action( 'woocommerce_edit_account_form_tag', 'action_woocommerce_edit_account_form_tag' );
function action_woocommerce_edit_account_form_tag() {
    echo 'enctype="multipart/form-data"';
} 

// Display
add_action('woocommerce_account_navigation', 'action_woocommerce_edit_account_form');
function action_woocommerce_edit_account_form() {
    // Get current user id
    $user_id = get_current_user_id();

    // Get attachment id
    $attachment_id = get_user_meta( $user_id, 'image', true );

    // True
    if ( $attachment_id ) {
        $original_image_url = wp_get_attachment_url( $attachment_id );
         $img = wp_get_attachment_image_url( $attachment_id, 'thumbnail', true );

        ?>
        <div>
            <img style="border-radius:50%;width: 100px;height: 100px;box-shadow: 0px 0px 15px 1px #7d7d7d;" src="<?php echo $img; ?>" alt="">
        </div>
        <div style="margin: 20px 0;">
            <a href="<?php echo site_url('/my-account/edit-account/'); ?>" style="width: 100px;height: 100px;box-shadow: 0px 0px 15px 1px #7d7d7d;background: #3969f1;padding: 10px;color: #fff" >Upload Photo</a>
        </div>
        <!-- <div style="text-align: center;width: 100px; margin-top:10px; margin-bottom:10px " >Profile Photo</div> -->
        <?php
    }
    else{
        ?>
        <div style="margin: 20px 0;">
            <a href="<?php echo site_url('/my-account/edit-account/'); ?>" style="width: 100px;height: 100px;box-shadow: 0px 0px 15px 1px #7d7d7d;background: #3969f1;padding: 10px;color: #fff" >Upload Photo</a>
        </div>
        <div style="text-align: center;width: 100px; margin-top:10px " >Profile Photo</div>
        <?php
    }
} 





