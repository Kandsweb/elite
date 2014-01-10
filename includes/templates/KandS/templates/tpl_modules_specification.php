<?php
/**
 * KandS Extra Product Fields - This builds the output for the specification table on the
 * product info page
 *
 * Portions Copyright (c) 2002 osCommerce
 *  From Steven Mewhirter admin@kandsweb.com
 * license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 *
 * issue 1.0  March 2011
 */

  $tech_specs = new ProductData;

  $specs_array = $tech_specs->get_product_specs($_GET['products_id']);

  if(sizeof($specs_array)> 0) {
    if($_SESSION['department'] == 2){
      //Reorder the specs
      $specs_array = $tech_specs->reOrder($specs_array);
    }
   ?>


  <div id="tecSpecHeading">
  Technical Specifications
  </div>
  <div id="tecSpec">
  <?php
    //items not to be displayed
    //$skip_array = array('products_id', 'manufactures_code');

    if(is_array($specs_array)){
      foreach($specs_array as $key => $value){
        echo '<div id="tecSpecItem">';
        echo '<div id="tecSpecName">';
        echo $key;
        echo '</div><div id="tecSpecValue">';
        echo $value;
        echo '</div>';
        echo '<br class="clearBoth" />';
        echo '</div>';
      }
    }
  ?>
  <!--<div id="tecSpecItem"><br />&nbsp;<br /></div>-->
  </div>
  <?php
  }
  ?>
