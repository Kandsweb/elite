<?php
/**
 * Module Template
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_modules_featured_products.php 2935 2006-02-01 11:12:40Z birdbrain $
 */
  $zc_show_featured = false;
  include(DIR_WS_MODULES . zen_get_module_directory('recent_products.php'));
?>

<?php if ($zc_show_featured == true) { ?>
<div class="centerBoxWrapper" id="recentProducts">
<div id="productRecentHeading">Your recently viewed items</div>
<div id="productRecent">
<?php
    //set the carousel vars
    $carousel_id='recent_carousel';
    $carousel_class='jcarousel-skin-tango';

/**
 * require the list_box_content template to display the product
 */
  //require($template->get_template_dir('tpl_tabular_display.php',DIR_WS_TEMPLATE, $current_page_base,'common'). '/tpl_tabular_display.php');
  require($template->get_template_dir('tpl_carousel_display.php',DIR_WS_TEMPLATE, $current_page_base,'common'). '/tpl_carousel_display.php');
?>
</div>
</div>
<?php } ?>
