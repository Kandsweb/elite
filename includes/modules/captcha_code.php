<?php
//define('CAPTCHA_PUBLIC_KEY', '6LcDI78SAAAAAPtvT-GJOoW2rh4OaqaPm65GuUn-');//store @ Kands
//define('CAPTCHA_PRIVATE_KEY', '6LcDI78SAAAAAPuAfqAnnG-vUQvXul2pP2eHfvBw');
define('CAPTCHA_PUBLIC_KEY', '6Lek7cISAAAAAC8KQi-dnB7FvMa1D2kwqQhhPwVF ');//Live
define('CAPTCHA_PRIVATE_KEY', '6Lek7cISAAAAAAdOeNIV38usqoTzP-ZmRL4SEZKv');

define('EMAIL_CHECK_SENDER','info@elitelightingni.com');

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
