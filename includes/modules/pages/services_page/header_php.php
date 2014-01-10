<?php

 // This should be first line of the script:
$zco_notifier->notify('NOTIFY_HEADER_START_SERVICES_PAGE');
/**
 * load language files
 */
require(DIR_WS_MODULES . zen_get_module_directory('require_languages.php'));
$breadcrumb->add('Services');
// include template specific file name defines
$define_page_design = zen_get_file_directory(DIR_WS_LANGUAGES . $_SESSION['language'] . '/html_includes/', FILENAME_SERVICES_DESIGN, 'false');
$define_page_delivery = zen_get_file_directory(DIR_WS_LANGUAGES . $_SESSION['language'] . '/html_includes/', FILENAME_SERVICES_DELIVERY, 'false');
$define_page_installation = zen_get_file_directory(DIR_WS_LANGUAGES . $_SESSION['language'] . '/html_includes/', FILENAME_SERVICES_INSTALLATION, 'false');
?>
