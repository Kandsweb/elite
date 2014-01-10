<?php
/**
 * Cross Sell products
 *
 * Derived from:
 * Original Idea From Isaac Mualem im@imwebdesigning.com <mailto:im@imwebdesigning.com>
 * Portions Copyright (c) 2002 osCommerce
 * Complete Recoding From Stephen Walker admin@snjcomputers.com
 * license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 *
 * Adapted to Zen Cart by Merlin - Spring 2005
 * Reworked for Zen Cart v1.3.0  03-30-2006
 * Add Multi Cross Sells by Gilby 2010-06-26
 */

if(defined('MXSELL_ENABLED') &&	MXSELL_ENABLED == 'true')  {
  if(constant(MXSELL.$mxsell._ENABLED) == 'true')  {
    $xsell_data = NULL;

// calculate whether any cross-sell products are configured for the current product, and display if relevant
    include(DIR_WS_MODULES . zen_get_module_directory(FILENAME_MXSELL_PRODUCTS));

    if (zen_not_null($xsell_data)) {
      $info_box_contents = array();
      $list_box_contents = $xsell_data;
      $title = '';
?>

<br />
<div id="productRelatedHeading">Related Items</div>
  <div id="productRelated">
<?php
/**
 * require the list_box_content template to display the cross-sell info. This info was prepared in modules/xsell_products.php
 */
    require($template->get_template_dir('tpl_columnar_display.php',DIR_WS_TEMPLATE, $current_page_base,'common'). '/tpl_columnar_display.php');
?>
    </div>

<?php } } } ?>