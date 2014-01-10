<?php
/**
 *
 *
 * @package page
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: header_php.php 3230 2006-03-20 23:21:29Z drbyte $
 */
require(DIR_WS_MODULES . zen_get_module_directory('require_languages.php'));

$eId = isset($_GET['event'])? (int)$_GET['event']:null;
$event_images = array();
$event_items = array();

if($eId){
  //Specific event id passed in
  $event_sql = "SELECT * FROM events WHERE event_id = $eId AND event_status = 1";
  $res = $db->Execute($event_sql);
  if(!$res->EOF){
    $event_title = $res->fields['event_name'] . ' Event';
    $event_description = $res->fields['event_description'];
//echo $res->fields['event_description'];
    if($res->fields['event_images']!=NULL){
      $event_images = explode(';',$res->fields['event_images']);
    }
    //work out size of images according to the number of images available
    $event_images_count = sizeof($event_images);
    if($event_images_count>3)$event_images_count=3;
    switch($event_images_count){
      case 0:
        break;
      case 1:
        $event_image_max = 200;
        break;
      case 2:
        $event_image_max = 190;
        break;
      case 3:
        $event_image_max = 200;
        break;
    }
    //now get the items for the event
    $res = $db->Execute("SELECT * FROM events_items WHERE event_id = $eId");
    //$listing_sql = "SELECT e.event_mid FROM events_items e WHERE e.event_id = $eId";
    $events_list_string="";
    while(!$res->EOF){
      $event_items[] = "'".$res->fields['event_mid']."'";
      $res->MoveNext();
    }
    $events_list_string = implode(',',$event_items);
    if($events_list_string=='')$events_list_string=-1;
    $sql=      "SELECT distinct
      p.products_image,
      pd.products_name,
      p.products_id,
      p.products_type,
      p.master_categories_id,
      p.manufacturers_id,
      p.products_price,
      p.products_tax_class_id,
      pd.products_description,
      p.products_sort_order,
      p.product_is_call,
      p.product_is_always_free_shipping,
      p.products_qty_box_status,
      p.products_model,
      pef.product_style
      FROM   products_to_categories p2c
      INNER JOIN products_description pd
      ON pd.products_id = p2c.products_id
      INNER JOIN products p
      ON p.products_id = p2c.products_id
      INNER JOIN product_extra_fields pef
      ON pef.products_id = p2c.products_id
      WHERE   p.products_status = 1
      AND  p.products_model IN($events_list_string)
       ";
    //$listing = $db->Execute($sql);
    $listing_sql = $sql;
    //The following line is required for use in the modules/product_listing.php file foe when in gallery list view
    $column_list = array(0=>'PRODUCT_LIST_IMAGE', 1=>'PRODUCT_LIST_NAME',2=>'PRODUCT_LIST_PRICE');
  }
}else{
  //No event so deal with all of them
  $res = $db->Execute("SELECT * FROM events WHERE event_status = 1 ORDER BY event_id DESC");
  $event_title = 'Events';
  while(!$res->EOF){
    $events_list[]=array('id'=>$res->fields['event_id'],'name'=>$res->fields['event_name']);
    $res->MoveNext();
  }
}

//echo array_walk(debug_backtrace(),create_function('$a,$b','print "{$a[\'function\']}()(".basename($a[\'file\']).":{$a[\'line\']}); ";'));
// include template specific file name defines
//$define_page = zen_get_file_directory(DIR_WS_LANGUAGES . $_SESSION['language'] . '/html_includes/', 'define_map', 'false');
require(DIR_WS_MODULES . zen_get_module_directory('gallery_view.php'));

$breadcrumb->add($event_title);
?>