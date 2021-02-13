<?php
add_action("admin_menu", "mohaazon_add_sms_submenu_page");

function mohaazon_add_sms_submenu_page(){
  add_submenu_page( 'woocommerce', 'SMS API Integration Page', 'Mohaazon SMS API',
    'manage_options', 'mohaazon-sms-integration', 'mohaazon_sms_integration_callback');
}

function mohaazon_sms_integration_callback(){

  if (!current_user_can('manage_options')) {
      wp_die('Unauthorized user');
  }

  if (isset($_POST['save'])) {

    if (isset($_POST['sms-api-url'])) {
      $url = $_POST["sms-api-url"];
      update_option('sms-api-url', $url);
    }

    if (isset($_POST['sms-api-username'])) {
      $username = $_POST["sms-api-username"];
      update_option('sms-api-username', $username);
    }

    if (isset($_POST['sms-api-password'])) {
      $password = $_POST["sms-api-password"];
      update_option('sms-api-password', $password);
    }

    if (isset($_POST['admin-phone-number'])) {
        $admin_phone = $_POST["admin-phone-number"];
        update_option('admin-phone-number', $admin_phone);
      }

    echo '<div id="setting-error-settings_updated" class="notice notice-success settings-error is-dismissible">
<p><strong>Settings saved.</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';

  }

  $sms_url = get_option('sms-api-url', 'http://66.45.237.70/api.php');
  $sms_username = get_option('sms-api-username', 'user');
  $sms_password = get_option('sms-api-password', 'pass');
  $sms_admin_number = get_option('admin-phone-number', '017xxxxxxxx');
  ?>
  <div class="wrap woocommerce">
    <form method="post" id="mainform" action="" enctype="multipart/form-data">
    <?php settings_errors(); ?>
    <h1>SMS API Integration</h1>
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row" class="titledesc">
            <label for="sms-api-url">API URL </label>
          </th>
          <td class="forminp">
            <fieldset>
              <legend class="screen-reader-text"><span>API URL</span></legend>
              <input class="input-text regular-input " type="url" name="sms-api-url" id="sms-api-url" style="" value="<?php echo $sms_url; ?>" placeholder="">
              <p class="description">The API link / url you have got from your sms gateway provider. It can be like this ( http://66.45.237.70/api.php )</p>
            </fieldset>
          </td>
        </tr>

        <tr valign="top">
          <th scope="row" class="titledesc">
            <label for="sms-api-username">Username </label>
          </th>
          <td class="forminp">
            <fieldset>
              <legend class="screen-reader-text"><span>Username</span></legend>
              <input class="input-text regular-input " type="text" name="sms-api-username" id="sms-api-username" style="" value="<?php echo $sms_username; ?>" placeholder="">
              <p class="description">The Username of your API</p>
            </fieldset>
          </td>
        </tr>

        <tr valign="top">
          <th scope="row" class="titledesc">
            <label for="sms-api-password">Password </label>
          </th>
          <td class="forminp">
            <fieldset>
              <legend class="screen-reader-text"><span>Password</span></legend>
              <input class="input-text regular-input " type="password" name="sms-api-password" id="sms-api-password" style="" value="<?php echo $sms_password; ?>" placeholder="">
              <p class="description">The Password of your API</p>
            </fieldset>
          </td>
        </tr>

        <tr valign="top">
          <th scope="row" class="titledesc">
            <label for="admin-phone-number">Admin Phone Number </label>
          </th>
          <td class="forminp">
            <fieldset>
              <legend class="screen-reader-text"><span>Admin Phone Number </span></legend>
              <input class="input-text regular-input " type="text" name="admin-phone-number" id="admin-phone-number" style="" value="<?php echo $sms_admin_number; ?>" placeholder="">
              <p class="description">Admin phone number to which sms will be received after successful payment (017xxxxxxxx)</p>
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

add_action( 'woocommerce_order_status_completed', 'mohaazon_send_sms_when_order_received');

function mohaazon_send_sms_when_order_received($order_id){

  //$order_id = get_query_var('order-received');
  $order = new WC_Order( $order_id );

  $sms_url = get_option('sms-api-url', 'http://66.45.237.70/api.php');
  $sms_username = get_option('sms-api-username', 'user');
  $sms_password = get_option('sms-api-password', 'pass');
  $sms_admin_number = "88" . get_option('admin-phone-number');

  $url = $sms_url;
  $username = $sms_username;
  $password = $sms_password;
  $phone = "88" . $order->get_billing_phone();
  $message = "Hello " . $order->get_billing_first_name() . " " . $order->get_billing_last_name() . ",\n\nthank you for your order \n Order#" . $order_id . ".\n Total amount : " .$order->get_total()."Tk.\n View the latest status of your order here\n" . site_url() ."/my-account/orders/";
  $admin_message = "Hello admin, \n new payment from" . $order->get_billing_first_name() . " " . $order->get_billing_last_name() . ",\n Order#" . $order_id . ".\n Total amount : " .$order->get_total()."Tk \n Contact no: ".$order->get_billing_phone();
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

/**
 * -----------------
 * Send SMS function
 * -----------------
 */
function mohaazon_send_sms($url, $data){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url );
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data) );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
    $smsresult = curl_exec($ch);
}
