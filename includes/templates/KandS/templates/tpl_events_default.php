<?php
/**
 * Page Template
 *
 * Loaded automatically by index.php?main_page=conditions.<br />
 * Displays conditions page.
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_conditions_default.php 3464 2006-04-19 00:07:26Z ajeh $
 */
?>
<div class="centerColumn" id="conditions">
<div class="listAreaTop">
<h2 id="conditionsHeading"><?php echo $event_title; ?></h2>
<?php
  if($event_images_count > 0){
    echo '<br /><div class="eventImages">';
    for($i=0; $i<$event_images_count; $i++){
      //echo DIR_WS_IMAGES . 'events/' . $event_images[$i];
      echo zen_image(DIR_WS_IMAGES . 'events/' . $event_images[$i],'',$event_image_max). '&nbsp;&nbsp;';
    }
    echo '</div>';
  }

  if($event_description){
    echo '<br /><div class="eventDescription">'.$event_description.'</div>';
  }
?>

</div>

<div id="productListing" class="content">
<?php

if($eId){
  //Show a single event
   include(DIR_WS_MODULES . zen_get_module_directory(FILENAME_PRODUCT_LISTING));
  if($event_title){
     if ( ($listing_split->number_of_rows > 0) && ( (PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3') ) ) {
      include($template->get_template_dir('tpl_module_gallery_view.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_module_gallery_view.php');

      require($template->get_template_dir('tpl_tabular_display.php',DIR_WS_TEMPLATE, $current_page_base,'common'). '/tpl_tabular_display.php');
     }

    ?>
<?php if ( ($listing_split->number_of_rows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3')) ) {
?>
<div id="productsListingBottomNumber" class="navSplitPagesResult back"><?php echo $listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></div>
<div  id="productsListingListingBottomLinks" class="navSplitPagesLinks forward"><?php echo TEXT_RESULT_PAGE . ' ' . $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, zen_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></div>
<br class="clearBoth" />
<?php
  }
?>

    <?php
  }
}else{
  //Show all events
  echo 'Below is a list of exhibitions we have taken part in<br /><br /><ul class="eventList">';
  foreach($events_list as $value){
    echo '<div class="eventLink"><li><a href="'. zen_href_link('events','event='.$value['id']).'">' . $value['name'] .'</a></li></div>';
  }
  echo '</ul>';
}










/**
 * require the html_define for the conditions page
 */
  //require($define_page);
?>
</div>



</div>
