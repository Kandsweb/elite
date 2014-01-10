<?php
/**
 * Contact Us Page
 *
 * @package page
 * @copyright Copyright 2003-2010 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: header_php.php 16305 2010-05-21 20:48:55Z wilt $
 */
require(DIR_WS_MODULES . zen_get_module_directory('require_languages.php'));

require(DIR_WS_MODULES . zen_get_module_directory('captcha_code.php'));

$error = false;
  //used when first sent here from product
  if(isset($_GET['pId'])){
    $productId = $_GET['pId'];
  }
  if($productId){
    $res = $db->Execute('SELECT products_model FROM ' . TABLE_PRODUCTS . ' WHERE products_id = '.$productId);
    if(!$res->EOF){
      $product_model =  $res->fields['products_model'];
    }
  }
  ///used when cust submits contact form
  if(isset($_POST['modelId'])){
    $product_model = $_POST['modelId'];
    $pId = mid_to_pid($product_model);
    $tcp = zen_get_product_path($pId);
    $product_url = zen_href_link('product_info&cPath=' . $tcp . '&products_id=' . $pId);
    $pPrice = zen_get_products_actual_price($pId);
    if($pPrice==''){
      $pPrice = 'No price listed';
    }else{
      $pPrice =  $pPrice;
    }
    $product_name = zen_get_products_name($pID);
    $res = $db->Execute('SELECT manufactures_code FROM ' . TABLE_PRODUCTS_EXTRA_FIELDS . ' WHERE products_id = '.$pId);
    if(!$res->EOF){
      $manufacturers_code  =  $res->fields['manufactures_code'];
    }
    $sql = "select p.products_image from " . TABLE_PRODUCTS . " p  where products_id='" . (int)$pId . "'";
    $look_up = $db->Execute($sql);

    $product_image = DIR_WS_IMAGES . $look_up->fields['products_image'];

  }

