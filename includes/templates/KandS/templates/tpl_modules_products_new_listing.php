<?php
/**
 * Module Template
 *
 * Loaded automatically by index.php?main_page=products_new.<br />
 * Displays listing of New Products
 *
 * @package templateSystem
 * @copyright Copyright 2003-2007 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_modules_products_new_listing.php 6096 2007-04-01 00:43:21Z ajeh $
 */
?>
<table border="0" width="100%" cellspacing="8" cellpadding="0">
          <!--<tr>
            <td colspan="<?php echo $cols_in_view; ?>"><hr /></td>
          </tr>-->
<?php
  $group_id = zen_get_configuration_key_value('PRODUCT_NEW_LIST_GROUP_ID');

  /////KandS Gallery view
  //Set cols and rows value
  $cur_row = 1;
  $cur_col = 1;
  $output = '';
  /// eof KandS Gallery view

  if ($products_new_split->number_of_rows > 0) {
    $products_new = $db->Execute($products_new_split->sql_query);
    while (!$products_new->EOF) {
      $products_new->fields['products_image']=get_image_xref($products_new->fields['products_image']);

      /////KandS Gallery view
      if($_SESSION['current_view']== GALLERY_VIEW_LIST){
      /// eof KandS Gallery view

      if (PRODUCT_NEW_LIST_IMAGE != '0') {
        if ($products_new->fields['products_image'] == '' and PRODUCTS_IMAGE_NO_IMAGE_STATUS == 0) {
          $display_products_image = str_repeat('<br clear="all" />', substr(PRODUCT_NEW_LIST_IMAGE, 3, 1));
        } else {
          $display_products_image = '<a href="' . zen_href_link(zen_get_info_page($products_new->fields['products_id']), 'cPath=' . zen_get_generated_category_path_rev($products_new->fields['master_categories_id']) . '&products_id=' . $products_new->fields['products_id']) . '">' . zen_image(DIR_WS_IMAGES . $products_new->fields['products_image'], $products_new->fields['products_name'], IMAGE_PRODUCT_NEW_LISTING_WIDTH, IMAGE_PRODUCT_NEW_LISTING_HEIGHT) . '</a>' . str_repeat('<br clear="all" />', substr(PRODUCT_NEW_LIST_IMAGE, 3, 1));
        }
      } else {
        $display_products_image = '';
      }

      if (PRODUCT_NEW_LIST_NAME != '0') {
        $display_products_name = '<a href="' . zen_href_link(zen_get_info_page($products_new->fields['products_id']), 'cPath=' . zen_get_generated_category_path_rev($products_new->fields['master_categories_id']) . '&products_id=' . $products_new->fields['products_id']) . '"><strong>' . $products_new->fields['products_name'] . '</strong></a>' . str_repeat('<br clear="all" />', substr(PRODUCT_NEW_LIST_NAME, 3, 1));
      } else {
        $display_products_name = '';
      }

      if (PRODUCT_NEW_LIST_MODEL != '0' and zen_get_show_product_switch($products_new->fields['products_id'], 'model')) {
        $display_products_model = TEXT_PRODUCTS_MODEL . $products_new->fields['products_model'] . str_repeat('<br clear="all" />', substr(PRODUCT_NEW_LIST_MODEL, 3, 1));
      } else {
        $display_products_model = '';
      }

      if (PRODUCT_NEW_LIST_WEIGHT != '0' and zen_get_show_product_switch($products_new->fields['products_id'], 'weight')) {
        $display_products_weight = '<br />' . TEXT_PRODUCTS_WEIGHT . $products_new->fields['products_weight'] . TEXT_SHIPPING_WEIGHT . str_repeat('<br clear="all" />', substr(PRODUCT_NEW_LIST_WEIGHT, 3, 1));
      } else {
        $display_products_weight = '';
      }

      if (PRODUCT_NEW_LIST_QUANTITY != '0' and zen_get_show_product_switch($products_new->fields['products_id'], 'quantity')) {
        if ($products_new->fields['products_quantity'] <= 0) {
          $display_products_quantity = TEXT_OUT_OF_STOCK . str_repeat('<br clear="all" />', substr(PRODUCT_NEW_LIST_QUANTITY, 3, 1));
        } else {
          $display_products_quantity = TEXT_PRODUCTS_QUANTITY . $products_new->fields['products_quantity'] . str_repeat('<br clear="all" />', substr(PRODUCT_NEW_LIST_QUANTITY, 3, 1));
        }
      } else {
        $display_products_quantity = '';
      }

      if (PRODUCT_NEW_LIST_DATE_ADDED != '0' and zen_get_show_product_switch($products_new->fields['products_id'], 'date_added')) {
        $display_products_date_added = TEXT_DATE_ADDED . ' ' . zen_date_long($products_new->fields['products_date_added']) . str_repeat('<br clear="all" />', substr(PRODUCT_NEW_LIST_DATE_ADDED, 3, 1));
      } else {
        $display_products_date_added = '';
      }

      if (PRODUCT_NEW_LIST_MANUFACTURER != '0' and zen_get_show_product_switch($products_new->fields['products_id'], 'manufacturer')) {
        $display_products_manufacturers_name = ($products_new->fields['manufacturers_name'] != '' ? TEXT_MANUFACTURER . ' ' . $products_new->fields['manufacturers_name'] . str_repeat('<br clear="all" />', substr(PRODUCT_NEW_LIST_MANUFACTURER, 3, 1)) : '');
      } else {
        $display_products_manufacturers_name = '';
      }

      if ((PRODUCT_NEW_LIST_PRICE != '0' and zen_get_products_allow_add_to_cart($products_new->fields['products_id']) == 'Y') and zen_check_show_prices() == true) {
        $products_price = zen_get_products_display_price($products_new->fields['products_id']);
        $display_products_price = TEXT_PRICE . ' ' . $products_price . str_repeat('<br clear="all" />', substr(PRODUCT_NEW_LIST_PRICE, 3, 1)) . (zen_get_show_product_switch($products_new->fields['products_id'], 'ALWAYS_FREE_SHIPPING_IMAGE_SWITCH') ? (zen_get_product_is_always_free_shipping($products_new->fields['products_id']) ? TEXT_PRODUCT_FREE_SHIPPING_ICON . '<br />' : '') : '');
      } else {
        $display_products_price = '';
      }

// more info in place of buy now
      if (PRODUCT_NEW_BUY_NOW != '0' and zen_get_products_allow_add_to_cart($products_new->fields['products_id']) == 'Y') {
        if (zen_has_product_attributes($products_new->fields['products_id'])) {
          $link = '<a href="' . zen_href_link(zen_get_info_page($products_new->fields['products_id']), 'cPath=' . zen_get_generated_category_path_rev($products_new->fields['master_categories_id']) . '&products_id=' . $products_new->fields['products_id']) . '">' . MORE_INFO_TEXT . '</a>';
        } else {
//          $link= '<a href="' . zen_href_link(FILENAME_PRODUCTS_NEW, zen_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . $products_new->fields['products_id']) . '">' . zen_image_button(BUTTON_IMAGE_IN_CART, BUTTON_IN_CART_ALT) . '</a>';
          if (PRODUCT_NEW_LISTING_MULTIPLE_ADD_TO_CART > 0 && $products_new->fields['products_qty_box_status'] != 0) {
//            $how_many++;
            $link = TEXT_PRODUCT_NEW_LISTING_MULTIPLE_ADD_TO_CART . "<input type=\"text\" name=\"products_id[" . $products_new->fields['products_id'] . "]\" value=\"0\" size=\"4\" />";
          } else {
            $link = '<a href="' . zen_href_link(FILENAME_PRODUCTS_NEW, zen_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . $products_new->fields['products_id']) . '">' . zen_image_button(BUTTON_IMAGE_BUY_NOW, BUTTON_BUY_NOW_ALT) . '</a>&nbsp;';
          }
        }

        $the_button = $link;
        $products_link = '<a href="' . zen_href_link(zen_get_info_page($products_new->fields['products_id']), 'cPath=' . zen_get_generated_category_path_rev($products_new->fields['master_categories_id']) . '&products_id=' . $products_new->fields['products_id']) . '">' . MORE_INFO_TEXT . '</a>';
        $display_products_button = zen_get_buy_now_button($products_new->fields['products_id'], $the_button, $products_link) . '<br />' . zen_get_products_quantity_min_units_display($products_new->fields['products_id']) . str_repeat('<br clear="all" />', substr(PRODUCT_NEW_BUY_NOW, 3, 1));
      } else {
        $link = '<a href="' . zen_href_link(zen_get_info_page($products_new->fields['products_id']), 'cPath=' . zen_get_generated_category_path_rev($products_new->fields['master_categories_id']) . '&products_id=' . $products_new->fields['products_id']) . '">' . MORE_INFO_TEXT . '</a>';
        $the_button = $link;
        $products_link = '<a href="' . zen_href_link(zen_get_info_page($products_new->fields['products_id']), 'cPath=' . zen_get_generated_category_path_rev($products_new->fields['master_categories_id']) . '&products_id=' . $products_new->fields['products_id']) . '">' . MORE_INFO_TEXT . '</a>';
        $display_products_button = zen_get_buy_now_button($products_new->fields['products_id'], $the_button, $products_link) . '<br />' . zen_get_products_quantity_min_units_display($products_new->fields['products_id']) . str_repeat('<br clear="all" />', substr(PRODUCT_NEW_BUY_NOW, 3, 1));
      }

      if (PRODUCT_NEW_LIST_DESCRIPTION > '0') {
        $disp_text = zen_get_products_description($products_new->fields['products_id']);
        $disp_text = zen_clean_html($disp_text);

        $display_products_description = stripslashes(zen_trunc_string($disp_text, PRODUCT_NEW_LIST_DESCRIPTION, '<a href="' . zen_href_link(zen_get_info_page($products_new->fields['products_id']), 'cPath=' . zen_get_generated_category_path_rev($products_new->fields['master_categories_id']) . '&products_id=' . $products_new->fields['products_id']) . '"> ' . MORE_INFO_TEXT . '</a>'));
      } else {
        $display_products_description = '';
      }

?>
          <tr>
            <td width="<?php echo IMAGE_PRODUCT_NEW_LISTING_WIDTH + 10; ?>"  class="productListing-data" align="center" valign="middle">
              <?php
                echo family_info_tooltip($products_new->fields['products_model'],IMAGE_PRODUCT_NEW_LISTING_HEIGHT, $display_products_image);
              ?>
            </td>
            <td class="productListing-data" align="center" valign="middle">
              <?php
                echo $display_products_name;
                echo $display_products_description;
                if($display_products_description!='')echo '<br />';
                echo '<span style="font-size:10px">'.$display_products_model.'</span>';
                echo '<br />'.product_promotion($products_new->fields['products_id'],$current_page);
              ?>
            </td>

            <td width="121px" class="productListing-data">
              <?php
                echo $display_products_button;
              ?>
            </td>
          </tr>
<?php
      $products_new->MoveNext();

      //eof if gallery view = list
      }else{
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //the gallery view is either meduim or small images
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($products_new->fields['products_image'] == '' and PRODUCTS_IMAGE_NO_IMAGE_STATUS == 0) {
          $display_products_image = str_repeat('<br clear="all" />', substr(PRODUCT_NEW_LIST_IMAGE, 3, 1));
        } else {
          $display_products_image = '<a href="' . zen_href_link(zen_get_info_page($products_new->fields['products_id']), 'cPath=' . zen_get_generated_category_path_rev($products_new->fields['master_categories_id']) . '&products_id=' . $products_new->fields['products_id']) . '">' . zen_image(DIR_WS_IMAGES . $products_new->fields['products_image'], $products_new->fields['products_name'], $image_width, $image_height) . '</a><br />'.product_promotion($products_new->fields['products_id'],$current_page)
          . str_repeat('<br clear="all" />', substr(PRODUCT_NEW_LIST_IMAGE, 3, 1));
        }

        if($_SESSION['current_view'] == GALLERY_VIEW_SMALL && FUAL_SLIMBOX_LIGHTBOX){
          $wd = (int)((int)substr($display_products_image, strpos($display_products_image,'width')+7,3)*GALLERY_FACTOR_SMALL);
          $hd =(int)((int)substr($display_products_image, strpos($display_products_image,'height')+8,3)* GALLERY_FACTOR_SMALL);
          $display_products_image = substr($display_products_image, 0, strpos($display_products_image,'width')+7) . $wd . '" height="' . $hd . '" ' .  substr($display_products_image, strpos($display_products_image,'style'));
        }elseif($_SESSION['current_view'] == GALLERY_VIEW_MEDIUM && FUAL_SLIMBOX_LIGHTBOX){
          $wd = (int)((int)substr($display_products_image, strpos($display_products_image,'width')+7,3)*GALLERY_FACTOR_MEDIUM);
          $hd =(int)((int)substr($display_products_image, strpos($display_products_image,'height')+8,3)* GALLERY_FACTOR_MEDIUM);
          $display_products_image = substr($display_products_image, 0, strpos($display_products_image,'width')+7) . $wd . '" height="' . $hd . '" ' .  substr($display_products_image, strpos($display_products_image,'style'));
        }


        if ($cur_col > $cols_in_view){
          $cur_col=1;
          echo '</tr>';
        }
        if ($cur_col == 1){
          echo '<tr>';
        }

        echo'<td class="productListing-data">'. family_info_tooltip($products_new->fields['products_model'],$hd,$display_products_image) . '</td>'; ?>
          </td>

        <?php
        $cur_col++;
        $products_new->MoveNext();
      }
    }
    echo '</tr>';
  } else {
?>
          <tr>
            <td class="main" colspan="2"><?php echo TEXT_NO_NEW_PRODUCTS; ?></td>
          </tr>
<?php
  }
?>
</table>
