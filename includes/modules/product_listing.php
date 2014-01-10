<?php
/**
 * product_listing module
 *
 * @package modules
 * @copyright Copyright 2003-2007 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: product_listing.php 6787 2007-08-24 14:06:33Z drbyte $
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

$more_info_image = zen_image($template->get_template_dir('additional_info.jpg',DIR_WS_TEMPLATE,$current_page_base, 'images').'/'.'additional_info.jpg');

$show_submit = zen_run_normal();

//echo $listing_sql.'<br />';
if($current_page == 'events'){
  $listing_split = new splitPageResults($listing_sql, $_SESSION['per_page'], 'products_model', 'page');
}else{
//echo __FILE__. __LINE__.'<br />';
  $listing_split = new splitPageResults($listing_sql, $_SESSION['per_page'], 'left(p.products_model,8)', 'page');  //left(p.products_model,8)  p.products_id
}
$zco_notifier->notify('NOTIFY_MODULE_PRODUCT_LISTING_RESULTCOUNT', $listing_split->number_of_rows);
$how_many = 0;

$list_box_contents[0] = array('params' => 'class="productListing-rowheading"');

$zc_col_count_description = 0;
$lc_align = '';

if($_SESSION['current_view']== GALLERY_VIEW_LIST){////For headings
//echo __FILE__. __LINE__.'<br />';
  for ($col=0, $n=sizeof($column_list); $col<$n; $col++) {
    switch ($column_list[$col]) {
      case 'PRODUCT_LIST_MODEL':
      $lc_text = TABLE_HEADING_MODEL;
      $lc_align = '';
      $zc_col_count_description++;
      break;
      case 'PRODUCT_LIST_NAME':
      $lc_text = TABLE_HEADING_PRODUCTS;
      $lc_align = '';
      $zc_col_count_description++;
      break;
      case 'PRODUCT_LIST_MANUFACTURER':
      $lc_text = TABLE_HEADING_MANUFACTURER;
      $lc_align = '';
      $zc_col_count_description++;
      break;
      case 'PRODUCT_LIST_PRICE':
      $lc_text = ''; // TABLE_HEADING_PRICE;
      $lc_align = 'right' . (PRODUCTS_LIST_PRICE_WIDTH > 0 ? '" width="' . PRODUCTS_LIST_PRICE_WIDTH : '');
      $zc_col_count_description++;
      break;
      case 'PRODUCT_LIST_QUANTITY':
      $lc_text = TABLE_HEADING_QUANTITY;
      $lc_align = 'right';
      $zc_col_count_description++;
      break;
      case 'PRODUCT_LIST_WEIGHT':
      $lc_text = TABLE_HEADING_WEIGHT;
      $lc_align = 'right';
      $zc_col_count_description++;
      break;
      case 'PRODUCT_LIST_IMAGE':
      $lc_text = ''; //TABLE_HEADING_IMAGE;
      $lc_align = 'center';
      $zc_col_count_description++;
      break;
    }

    if ( ($column_list[$col] != 'PRODUCT_LIST_IMAGE') ) {
      $lc_text = zen_create_sort_heading($_GET['sort'], $col+1, $lc_text);
    }



    $list_box_contents[0][$col] = array('align' => $lc_align,
                                        'params' => 'class="productListing-heading"',
                                        'text' => $lc_text );
  }
//eof if gallery view = list for headings only//////////////////////////////////////////////////////////////////////////

}else{
//echo __FILE__. __LINE__.'<br />';
  //the gallery view is either meduim or small images
  //so create the table with more cols as required by the selected view
  for($col=0, $n=($_SESSION['current_view'] == GALLERY_VIEW_MEDIUM ? GALLERY_COLS_MEDIUM : GALLERY_COLS_SMALL); $col<$n; $col++){
    $lc_text = '';
    $lc_align = 'center';
    $list_box_contents[0][$col] = array('align' => $lc_align,
                                        'params' => 'class="productListing-heading"',
                                        'text' => $lc_text );
  }
}
///eof table headings/////////////////////////////////////////////////////////////////////////////////////////////////
if ($listing_split->number_of_rows > 0) {
  $rows = 0;
  $listing = $db->Execute($listing_split->sql_query);
  $extra_row = 0;
  $cur_col = 0;
  //echo __FILE__. __LINE__.'<br />';
  while (!$listing->EOF) {

    $listing->fields['products_image'] = get_image_xref($listing->fields['products_image']);

    if(!isset($_SESSION['current_view']) || $_SESSION['current_view']== GALLERY_VIEW_LIST){
    //Is list view process as normal
      $rows++;
      if ((($rows-$extra_row)/2) == floor(($rows-$extra_row)/2)) {
        $list_box_contents[$rows] = array('params' => 'class="productListing-even"');
      } else {
        $list_box_contents[$rows] = array('params' => 'class="productListing-odd"');
      }

      $cur_row = sizeof($list_box_contents) - 1;

      for ($col=0, $n=sizeof($column_list); $col<$n; $col++) {
        $lc_align = '';
        switch ($column_list[$col]) {
          case 'PRODUCT_LIST_MODEL':
          $lc_align = '';
          $lc_text = $listing->fields['products_model'];
          break;
          case 'PRODUCT_LIST_NAME':
          $lc_align = '';
            $lc_text = '<h3 class="itemTitle"><a href="' . zen_href_link(zen_get_info_page($listing->fields['products_id']), 'cPath=' . (($_GET['manufacturers_id'] > 0 and $_GET['filter_id']) > 0 ?  zen_get_generated_category_path_rev($_GET['filter_id']) : ($_GET['cPath'] > 0 ? zen_get_generated_category_path_rev($_GET['cPath']) : zen_get_generated_category_path_rev($listing->fields['master_categories_id']))) . '&products_id=' . $listing->fields['products_id']) . '">' . $listing->fields['products_name'] . '</a></h3><div class="listingDescription">' . zen_trunc_string(zen_clean_html(stripslashes(zen_get_products_description($listing->fields['products_id'], $_SESSION['languages_id']))), PRODUCT_LIST_DESCRIPTION);
            $lc_text .= zen_get_products_description($listing->fields['products_id'], $_SESSION['languages_id'])==''?'':'<a href="' . zen_href_link(zen_get_info_page($listing->fields['products_id']), 'cPath=' . zen_get_generated_category_path_rev($listing->fields['master_categories_id']) . '&products_id=' . $listing->fields['products_id']) . '"> ' . MORE_INFO_TEXT . '</a>';
            $lc_text.= '<br /><span style="font-size:10px">Model:'.$listing->fields['products_model'].'</span></div>';
            $pt = product_promotion($listing->fields['products_id'],$current_page);
            $lc_text .= ($pt!=''?'<br />'.$pt:'');
          break;
          case 'PRODUCT_LIST_MANUFACTURER':
          $lc_align = '';
          $lc_text = '<a href="' . zen_href_link(FILENAME_DEFAULT, 'manufacturers_id=' . $listing->fields['manufacturers_id']) . '">' . $listing->fields['manufacturers_name'] . '</a>';
          break;
          case 'PRODUCT_LIST_PRICE':
          $lc_price = zen_get_products_display_price($listing->fields['products_id']) . '<br />';
          $lc_align = 'center';
          $lc_text =  $lc_price;

          // more info in place of buy now
          $lc_button = '';
          if (zen_has_product_attributes($listing->fields['products_id']) or PRODUCT_LIST_PRICE_BUY_NOW == '0') {
            $lc_button = '<a href="' . zen_href_link(zen_get_info_page($listing->fields['products_id']), 'cPath=' . (($_GET['manufacturers_id'] > 0 and $_GET['filter_id']) > 0 ?  zen_get_generated_category_path_rev($_GET['filter_id']) : ($_GET['cPath'] > 0 ? $_GET['cPath'] : zen_get_generated_category_path_rev($listing->fields['master_categories_id']))) . '&products_id=' . $listing->fields['products_id']) . '">' . MORE_INFO_TEXT . '</a><br />Model:'.$listing->fields['products_model'];
          } else {
            if (PRODUCT_LISTING_MULTIPLE_ADD_TO_CART != 0) {
              if (
                  // not a hide qty box product
                  $listing->fields['products_qty_box_status'] != 0 &&
                  // product type can be added to cart
                  zen_get_products_allow_add_to_cart($listing->fields['products_id']) != 'N'
                  &&
                  // product is not call for price
                  $listing->fields['product_is_call'] == 0
                  &&
                  // product is in stock or customers may add it to cart anyway
                  ($listing->fields['products_quantity'] > 0 || SHOW_PRODUCTS_SOLD_OUT_IMAGE == 0) ) {
                $how_many++;
              }
              // hide quantity box
              if ($listing->fields['products_qty_box_status'] == 0) {
                $lc_button = '<a href="' . zen_href_link($_GET['main_page'], zen_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . $listing->fields['products_id']) . '">' . zen_image_button(BUTTON_IMAGE_BUY_NOW, BUTTON_BUY_NOW_ALT, 'class="listingBuyNowButton"') . '</a>';
              } else {
                $lc_button = TEXT_PRODUCT_LISTING_MULTIPLE_ADD_TO_CART . "<input type=\"text\" name=\"products_id[" . $listing->fields['products_id'] . "]\" value=\"0\" size=\"4\" />";
              }
            } else {
  // qty box with add to cart button
              if (PRODUCT_LIST_PRICE_BUY_NOW == '2' && $listing->fields['products_qty_box_status'] != 0) {
                $lc_button= zen_draw_form('cart_quantity', zen_href_link(zen_get_info_page($listing->fields['products_id']), zen_get_all_get_params(array('action')) . 'action=add_product&products_id=' . $listing->fields['products_id']), 'post', 'enctype="multipart/form-data"') . '<input type="text" name="cart_quantity" value="' . (zen_get_buy_now_qty($listing->fields['products_id'])) . '" maxlength="6" size="4" /><br />' . zen_draw_hidden_field('products_id', $listing->fields['products_id']) . zen_image_submit(BUTTON_IMAGE_IN_CART, BUTTON_IN_CART_ALT) . '</form>';
              } else {
                $lc_button = '<a href="' . zen_href_link($_GET['main_page'], zen_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . $listing->fields['products_id']) . '">' . zen_image_button(BUTTON_IMAGE_BUY_NOW, BUTTON_BUY_NOW_ALT, 'class="listingBuyNowButton"') . '</a>';
              }
            }
          }
          $the_button = $lc_button;
          $products_link = '<a href="' . zen_href_link(zen_get_info_page($listing->fields['products_id']), 'cPath=' . ( ($_GET['manufacturers_id'] > 0 and $_GET['filter_id']) > 0 ? zen_get_generated_category_path_rev($_GET['filter_id']) : $_GET['cPath'] > 0 ? zen_get_generated_category_path_rev($_GET['cPath']) : zen_get_generated_category_path_rev($listing->fields['master_categories_id'])) . '&products_id=' . $listing->fields['products_id']) . '">' . MORE_INFO_TEXT . '</a>';
          $lc_text .= '<br />' . zen_get_buy_now_button($listing->fields['products_id'], $the_button, $products_link) . '<br />' . zen_get_products_quantity_min_units_display($listing->fields['products_id']);
          $lc_text .= '<br />' . (zen_get_show_product_switch($listing->fields['products_id'], 'ALWAYS_FREE_SHIPPING_IMAGE_SWITCH') ? (zen_get_product_is_always_free_shipping($listing->fields['products_id']) ? TEXT_PRODUCT_FREE_SHIPPING_ICON . '<br />' : '') : '');
          break;
          case 'PRODUCT_LIST_QUANTITY':
          $lc_align = 'right';
          $lc_text = $listing->fields['products_quantity'];
          break;
          case 'PRODUCT_LIST_WEIGHT':
          $lc_align = 'right';
          $lc_text = $listing->fields['products_weight'];
          break;
          case 'PRODUCT_LIST_IMAGE':
          $lc_align = 'center';
          if ($listing->fields['products_image'] == '' and PRODUCTS_IMAGE_NO_IMAGE_STATUS == 0) {
            $lc_text = '';
          } else {
              $lc_text = '<a href="' . zen_href_link(zen_get_info_page($listing->fields['products_id']), 'cPath=' . (($_GET['manufacturers_id'] > 0 and $_GET['filter_id']) > 0 ?  zen_get_generated_category_path_rev($_GET['filter_id']) : ($_GET['cPath'] > 0 ? zen_get_generated_category_path_rev($_GET['cPath']) : zen_get_generated_category_path_rev($listing->fields['master_categories_id']))) . '&products_id=' . $listing->fields['products_id']) . '">' . zen_image(DIR_WS_IMAGES . $listing->fields['products_image'], $listing->fields['products_name'], IMAGE_PRODUCT_LISTING_WIDTH, IMAGE_PRODUCT_LISTING_HEIGHT, 'class="listingProductImage"') . '</a>';
              $lc_text = family_info_tooltip($listing->fields['products_model'],IMAGE_PRODUCT_LISTING_HEIGHT, $lc_text);
          }
          break;
        }

        $list_box_contents[$rows][$col] = array('align' => $lc_align,
                                                'params' => 'class="productListing-data"',
                                                'text'  => $lc_text);
      }

      // add description and match alternating colors
      //if (PRODUCT_LIST_DESCRIPTION > 0) {
      //  $rows++;
      //  if ($extra_row == 1) {
      //    $list_box_description = "productListing-data-description-even";
      //    $extra_row=0;
      //  } else {
      //    $list_box_description = "productListing-data-description-odd";
      //    $extra_row=1;
      //  }
      //  $list_box_contents[$rows][] = array('params' => 'class="' . $list_box_description . '" colspan="' . $zc_col_count_description . '"',
      //  'text' => zen_trunc_string(zen_clean_html(stripslashes(zen_get_products_description($listing->fields['products_id'], $_SESSION['languages_id']))), PRODUCT_LIST_DESCRIPTION));
      //}

    }else{

      ///////////////////////////////////////////////////////////////////
      //gallery view is not list so it is either medium or small images//
      ///////////////////////////////////////////////////////////////////

      $lc_align = 'center';
      if ($listing->fields['products_image'] == '' and PRODUCTS_IMAGE_NO_IMAGE_STATUS == 0) {
        $lc_text = '';
      } else {
        //if (isset($_GET['manufacturers_id'])) {
          //$lc_text = '<a href="' . zen_href_link(zen_get_info_page($listing->fields['products_id']), 'cPath=' . (($_GET['manufacturers_id'] > 0 and $_GET['filter_id']) > 0 ?  zen_get_generated_category_path_rev($_GET['filter_id']) : ($_GET['cPath'] > 0 ? zen_get_generated_category_path_rev($_GET['cPath']) : zen_get_generated_category_path_rev($listing->fields['master_categories_id']))) . '&products_id=' . $listing->fields['products_id']) . '">' . zen_image(DIR_WS_IMAGES . $listing->fields['products_image'], $listing->fields['products_name'], $image_width, $image_height, 'class="listingProductImage"') . '</a>';
        //} else {
          $lc_text = '<a href="' . zen_href_link(zen_get_info_page($listing->fields['products_id']), 'cPath=' . (($_GET['manufacturers_id'] > 0 and $_GET['filter_id']) > 0 ?  zen_get_generated_category_path_rev($_GET['filter_id']) : ($_GET['cPath'] > 0 ? zen_get_generated_category_path_rev($_GET['cPath']) : zen_get_generated_category_path_rev($listing->fields['master_categories_id']))) . '&products_id=' . $listing->fields['products_id']) . '">' . zen_image(DIR_WS_IMAGES . $listing->fields['products_image'], $listing->fields['products_name'], $image_width, $image_height, 'class="listingProductImage"') . '</a>';

          //Add on promotion text
          if(count_promotion_items()>0){
            $lc_text .= product_promotion($listing->fields['products_id'],$current_page);
          }
        //}
      }
      //Force size of images to suit size of display. This has to be calulated manually otherwise the hover function will not work
      //IMPORTANT - lc_text must not be change in any way before this calulation. Do changes after
      if($_SESSION['current_view'] == GALLERY_VIEW_SMALL && FUAL_SLIMBOX_LIGHTBOX){
        $wd = (int)((int)substr($lc_text, strpos($lc_text,'width=')+7,3)*GALLERY_FACTOR_SMALL);
        $hd =(int)((int)substr($lc_text, strpos($lc_text,'height=')+8,3)* GALLERY_FACTOR_SMALL);
        $lc_text = substr($lc_text, 0, strpos($lc_text,'width')+7) . $wd . '" height="' . $hd . '" ' .  substr($lc_text, strpos($lc_text,'class'));
      }elseif($_SESSION['current_view'] == GALLERY_VIEW_MEDIUM && FUAL_SLIMBOX_LIGHTBOX){
        $wd = (int)((int)substr($lc_text, strpos($lc_text,'width=')+7,3)*GALLERY_FACTOR_MEDIUM);
        $hd =(int)((int)substr($lc_text, strpos($lc_text,'height=')+8,3)* GALLERY_FACTOR_MEDIUM);
        $lc_text = substr($lc_text, 0, strpos($lc_text,'width')+7) . $wd . '" height="' . $hd . '" ' .  substr($lc_text, strpos($lc_text,'class'));
      }

      //Changes all done now add the final output to the
      $list_box_contents[$rows][$cur_col] = array('align' => $lc_align,
                                                  'params' => 'class="productListing-data"',
                                                  'text'  => family_info_tooltip($listing->fields['products_model'],$hd,$lc_text));
      $cur_col++;
      if($_SESSION['current_view'] == GALLERY_VIEW_MEDIUM && $cur_col == GALLERY_COLS_MEDIUM){
        $cur_col=0;
        $rows++;
      }
      if($_SESSION['current_view'] == GALLERY_VIEW_SMALL && $cur_col == GALLERY_COLS_SMALL){
        $cur_col=0;
        $rows++;
      }
      }//eof if gallery view
    $listing->MoveNext();
    }
  $error_categories = false;
} else {
  $list_box_contents = array();

  $list_box_contents[0] = array('params' => 'class="productListing-odd"');
  if($_SESSION['OptionFilter']->is_filter_on()==0){
    $list_box_contents[0][] = array('params' => 'class="productListing-data"',
                                              'text' => TEXT_NO_PRODUCTS);
  }else{
    $list_box_contents[0][] = array('params' => 'class="productListing-data"',
                                              'text' => TEXT_NO_PRODUCTS_FILTERED);
  }

  $error_categories = true;
}

if (($how_many > 0 and $show_submit == true and $listing_split->number_of_rows > 0) and (PRODUCT_LISTING_MULTIPLE_ADD_TO_CART == 1 or  PRODUCT_LISTING_MULTIPLE_ADD_TO_CART == 3) ) {
  $show_top_submit_button = true;
} else {
  $show_top_submit_button = false;
}
if (($how_many > 0 and $show_submit == true and $listing_split->number_of_rows > 0) and (PRODUCT_LISTING_MULTIPLE_ADD_TO_CART >= 2) ) {
  $show_bottom_submit_button = true;
} else {
  $show_bottom_submit_button = false;
}



  if ($how_many > 0 && PRODUCT_LISTING_MULTIPLE_ADD_TO_CART != 0 and $show_submit == true and $listing_split->number_of_rows > 0) {
  // bof: multiple products
    echo zen_draw_form('multiple_products_cart_quantity', zen_href_link(FILENAME_DEFAULT, zen_get_all_get_params(array('action')) . 'action=multiple_products_add_product'), 'post', 'enctype="multipart/form-data"');
  }
 // echo __FILE__. __LINE__.var_dump($list_box_contents);

?>
