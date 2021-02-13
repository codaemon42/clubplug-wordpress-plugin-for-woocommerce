<?php
/**
 * OPTIONS : 
 * Monthly Cost  => mohaazon-monthly-membership-price
 * premium Cost  => mohaazon-premium-monthly-membership-price
 * Lifetime Cost => mohaazon-lifetime-membership-price
 * Monthly due   => mohaazon-due-fee-monthly
 * Due date      => mohaazon-due-date
 * Min amount      => mohaazon-min-amount
 */
add_action("admin_menu", "mohaazon_membership_pricing");

function mohaazon_membership_pricing(){
  add_submenu_page( 'woocommerce', 'Mohaazon Membership Pricing Page', 'Membership Pricing',
    'manage_options', 'mohaazon_membership_pricing', 'mohaazon_membership_pricing_callback');
}

function mohaazon_membership_pricing_callback(){

  if (!current_user_can('manage_options')) {
      wp_die('Unauthorized user');
  }

  if (isset($_POST['save'])) {

    if (isset($_POST['mohaazon-monthly-membership-price'])) {
      $monthly = $_POST["mohaazon-monthly-membership-price"];
      update_option('mohaazon-monthly-membership-price', $monthly);
    }

    if (isset($_POST['mohaazon-premium-monthly-membership-price'])) {
      $prem_monthly = $_POST["mohaazon-premium-monthly-membership-price"];
      update_option('mohaazon-premium-monthly-membership-price', $prem_monthly);
    }

    if (isset($_POST['mohaazon-lifetime-membership-price'])) {
      $lifetime = $_POST["mohaazon-lifetime-membership-price"];
      update_option('mohaazon-lifetime-membership-price', $lifetime);
        
    }

    if (isset($_POST['mohaazon-due-fee-monthly'])) {
      $due_fee = $_POST["mohaazon-due-fee-monthly"];
      update_option('mohaazon-due-fee-monthly', $due_fee);
    }

    if (isset($_POST['mohaazon-due-date'])) {
      $due_date = $_POST["mohaazon-due-date"];
      update_option('mohaazon-due-date', $due_date);
    }

    if (isset($_POST['mohaazon-min-amount'])) {
      $min_amount = $_POST["mohaazon-min-amount"];
      update_option('mohaazon-min-amount', $min_amount);

      global $wpdb;
      $title = 'Due';
      $query = "SELECT ID FROM {$wpdb->prefix}posts WHERE post_title = '$title' AND `post_status` = 'publish' AND `post_type` = 'product'";
      $product_id = $wpdb->get_var($query);
      update_post_meta($product_id, '_regular_price', $min_amount);
      update_post_meta($product_id, '_price', $min_amount);
    }

    echo '<div id="setting-error-settings_updated" class="notice notice-success settings-error is-dismissible">
<p><strong>Settings saved.</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';

  }

  $monthly_cost = get_option('mohaazon-monthly-membership-price', 1000);
  $premium_monthly = get_option('mohaazon-premium-monthly-membership-price', 2000);
  $lifetime_cost = get_option('mohaazon-lifetime-membership-price', 10000);
  $due_fee_percentage = get_option('mohaazon-due-fee-monthly', 1);
  $due_date_monthly = get_option('mohaazon-due-date', 21);
  $minimum_amount = get_option('mohaazon-min-amount', 1000);
  ?>
  <div class="wrap woocommerce">
    <form method="post" id="mainform" action="" enctype="multipart/form-data">
    <?php settings_errors(); ?>
    <h1>Membership pricing form</h1>
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row" class="titledesc">
            <label for="mohaazon-monthly-membership-price">Monthly Membership cost (Tk) </label>
          </th>
          <td class="forminp">
            <fieldset>
              <legend class="screen-reader-text"><span>Monthly Membership cost </span></legend>
              <input class="input-text regular-input " type="number" name="mohaazon-monthly-membership-price" id="mohaazon-monthly-membership-price" style="" value="<?php echo $monthly_cost; ?>" placeholder=""><span> TK</span>
              <p class="description">set the cost for monthly membership per person...</p>
            </fieldset>
          </td>
        </tr>

        <tr valign="top">
          <th scope="row" class="titledesc">
            <label for="mohaazon-monthly-membership-price">Premium Monthly Membership cost (Tk) </label>
          </th>
          <td class="forminp">
            <fieldset>
              <legend class="screen-reader-text"><span>Premium Monthly Membership cost </span></legend>
              <input class="input-text regular-input " type="number" name="mohaazon-premium-monthly-membership-price" id="mohaazon-premium-monthly-membership-price" style="" value="<?php echo $premium_monthly; ?>" placeholder=""><span> TK</span>
              <p class="description">set the cost for Premium monthly membership per person...</p>
            </fieldset>
          </td>
        </tr>

        <tr valign="top">
          <th scope="row" class="titledesc">
            <label for="mohaazon-due-fee-monthly">Delay Fee in Percentage (%) </label>
          </th>
          <td class="forminp">
            <fieldset>
              <legend class="screen-reader-text"><span>Monthly Membership Due Fee in Percentage </span></legend>
              <input class="input-text regular-input " type="number" name="mohaazon-due-fee-monthly" id="mohaazon-due-fee-monthly" style="" value="<?php echo $due_fee_percentage; ?>" placeholder=""><span> %</span>
              <p class="description">set the percentage of extra fees you want to charge of monthly payment for delay... ex. set 2 for 2% charge for delay...</p>
            </fieldset>
          </td>
        </tr>

        <tr valign="top">
          <th scope="row" class="titledesc">
            <label for="mohaazon-lifetime-membership-price">Lifetime Membership cost (Tk) </label>
          </th>
          <td class="forminp">
            <fieldset>
              <legend class="screen-reader-text"><span>Lifetime Membership cost</span></legend>
              <input class="input-text regular-input " type="number" name="mohaazon-lifetime-membership-price" id="mohaazon-lifetime-membership-price" style="" value="<?php echo $lifetime_cost; ?>" placeholder=""><span> TK</span>
              <p class="description">set the cost for Lifetime membership per person...</p>
            </fieldset>
          </td>
        </tr>

        <tr valign="top">
          <th scope="row" class="titledesc">
            <label for="mohaazon-lifetime-membership-price">Last date for monthly payment</label>
          </th>
          <td class="forminp">
            <fieldset>
              <legend class="screen-reader-text"><span>Last date for monthly payment</span></legend>
              <input class="input-text regular-input " type="number" name="mohaazon-due-date" id="mohaazon-due-date" style="" value="<?php echo $due_date_monthly; ?>" placeholder="">
              <p class="description">set the last date for monthly payment...</p>
            </fieldset>
          </td>
        </tr>

        <tr valign="top">
          <th scope="row" class="titledesc">
            <label for="mohaazon-monthly-membership-price">Minimum Amount to pay at a time (Tk) </label>
          </th>
          <td class="forminp">
            <fieldset>
              <legend class="screen-reader-text"><span>Minimum Amount to pay at a time (Tk) </span></legend>
              <input class="input-text regular-input " type="number" name="mohaazon-min-amount" id="mohaazon-min-amount" style="" value="<?php echo $minimum_amount; ?>" placeholder=""><span> TK</span>
              <p class="description">Minimum Amount to pay at a single transaction through surjopay payment gateway from your members (Tk) </p>
            </fieldset>
          </td>
        </tr>

      </tbody>
    </table>

    <p class="submit">
      <?php wp_nonce_field( 'sms_nonce_field' ); ?>
      <button name="save" class="button-primary woocommerce-save-button" type="submit" value="Save changes">Save changes</button>

    </p>
  </form>
  </div>
  <?php
}

