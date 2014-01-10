<?php
/**
 * products_new header_php.php
 *
 * @package page
 * @copyright Copyright 2003-2007 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: header_php.php 6912 2007-09-02 02:23:45Z drbyte $
 */

  $promotion_active = (count_promotion_items()>0?TRUE:FALSE);

  require(DIR_WS_MODULES . zen_get_module_directory('require_languages.php'));
  $breadcrumb->add($promotion_active?PROMOTION_NAME:PROMOTION);


////// KandS  Gallery/List view///////////////////////////////////////////////////////////////////////////////////
if(!isset($_SESSION['current_view']))$_SESSION['current_view'] = GALLERY_DEFAULT_VIEW;                          //
if(isset($_GET['view'])){                                                                                       //
  $_SESSION['current_view'] = $_GET['view'];                                                                    //
  unset($_SESSION['per_page']);                                                                                 //
}                                                                                                               //
if(isset($_POST['pp']))$_SESSION['per_page']= $_POST['pp'];                                         //
///eof KandS//////////////////////////////////////////////////////////////////////////////////////////////////////

// display order dropdown
  $disp_order_default = PRODUCT_NEW_LIST_SORT_DEFAULT;
  require(DIR_WS_MODULES . zen_get_module_directory(FILENAME_LISTING_DISPLAY_ORDER));
  $products_new_array = array();

  $products_new_query_raw = "SELECT p.products_id, p.products_image, pd.products_name, p.products_model, pef.now_price,
                          p.master_categories_id
                         FROM (" . TABLE_PRODUCTS . " p
                         LEFT JOIN " . TABLE_PRODUCTS_EXTRA_FIELDS . " pef on p.products_id = pef.products_id
                         LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd on p.products_id = pd.products_id )
                         WHERE p.products_id = pef.products_id and p.products_id = pd.products_id and p.products_status = '1'
                         AND (pef.now_price IS NOT NULL AND pef.now_price != '' AND now_price !=0) AND p.manufacturers_id != 21
                         AND pd.language_id = :languageID";

  $products_new_query_raw = $db->bindVars($products_new_query_raw, ':languageID', $_SESSION['languages_id'], 'integer');
  //$products_new_split = new splitPageResults($products_new_query_raw, MAX_DISPLAY_PRODUCTS_NEW);
  $products_new_split = new splitPageResults($products_new_query_raw, $_SESSION['per_page']);
//check to see if we are in normal mode ... not showcase, not maintenance, etc
  $show_submit = zen_run_normal();

// check whether to use multiple-add-to-cart, and whether top or bottom buttons are displayed
  if (PRODUCT_NEW_LISTING_MULTIPLE_ADD_TO_CART > 0 and $show_submit == true and $products_new_split->number_of_rows > 0) {

    // check how many rows
    $check_products_all = $db->Execute($products_new_split->sql_query);
    $how_many = 0;
    while (!$check_products_all->EOF) {
      if (zen_has_product_attributes($check_products_all->fields['products_id'])) {
      } else {
// needs a better check v1.3.1
        if ($check_products_all->fields['products_qty_box_status'] != 0) {
          if (zen_get_products_allow_add_to_cart($check_products_all->fields['products_id']) !='N') {
            if ($check_products_all->fields['product_is_call'] == 0) {
              if ((SHOW_PRODUCTS_SOLD_OUT_IMAGE == 1 and $check_products_all->fields['products_quantity'] > 0) or SHOW_PRODUCTS_SOLD_OUT_IMAGE == 0) {
                if ($check_products_all->fields['products_type'] != 3) {
                  if (zen_has_product_attributes($check_products_all->fields['products_id']) < 1) {
                    $how_many++;
                  }
                }
              }
            }
          }
        }
      }
      $check_products_all->MoveNext();
    }

    if ( (($how_many > 0 and $show_submit == true and $products_new_split->number_of_rows > 0) and (PRODUCT_NEW_LISTING_MULTIPLE_ADD_TO_CART == 1 or  PRODUCT_NEW_LISTING_MULTIPLE_ADD_TO_CART == 3)) ) {
      $show_top_submit_button = true;
    } else {
      $show_top_submit_button = false;
    }
    if ( (($how_many > 0 and $show_submit == true and $products_new_split->number_of_rows > 0) and (PRODUCT_NEW_LISTING_MULTIPLE_ADD_TO_CART >= 2)) ) {
      $show_bottom_submit_button = true;
    } else {
      $show_bottom_submit_button = false;
    }
  }

////// KandS  Gallery/List view//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$image_button_list = IMAGE_GALLERY_LIST;
$image_button_medium = IMAGE_GALLERY_MEDIUM;
$image_button_small = IMAGE_GALLERY_SMALL;
//$max_display = $_SESSION['per_view'];
switch($_SESSION['current_view']){
  case GALLERY_VIEW_LIST:
    $image_button_list = IMAGE_GALLERY_LIST_SELECTED;
    $max_display = MAX_DISPLAY_PRODUCTS_LISTING;
    $image_width = IMAGE_PRODUCT_LISTING_WIDTH;
    $image_height = IMAGE_PRODUCT_LISTING_HEIGHT;
    $cols_in_view = 3;
    break;
  case GALLERY_VIEW_MEDIUM:
    $image_button_medium = IMAGE_GALLERY_MEDIUM_SELECTED;
    $max_display = GALLERY_COLS_MEDIUM * GALLERY_MAX_ROWS;
    $image_width = IMAGE_PRODUCT_LISTING_WIDTH;
    $image_height = IMAGE_PRODUCT_LISTING_HEIGHT;
    $cols_in_view = GALLERY_COLS_MEDIUM;
    if(!FUAL_SLIMBOX_LIGHTBOX){
      $image_width = IMAGE_PRODUCT_LISTING_WIDTH * GALLERY_FACTOR_MEDIUM;
      $image_height = IMAGE_PRODUCT_LISTING_HEIGHT * GALLERY_FACTOR_MEDIUM;
    }
    break;
  case GALLERY_VIEW_SMALL:
    $image_button_small = IMAGE_GALLERY_SMALL_SELECTED;
    $max_display = GALLERY_COLS_SMALL * GALLERY_MAX_ROWS;
    $image_width = IMAGE_PRODUCT_LISTING_WIDTH;
    $image_height = IMAGE_PRODUCT_LISTING_HEIGHT;
    $cols_in_view = GALLERY_COLS_SMALL;
    if(!FUAL_SLIMBOX_LIGHTBOX){
      $image_width = IMAGE_PRODUCT_LISTING_WIDTH * GALLERY_FACTOR_SMALL;
      $image_height = IMAGE_PRODUCT_LISTING_HEIGHT * GALLERY_FACTOR_SMALL;
    }

    break;
}
if(!isset($_SESSION['per_page']))$_SESSION['per_page'] = $max_display;

//echo $_SESSION['current_view'];

/*$factor = ($products_new_split->number_of_rows > 100?2:1);
for($i=GALLERY_MAX_ROWS * $cols_in_view, $n= $products_new_split->number_of_rows+(GALLERY_MAX_ROWS * $cols_in_view); $i<$n; $i+=(GALLERY_MAX_ROWS * $cols_in_view)*$factor){
  if ($i<100){
    $view_array[] = array('id' => $i, 'text' => $i);
  }else{
    break;
  }
}*/
//////Eof KandS///////////////////////////////////////////////////////////////////////////////////////////////////////////////////


?>