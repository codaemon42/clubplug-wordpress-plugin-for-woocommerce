<?php
    /**
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
    add_action( 'woocommerce_register_form_start', 'naims_wooc_extra_register_fields' );
    function naims_wooc_extra_register_fields() {
        $monthly_cost = get_option('mohaazon-monthly-membership-price', 2000);
        $premium_monthly = get_option('mohaazon-premium-monthly-membership-price', 5000);
        $lifetime_cost = get_option('mohaazon-lifetime-membership-price', 10000);
        ?>
        <p class="form-row form-row-first">
        <label for="reg_billing_first_name"><?php _e( 'First name', 'woocommerce' ); ?><span class="required">*</span></label>
        <input type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) esc_attr_e( $_POST['billing_first_name'] ); ?>" />
        </p>
        <p class="form-row form-row-last">
        <label for="reg_billing_last_name"><?php _e( 'Last name', 'woocommerce' ); ?><span class="required">*</span></label>
        <input type="text" class="input-text" name="billing_last_name" id="reg_billing_last_name" value="<?php if ( ! empty( $_POST['billing_last_name'] ) ) esc_attr_e( $_POST['billing_last_name'] ); ?>" />
        </p>
        <p class="form-row form-row-wide">
        <label for="reg_billing_phone"><?php _e( 'Phone', 'woocommerce' ); ?><span class="required">*</span></label>
        <input type="text" class="input-text" name="billing_phone" id="reg_billing_phone" value="<?php if ( ! empty( $_POST['billing_phone'] ) ) esc_attr_e( $_POST['billing_phone'] ); ?>" />
        </p>

        <p class="form-row form-row-wide">
        <label for="membership"><h3><b><?php _e( 'Select Membership', 'woocommerce' ); ?><span class="required">*</span></b></h3></label>
        <!-- <p><h3>Please select your Membership plan:</h3></p> -->
        <div id="membership" style="margin-left: 20px;">
            <input type="radio" id="monthly" name="membership_plan" value="monthly">
            <label for="monthly">Monthly (<?php echo $monthly_cost.'Tk / month'; ?>)</label><br>
            <input type="radio" id="prem_monthly" name="membership_plan" value="premium_monthly">
            <label for="prem_monthly">Premium Monthly (<?php echo $premium_monthly.'Tk / month'; ?>)</label><br>
            <input type="radio" id="lifetime" name="membership_plan" value="lifetime">
            <label for="lifetime">Lifetime (<?php echo $lifetime_cost.'Tk for Lifetime'; ?>)</label>
        </div>
        </p>
        <div class="clear"></div>
        <?php
  }

    /**
    * register fields Validating.
    */
    add_action( 'woocommerce_register_post', 'naims_wooc_validate_extra_register_fields', 10, 3 );
    function naims_wooc_validate_extra_register_fields( $username, $email, $validation_errors ) {
        if ( isset( $_POST['billing_first_name'] ) && empty( $_POST['billing_first_name'] ) ) {
               $validation_errors->add( 'billing_first_name_error', __( '<strong>Error</strong>: First name is required!', 'woocommerce' ) );
        }
        if ( isset( $_POST['billing_last_name'] ) && empty( $_POST['billing_last_name'] ) ) {
               $validation_errors->add( 'billing_last_name_error', __( '<strong>Error</strong>: Last name is required!.', 'woocommerce' ) );
        }
        if ( isset( $_POST['billing_phone'] ) && empty( $_POST['billing_phone'] ) ) {
            $validation_errors->add( 'billing_phone_error', __( '<strong>Error</strong>: Phone number is required!.', 'woocommerce' ) );
        }
        if ( isset( $_POST['membership_plan'] ) && empty( $_POST['membership_plan'] ) ) {
            $validation_errors->add( 'membership_plan_error', __( '<strong>Error</strong>: Membership Plan is required!.', 'woocommerce' ) );
        }
           return $validation_errors;
  }


      /**
    * Below code save extra fields.
    */
    add_action( 'woocommerce_created_customer', 'naims_wooc_save_extra_register_fields' );
    function naims_wooc_save_extra_register_fields( $customer_id ) {
        if ( isset( $_POST['billing_phone'] ) ) {
                     // Phone input filed which is used in WooCommerce
                     update_user_meta( $customer_id, 'billing_phone', sanitize_text_field( $_POST['billing_phone'] ) );
              }
          if ( isset( $_POST['billing_first_name'] ) ) {
                 //First name field which is by default
                 update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
                 // First name field which is used in WooCommerce
                 update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
          }
          if ( isset( $_POST['billing_last_name'] ) ) {
                 // Last name field which is by default
                 update_user_meta( $customer_id, 'last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
                 // Last name field which is used in WooCommerce
                 update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
          }
          if ( isset( $_POST['membership_plan'] ) ) {
                // membership radio input filed which is used in WooCommerce
                update_user_meta( $customer_id, 'naims_membership', sanitize_text_field( $_POST['membership_plan'] ) );

                // updating total_due usermeta
                if($_POST['membership_plan'] == 'monthly'){
                    // here
                    update_user_meta( $customer_id, 'total_due', get_option('mohaazon-monthly-membership-price') );
                }                 
                if($_POST['membership_plan'] == 'premium_monthly'){
                    // here
                    update_user_meta( $customer_id, 'total_due', get_option('mohaazon-premium-monthly-membership-price') );
                }
                if($_POST['membership_plan'] == 'lifetime'){
                    // here
                    update_user_meta( $customer_id, 'total_due', get_option('mohaazon-lifetime-membership-price') );

                    // sms 
                    $sms_url = get_option('sms-api-url', 'http://66.45.237.70/api.php');
                    $sms_username = get_option('sms-api-username', 'user');
                    $sms_password = get_option('sms-api-password', 'pass');
                    $sms_admin_number = "88" . get_option('admin-phone-number');
                  
                    $url = $sms_url;
                    $username = $sms_username;
                    $password = $sms_password;
                    $phone = "88" . $order->get_billing_phone();
                    $message = "Hello " . $order->get_billing_first_name() . " " . $order->get_billing_last_name() . ",\n\nthank you for your Registration  as a Lifetime Member\n  After confirmation please pay the fees of lifetime membership plan otherwise your membership will be cancelled\n pay here: ".site_url()."/my-account/club-payment/";
                    $data = array(
                      "username" => $username,
                      "password" => $password,
                      "number" => $phone,
                      "message" => $message,
                    );
                    $data_admin = array(
                      "username" => $username,
                      "password" => $password,
                      "number" => $sms_admin_number,
                      "message" => $admin_message,
                    );
                  
                    mohaazon_send_sms($url, $data);
                    mohaazon_send_sms($url, $data_admin);
                }
                else{
                    // do nothing
                }
            }
    }