if (isset($_GET['action']) && ($_GET['action'] == 'send')) {
  $name = zen_db_prepare_input($_POST['contactname']);
  $email_address = zen_db_prepare_input($_POST['email']);
  $enquiry = zen_db_prepare_input(strip_tags($_POST['enquiry']));

  $zc_validate_email = zen_validate_email($email_address);

  $invalid_address = false;
  $sender = EMAIL_CHECK_SENDER;

  $SMTP_Valid = new SMTP_validateEmail();
  if(!$SMTP_Valid->validate($email_address, $sender)){
    $messageStack->add('contact',$SMTP_Valid->message,'error');
    //$invalid_address = true;
  }

  if ($zc_validate_email and !empty($enquiry) and !empty($name)) {
    //Validate recaptcha
    $errors=array();
    $privatekey = CAPTCHA_PRIVATE_KEY;
    $resp = recaptcha_check_answer ($privatekey,
                                    $_SERVER["REMOTE_ADDR"],
                                    $_POST["recaptcha_challenge_field"],
                                    $_POST["recaptcha_response_field"]);
    if (!$resp->is_valid) {
      //Display error back to the client is the recaptcha entered is incorrect.
        $messageStack->add('contact', TEXT_CAPTCHA_ERROR);
      //End of recaptcha validation
    }else{

    // auto complete when logged in
    if($_SESSION['customer_id']) {
      $sql = "SELECT customers_id, customers_firstname, customers_lastname, customers_password, customers_email_address, customers_default_address_id
              FROM " . TABLE_CUSTOMERS . "
              WHERE customers_id = :customersID";

      $sql = $db->bindVars($sql, ':customersID', $_SESSION['customer_id'], 'integer');
      $check_customer = $db->Execute($sql);
      $customer_email= $check_customer->fields['customers_email_address'];
      $customer_name= $check_customer->fields['customers_firstname'] . ' ' . $check_customer->fields['customers_lastname'];
    } else {
      $customer_email = NOT_LOGGED_IN_TEXT;
      $customer_name = NOT_LOGGED_IN_TEXT;
    }

    // use contact us dropdown if defined
    if (CONTACT_US_LIST !=''){
      $send_to_array=explode("," ,CONTACT_US_LIST);
      preg_match('/\<[^>]+\>/', $send_to_array[$_POST['send_to']], $send_email_array);
      $send_to_email= preg_replace ("/>/", "", $send_email_array[0]);
      $send_to_email= trim(preg_replace("/</", "", $send_to_email));
      $send_to_name = trim(preg_replace('/\<[^*]*/', '', $send_to_array[$_POST['send_to']]));
    } else {  //otherwise default to EMAIL_FROM and store name
    $send_to_email = trim(EMAIL_FROM);
    $send_to_name =  trim(STORE_NAME);
    }
    // Prepare extra-info details
    $extra_info = email_collect_extra_info($name, $email_address, $customer_name, $customer_email);
    // Prepare Text-only portion of message
    $text_message = OFFICE_FROM . "\t" . $name . "\n" .
    OFFICE_EMAIL . "\t" . $email_address . "\n\n" ;
    ////Product enquiry///////////////
    $email_subject = EMAIL_SUBJECT;
    if($product_model){
      $email_subject = 'Product Enquiry from Website';
      $text_message .='------------ Product Info ----------------------------' . "\n\n" ;
      $text_message .= 'Product Code ' . $manufacturers_code . "\n\n";
      $text_message .= 'Product Model ' . $product_model . "\n\n" . $product_url . "\n\nPrice: £"  . $pPrice . "\n\n" ;
    }
    $text_message .= '------------ Message ---------------------------------' . "\n\n" ;
    $text_message .= strip_tags($_POST['enquiry']) .  "\n\n" ;
    $text_message .= '------------------------------------------------------' . "\n\n" .
    $extra_info['TEXT'];
    // Prepare HTML-portion of message
    //KandS - Do the product info to send in email
    $html = "<h2>Product Enquiry From Website</h2>
<hr /><h3>Product Info </h3>
<h4>".$product_name."</h4>
  Product Code:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>".$manufacturers_code."</b><br />
  Product Model:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>".$product_model."</b><br /><br />
  Price:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;£".$pPrice."<br /><br />
  Website Link:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$product_url."<br />".
  "<img name=\"product\" src=\"http://elitelightingni.com/".$product_image."\" width=\"120\" height=\"120\" alt=\"\" />";

    $html_msg['EMAIL_MESSAGE_HTML'] = $html;
    $html_msg['EMAIL_MESSAGE_HTML'] .= strip_tags($_POST['enquiry']);
    $html_msg['CONTACT_US_OFFICE_FROM'] = OFFICE_FROM . ' ' . $name . '<br />' . OFFICE_EMAIL . '(' . $email_address . ')';
    $html_msg['EXTRA_INFO'] = $extra_info['HTML'];
    // Send message
    zen_mail($send_to_name, $send_to_email, $email_subject, $text_message, $name, $email_address, $html_msg,'contact_us');

    zen_redirect(zen_href_link(FILENAME_CONTACT_US, 'action=success'));
    }
  } else {
    $error = true;
    if (empty($name)) {
      $messageStack->add('contact', ENTRY_EMAIL_NAME_CHECK_ERROR);
    }
    if ($zc_validate_email == false) {
      $messageStack->add('contact', ENTRY_EMAIL_ADDRESS_CHECK_ERROR);
    }
    if (empty($enquiry)) {
      $messageStack->add('contact', ENTRY_EMAIL_CONTENT_CHECK_ERROR);
    }
  }

} // end action==send

// default email and name if customer is logged in
if($_SESSION['customer_id']) {
  $sql = "SELECT customers_id, customers_firstname, customers_lastname, customers_password, customers_email_address, customers_default_address_id
          FROM " . TABLE_CUSTOMERS . "
          WHERE customers_id = :customersID";

  $sql = $db->bindVars($sql, ':customersID', $_SESSION['customer_id'], 'integer');
  $check_customer = $db->Execute($sql);
  $email_address = $check_customer->fields['customers_email_address'];
  $name= $check_customer->fields['customers_firstname'] . ' ' . $check_customer->fields['customers_lastname'];
}

if (CONTACT_US_LIST !=''){
  foreach(explode(",", CONTACT_US_LIST) as $k => $v) {
    $send_to_array[] = array('id' => $k, 'text' => preg_replace('/\<[^*]*/', '', $v));
  }
}

// include template specific file name defines
$define_page = zen_get_file_directory(DIR_WS_LANGUAGES . $_SESSION['language'] . '/html_includes/', FILENAME_DEFINE_CONTACT_US, 'false');

$breadcrumb->add(NAVBAR_TITLE);
?>