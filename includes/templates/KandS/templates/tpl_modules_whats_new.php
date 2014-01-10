<?php
/**
 * Module Template
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_modules_whats_new.php 2935 2006-02-01 11:12:40Z birdbrain $
 */
  $zc_show_new_products = false;

  //KandS Restrict new products listing to the root category
  if($cpath != '' && $cpath != NULL){
    if(sizeof($cPath_array)==1){
      $new_products_category_id = $cPath_array[0];
    }else{
      $new_products_category_id = $cPath_array[(sizeof($cPath_array))-1];
    }
  }else{
    $new_products_category_id = 2;
    $at_home = true;
  }

  include(DIR_WS_MODULES . zen_get_module_directory(FILENAME_NEW_PRODUCTS));


?>

<?php if ($zc_show_new_products == true) { ?>

<?php
    //set the carousel vars
    $carousel_id='recent_carousel';
    $carousel_class='jcarousel-skin-tango';
?>
<div class="centerBoxWrapper whatsNew">
<?php
  //require($template->get_template_dir('tpl_columnar_display.php',DIR_WS_TEMPLATE, $current_page_base,'common'). '/tpl_columnar_display.php');
  require($template->get_template_dir('tpl_carousel_display.php',DIR_WS_TEMPLATE, $current_page_base,'common'). '/tpl_carousel_display.php');
?>
</div>
<?php }
  //Only show second new items box if on lighting or interiors home page - path has only one level or on the home page
  if(sizeof($cPath_array)==1 || $at_home){
    $new_products_category_id==1?$new_products_category_id=2:$new_products_category_id=1;

    include(DIR_WS_MODULES . zen_get_module_directory(FILENAME_NEW_PRODUCTS));

    if ($zc_show_new_products == true) {
      //set the carousel vars
      $carousel_id='recent_carousel_2';
      $carousel_class='jcarousel-skin-tango';
      ?>
      <div class="centerBoxWrapper whatsNew">
      <?php
        //require($template->get_template_dir('tpl_columnar_display.php',DIR_WS_TEMPLATE, $current_page_base,'common'). '/tpl_columnar_display.php');
        require($template->get_template_dir('tpl_carousel_display.php',DIR_WS_TEMPLATE, $current_page_base,'common'). '/tpl_carousel_display.php');
      ?>
      </div>
      <?php
    }
  }
?>
