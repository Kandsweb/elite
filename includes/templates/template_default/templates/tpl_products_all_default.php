<?php
/**
 * Page Template
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_products_all_default.php 2603 2005-12-19 20:22:08Z wilt $
 */
?>
<div class="centerColumn" id="allProductsDefault">
<div class="listAreaTop">
<h1 id="allProductsDefaultHeading"><?php echo HEADING_TITLE; ?></h1>

<?php
require($template->get_template_dir('/tpl_modules_listing_display_order.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_listing_display_order.php'); ?>

<br class="clearBoth" />

<?php
  if (PRODUCT_ALL_LISTING_MULTIPLE_ADD_TO_CART > 0 and $show_submit == true and $products_all_split->number_of_rows > 0) {
?>

<?php
    if ($show_top_submit_button == true or $show_bottom_submit_button == true) {
      echo zen_draw_form('multiple_products_cart_quantity', zen_href_link(FILENAME_PRODUCTS_ALL, zen_get_all_get_params(array('action')) . 'action=multiple_products_add_product'), 'post', 'enctype="multipart/form-data"');
    }
  }
?>

<?php
  if ($show_top_submit_button == true) {
// only show when there is something to submit
?>
<div class="buttonRow forward"><?php echo zen_image_submit(BUTTON_IMAGE_ADD_PRODUCTS_TO_CART, BUTTON_ADD_PRODUCTS_TO_CART_ALT, 'id="submit1" name="submit1"'); ?></div>

<?php
  } // top submit button
?>

<br class="clearBoth" />

<?php
  //if (($products_all_split->number_of_rows > 0) && ((PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3'))) {
?>
  <div id="allProductsListingTopNumber" class="navSplitPagesResult back"><?php echo $products_all_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS_ALL); ?></div>
  <div id="galleryBox">
  <?php
  echo zen_draw_form('galleryViewSelect', zen_href_link($_GET['main_page'], zen_get_all_get_params(array('action'))) );

echo TEXT_GALLERY_SELECT_VIEW ;
if($_SESSION['current_view']!=GALLERY_VIEW_LIST){
  echo '<a href="' . zen_href_link($_GET['main_page'], zen_get_all_get_params(array('action', 'view')) . 'view=1') . '">' . zen_image_button($image_button_list, GALLERY_BUTTON_LIST_ALT) . '</a>';
}else{
  echo zen_image_button($image_button_list, GALLERY_BUTTON_LIST_ALT);
}
if($_SESSION['current_view']!=GALLERY_VIEW_MEDIUM){
  echo '<a href="' . zen_href_link($_GET['main_page'], zen_get_all_get_params(array('action', 'view')) . 'view=2') . '">' . zen_image_button($image_button_medium, GALLERY_BUTTON_MEDIUM_ALT) . '</a>';
}else{
  echo zen_image_button($image_button_medium, GALLERY_BUTTON_MEDIUM_ALT);
}
if($_SESSION['current_view']!=GALLERY_VIEW_SMALL){
  echo '<a href="' . zen_href_link($_GET['main_page'], zen_get_all_get_params(array('action', 'view')) . 'view=3') . '">' . zen_image_button($image_button_small, GALLERY_BUTTON_SMALL_ALT) . '</a>';
}else{
  echo zen_image_button($image_button_small, GALLERY_BUTTON_SMALL_ALT);
}
echo '<br />';
if(sizeof($view_array) > 1){
  echo TEXT_GALLERY_NUMBER_PER_PG . zen_draw_pull_down_menu('per_page',$view_array, $_SESSION['per_page'], 'onchange="submitform()"');
}
echo '</form>';
?></div><div id="productsListingListingTopLinks" class="navSplitPagesLinks forward"><?php echo TEXT_RESULT_PAGE . ' ' . $products_all_split->display_links(MAX_DISPLAY_PAGE_LINKS, zen_get_all_get_params(array('page', 'info', 'x', 'y', 'main_page'))); ?></div>

<br class="clearBoth" />

<script type="text/javascript">
  <!--
  function submitform()
  {
      if(document.galleryViewSelect.onsubmit &&
      !document.galleryViewSelect.onsubmit())
      {
          return;
  }
document.galleryViewSelect.submit();
  }
  //-->
</script>

<?php

 // }
?>

<br class="clearBoth" />
</div>
<div id="bodyWrap">
<?php
/**
 * display the new products
 */
require($template->get_template_dir('/tpl_modules_products_all_listing.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_products_all_listing.php'); ?>

<?php
  if (($products_all_split->number_of_rows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3'))) {
?>
  <div id="allProductsListingBottomNumber" class="navSplitPagesResult back"><?php echo $products_all_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS_ALL); ?></div>
  <div id="allProductsListingBottomLinks" class="navSplitPagesLinks forward"><?php echo TEXT_RESULT_PAGE . ' ' . $products_all_split->display_links(MAX_DISPLAY_PAGE_LINKS, zen_get_all_get_params(array('page', 'info', 'x', 'y', 'main_page'))); ?></div>
<?php
  }
?>
<br class="clearBoth" />

<?php
  if ($show_bottom_submit_button == true) {
// only show when there is something to submit
?>
  <div class="buttonRow forward"><?php echo zen_image_submit(BUTTON_IMAGE_ADD_PRODUCTS_TO_CART, BUTTON_ADD_PRODUCTS_TO_CART_ALT, 'id="submit2" name="submit1"'); ?></div>

<?php
  }  // bottom submit button
?>

<?php
// only end form if form is created
    if ($show_top_submit_button == true or $show_bottom_submit_button == true) {
?>
</form>
<?php } // end if form is made ?>
</div></div>