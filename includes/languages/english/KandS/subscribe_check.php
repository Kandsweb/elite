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
// $Id: subscribe.php,v 1.1 2006/06/16 01:46:14 Owner Exp $
//
//define('CAPTCHA_PUBLIC_KEY', '6LcDI78SAAAAAPtvT-GJOoW2rh4OaqaPm65GuUn-');//Testing
//define('CAPTCHA_PRIVATE_KEY', '6LcDI78SAAAAAPuAfqAnnG-vUQvXul2pP2eHfvBw');
define('CAPTCHA_PUBLIC_KEY', '6Lek7cISAAAAAC8KQi-dnB7FvMa1D2kwqQhhPwVF ');//Live
define('CAPTCHA_PRIVATE_KEY', '6Lek7cISAAAAAAdOeNIV38usqoTzP-ZmRL4SEZKv');

define('EMAIL_CHECK_SENDER','info@elitelightingni.com');

define('NAVBAR_TITLE', 'Subscribe');
define('HEADING_TITLE', 'Newsletter Subscribe');

define('SUBSCRIBE_CAPTCHA_INSTRUCTIONS', 'Thank you for your interest in the Elite Lighting newsletter. We just need you to type the captcha code below to prevent  automated bot submissions to our system.<br/>');
define('SUBSCRIBE_EMAIL_ENTERED','<br/>The email address you have entered is');

define('TEXT_INFORMATION', '');
// you don't need to fill in TEXT_INFORMATION if you wish to edit the subscribe text from the Admin area
// If filled in, this text is shown below the defined page text
// Note: This uses the same defined_page for both subscriptions and confirmation
define('TEXT_INFORMATION_RESEND', 'A confirmation email has been resent to you<br/><br/><span style="font-size:12px; color:#F00"><b>IMPORTANT</b><br/>
  Before you begin receiving your subscription to our newsletter, you MUST reply to our subscribe-confirm request sent to your email <strong>%s</strong>.
  <br />
  <br /></span>
  <span style="font-size:12px">Please check your e-mail inbox. When you receive the confirmation request, just click on the confirmation link enclosed in the email.
   Don\'t miss out on the latest '. STORE_NAME . ' offers - simply add '. STORE_OWNER_EMAIL_ADDRESS .' to your address book or contact list to ensure our emails always go into your inbox.
  <br />
  <br /></span>
  If you have trouble signing up, please send a message to <a href="mailto:' . STORE_OWNER_EMAIL_ADDRESS . '">'. STORE_OWNER_EMAIL_ADDRESS .'</a>.');

define('TEXT_CAPTCHA_ERROR', 'The code you typed does not match. Please try again.');
define('TEXT_BAD_EMAIL','The email address you have give is invalid.');

define('TEXT_EMAIL_ERROR_DOMAIN', '<b>%s</b> is an not a valid domain.<br/>Please check the part after the @ for spelling or typing errors.<br/>If you have trouble signing up, please send a message to <a href="mailto:' . STORE_OWNER_EMAIL_ADDRESS . '">'. STORE_OWNER_EMAIL_ADDRESS .'</a>.');
define('TEXT_EMAIL_ERROR_UNKNOWEN', 'Sorry an unknown error has occured and we can not verify your email address try refreshing this page.<br/>If you have trouble signing up, please send a message to <a href="mailto:' . STORE_OWNER_EMAIL_ADDRESS . '?subject=Newsletter Sign up (Error Code %d)">'. STORE_OWNER_EMAIL_ADDRESS .'</a>.<br/>Code:%d');
define('TEXT_EMAIL_ERROR_512', 'The mail server %s can not be found.<br/>Please check the part before the @ for spelling or typing errors.<br/>If you have trouble signing up, please send a message to <a href="mailto:' . STORE_OWNER_EMAIL_ADDRESS . '">'. STORE_OWNER_EMAIL_ADDRESS .'</a>.');
define('TEXT_EAIL_ERROR_541','The mail server %s has not answered our check on your email address. Please check your mail server is running and try again.<br/>If you have trouble signing up, please send a message to <a href="mailto:' . STORE_OWNER_EMAIL_ADDRESS . '">'. STORE_OWNER_EMAIL_ADDRESS .'</a>.');
define('TEXT_EAIL_ERROR_547','The mail server %s has not responded within a reasonable time to our check on your email address. Please check your mail server is running and try again.<br/>If you have trouble signing up, please send a message to <a href="mailto:' . STORE_OWNER_EMAIL_ADDRESS . '">'. STORE_OWNER_EMAIL_ADDRESS .'</a>.');
define('TEXT_EMAIL_ERROR_550', 'The mail server %s says %s is not a valid mail box<br/>Please check the part before the @ for spelling or typing errors');
define('TEXT_EMAIL_ERROR_554', 'The mail server %s says is not accepting mail');
define('TEXT_EMAIL_ERROR_552', 'The mail server %s has rejected our check. It says this is because your mail box is full. Please check your email account and try again.');
?>
