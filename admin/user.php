<?php
/**
 * 
 * f(1)-> show membership column on users->all users --usermeta = naims_membership
 * f(2)-> show Due column on users->all users --usermeta = total_due
 * 
 * f(3)-> membership selection otpion on users->edit_user --usermeta = naims_membership
 * f(4)-> total due edit option on users->edit_user --usermeta = total_due
 * 
 * @param total_due -> case: due
 * @param naims_membership -> case: membership
 * 
 */
add_action('manage_users_columns', 'register_custom_user_column');
add_action('manage_users_custom_column', 'register_custom_user_column_view', 10, 3);
function register_custom_user_column($columns) {
    unset($columns['posts']);
    $columns['phone'] = 'Phone';
    $columns['due'] = 'Due';
    $columns['membership'] = 'Membership';
    $columns['registration_date'] = 'Registration date';
    $columns = 
    array_slice($columns, 0, 1) // leave the checkbox in place (the 0th column)
    + array("profile_image" => "Image") // splice in a custom avatar column in the next space
    + array_slice($columns, 1) // include any other remaining columns (the 1st column onwards)
;
    return $columns;
}
function register_custom_user_column_view($value, $column_name, $user_id) {
    //$user_info = get_userdata( $user_id );
    switch( $column_name ) {
        case 'profile_image':
            $url = wp_get_attachment_url(get_user_meta($user_id, 'image', true ));
            $value = $value.'<img src="'.$url.'" width="30" height="30" style="border-radius: 5px; box-shadow:1px 1px 2px 1px #666" />';  
            return $value;
            //? $value : '---'
        break;
        case 'phone':
            return ($value = get_user_meta($user_id, 'billing_phone', true ) ) ;
            //? $value : '---'
        break;
        case 'due':
            return ($value = get_user_meta($user_id, 'total_due', true ) ) ;
            //? $value : '---'
        break;
        case 'membership':
            return ($value = get_user_meta($user_id, 'naims_membership', true ));
        break;
        case 'registration_date' :
			return date( 'j M, Y H:i', strtotime( get_the_author_meta( 'registered', $user_id ) ) );
		break;
    }

    //update_user_meta( $customer_id, 'billing_phone', sanitize_text_field( $_POST['billing_phone'] ) );
}

add_filter( 'manage_users_sortable_columns', 'mohaazon_make_registered_column_sortable' );
 
function mohaazon_make_registered_column_sortable( $columns ) {
	return wp_parse_args( array( 'registration_date' => 'registered' ), $columns );
}

/**
 * user meta box
 * @param total_due -> $due (int)
 * @param naims_membership -> $membership
 * 
 * usermeta naims_membership
     * @param monthly
     * @param premium_monthly
     * @param lifetime
 */

// Then we hook the function to "show_user_profile" and "edit_user_profile"
add_action( 'show_user_profile', 'mohaazon_extra_profile_fields', 10 );
add_action( 'edit_user_profile', 'mohaazon_extra_profile_fields', 10 );

function mohaazon_extra_profile_fields( $user ) { 

    $user_meta=get_userdata($user->ID); 
    $user_roles=$user_meta->roles; 
if(in_array("customer", $user_roles)){
    //$value = get_user_meta($user->ID, 'lifetime_member', true);
    $membership = get_user_meta($user->ID, 'naims_membership', true);
    $due = intval(get_user_meta($user->ID, 'total_due', true));
    ?>

   <h3><?php _e('Membership Details'); ?></h3>
   <table class="form-table" style="background: #0b7ccc; color: #fff;padding: 10px;border-radius: 5px;box-shadow: 2px 2px 5px 2px #909091;">
       <tr>
           <th><label style="margin-left:20px; color:#fff;" for="membership">Membership</label></th>
           <td>
           <!-- <input type="text" name="membership" id="membership" value="<?php echo $membership; ?>" class="regular-text" /><br /> -->
           <select name="mohaazon_membership" id="membership">
                <option value="<?php echo (!empty($membership) ?  $membership : '') ?>"><?php echo (!empty($membership) ?  $membership : 'Select something...') ?></option>
                <option value="monthly" <?php selected( $membership, 'something' ); ?>>monthly</option>
                <option value="premium_monthly" <?php selected( $membership, 'something' ); ?>>premium_monthly</option>
                <option value="lifetime" <?php selected( $membership, 'something' ); ?>>lifetime</option>
            </select>
           <span class="description">Choose a membership plan.</span>
           </td>
       </tr>
       <tr>
           <th><label style="margin-left:20px; color:#fff;" for="total_due">Total Due</label></th>
           <td>
                <input type="number" name="mohaazon_total_due" id="membership" value="<?php echo $due; ?>" class="regular-text" /><span>Tk</span><br />
           <span class="description">edit due amount ( due amount will be negative if the member pays as advance ex. -200)</span>
           </td>
       </tr>
       <tr>
        <th><label style="margin-left:20px; color:#fff;" for="total_due">Profile Photo</label></th>
        <td>
            <?php
            $image_id = get_user_meta($user->ID, 'image', true);
            if( $image = wp_get_attachment_image_src( $image_id ) ) {
        
                    echo '<a href="#" class="misha-upl"><img style="border-radius:5px;" src="' . $image[0] . '" /></a>
                        <a href="#" class="misha-rmv" style="border-radius:5px; padding: 10px;background: #0e0487;box-shadow: 1px 1px 22px 2px #0e035d78;text-decoration: none;">Remove image</a>
                        <input style="display:none" type="text" name="naims_pro_pic" value="' . $image_id . '">
                        <input type="hidden" name="misha-img" value="' . $image_id . '">';

                    } else {

                    echo '<a href="#" class="misha-upl" style="padding: 10px;background: #0e0487;box-shadow: 1px 1px 22px 2px #0e035d78;text-decoration: none;">Upload image</a>
                        <a href="#" class="misha-rmv" style="display:none">Remove image</a>
                        <input style="display:none" type="text" name="naims_pro_pic" value="">
                        <input type="hidden" name="misha-img" value="">';

                    }
                    ?>      
        </td>
       </tr>
      
   </table>
<?php
}

}

/**
 * user meta box save
 * @param naims_membership -> name: mohaazon_membership
 * @param total_due -> name: mohaazon_total_due
 * 
 * usermeta naims_membership
     * @param monthly
     * @param premium_monthly
     * @param lifetime
 */

// Then we hook the function to "show_user_profile" and "edit_user_profile"
add_action( 'personal_options_update', 'mohaazon_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'mohaazon_save_extra_profile_fields' );
function mohaazon_save_extra_profile_fields( $user_id ) {
    $user_meta=get_userdata($user_id); 
    $user_roles=$user_meta->roles; 

    if(in_array("customer", $user_roles)){
        if ( !current_user_can( 'edit_user', $user_id ) )
            return false;

        /* Edit the following lines according to your set fields */
        update_usermeta( $user_id, 'naims_membership', $_POST['mohaazon_membership'] );
        update_usermeta( $user_id, 'total_due', $_POST['mohaazon_total_due'] );
        update_usermeta( $user_id, 'image', $_POST['naims_pro_pic'] );
       
    }

}

