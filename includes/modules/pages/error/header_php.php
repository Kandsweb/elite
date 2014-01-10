<?php
/**
 * Contact Us Page
 *
 * @package page
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: header_php.php 3230 2006-03-20 23:21:29Z drbyte $
 */
require(DIR_WS_MODULES . zen_get_module_directory('require_languages.php'));

$ec = $_GET['ec'];

switch($ec){
  case 400:
    $e_title = 'Invalid Request';
    $e_mgs = 'Sorry an error has occoured while processing your request. Please try again.';
    break;
  case 403:
    $e_title = 'Forbiden Area';
    $e_mgs = 'The page you are looking for is restricted.';
    break;
  case 404:
    $e_title = 'Page Not Found';
    $e_mgs = 'Sorry the page you are looking for is no longer available.';
    break;
  case 500:
    $e_title = 'Technical Error';
    $e_mgs = 'Sorry a technical error has occoured while processing your request. Please try again. If you find this is a recurring problem please <a href="'. zen_href_link(FILENAME_CONTACT_US).'">contact us</a>';
    break;
}

// include template specific file name defines
//$define_page = zen_get_file_directory(DIR_WS_LANGUAGES . $_SESSION['language'] . '/html_includes/', 'define_map', 'false');

$breadcrumb->add($e_title);
?>