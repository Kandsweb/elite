<?php
/**
 * Module Template
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_modules_product_listing.php 3241 2006-03-22 04:27:27Z ajeh $
 */
 include(DIR_WS_MODULES . zen_get_module_directory(FILENAME_PRODUCT_LISTING));

$content = '<div id="productListing">';

// only show when there is something to submit and enabled

 //if ( ($listing_split->number_of_rows > 0) && ( (PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3') ) ) {

  $content .=  '<div id="productsListingTopNumber" class="navSplitPagesResult back">' .  $listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS). '</div>
<div id="galleryBox">';

$content .=  zen_draw_form('galleryViewSelect', zen_href_link('index', zen_get_all_get_params(array('action'))) );

$content .=  TEXT_GALLERY_SELECT_VIEW ;
if($_SESSION['current_view']!=GALLERY_VIEW_LIST){
  $content .=  '<a href="' . zen_href_link('index', zen_get_all_get_params(array('action', 'view')) . 'view=1') . '">' . zen_image_button($image_button_list, GALLERY_BUTTON_LIST_ALT) . '</a>';
}else{
  $content .=  zen_image_button($image_button_list, GALLERY_BUTTON_LIST_ALT);
}
if($_SESSION['current_view']!=GALLERY_VIEW_MEDIUM){
  $content .=  '<a href="' . zen_href_link('index', zen_get_all_get_params(array('action', 'view')) . 'view=2') . '">' . zen_image_button($image_button_medium, GALLERY_BUTTON_MEDIUM_ALT) . '</a>';
}else{
  $content .=  zen_image_button($image_button_medium, GALLERY_BUTTON_MEDIUM_ALT);
}
if($_SESSION['current_view']!=GALLERY_VIEW_SMALL){
  $content .=  '<a href="' . zen_href_link('index', zen_get_all_get_params(array('action', 'view')) . 'view=3') . '">' . zen_image_button($image_button_small, GALLERY_BUTTON_SMALL_ALT) . '</a>';
}else{
  $content .=  zen_image_button($image_button_small, GALLERY_BUTTON_SMALL_ALT);
}
$content .=  '<br />';
if(sizeof($view_array) > 1){
  $content .=  TEXT_GALLERY_NUMBER_PER_PG . zen_draw_pull_down_menu('per_page',$view_array, $_SESSION['per_page'], 'onchange="submitform()"');
}
$content .=  '</form>';
$content .=  '</div><div id="productsListingListingTopLinks" class="navSplitPagesLinks forward">' . TEXT_RESULT_PAGE . ' ' . $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, zen_get_all_get_params(array('page', 'info', 'x', 'y', 'main_page'))). '</div>';

$content .=  '<br class="clearBoth" />';

$content .=  '<script type="text/javascript">
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
</script>';

/**
 * load the list_box_content template to display the products
 */
  //require($template->get_template_dir('tpl_tabular_display.php',DIR_WS_TEMPLATE, $current_page_base,'common'). '/tpl_tabular_display_ajax.php');
require( 'includes/templates/KandS/common'. '/' . 'tpl_tabular_display_ajax.php');
//$content2 = 'HHHHHHHHHHHHHHHHHHHHH';
$content .= $content2;

 if ( ($listing_split->number_of_rows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3')) ) {

$content .=  '<div id="productsListingBottomNumber" class="navSplitPagesResult back">'. $listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS) . '</div>' .
'<div  id="productsListingListingBottomLinks" class="navSplitPagesLinks forward">' . TEXT_RESULT_PAGE . ' ' . $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, zen_get_all_get_params(array('page', 'info', 'x', 'y'))) . '</div>';
$content .=  '<br class="clearBoth" />';

  }
?>

<?php
// only show when there is something to submit and enabled
    if ($show_bottom_submit_button == true) {
      $content .=  '<div class="buttonRow forward">' . zen_image_submit(BUTTON_IMAGE_ADD_PRODUCTS_TO_CART, BUTTON_ADD_PRODUCTS_TO_CART_ALT, 'id="submit2" name="submit1"') . '</div> <br class="clearBoth" />';

    } // show_bottom_submit_button

$content .=  '</div>';


// if ($show_top_submit_button == true or $show_bottom_submit_button == true or (PRODUCT_LISTING_MULTIPLE_ADD_TO_CART != 0 and $show_submit == true and $listing_split->number_of_rows > 0)) {
  if ($show_top_submit_button == true or $show_bottom_submit_button == true) {

$content .=  '</form>';
 }

 return $content;
 ?>
