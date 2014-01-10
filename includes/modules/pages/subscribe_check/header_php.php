<?php
//
// +----------------------------------------------------------------------+
// |zen-cart Open Source E-commerce                                       |
// +----------------------------------------------------------------------+
// | Copyright (c) 2003 The zen-cart developers                           |
// |                                                                      |
// | http://www.zen-cart.com/index.php                                    |
// |                                                                      |
// | Portions Copyright (c) 2003 osCommerce                               |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the GPL license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.zen-cart.com/license/2_0.txt.                             |
// | If you did not receive a copy of the zen-cart license and are unable |
// | to obtain it through the world-wide-web, please send a note to       |
// | license@zen-cart.com so we can mail you a copy immediately.          |
// +----------------------------------------------------------------------+
// $Id: header_php.php,v 1.0 05/12/2010 10:59:45 Owner KandS
//
	if ($_SESSION['customer_id'])
		zen_redirect(zen_href_link(FILENAME_ACCOUNT_NEWSLETTERS));


	$_SESSION['navigation']->remove_current_page();

	//require(DIR_WS_MODULES . 'require_languages.php');
  require(DIR_WS_MODULES . zen_get_module_directory('require_languages.php'));

  $emailaddress = (isset($_GET['e'])?$_GET['e'] : $_POST['email']);
  $emailformat = (isset($_GET['f'])?$_GET['f'] : $_POST['email_format']);

  $email_address = zen_db_prepare_input($emailaddress);
  $email_format = zen_db_prepare_input($emailformat);

  $invalid_address = false;
  $sender = EMAIL_CHECK_SENDER;

 // $SMTP_Valid = new SMTP_validateEmail();
 // if(!$SMTP_Valid->validate($emailaddress, $sender)){
 //   $messageStack->add('invalidemail',$SMTP_Valid->message,'error');
 //   $invalid_address = true;
 // }

  //if(!validate_email($emailaddress)){
 //  $messageStack->add('invalidemail','Fail 2nd','error');
    //$invalid_address = true;
 // }


   if(!defined('NEWSONLY_SUBSCRIPTION_ENABLED') ||  (NEWSONLY_SUBSCRIPTION_ENABLED=='false')) {
    $error = true;
    $messageStack->add('subscribe', TEXT_NEWSONLY_SUBSCRIPTIONS_DISABLED, 'error');
  } else {

  }


  if(isset($_POST['pass'])){
    if($_POST['pass'] == 'check' && !$invalid_address && !$error){
      //Validate recaptcha
      $errors=array();
      $privatekey = CAPTCHA_PRIVATE_KEY;
      $resp = recaptcha_check_answer ($privatekey,
                                      $_SERVER["REMOTE_ADDR"],
                                      $_POST["recaptcha_challenge_field"],
                                      $_POST["recaptcha_response_field"]);
      unset($_SESSION['pass']);
      if (!$resp->is_valid) {
        //Display error back to the client is the recaptcha entered is incorrect.
          $messageStack->add('captcha', TEXT_CAPTCHA_ERROR);
        //End of recaptcha validation
      }else {
        //Successful Recaptcha validation, get posted values from the web form
        // check if email address exists in CUSTOMERS table or in SUBSCRIBERS table
        $check_cust_email_query = "select count(*) as total from " . TABLE_CUSTOMERS .
          " where customers_email_address = '" . zen_db_input($email_address) . "'";
        $check_cust_email = $db->Execute($check_cust_email_query);

        $check_news_email_query = "select confirmed, count(*) as total from " . TABLE_SUBSCRIBERS .
          " where email_address = '" . zen_db_input($email_address) . "'";
        $check_news_email = $db->Execute($check_news_email_query);

        if ($check_cust_email->fields['total'] > 0) {
          $error = true;
          $messageStack->add('subscribe', SUBSCRIBE_DUPLICATE_CUSTOMERS_ERROR, 'error');
        } elseif ($check_news_email->fields['total'] > 0) {
          $error = true;
          if($check_news_email->fields['confirmed']=='1'){
            $messageStack->add('subscribe', SUBSCRIBE_DUPLICATE_NEWSONLY_ACCT, 'caution');
          }else{
            $messageStack->add('subscribe', sprintf(SUBSCRIBE_DUPLICATE_NEWSONLY_ERROR, zen_href_link(FILENAME_SUBSCRIBE, 'a=E5D1&e='.$emailaddress),$SMTP_Valid->return_code), 'error');
          }
        }

        if(!$error){
          $_SESSION['sign_up_pass']['email'] = $emailaddress;
          $_SESSION['sign_up_pass']['email_format'] = $emailformat;

          zen_redirect(zen_href_link(FILENAME_SUBSCRIBE, ''));
        }
      }
    }
  }

  $breadcrumb->add(NAVBAR_TITLE);

?>
