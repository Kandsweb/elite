<?php
  //Set the following valu to false to to hide include default image check box,
  //If set to true this adds the default image for the attribute to products_with_stock table
  define ('INCLUDE_DEFAULT_IMAGE', TRUE);
	define ('INCLUDE_DEFAULT_IMAGE_DEFAULT', TRUE);//This causes the check boxes to do with the default image to be auto ticked

  $colspal = 9;
    require('includes/application_top.php');

  // verify option names, values, products
  $chk_option_names = $db->Execute("select * from " . TABLE_PRODUCTS_OPTIONS . " where language_id='" . $_SESSION['languages_id'] . "' limit 1");
  if ($chk_option_names->RecordCount() < 1) {
    $messageStack->add_session(ERROR_DEFINE_OPTION_NAMES, 'success');
    zen_redirect(zen_href_link(FILENAME_OPTIONS_NAME_MANAGER));
  }
  $chk_option_values = $db->Execute("select * from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where language_id='" . $_SESSION['languages_id'] . "' limit 1");
  if ($chk_option_values->RecordCount() < 1) {
    $messageStack->add_session(ERROR_DEFINE_OPTION_VALUES, 'caution');
    zen_redirect(zen_href_link(FILENAME_OPTIONS_VALUES_MANAGER));
  }
  $chk_products = $db->Execute("select * from " . TABLE_PRODUCTS . " limit 1");
  if ($chk_products->RecordCount() < 1) {
    $messageStack->add_session(ERROR_DEFINE_PRODUCTS, 'caution');
    zen_redirect(zen_href_link(FILENAME_CATEGORIES));
  }

  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

  $languages = zen_get_languages();

  $action = (isset($_GET['action']) ? $_GET['action'] : '');
  $products_filter = (isset($_GET['pID']) ? $_GET['pID'] : $products_filter);
  $current_category_id = (isset($_GET['cPath']) ? $_GET['cPath'] : $current_category_id);

  if (zen_not_null($action)) {
    $_SESSION['page_info'] = '';
    if (isset($_GET['option_page'])) $_SESSION['page_info'] .= 'option_page=' . $_GET['option_page'] . '&';
    if (isset($_GET['value_page'])) $_SESSION['page_info'] .= 'value_page=' . $_GET['value_page'] . '&';
    if (isset($_GET['attribute_page'])) $_SESSION['page_info'] .= 'attribute_page=' . $_GET['attribute_page'] . '&';
    if (isset($_GET['pID'])) $_SESSION['page_info'] .= 'pID=' . $_GET['pID'] . '&';
    if (isset($_GET['cPath'])) $_SESSION['page_info'] .= 'cPath=' . $_GET['cPath'] . '&';

    if (zen_not_null($_SESSION['page_info'])) {
      $_SESSION['page_info'] = substr($_SESSION['page_info'], 0, -1);
    }

	switch ($action) {
	/////////////////////////////////////////
	//// BOF OF FLAGS
			case 'set_flag_attributes_default':
					$new_flag= $db->Execute("select products_attributes_id, products_id, attributes_default from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id='" . $_GET['pID'] . "' and products_attributes_id='" . $_GET['attributes_id'] . "'");
					if ($new_flag->fields['attributes_default'] == '0') {
            //unset all first
            $db->Execute("update " . TABLE_PRODUCTS_ATTRIBUTES . " set attributes_default='0' where products_id='" . $_GET['pID'] . "'");
						$db->Execute("update " . TABLE_PRODUCTS_ATTRIBUTES . " set attributes_default='1' where products_id='" . $_GET['pID'] . "' and products_attributes_id='" . $_GET['attributes_id'] . "'");
					} else {
            //unset all first
            $db->Execute("update " . TABLE_PRODUCTS_ATTRIBUTES . " set attributes_default='0' where products_id='" . $_GET['pID'] . "'");
						$db->Execute("update " . TABLE_PRODUCTS_ATTRIBUTES . " set attributes_default='0' where products_id='" . $_GET['pID'] . "' and products_attributes_id='" . $_GET['attributes_id'] . "'");
					}
          $action='';
          zen_redirect(zen_href_link(FILENAME_QUICK_ATTRIBUTES, $_SESSION['page_info']));
					break;



      case 'delete_attribute':
        // demo active test
        if (zen_admin_demo()) {
          $_GET['action']= '';
          $messageStack->add_session(ERROR_ADMIN_DEMO, 'caution');
          zen_redirect(zen_href_link(FILENAME_QUICK_ATTRIBUTES, $_SESSION['page_info'] . '&cPath=' . $_POST['cPage']));
        }
        $attribute_id = zen_db_prepare_input($_GET['attribute_id']);

        $db->Execute("delete from " . TABLE_PRODUCTS_ATTRIBUTES . "
                      where products_attributes_id = '" . (int)$attribute_id . "'");

        // added for DOWNLOAD_ENABLED. Always try to remove attributes, even if downloads are no longer enabled
        $db->Execute("delete from " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . "
                      where products_attributes_id = '" . (int)$attribute_id . "'");


        ////// Delete stcok level from products_with_stock_attributes table
        $query = 'delete from '.TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK.' where products_id="'.$_GET['pID'].'" and stock_attributes="'.$attribute_id.'" limit 1';
        $db->Execute($query);

        $messageStack->add_session(sprintf(WARNING_ONE_DELETED, $_GET['attribute_id']), 'success');

        //Update parents (master) stock quantity)
        $query = 'select sum(quantity) as quantity from '.TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK.' where products_id = "'.(int)$products_filter.'"';
        $quantity = $db->Execute($query);
        $query = 'update '.TABLE_PRODUCTS.' set  products_quantity="'.$quantity->fields['quantity'].'" where products_id="'.(int)$products_filter.'"';
        $db->Execute($query);


        // reset products_price_sorter for searches etc.
        zen_update_products_price_sorter($products_filter);

        zen_redirect(zen_href_link(FILENAME_QUICK_ATTRIBUTES, $_SESSION['page_info'] . '&cPath=' . $current_category_id));
				break;


      case 'update_product_attribute':
      for($rowC=1; $rowC<= $_POST['att_count']; $rowC++){
        $check_duplicate = $db->Execute("select * from " . TABLE_PRODUCTS_ATTRIBUTES . "
                                         where products_id ='" . $_POST['products_id'] . "'
                                         and options_id = '" . $_POST['options_id' .  $rowC] . "'
                                         and options_values_id = '" . $_POST['values_id_' .  $rowC] . "'
                                         and products_attributes_id != '" . $_POST['attribute_id'] . "'");

        if ($check_duplicate->RecordCount() > 0) {
          // do not add duplicates give a warning
          $messageStack->add_session(ATTRIBUTE_WARNING_DUPLICATE_UPDATE . ' - ' . zen_options_name($_POST['options_id' .  $rowC]) . ' : ' . zen_values_name($_POST['values_id']), 'error');
        } else {
          // Validate options_id and options_value_id
          if (!zen_validate_options_to_options_value($_POST['options_id'], $_POST['values_id_' . $rowC])) {
            // do not add invalid match
            $messageStack->add_session(ATTRIBUTE_WARNING_INVALID_MATCH_UPDATE . ' - ' . zen_options_name($_POST['options_id']) . ' : ' . zen_values_name($_POST['values_id_' . $rowC]), 'error');
          } else {
            // add the new attribute
// iii 030811 added:  Enforce rule that TEXT and FILE Options use value PRODUCTS_OPTIONS_VALUES_TEXT_ID
        $products_options_query = $db->Execute("select products_options_type from " . TABLE_PRODUCTS_OPTIONS . " where products_options_id = '" . $_POST['options_id'] . "'");
        switch ($products_options_query->fields['products_options_type']) {
          case PRODUCTS_OPTIONS_TYPE_TEXT:
          case PRODUCTS_OPTIONS_TYPE_FILE:
            $values_id = PRODUCTS_OPTIONS_VALUES_TEXT_ID;
            break;
          default:
          $values_id = zen_db_prepare_input($_POST['values_id_' . $rowC]);
        }
          // iii 030811 added END

        $products_id = zen_db_prepare_input($_POST['products_id']);
        $options_id = zen_db_prepare_input($_POST['options_id']);
        $include_default_image = zen_db_prepare_input($_POST['include_default_image_' . $rowC]);
        $value_price = zen_db_prepare_input($_POST['value_price_' .  $rowC]);
        $price_prefix = '+';

        $products_options_sort_order = zen_db_prepare_input($_POST['products_options_sort_order_' .  $rowC]);
        $product_attribute_is_free = zen_db_prepare_input('1');
        $products_attributes_weight = zen_db_prepare_input('0');
        $products_attributes_weight_prefix = zen_db_prepare_input('+');
        $attributes_display_only = zen_db_prepare_input('0');
        $attributes_default = zen_db_prepare_input($_POST['attributes_default']);
        $attributes_discounted = zen_db_prepare_input('1');
        $attributes_price_base_included = zen_db_prepare_input('1');

        $attributes_price_onetime = zen_db_prepare_input('0');
        $attributes_price_factor = zen_db_prepare_input('0');
        $attributes_price_factor_offset = zen_db_prepare_input('0');
        $attributes_price_factor_onetime = zen_db_prepare_input('0');
        $attributes_price_factor_onetime_offset = zen_db_prepare_input('0');
        $attributes_qty_prices = zen_db_prepare_input('');
        $attributes_qty_prices_onetime = zen_db_prepare_input('');

        $attributes_price_words = zen_db_prepare_input('0.0000');
        $attributes_price_words_free = zen_db_prepare_input('0');
        $attributes_price_letters = zen_db_prepare_input('0.0000');
        $attributes_price_letters_free = zen_db_prepare_input('0');
        $attributes_required = zen_db_prepare_input('0');

        $attribute_id = zen_db_prepare_input($_POST['attribute_id_' . $rowC]);

        // Following line will be needed if evere allowing user to upload a specific image
        // attributes images
        // when set to none remove from database
        /* if (isset($_POST['attributes_image']) && zen_not_null($_POST['attributes_image']) && ($_POST['attributes_image'] != 'none')) {
          // Following line will be needed if evere allowing user to upload a specific image
          //$attributes_image = zen_db_prepare_input($_POST['attributes_image']);
          $attributes_image_none = false;
        } else {
          $attributes_image = '';
          $attributes_image_none = true;
        }
           //This will upload users image
          $attributes_image = new upload('attributes_image');
          $attributes_image->set_destination(DIR_FS_CATALOG_IMAGES . $_POST['img_dir']);
          if ($attributes_image->parse() && $attributes_image->save($_POST['overwrite'])) {
            $attributes_image_name = ($attributes_image->filename != 'none' ? ($_POST['img_dir'] . $attributes_image->filename) : '');
          } else {
            $attributes_image_name = ((isset($_POST['attributes_previous_image']) and $_POST['attributes_image'] != 'none') ? $_POST['attributes_previous_image'] : '');
          }

          if ($_POST['image_delete'] == 1) {
            $attributes_image_name = '';
          }
          */

          if ($include_default_image){
            //look up default image from TABLE_ATTRIBUTES_IMAGE_DEFAULTS
            $sql = 'SELECT image FROM ' . TABLE_ATTRIBUTES_IMAGE_DEFAULTS . ' WHERE products_options_values_id = "' . (int)$values_id . '"' ;
            $attributes_images_rs = $db->Execute($sql);
            if (!$attributes_images_rs->EOF){
              $attributes_image_name = $attributes_images_rs->fields['image'];
            }   else {
              $attributes_image_name = '';
            }

            $db->Execute("update " . TABLE_PRODUCTS_ATTRIBUTES . "
                        set attributes_image = '" .  $attributes_image_name . "'
                        where products_attributes_id = '" . (int)$attribute_id . "'");
          }


          $db->Execute("update " . TABLE_PRODUCTS_ATTRIBUTES . "
                        set products_id = '" . (int)$products_id . "',
                            options_id = '" . (int)$options_id . "',
                            options_values_id = '" . (int)$values_id . "',
                            options_values_price = '" . zen_db_input($value_price) . "',
                            price_prefix = '" . zen_db_input($price_prefix) . "',
                            products_options_sort_order = '" . zen_db_input($products_options_sort_order) . "',
                            product_attribute_is_free = '" . zen_db_input($product_attribute_is_free) . "',
                            products_attributes_weight = '" . zen_db_input($products_attributes_weight) . "',
                            products_attributes_weight_prefix = '" . zen_db_input($products_attributes_weight_prefix) . "',
                            attributes_display_only = '" . zen_db_input($attributes_display_only) . "',
                            attributes_discounted = '" . zen_db_input($attributes_discounted) . "',
                            attributes_price_base_included = '" . zen_db_input($attributes_price_base_included) . "',
                            attributes_price_onetime = '" . zen_db_input($attributes_price_onetime) . "',
                            attributes_price_factor = '" . zen_db_input($attributes_price_factor) . "',
                            attributes_price_factor_offset = '" . zen_db_input($attributes_price_factor_offset) . "',
                            attributes_price_factor_onetime = '" . zen_db_input($attributes_price_factor_onetime) . "',
                            attributes_price_factor_onetime_offset = '" . zen_db_input($attributes_price_factor_onetime_offset) . "',
                            attributes_qty_prices = '" . zen_db_input($attributes_qty_prices) . "',
                            attributes_qty_prices_onetime = '" . zen_db_input($attributes_qty_prices_onetime) . "',
                            attributes_price_words = '" . zen_db_input($attributes_price_words) . "',
                            attributes_price_words_free = '" . zen_db_input($attributes_price_words_free) . "',
                            attributes_price_letters = '" . zen_db_input($attributes_price_letters) . "',
                            attributes_price_letters_free = '" . zen_db_input($attributes_price_letters_free) . "',
                            attributes_required = '" . zen_db_input($attributes_required) . "'
                        where products_attributes_id = '" . (int)$attribute_id . "'");

            if (DOWNLOAD_ENABLED == 'true') {
              $products_attributes_filename = zen_db_prepare_input($_POST['products_attributes_filename']);
              $products_attributes_maxdays = zen_db_prepare_input($_POST['products_attributes_maxdays']);
              $products_attributes_maxcount = zen_db_prepare_input($_POST['products_attributes_maxcount']);

              if (zen_not_null($products_attributes_filename)) {
                $db->Execute("replace into " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . "
                              set products_attributes_id = '" . (int)$attribute_id . "',
                                  products_attributes_filename = '" . zen_db_input($products_attributes_filename) . "',
                                  products_attributes_maxdays = '" . zen_db_input($products_attributes_maxdays) . "',
                                  products_attributes_maxcount = '" . zen_db_input($products_attributes_maxcount) . "'");
              }
            }
          }
        }

        // reset products_price_sorter for searches etc.
        zen_update_products_price_sorter($_POST['products_id']);

      //Update stock levels
      $query = 'select * from '.TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK.' where products_id="'.$products_filter.'" AND stock_attributes="' .        (int)$attribute_id . '"';

      $attribute_products = $db->Execute($query);
      if($attribute_products->RecordCount() > 0){
        //record exists so update it
        $db->Execute('update ' . TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK . '
                        set quantity = "' .  (int)$_POST['stock_quantity_'. $rowC] . '"
                        where products_id = "'.$products_filter .'" AND stock_attributes="' . (int)$attribute_id .  '"');
      } else {
         //No record so insert it
         $query = 'insert into `'.TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK.'` (`products_id`,`stock_attributes`,`quantity`) values ('.(int)$products_filter.',"'.(int)$attribute_id.'",'.(int)$_POST['stock_quantity_'. $rowC].')';
         $db->Execute($query);
      }

      //Update parents (master) stock quantity)
      $query = 'select sum(quantity) as quantity from '.TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK.' where products_id = "'.(int)$products_filter.'"';
      $quantity = $db->Execute($query);
      $query = 'update '.TABLE_PRODUCTS.' set  products_quantity="'.$quantity->fields['quantity'].'" where products_id="'.(int)$products_id.'"';
      $db->Execute($query);


      }//eof For loop

       $messageStack->add_session(WARNING_ALL_ATTRIBUTES_UPDATED  . $_POST['products_id'], 'success');
      //Check what submit button was used
      if($_POST['dos'] == 'SaC'){
        zen_redirect(zen_href_link(FILENAME_QUICK_ATTRIBUTES, $_SESSION['page_info']));
      } else {
        zen_redirect(zen_href_link(FILENAME_CATEGORIES, 'cPath=' . $_POST['current_category_id']));
      }
      break;

      case 'add_product_attributes':
        //Check values have been selected to add
        if(sizeof($_POST['values_id']) < 1){
            $messageStack->add_session(ATTRIBUTE_WARNING_NONE_SELECTED . ' - ' . zen_options_name($_POST['options_id']) . ' : ' . zen_values_name($_POST['values_id'][$i]), 'error');
          break;  //Skip rest of add product attributes
        }

        // check for duplicate and block them
        // ***************    start modification for improved attributes controller

        for ($i=0; $i<sizeof($_POST['values_id']); $i++) {
          $check_duplicate = $db->Execute("select * from " . TABLE_PRODUCTS_ATTRIBUTES . "
                                           where products_id ='" . $_POST['products_id'] . "'
                                           and options_id = '" . $_POST['options_id'] . "'
                                           and options_values_id = '" . $_POST['values_id'][$i] . "'");

          if ($check_duplicate->RecordCount() > 0) {
            // do not add duplicates give a warning
            $messageStack->add_session(ATTRIBUTE_WARNING_DUPLICATE . ' - ' . zen_options_name($_POST['options_id']) . ' : ' . zen_values_name($_POST['values_id'][$i]), 'error');
          } else {
            // iii 030811 added:  For TEXT and FILE option types, ignore option value
            // entered by administrator and use PRODUCTS_OPTIONS_VALUES_TEXT instead.
            $products_options_array = $db->Execute("select products_options_type from " . TABLE_PRODUCTS_OPTIONS . " where products_options_id = '" . $_POST['options_id'] . "'");
            $values_id = zen_db_prepare_input((($products_options_array->fields['products_options_type'] == PRODUCTS_OPTIONS_TYPE_TEXT) or ($products_options_array->fields['products_options_type'] == PRODUCTS_OPTIONS_TYPE_FILE)) ? PRODUCTS_OPTIONS_VALUES_TEXT_ID : $_POST['values_id'][$i]);
            $products_id = zen_db_prepare_input($_POST['products_id']);
            $options_id = zen_db_prepare_input($_POST['options_id']);
//            $values_id = zen_db_prepare_input($_POST['values_id']);
            $value_price = zen_db_prepare_input('0.0000');
            $price_prefix = zen_db_prepare_input('+');
            $include_default_image = $_POST['add_default_image'];

            // modified options sort order to use default if not otherwise set
            if (zen_not_null($_POST['products_options_sort_order'])) {
              $products_options_sort_order = zen_db_prepare_input($_POST['products_options_sort_order']);
            } else {
              $sort_order_query = $db->Execute("select products_options_values_sort_order from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . $_POST['values_id'][$i] . "'");
              $products_options_sort_order = $sort_order_query->fields['products_options_values_sort_order'];
            } // end if (zen_not_null($_POST['products_options_sort_order'])

            // end modification for sort order
            $product_attribute_is_free = zen_db_prepare_input('1');
            $products_attributes_weight = zen_db_prepare_input('0');
            $products_attributes_weight_prefix = zen_db_prepare_input('+');
            $attributes_display_only = zen_db_prepare_input('0');
            $attributes_default = zen_db_prepare_input('0');
            $attributes_discounted = zen_db_prepare_input('1');
            $attributes_price_base_included = zen_db_prepare_input('1');

            $attributes_price_onetime = zen_db_prepare_input('');
            $attributes_price_factor = zen_db_prepare_input('0.0000');
            $attributes_price_factor_offset = zen_db_prepare_input('0.0000');
            $attributes_price_factor_onetime = zen_db_prepare_input('0.0000');
            $attributes_price_factor_onetime_offset = zen_db_prepare_input('0.0000');
            $attributes_qty_prices = zen_db_prepare_input('');
            $attributes_qty_prices_onetime = zen_db_prepare_input('');

            $attributes_price_words = zen_db_prepare_input('0.0000');
            $attributes_price_words_free = zen_db_prepare_input('0');
            $attributes_price_letters = zen_db_prepare_input('0.0000');
            $attributes_price_letters_free = zen_db_prepare_input('0');
            $attributes_required = zen_db_prepare_input('0');

          // add - update as record exists
          // attributes images
          if ($include_default_image){
            //look up default image from TABLE_ATTRIBUTES_IMAGE_DEFAULTS
            $sql = 'SELECT image FROM ' . TABLE_ATTRIBUTES_IMAGE_DEFAULTS . ' WHERE products_options_values_id = "' . (int)$values_id . '"' ;
            $attributes_images_rs = $db->Execute($sql);
            if (!$attributes_images_rs->EOF){
              $attributes_image_name = $attributes_images_rs->fields['image'];
            }   else {
              $attributes_image_name = '';
            }
          }

          // when set to none remove from database
          if (isset($_POST['attributes_image']) && zen_not_null($_POST['attributes_image']) && ($_POST['attributes_image'] != 'none')) {
            $attributes_image = zen_db_prepare_input($_POST['attributes_image']);
          } else {
            $attributes_image = '';
          } // end  if (isset($_POST['attributes_image']) && zen_not_null($_POST['attributes_image']) && ($_POST['attributes_image'] != 'none'))

          /*$attributes_image = new upload('attributes_image');
          $attributes_image->set_destination(DIR_FS_CATALOG_IMAGES . $_POST['img_dir']);
          if ($attributes_image->parse() && $attributes_image->save($_POST['overwrite'])) {
            $attributes_image_name = $_POST['img_dir'] . $attributes_image->filename;
          } else {
            $attributes_image_name = (isset($_POST['attributes_previous_image']) ? $_POST['attributes_previous_image'] : '');
          }*/

            $db->Execute("insert into " . TABLE_PRODUCTS_ATTRIBUTES . " (products_attributes_id, products_id, options_id, options_values_id, options_values_price, price_prefix, products_options_sort_order, product_attribute_is_free, products_attributes_weight, products_attributes_weight_prefix, attributes_display_only, attributes_default, attributes_discounted, attributes_image, attributes_price_base_included, attributes_price_onetime, attributes_price_factor, attributes_price_factor_offset, attributes_price_factor_onetime, attributes_price_factor_onetime_offset, attributes_qty_prices, attributes_qty_prices_onetime, attributes_price_words, attributes_price_words_free, attributes_price_letters, attributes_price_letters_free, attributes_required)
                          values (0,
                                  '" . (int)$products_id . "',
                                  '" . (int)$options_id . "',
                                  '" . (int)$values_id . "',
                                  '" . (float)zen_db_input($value_price) . "',
                                  '" . zen_db_input($price_prefix) . "',
                                  '" . (int)zen_db_input($products_options_sort_order) . "',
                                  '" . (int)zen_db_input($product_attribute_is_free) . "',
                                  '" . (float)zen_db_input($products_attributes_weight) . "',
                                  '" . zen_db_input($products_attributes_weight_prefix) . "',
                                  '" . (int)zen_db_input($attributes_display_only) . "',
                                  '" . (int)zen_db_input($attributes_default) . "',
                                  '" . (int)zen_db_input($attributes_discounted) . "',
                                  '" . zen_db_input($attributes_image_name) . "',
                                  '" . (int)zen_db_input($attributes_price_base_included) . "',
                                  '" . (float)zen_db_input($attributes_price_onetime) . "',
                                  '" . (float)zen_db_input($attributes_price_factor) . "',
                                  '" . (float)zen_db_input($attributes_price_factor_offset) . "',
                                  '" . (float)zen_db_input($attributes_price_factor_onetime) . "',
                                  '" . (float)zen_db_input($attributes_price_factor_onetime_offset) . "',
                                  '" . zen_db_input($attributes_qty_prices) . "',
                                  '" . zen_db_input($attributes_qty_prices_onetime) . "',
                                  '" . (float)zen_db_input($attributes_price_words) . "',
                                  '" . (int)zen_db_input($attributes_price_words_free) . "',
                                  '" . (float)zen_db_input($attributes_price_letters) . "',
                                  '" . (int)zen_db_input($attributes_price_letters_free) . "',
                                  '" . (int)zen_db_input($attributes_required) . "')");

            $products_attributes_id = $db->Insert_ID();

            if (DOWNLOAD_ENABLED == 'true') {
              $products_attributes_id = $db->Insert_ID();

              $products_attributes_filename = zen_db_prepare_input($_POST['products_attributes_filename']);
              $products_attributes_maxdays = (int)zen_db_prepare_input($_POST['products_attributes_maxdays']);
              $products_attributes_maxcount = (int)zen_db_prepare_input($_POST['products_attributes_maxcount']);

              if (zen_not_null($products_attributes_filename)) {
                $db->Execute("insert into " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . "
                              values (" . (int)$products_attributes_id . ",
                                      '" . zen_db_input($products_attributes_filename) . "',
                                      '" . zen_db_input($products_attributes_maxdays) . "',
                                      '" . zen_db_input($products_attributes_maxcount) . "')");
              } // end if (zen_not_null($products_attributes_filename))
            } // end if (DOWNLOAD_ENABLED == 'true')
        //} // end if (!zen_validate_options_to_options_value($_POST['options_id'], $_POST['values_id'])) #####  no longer required

        //Insert stock quanties
        $query = 'select * from '.TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK.' where products_id="'.$_POST['products_id'].'" AND stock_attributes="' .        (int)$products_attributes_id . '"';

        $attribute_products = $db->Execute($query);
        if($attribute_products->RecordCount() > 0){
          //record exists so update it
          $db->Execute('update ' . TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK . '
                          set quantity = "' .  (int)$_POST['default_stock_quantity'] . '"
                          where products_id = "'.$products_filter .'" AND stock_attributes="' . (int)$products_attributes_id .  '"');
        } else {
           //No record so insert it
           $query = 'insert into `'.TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK.'` (`products_id`,`stock_attributes`,`quantity`) values ('.(int)$products_filter.',"'.(int)$products_attributes_id.'",'.(int)$_POST['default_stock_quantity'].')';
           $db->Execute($query);
        }

      } // end if ($check_duplicate->RecordCount() > 0)
    } // end for ($i=0; $i<sizeof($_POST['values_id']); $i++)

    //Update parents (master) stock quantity)
    $query = 'select sum(quantity) as quantity from '.TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK.' where products_id = "'.(int)$products_filter.'"';
    $quantity = $db->Execute($query);
    $query = 'update '.TABLE_PRODUCTS.' set  products_quantity="'.$quantity->fields['quantity'].'" where products_id="'.(int)$products_id.'"';
    $db->Execute($query);

// ************* End modification for improved attributes controller

        // reset products_price_sorter for searches etc.
        zen_update_products_price_sorter($_POST['products_id']);

        zen_redirect(zen_href_link(FILENAME_QUICK_ATTRIBUTES, $_SESSION['page_info'] . '&pID=' . $_POST['products_id'] . '&cPath=' . $_POST['current_category_id']));
        break;


				case 'delete_option_name_values':
						$delete_attributes_options_id = $db->Execute("select * from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id='" . $_GET['pID'] . "' and options_id='" . $_GET['products_options_id_all'] . "'");
						while (!$delete_attributes_options_id->EOF) {
              $attribute_id = $delete_attributes_options_id->fields['products_attributes_id'];
		          // remove any attached downloads
							$remove_downloads = $db->Execute("delete from " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " where products_attributes_id= '" . $delete_attributes_options_id->fields['products_attributes_id'] . "'");
		          // remove all option values
							$delete_attributes_options_id_values = $db->Execute("delete from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id='" . $_GET['pID'] . "' and options_id='" . $_GET['products_options_id_all'] . "'");

              //Remove stock levels
              $query = 'delete from '.TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK.' where products_id="'.$_GET['pID'].'" and stock_attributes="'.$attribute_id.'" limit 1';
              $db->Execute($query);

              //$messageStack->add_session('Product Variant was deleted', 'failure');

              $delete_attributes_options_id->MoveNext();
						}

            //Update parents (master) stock quantity)
            $query = 'select sum(quantity) as quantity from '.TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK.' where products_id = "'.(int)$_GET['pID'].'"';
            $quantity = $db->Execute($query);
            $query = 'update '.TABLE_PRODUCTS.' set  products_quantity="'.$quantity->fields['quantity'].'" where products_id="'.(int)$_GET['pID'].'"';
            $db->Execute($query);
						$messageStack->add_session(SUCCESS_ATTRIBUTES_DELETED_OPTION_NAME_VALUES. ' ID: ' . $_GET['products_options_id_all'] . ' - '. zen_options_name($_GET['products_options_id_all']), 'success');

						// reset products_price_sorter for searches etc.
						zen_update_products_price_sorter($_GET['pID']);

            $action='';
						zen_redirect(zen_href_link(FILENAME_QUICK_ATTRIBUTES, $_SESSION['page_info'] ));
        break;


        case 'update_attributes_copy_to_category':
          $copy_attributes_delete_first = ($_POST['copy_attributes'] == 'copy_attributes_delete' ? '1' : '0');
          $copy_attributes_duplicates_skipped = ($_POST['copy_attributes'] == 'copy_attributes_ignore' ? '1' : '0');
          $copy_attributes_duplicates_overwrite = ($_POST['copy_attributes'] == 'copy_attributes_update' ? '1' : '0');
          if ($_POST['categories_update_id'] == '') {
            $messageStack->add_session(WARNING_PRODUCT_COPY_TO_CATEGORY_NONE . ' ID#' . $_POST['pID'], 'warning');
          } else {
            $copy_to_category = $db->Execute("select products_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " where categories_id='" . $_POST['categories_update_id'] . "' order by products_id");



            while (!$copy_to_category->EOF) {
              zen_copy_products_attributes($_POST['pID'], $copy_to_category->fields['products_id']);
              //////////////////////////////////////////////////////////////////////////////////////////
                //Get orginal values to copy from
               $quantities_to_copy = $db->Execute('SELECT distinct pa.products_id, pa.options_values_id, pwas.quantity FROM ' . TABLE_PRODUCTS_ATTRIBUTES . ' pa JOIN ' . TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK . ' pwas ON pa.products_attributes_id = pwas.stock_attributes WHERE pa.products_id = "' . $_POST['pID'] . '" order by pa.products_id');
               //@ check $quantities to copy not EOF, if so no atts to copy
               if (!$quantities_to_copy->EOF){
                 if($copy_attributes_delete_first){
                    //Delete all references to this product attribute from the 'products_with_stock' table as delete option is selected by user
                    $query = 'DELETE FROM '.TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK.' where products_id="'.$copy_to_category->fields['products_id']. '"';
                    $db->Execute($query);
                  }

                $xx = $quantities_to_copy->RecordCount();

                //Loop through all orginal values
                while (!$quantities_to_copy->EOF){
                  //Get the products_attributes_id for current pID and each copy options_value
                  $attributes_to_copy = $db->Execute('SELECT products_id, options_values_id, products_attributes_id FROM ' . TABLE_PRODUCTS_ATTRIBUTES . ' WHERE products_id = "' . $copy_to_category->fields['products_id'] . '" AND options_values_id = "'  . $quantities_to_copy->fields['options_values_id'] . '"');

                  //Determine how insert/ update should be done
                  if($copy_attributes_delete_first){
                    $query = 'insert into `'.TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK.'`
                        (`products_id`,`stock_attributes`,`quantity`) values
                        ('. $copy_to_category->fields['products_id'] .',"'. $attributes_to_copy->fields['products_attributes_id'] .'",'. $quantities_to_copy->fields['quantity'] .')';
                     $db->Execute($query);
                     $messageStack->add_session(WARNING_ATTRIBUTES_INSERTED . ' ID:' . $copy_to_category->fields['products_id'], 'success');
                  } else {
                    //Update or Ignore selected
                    //Determine if this is a new attribute
                    $query = 'select * from '.TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK.' where products_id="' . $copy_to_category->fields['products_id'] . '"
                        AND stock_attributes="' . $attributes_to_copy->fields['products_attributes_id'] . '"';
                    $attributes_products_exists = $db->Execute($query);
                    if ($attributes_products_exists->EOF){
                      //New
                      $query = 'insert into `'.TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK.'`
                        (`products_id`,`stock_attributes`,`quantity`) values
                        ('. $copy_to_category->fields['products_id'] .',"'. $attributes_to_copy->fields['products_attributes_id'] .'",'. $quantities_to_copy->fields['quantity'] .')';
                      $db->Execute($query);
                      //$messageStack->add_session(WARNING_ATTRIBUTES_INSERTED . ' ID:' . $copy_to_category->fields['products_id'], 'success');
                    } else {
                      //Update
                      if ($copy_attributes_duplicates_overwrite) {
                        $db->Execute('update ' . TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK . '
                              set quantity = "' .  $quantities_to_copy->fields['quantity'] . '"
                              where products_id = "'. $copy_to_category->fields['products_id'] .'"
                              AND stock_attributes="' .  $attributes_to_copy->fields['products_attributes_id'] .  '"');
                        //$messageStack->add_session(WARNING_ATTRIBUTES_UPDATED . ' ID:' . $copy_to_category->fields['products_id'], 'success');
                      }
                    }
                  }
                  $quantities_to_copy->MoveNext();
                } // eof while $quantities_to_copy
                $messageStack->add_session(WARNING_ATTRIBUTES_INSERTED, 'sucess');
              } else {
                //No Attributes to copy
                $messageStack->add_session(WARNING_NO_COPY_ATTRIBUTES . ' ID:' . $_POST['pID'], 'warning');
              }
              //Update parents (master) stock quantity)
              $query = 'select sum(quantity) as quantity from '.TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK.' where products_id = "'.$copy_to_category->fields['products_id'].'"';
              $quantity = $db->Execute($query);
              $query = 'update '.TABLE_PRODUCTS.' set  products_quantity="'.$quantity->fields['quantity'].'" where products_id="'. $copy_to_category->fields['products_id'] .'"';
              $db->Execute($query);
              $copy_to_category->MoveNext();  //move on to next product
            } //eof while copy_to_category
          }
          //
          $_GET['action']= '';

          zen_redirect(zen_href_link(FILENAME_QUICK_ATTRIBUTES, 'pID=' . $_POST['pID'] . '&cPath=' . $_POST['current_category_id'] . '&attribute_page=' . $_POST['attribute_page'] ));

            break;

	        }     //action switch end
        } //eof zen_not_null
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
<script language="javascript" src="includes/menu.js"></script>
<script language="javascript" src="includes/general.js"></script>
<script language="javascript"><!--
function go_option() {
  if (document.option_order_by.selected.options[document.option_order_by.selected.selectedIndex].value != "none") {
    location = "<?php echo zen_href_link(FILENAME_QUICK_ATTRIBUTES, 'option_page=' . ($_GET['option_page'] ? $_GET['option_page'] : 1)); ?>&option_order_by="+document.option_order_by.selected.options[document.option_order_by.selected.selectedIndex].value;
  }
}
function popupWindow(url) {
  window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=600,height=460,screenX=150,screenY=150,top=150,left=150')
}
//--></script>
<script type="text/javascript">
  <!--
  function init()
  {
    cssjsmenu('navbar');
    if (document.getElementById)
    {
      var kill = document.getElementById('hoverJS');
      kill.disabled = true;
    }
  }
  // -->
</script>
</head>
<!-- <body onload="init()"> -->
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="init()">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->

<table width="75%" border="0" cellspacing="2" cellpadding="4">
  <tr class="dataTableHeadingContent"><?php //Main table bof row 1================================?>
   <td class="pageHeading"><?php
	 			echo K_TEXT_PRODUCTS_LISTING . K_TEXT_PRODUCTS_ID . $products_filter .  K_TEXT_PRODUCT_IN_CATEGORY_NAME . zen_get_category_name(zen_get_products_category_id($products_filter), (int)$_SESSION['languages_id']) ?>
    </td>
  </tr><?php //Main table eof row 1==================================================?>


  <tr><?php //Main table bof row 2 -----------------------------------------------------
  	$per_page = MAX_ROW_LISTS_ATTRIBUTES_CONTROLLER;
  	$attributes = "select pa.* from (" . TABLE_PRODUCTS_ATTRIBUTES . " pa left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on pa.products_id = pd.products_id and pd.language_id = '" . (int)$_SESSION['languages_id'] . "' left join " . TABLE_PRODUCTS_OPTIONS . " po on pa.options_id = po.products_options_id and po.language_id = '" . (int)$_SESSION['languages_id'] . "'" . ") where pa.products_id ='" . $products_filter . "' order by pd.products_name, LPAD(po.products_options_sort_order,11,'0'), LPAD(pa.options_id,11,'0'), LPAD(pa.products_options_sort_order,11,'0')";
			?>

    <td>
    	<table border="0" cellspacing="0" cellpadding="3">
      <?php //BOF Edit form+++++++++++++++++?>
      <form name="attributes" action="<?php echo zen_href_link(FILENAME_QUICK_ATTRIBUTES, 'action=update_product_attribute' . (isset($_GET['option_page']) ? '&option_page=' . $_GET['option_page'] . '&' : '&cPath=' . $current_category_id) . (isset($_GET['value_page']) ? '&value_page=' . $_GET['value_page'] . '&' : '') . (isset($_GET['attribute_page']) ? '&attribute_page=' . $_GET['attribute_page'] : '') . '&pID=' . $products_filter ); ?>" method="post", enctype="multipart/form-data">
    <?php echo zen_draw_hidden_field('products_id', $_GET['pID']); ?>
    <?php echo zen_draw_hidden_field('current_category_id', $_GET['cPath']); ?>
    <?php $next_id = 1;
		$attributes_values = $db->Execute($attributes);
		if ($attributes_values->RecordCount() == 0) { //Show product name or no attributes defined
		?>

    <tr class="attributeBoxContent">
      <td colspan="<?php echo $colspal ?>" class="dataTableHeadingContent">&nbsp;</td>
    </tr>
    <tr class="attributes-even">
      <td colspan="<?php echo $colspal ?>" class="pageHeading" align="center">
        <?php echo ($products_filter == '' ? TEXT_NO_PRODUCTS_SELECTED : TEXT_NO_ATTRIBUTES_DEFINED . $products_filter . ' (' . zen_get_products_model($products_filter) . ') - ' . zen_get_products_name($products_filter)); ?>
      </td>
    </tr>
  	<?php
    } else {
    ?>
    <tr class="attributeBoxContent">
      <td colspan="<?php echo $colspal ?>" class="dataTableHeadingContent">&nbsp;</td>
    </tr>
    <tr class="attributes-even">
      <td colspan="<?php echo $colspal ?>" class="pageHeading" align="center">
        <?php echo TEXT_INFO_ID . $products_filter . ' ' . zen_get_products_model($products_filter) . ' - ' . zen_get_products_name($products_filter); ?>
      </td>
    </tr>
		<?php } ?>


           <tr class="attributeBoxContent">
        <td colspan="<?php echo $colspal ?>" class="dataTableHeadingContent" align="center">
        <?php
 				 //Page count and pages nav links
				$per_page = MAX_ROW_LISTS_ATTRIBUTES_CONTROLLER;
				$attributes = "select pa.* from (" . TABLE_PRODUCTS_ATTRIBUTES . " pa left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on pa.products_id = pd.products_id and pd.language_id = '" . (int)$_SESSION['languages_id'] . "' left join " . TABLE_PRODUCTS_OPTIONS . " po on pa.options_id = po.products_options_id and po.language_id = '" . (int)$_SESSION['languages_id'] . "'" . ") where pa.products_id ='" . $products_filter . "' order by pd.products_name, LPAD(po.products_options_sort_order,11,'0'), LPAD(pa.options_id,11,'0'), LPAD(pa.products_options_sort_order,11,'0')";
				$attribute_query = $db->Execute($attributes);

				$attribute_page_start = ($per_page * $_GET['attribute_page']) - $per_page;
				$num_rows = $attribute_query->RecordCount();

				if ($num_rows <= $per_page) {
					 $num_pages = 1;
				} else if (($num_rows % $per_page) == 0) {
					 $num_pages = ($num_rows / $per_page);
				} else {
					 $num_pages = ($num_rows / $per_page) + 1;
				}
				$num_pages = (int) $num_pages;

			// fix limit error on some versions
					if ($attribute_page_start < 0) { $attribute_page_start = 0; }

				$attributes = $attributes . " LIMIT $attribute_page_start, $per_page";

				// Previous
				if ($prev_attribute_page) {
					echo '<a href="' . zen_href_link(FILENAME_QUICK_ATTRIBUTES, 'attribute_page=' . $prev_attribute_page . '&pID=' . $products_filter) . '"> &lt;&lt; </a> | ';
				}

				for ($i = 1; $i <= $num_pages; $i++) {
					if ($i != $_GET['attribute_page']) {
						echo '<a href="' . zen_href_link(FILENAME_QUICK_ATTRIBUTES, 'attribute_page=' . $i  . '&pID=' . $products_filter) . '">' . $i . '</a> | ';
					} else {
						echo '<b><font color="red">' . $i . '</font></b> | ';
					}
				}

				// Next
				if ($_GET['attribute_page'] != $num_pages) {
					echo '<a href="' . zen_href_link(FILENAME_QUICK_ATTRIBUTES, 'attribute_page=' . $next_attribute_page . '&pID=' . $products_filter) . '"> &gt;&gt; </a>';
				}
			?>
        </td>
      </tr>



      <tr class="dataTableHeadingRow"><?php		// Table headings ?>
				<td class="dataTableHeadingContent" width="12%" align="center">&nbsp;<?php echo TABLE_HEADING_ID; ?>&nbsp;</td>
				<td class="dataTableHeadingContent" align="center">&nbsp;<?php echo TABLE_HEADING_OPT_NAME; ?>&nbsp;</td>
				<td class="dataTableHeadingContent" align="center">&nbsp;<?php echo TABLE_HEADING_OPT_VALUE; ?>&nbsp;</td>
				<td class="dataTableHeadingContent" align="right">&nbsp;<?php echo TABLE_HEADING_OPT_PRICE_PREFIX; ?>&nbsp;<?php echo TABLE_HEADING_OPT_PRICE; ?>&nbsp;</td>
        <td class="dataTableHeadingContent" align="center">&nbsp;<?php echo TABLE_HEADING_QUANTITY; echo(INCLUDE_DEFAULT_IMAGE==TRUE ? '/' . TEXT_INCLUDE_DEFAULT_IMAGE: '');?>&nbsp;</td>
				<td class="dataTableHeadingContent" align="right">&nbsp;<?php echo TABLE_HEADING_OPT_SORT_ORDER; ?>&nbsp;</td>
				<td class="dataTableHeadingContent" align="center"><?php echo LEGEND_BOX; ?></td>
        <td class="dataTableHeadingContent" align="right">&nbsp;<?php echo TABLE_HEADING_PRICE_TOTAL; ?>&nbsp;</td>
        <td class="dataTableHeadingContent" align="center">&nbsp;<?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
      </tr>
      <tr>
        <td colspan="<?php echo $colspal ?>"><?php echo zen_black_line(); ?></td>
      </tr>
			<?php
      $current_options_name = '';
      while (!$attributes_values->EOF) {  //*********** Loop start****************************
        $current_attributes_products_id = $attributes_values->fields['products_id'];
        $current_attributes_options_id = $attributes_values->fields['options_id'];
        $products_name_only = zen_get_products_name($attributes_values->fields['products_id']);
        $options_name = zen_options_name($attributes_values->fields['options_id']);
        $values_name = zen_values_name($attributes_values->fields['options_values_id']);
        $attributes_image_name = $attributes_values->fields['attributes_image'];
        $rows++;

        // delete all option name values
        if ($current_options_name != $options_name) {
          $current_options_name = $options_name;
        ?>
      <tr>
        <td>
          <?php
          if ($action == '') {
            echo '<a href="' . zen_href_link(FILENAME_QUICK_ATTRIBUTES, 'action=delete_option_name_values_confirm&products_options_id_all=' . $current_attributes_options_id . (isset($_GET['option_page']) ? '&option_page=' . $_GET['option_page'] . '&' : '') . (isset($_GET['value_page']) ? '&value_page=' . $_GET['value_page'] . '&' : '') . (isset($_GET['attribute_page']) ? '&attribute_page=' . $_GET['attribute_page'] : '') . '&pID=' . $products_filter . '&cPath=' . $current_category_id ) , '">' .
            zen_image_button('button_delete.gif', IMAGE_DELETE) . '</a>';
          } ?>
        </td>
        <td class="pageHeading"><?php echo $current_options_name; ?><input type="hidden" name="options_id" value="<?php echo $attributes_values->fields['options_id']; ?>"></td>
     </tr>

     <?php //Show confirm delete all option values for this product, the delete at top of all attributes
     if($action == 'delete_option_name_values_confirm' && $current_attributes_options_id == $_GET['products_options_id_all']){?>
      <tr>
        <td colspan="8"><table border="2" cellspacing="2" cellpadding="4">
          <tr>
            <td><table width="600" border="0" cellspacing="2" cellpadding="2">
              <tr class="pageHeading">
                <td class="alert" align="center" colspan="2"><?php echo TEXT_DELETE_ATTRIBUTES_OPTION_NAME_VALUES; ?></td>
              </tr>
              <tr>
             <td class="main" align="left"><?php echo TEXT_INFO_PRODUCT_NAME . zen_get_products_name($products_filter) . '<br />' . TEXT_INFO_PRODUCTS_OPTION_ID . $_GET['products_options_id_all'] . '&nbsp;' . TEXT_INFO_PRODUCTS_OPTION_NAME . '&nbsp;' . zen_options_name($_GET['products_options_id_all']); ?></td>
                <td class="main" align="left"><?php
                 echo '<a href="' . zen_href_link(FILENAME_QUICK_ATTRIBUTES, 'action=delete_option_name_values&products_options_id_all=' . $current_attributes_options_id . (isset($_GET['option_page']) ? '&option_page=' . $_GET['option_page'] . '&' : '') . (isset($_GET['value_page']) ? '&value_page=' . $_GET['value_page'] . '&' : '') . (isset($_GET['attribute_page']) ? '&attribute_page=' . $_GET['attribute_page'] : '') . '&pID=' . $products_filter . '&cPath=' . $current_category_id ) , '">' .
                  zen_image_button('button_delete.gif', IMAGE_DELETE) . '</a>'
                  . '&nbsp;&nbsp;' . '<a href="' . zen_href_link(FILENAME_QUICK_ATTRIBUTES, $_SESSION['page_info']) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>'; ?></td>
              </tr>
            </table></td>
          </table></td>
        </tr>
      </tr>
     <?php } //EOF Show confirm delete all option values for this product
     ?>

      <?php } // option name delete
			// attributes display listing
			// calculate current total attribute price
			$attributes_price_final = zen_get_attributes_price_final($attributes_values->fields["products_attributes_id"], 1, $attributes_values, 'false');
			$attributes_price_final_value = $attributes_price_final;
			$attributes_price_final = $currencies->display_price($attributes_price_final, zen_get_tax_rate(1), 1);
			$attributes_price_final_onetime = zen_get_attributes_price_final_onetime($attributes_values->fields["products_attributes_id"], 1, $attributes_values);
			$attributes_price_final_onetime = $currencies->display_price($attributes_price_final_onetime, zen_get_tax_rate(1), 1);

      //#########################################################################################################################################
      if (($action == 'update_attribute') && ($_GET['attribute_id'] == $attributes_values->fields['products_attributes_id'])) {


      //#########################################################################################################################################
      } elseif (($action == 'delete_product_attribute') && ($_GET['attribute_id'] == $attributes_values->fields['products_attributes_id'])) {
        ?>
        <tr>
          <td colspan=" <?php echo $colspal; ?> >"><?php echo zen_black_line(); ?></td>
        </tr>
        <tr class="attributeBoxContent">
          <td align="left" colspan="6" class="pageHeading"><?php echo PRODUCTS_ATTRIBUTES_DELETE; ?></td><td colspan="3" align="center" class="pageHeading"><?php echo PRODUCTS_ATTRIBUTES_DELETE; ?></td>
        </tr>
        <tr class="attributeBoxContent">
          <td colspan="<?php echo $colspal; ?>" align="center" class="pageHeading"><strong><?php echo $products_name_only; ?></strong>
          </td>
        </tr>
        <tr>
          <td class="attributeBoxContent">&nbsp;<b> <?php echo $attributes_values->fields["products_attributes_id"]; ?> </b>&nbsp;</td>
          <td class="attributeBoxContent">&nbsp;<b> <?php echo $options_name; ?> </b>&nbsp;</td>
          <td class="attributeBoxContent">&nbsp;<b> <?php echo $values_name; ?> </b>&nbsp;</td>
          <td align="right" class="attributeBoxContent">&nbsp;<b><?php echo $attributes_values->fields["price_prefix"]; ?>&nbsp;£<?php echo $attributes_values->fields["options_values_price"]; ?></b>&nbsp;</td>
          <td align="center" class="attributeBoxContent">&nbsp;<b>&nbsp;</b>&nbsp;</td>
          <td align="center" class="attributeBoxContent">&nbsp;<b><?php echo $attributes_values->fields["products_options_sort_order"]; ?></b>&nbsp;</td>
          <td colspan="3" align="center" class="attributeBoxContent">&nbsp;<b><?php echo '<a href="' . zen_href_link(FILENAME_QUICK_ATTRIBUTES, 'action=delete_attribute&attribute_id=' . $_GET['attribute_id'] . (isset($_GET['option_page']) ? '&option_page=' . $_GET['option_page'] . '&' : '') . (isset($_GET['value_page']) ? '&value_page=' . $_GET['value_page'] . '&' : '') . (isset($_GET['attribute_page']) ? '&attribute_page=' . $_GET['attribute_page'] : '') . '&pID=' . $products_filter . '&cPath=' . $current_category_id) . '">'; ?><?php echo zen_image_button('button_confirm.gif', IMAGE_CONFIRM); ?></a>&nbsp;&nbsp;<?php echo '<a href="' . zen_href_link(FILENAME_QUICK_ATTRIBUTES, (isset($_GET['option_page']) ? '&option_page=' . $_GET['option_page'] . '&' : '') . (isset($_GET['value_page']) ? '&value_page=' . $_GET['value_page'] . '&' : '') . (isset($_GET['attribute_page']) ? '&attribute_page=' . $_GET['attribute_page'] : '') . '&pID=' . $products_filter . '&cPath=' . $current_category_id ) . '">'; ?><?php echo zen_image_button('button_cancel.gif', IMAGE_CANCEL); ?></a>&nbsp;</b></td>


        </tr>
        <tr class="attributeBoxContent">
          <td colspan="10" class="attributeBoxContent">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="10"><?php echo zen_black_line(); ?></td>
        </tr>
        <tr>

      <?php
      //#########################################################################################################################################
      } else {  //~~~~~~~~~~~~~~~~~~~ Display current attribs in the non-editing format
        //Get stock values from db
      $query = 'select * from '.TABLE_PRODUCTS_WITH_ATTRIBUTES_STOCK.' where products_id="'.$products_filter.'" AND stock_attributes="' . $attributes_values->fields["products_attributes_id"] . '"';

      $attribute_products = $db->Execute($query);
      if($attribute_products->RecordCount() > 0){
        $att_stock_qty = $attribute_products->fields['quantity'];
      } else {
         $att_stock_qty = 0;
      }

			if($action == ''){
				$inputs_readonly = '';
			} else {
				$inputs_readonly = 'readonly';
			}
			?>

			<tr class="<?php echo (floor($rows/2) == ($rows/2) ? 'attributes-even' : 'attributes-odd'); ?>">

									<td class="smallText" align="center">&nbsp;<?php echo $attributes_values->fields["products_attributes_id"]; ?>
                    <?php echo zen_draw_hidden_field('values_id_' . $rows,$attributes_values->fields["options_values_id"]);?>&nbsp;</td>

									<td class="smallText" align="center">&nbsp;<?php echo $options_name; ?>&nbsp;
                   <?php echo zen_draw_hidden_field('attribute_id_' . $rows,$attributes_values->fields['products_attributes_id']);?></td>

									<td class="smallText" align="center">&nbsp;<?php echo ($attributes_values->fields['attributes_image'] != '' ? zen_image(DIR_WS_IMAGES . 'icon_status_yellow.gif') . '&nbsp;' : '&nbsp;&nbsp;') . $values_name; ?>&nbsp;</td>

									<td align="right" class="smallText" align="center">&nbsp;<?php echo $attributes_values->fields["price_prefix"]; ?>&nbsp;
                    <input name="value_price_<?php echo $rows?>" type="text" size="8" autocomplete=OFF <?php echo $inputs_readonly; ?> value="<?php echo $attributes_values->fields["options_values_price"]; ?>" size="8" tabindex="<?php echo $rows ?>">&nbsp;

                  <td align="right" class="smallText" align="center">&nbsp;
                    <input name="stock_quantity_<?php echo $rows?>" type="text" size="3" autocomplete=OFF <?php echo $inputs_readonly ?> value="<?php echo $att_stock_qty; ?>" size="8" tabindex="<?php echo $rows ?>">
                    <?php if(INCLUDE_DEFAULT_IMAGE){ ?>
                       <input name="include_default_image_<?php echo $rows?>" type="checkbox" value="1" <?php echo($attributes_image_name != '' ? 'checked' : '')?>></td>
                    <?php } ?>
                    &nbsp;</td>
									<td align="right" class="smallText" align="center">&nbsp;<?php echo $attributes_values->fields["products_options_sort_order"]; ?>
                  <?php echo zen_draw_hidden_field('products_options_sort_order_' . $rows,$attributes_values->fields['products_options_sort_order']);?>&nbsp;</td>

			<?php
			if ($action == '') {  //Image buttons//////////////////
			?>
			<td>
				<table border="0" align="center" cellpadding="2" cellspacing="2">
				<tr>
				<td class="smallText" align="center">
        <?php echo ($attributes_values->fields["attributes_default"] == '0' ? '<a href="' .
        zen_href_link(FILENAME_QUICK_ATTRIBUTES, 'action=set_flag_attributes_default' . '&cPath=' . $current_category_id . '&attributes_id=' .$attributes_values->fields["products_attributes_id"] . (isset($_GET['option_page']) ? '&option_page=' . $_GET['option_page'] . '&' : '') . (isset($_GET['value_page']) ? '&value_page=' . $_GET['value_page'] . '&' : '') . (isset($_GET['attribute_page']) ? '&attribute_page=' . $_GET['attribute_page'] : '') . '&pID=' . $products_filter) . '">' . zen_image(DIR_WS_IMAGES . 'icon_orange_off.gif', LEGEND_ATTRIBUTES_DEFAULT) . '</a>' : '<a href="' . zen_href_link(FILENAME_QUICK_ATTRIBUTES, 'action=set_flag_attributes_default' . '&cPath=' . $current_category_id . '&attributes_id=' . $attributes_values->fields["products_attributes_id"] . (isset($_GET['option_page']) ? '&option_page=' . $_GET['option_page'] . '&' : '') . (isset($_GET['value_page']) ? '&value_page=' . $_GET['value_page'] . '&' : '') . (isset($_GET['attribute_page']) ? '&attribute_page=' . $_GET['attribute_page'] : '') . '&pID=' . $products_filter) . '">' . zen_image(DIR_WS_IMAGES . 'icon_orange_on.gif', LEGEND_ATTRIBUTES_DEFAULT)) . '</a>' ?>
        </td>
				</tr>
				</table>
			</td>

			<?php
				$new_attributes_price= '';
				if ($attributes_values->fields["attributes_discounted"]) {
					$new_attributes_price = zen_get_attributes_price_final($attributes_values->fields["products_attributes_id"], 1, '', 'false');
					$new_attributes_price = zen_get_discount_calc($products_filter, true, $new_attributes_price);
					if ($new_attributes_price != $attributes_price_final_value) {
						$new_attributes_price = '|' . $currencies->display_price($new_attributes_price, zen_get_tax_rate(1), 1);
					} else {
						$new_attributes_price = '';
					}
				}
      } //end Image Buttons
    ?>

    <td align="right" class="smallText" align="center">
      <?php echo $attributes_price_final . $new_attributes_price . ' '  . $attributes_price_final_onetime; ?>
    </td>
    <?php
      if ($action != '') { //decide if to show buttons      ?>
        <td align="center" class="smallText">&nbsp;</td>
    <?php
      } else { ?>
        <td align="center" class="smallText" align="center">
        <?php echo '<a href="' . zen_href_link(FILENAME_QUICK_ATTRIBUTES, 'action=delete_product_attribute&attribute_id=' . $attributes_values->fields['products_attributes_id'] . (isset($_GET['option_page']) ? '&option_page=' . $_GET['option_page'] . '&' : '') . (isset($_GET['value_page']) ? '&value_page=' . $_GET['value_page'] . '&' : '') . (isset($_GET['attribute_page']) ? '&attribute_page=' . $_GET['attribute_page'] : '') . '&pID=' . $products_filter . '&cPath=' . $cPath ) , '">'; ?><?php echo zen_image_button('button_delete.gif', IMAGE_DELETE); ?></a>&nbsp;
        </td>
    <?php }

   } // End of action =

		$max_attributes_id_values = $db->Execute("select max(products_attributes_id) + 1 as next_id from " . TABLE_PRODUCTS_ATTRIBUTES);
    $next_id = $max_attributes_id_values->fields['next_id'];

  //////////////////////////////////////////////////////////////
// BOF: Add dividers between Product Names and between Option Names
    $attributes_values->MoveNext();
    if (!$attributes_values->EOF) {
      if ($current_attributes_products_id != $attributes_values->fields['products_id']) {
?>
          <tr class="<?php echo (floor($rows/2) == ($rows/2) ? 'attributes-even' : 'attributes-odd'); ?>">
            <td colspan="<?php echo $colspal ?>">
              <table width="100%"><td>
              <tr>
                <td><?php echo zen_draw_separator('pixel_black.gif', '100%', '3'); ?></td>
              </tr>
              </table>
            </td>
          </tr>
<?php
        } else {
        if ($current_attributes_options_id != $attributes_values->fields['options_id']) {  ?>
          <tr class="<?php echo (floor($rows/2) == ($rows/2) ? 'attributes-even' : 'attributes-odd'); ?>">
            <td colspan="<?php echo $colspal ?>">
              <table width="100%"><td>
              <tr>
                <td><?php echo zen_draw_separator('pixel_black.gif', '100%', '1'); ?></td>
              </tr>
              </table>
            </td>
          </tr>
<?php
          }
        }
     }

// EOF: Add dividers between Product Names and between Option Names
//////////////////////////////////////////////////////////////
    } //end of while
    ?>

    <?php if($rows > 0 ){
			if($action==''){?>
        <tr> <td colspan="3">&nbsp;</td>
           <td><input name="att_count" type="hidden" value="<?php echo $rows; ?>" size="8">
           <?php
            echo zen_image_submit('button_update.gif', IMAGE_UPDATE, 'name="dos" value="SaC"') . '&nbsp;&nbsp;&nbsp;';
            echo zen_image_submit('button_update_return.jpg', 'Update and return', 'name="dos" value="SaR"'); ?>
           </td>
           <td colspan="2">&nbsp;</td>
           <td colspan="2">
           <table border="1" cellpadding="2">
           	<tr>
            	<td align="center" width="140px" bgcolor="#CCCCCC">
      <?php echo '<a href="' . zen_href_link(FILENAME_CATEGORIES, 'cPath=' . $current_category_id  . '&page=categories') . '">'; ?><?php echo zen_image_button('button_return.jpg', 'Return to Categories'); ?></a>&nbsp;
      				</td>
             </tr>
            </table>
           </td>
        <?php } else {?>
            <td colspan="3">&nbsp;</td>
        <?php }?>
        </tr>
      <?php } else { ?>
        <tr class="attributeBoxContent">
        <td colspan="3" class="pageHeading" align="center">
					<?php
          echo TEXT_INFO_NO_ATTRIBUTES; ?>
       </td>
			 <td colspan="2">&nbsp;</td>
           <td colspan="2">
           <table border="1" cellpadding="2">
           	<tr>
            	<td align="center" width="140px" bgcolor="#CCCCCC">
      <?php echo '<a href="' . zen_href_link(FILENAME_CATEGORIES, 'cPath=' . $current_category_id  . '&page=categories') . '">'; ?><?php echo zen_image_button('button_return.jpg', 'Return to Categories'); ?></a>&nbsp;
      				</td>
             </tr>
            </table>
           </td>
    	<?php }?>


    </tr>
    </form><?php //EOF Edit form++++++++++++++++?>
     </table>

     </td>
  </tr><?php //Main table eof row 2-------------------------------------------------------------?>

  <tr><?php //Main table bof row 3^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^?>
  <table border="0" cellspacing="2" cellpadding="2">
    <tr>
    	<td><?php /////////////// BOF Insert new////////////////////////////////////////////////////////////////

			$max_attributes_id_values = $db->Execute("select max(products_attributes_id) + 1 as next_id from " . TABLE_PRODUCTS_ATTRIBUTES);
			$next_id = $max_attributes_id_values->fields['next_id'];
			?>

      <form name="addnew" action="<?php echo zen_href_link(FILENAME_QUICK_ATTRIBUTES, 'action=add_product_attributes' . (isset($_GET['option_page']) ? '&option_page=' . $_GET['option_page'] . '&' : '') . (isset($_GET['value_page']) ? '&value_page=' . $_GET['value_page'] . '&' : '') . (isset($_GET['attribute_page']) ? '&attribute_page=' . $_GET['attribute_page'] : '') . '&pID=' . $products_filter ); ?>" method="post", enctype="multipart/form-data">

      <table border="0" cellspacing="0" cellpadding="2">
       <tr class="attributeBoxContent">
        <td class="main">&nbsp;<?php echo $next_id; ?></td>
        <td class="pageHeading">
          <?php echo PRODUCTS_ATTRIBUTES_ADDING;//TEXT_ATTRIBUTES_INSERT_INFO; ?></td>
        <td align="center" class="attributeBoxContent" height="30" valign="bottom">&nbsp;
         <input type="hidden" name="products_id" value="<?php echo $products_filter; ?>">
         <input type="hidden" name="current_category_id" value="<?php echo $current_category_id; ?>">
         <input type="hidden" name="attribute_page" value="<?php echo $_GET['attribute_page']; ?>">
        </td>
       </tr>

       <tr>
        <td class="attributeBoxContent">&nbsp;
					<?php echo K_TABLE_HEADING_OPT_NAME . '<br />'; ?>
          &nbsp;&nbsp;<select name="options_id" id="Option Name" onChange="TCN_reload(this)" size="<?php echo ($action != 'delete_attribute' ? "15" : "1"); ?>" >
          <option selected>Option Name</option>
          </select>&nbsp;
        </td>
        <td class="attributeBoxContent">&nbsp;
					<?php echo K_TABLE_HEADING_OPT_VALUE . '<br />'; ?><select name="values_id[]" id="Option Value" onChange="TCN_reload(this)" multiple size="<?php echo ($action != 'delete_attribute' ? "15" : "1"); ?>" >
          <option selected>Option Value</option>
          </select>&nbsp;
         </td>
				 <td class="attributeBoxContent">
         Stock Level <input name="default_stock_quantity" type="text" value="1" size="2"><br>
        <br>
        <?php if (INCLUDE_DEFAULT_IMAGE==TRUE){?>
            Add default image <input name="add_default_image" type="checkbox" value="TRUE" <?php echo(INCLUDE_DEFAULT_IMAGE_DEFAULT ? 'checked' : '')?> ><br />
        <?php } ?>
        <br>
         	<?php
					if ($action == '') {
						echo zen_image_submit('button_insert.gif', IMAGE_INSERT) . '&nbsp;&nbsp;&nbsp;';
					} else {
						// hide button
						echo '&nbsp;';
					}
      	 ?>
				 </td>
      </tr>
      </table>
      </form>
      </td><?php /////////////// EOF Insert new?>

 <script language="JavaScript">
TCN_contents=new Array();
TCN_tempArray=new Array();
TCN_counter=0;
function TCN_addContent(str){
	TCN_contents[TCN_counter]=str;
	TCN_counter++;
}
function TCN_split(){
	TCN_arrayValues = new Array();
	for(i=0;i<TCN_contents.length;i++){
		TCN_arrayValues[i]=TCN_contents[i].split(separator);
		TCN_tempArray[0]=TCN_arrayValues;
	}
}
function TCN_makeSelValueGroup(){
	TCN_selValueGroup=new Array();
	var args=TCN_makeSelValueGroup.arguments;
	for(i=0;i<args.length;i++){
		TCN_selValueGroup[i]=args[i];
		TCN_tempArray[i]=new Array();
	}
}
function TCN_makeComboGroup(){
	TCN_comboGroup=new Array();
	var args=TCN_makeComboGroup.arguments;
	for(i=0;i<args.length;i++) TCN_comboGroup[i]=findObj(args[i]);
}
function TCN_setDefault(){
	for (i=TCN_selValueGroup.length-1;i>=0;i--){
		if(TCN_selValueGroup[i]!=""){
			for(j=0;j<TCN_contents.length;j++){
				if(TCN_arrayValues[j][(i*2)+1]==TCN_selValueGroup[i]){
					for(k=i;k>=0;k--){
						if(TCN_selValueGroup[k]=="") TCN_selValueGroup[k]=TCN_arrayValues[j][(k*2)+1];
					}
				}
			}
		}
	}
}
function TCN_loadMenu(daIndex){
	var selectionMade=false;
	daArray=TCN_tempArray[daIndex];
	TCN_comboGroup[daIndex].options.length=0;
	for(i=0;i<daArray.length;i++){
		existe=false;
		for(j=0;j<TCN_comboGroup[daIndex].options.length;j++){
			if(daArray[i][(daIndex*2)+1]==TCN_comboGroup[daIndex].options[j].value) existe=true;
		}
		if(existe==false){
			lastValue=TCN_comboGroup[daIndex].options.length;
			TCN_comboGroup[daIndex].options[TCN_comboGroup[daIndex].options.length]=new Option(daArray[i][daIndex*2],daArray[i][(daIndex*2)+1]);
			if(TCN_selValueGroup[daIndex]==TCN_comboGroup[daIndex].options[lastValue].value){
				TCN_comboGroup[daIndex].options[lastValue].selected=true;
				selectionMade=true;
			}
		}
	}
	if(selectionMade==false) TCN_comboGroup[daIndex].options[0].selected=true;
}
function TCN_reload(from){
	if(!from){
		TCN_split();
		TCN_setDefault();
		TCN_loadMenu(0);
		TCN_reload(TCN_comboGroup[0]);
	}else{
		for(j=0; j<TCN_comboGroup.length; j++){
			if(TCN_comboGroup[j]==from) index=j+1;
		}
		if(index<TCN_comboGroup.length){
			TCN_tempArray[index].length=0;
			for(i=0;i<TCN_comboGroup[index-1].options.length;i++){
				if(TCN_comboGroup[index-1].options[i].selected==true){
					for(j=0;j<TCN_tempArray[index-1].length;j++){
						if(TCN_comboGroup[index-1].options[i].value==TCN_tempArray[index-1][j][(index*2)-1]) TCN_tempArray[index][TCN_tempArray[index].length]=TCN_tempArray[index-1][j];
					}
				}
			}
		TCN_loadMenu(index);
		TCN_reload(TCN_comboGroup[index]);
		TCN_sort(TCN_comboGroup[index-1]);
		}
	}
}

function TCN_sort(lb){
	arrTexts = new Array();
	arrValues = new Array();
	arrOldTexts = new Array();

	for(i=0; i<lb.length; i++)
	{
		arrTexts[i] = lb.options[i].text;
		arrValues[i] = lb.options[i].value;
		arrOldTexts[i] = lb.options[i].text;
	}

	arrTexts.sort();

	for(i=0; i<lb.length; i++)
	{
		lb.options[i].text = arrTexts[i];
		for(j=0; j<lb.length; j++)
		{
			if (arrTexts[i] == arrOldTexts[j])
			{
				lb.options[i].value = arrValues[j];
				j = lb.length;
			}
		}
	}
}

function findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}
TCN_makeSelValueGroup("","");
TCN_makeComboGroup("Option Name","Option Value");
 var separator="+#+";
<?php
$attributes_sql = "SELECT povpo.products_options_id, povpo.products_options_values_id, po.products_options_name, po.products_options_sort_order,
                   pov.products_options_values_name, pov.products_options_values_sort_order
				   FROM " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " povpo, " . TABLE_PRODUCTS_OPTIONS . " po, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov
				   WHERE povpo.products_options_id = po.products_options_id
				   AND povpo.products_options_values_id = pov.products_options_values_id
				   AND pov.language_id = po.language_id
				   AND po.language_id = " . $_SESSION['languages_id'] . "
				   ORDER BY po.products_options_name, pov.products_options_values_sort_order";

$attributes = $db->Execute($attributes_sql);
while(!$attributes->EOF) {
$products_options_values_name = str_replace('-', '\-', $attributes->fields['products_options_values_name']);
$products_options_values_name = str_replace('(', '\(', $products_options_values_name);
$products_options_values_name = str_replace(')', '\)', $products_options_values_name);
$products_options_values_name = str_replace('"', '\"', $products_options_values_name);
$products_options_values_name = str_replace('&quot;', '\"', $products_options_values_name);
$products_options_values_name = str_replace('&frac12;', '1/2', $products_options_values_name);
?>
TCN_addContent("<?php echo $attributes->fields['products_options_name']; ?>+#+<?php echo $attributes->fields['products_options_id']; ?>+#+<?php echo $products_options_values_name; ?>+#+<?php echo $attributes->fields['products_options_values_id']; ?>+#+<?php echo $attributes->fields['products_options_values_sort_order'] ?> ");
<?php
$attributes->MoveNext();
}
?>
TCN_reload();

</script>
<!-- end modification for improved attributes controller -->

<?php
$chk_defaults = $db->Execute("select products_type from " . TABLE_PRODUCTS . " where products_id=" . $products_filter);
// set defaults for adding attributes

$on_product_attribute_is_free = (zen_get_show_product_switch($products_filter, 'ATTRIBUTE_IS_FREE', 'DEFAULT_', '') == 1 ? true : false);
$off_product_attribute_is_free = ($on_product_attribute_is_free == 1 ? false : true);
$on_attributes_display_only = (zen_get_show_product_switch($products_filter, 'ATTRIBUTES_DISPLAY_ONLY', 'DEFAULT_', '') == 1 ? true : false);
$off_attributes_display_only = ($on_attributes_display_only == 1 ? false : true);
$on_attributes_default = (zen_get_show_product_switch($products_filter, 'ATTRIBUTES_DEFAULT', 'DEFAULT_', '') == 1 ? true : false);
$off_attributes_default = ($on_attributes_default == 1 ? false : true);
$on_attributes_discounted = (zen_get_show_product_switch($products_filter, 'ATTRIBUTES_DISCOUNTED', 'DEFAULT_', '') == 1 ? true : false);
$off_attributes_discounted = ($on_attributes_discounted == 1 ? false : true);
$on_attributes_price_base_included = (zen_get_show_product_switch($products_filter, 'ATTRIBUTES_PRICE_BASE_INCLUDED', 'DEFAULT_', '') == 1 ? true : false);
$off_attributes_price_base_included = ($on_attributes_price_base_included == 1 ? false : true);
$on_attributes_required = (zen_get_show_product_switch($products_filter, 'ATTRIBUTES_REQUIRED', 'DEFAULT_', '') == 1 ? true : false);
$off_attributes_required = ($on_attributes_required == 1 ? false : true);

$default_price_prefix = zen_get_show_product_switch($products_filter, 'PRICE_PREFIX', 'DEFAULT_', '');
$default_price_prefix = ($default_price_prefix == 1 ? '+' : ($default_price_prefix == 2 ? '-' : ''));
$default_products_attributes_weight_prefix  = zen_get_show_product_switch($products_filter, 'PRODUCTS_ATTRIBUTES_WEIGHT_PREFIX', 'DEFAULT_', '');
$default_products_attributes_weight_prefix  = ($default_products_attributes_weight_prefix  == 1 ? '+' : ($default_products_attributes_weight_prefix == 2 ? '-' : ''));

// set defaults for copying
$on_overwrite = true;
$off_overwrite = false;
?>




      <td>&nbsp;&nbsp;</td><?php //Spacer column?>
      <?php /////////////// BOF Copy   ?>
      <td>
			<?php if ($action == '' && $rows > 0){?>
        <table border="0" cellspacing="0" cellpadding="2">
         <tr class="attributeBoxContent">
						<td class="pageHeading" align="center"><?php echo TEXT_COPY_ATTRIBUTES;?>
            </td>
          </tr>
         <tr class="attributeBoxContent">
         <form name="product_copy_to_category"<?php echo 'action="' . zen_href_link(FILENAME_QUICK_ATTRIBUTES, 'action=update_attributes_copy_to_category') . '"'; ?> method="post"><?php echo zen_draw_hidden_field('pID', $_GET['pID']) . zen_draw_hidden_field('products_id', $_GET['pID']) . zen_draw_hidden_field('products_update_id', $_GET['products_update_id']) . zen_draw_hidden_field('copy_attributes', $_GET['copy_attributes']) . zen_draw_hidden_field('current_category_id', $_GET['cPath']) . zen_draw_hidden_field('categories_update_id',$_GET['cPath'])  . zen_draw_hidden_field('attribute_page',$_GET['attribute_page']); ?>
          <td>
          <table border="0" cellspacing="2" cellpadding="4">
            <tr>
              <td colspan="2" class="main" align="center" style="font-size:12px">
								<?php echo TEXT_INFO_ATTRIBUTES_FEATURES_COPY_TO_CATEGORY . zen_get_products_category_id($products_filter) . ' - ' . zen_get_category_name(zen_get_products_category_id($products_filter), (int)$_SESSION['languages_id']) . '</br>';
                echo TEXT_INFO_ATTRIBUTES_FEATURES_COPY_PRODUCT . $products_filter . ' - '.  zen_get_products_name($products_filter); ?>
              </td>
						</tr>
            <tr>
            	<td class="main" align="left"><?php echo TEXT_COPY_ATTRIBUTES_CONDITIONS . '<br />' . zen_draw_radio_field('copy_attributes', 'copy_attributes_delete', true) . ' ' . TEXT_COPY_ATTRIBUTES_DELETE . '<br />' . zen_draw_radio_field('copy_attributes', 'copy_attributes_update') . ' ' . TEXT_COPY_ATTRIBUTES_UPDATE . '<br />' . zen_draw_radio_field('copy_attributes', 'copy_attributes_ignore') . ' ' . TEXT_COPY_ATTRIBUTES_IGNORE; ?>
              </td>
             </tr>
             <tr>
              <td class="main" align="center"><?php echo zen_image_submit('button_copy.gif', IMAGE_COPY) . '&nbsp;'; ?></td>
            </tr>
          </table></td>
      </form></tr>
			<tr><td>&nbsp;</td></tr>
      <tr><td>&nbsp;</td></tr>
      <tr><td>&nbsp;</td></tr>


        </table>
      <?php } else { ?>
				&nbsp;
      <?php } ?>
      </td>
       <?php /////////////// EOF Copy?>




    </tr>
  </table>
  </tr><?php //Main table eof row 3 ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^?>
</table><?php //Main table END  ?>



</body>
</html>