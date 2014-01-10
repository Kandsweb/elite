<?php
/**
 * Cross Sell products
 *
 * Derived from:
 * Original Idea From Isaac Mualem im@imwebdesigning.com <mailto:im@imwebdesigning.com>
 * Portions Copyright (c) 2002 osCommerce
 * Complete Recoding From Stephen Walker admin@snjcomputers.com
 * license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 *
 * Adapted to Zen Cart by Merlin - Spring 2005
 * Reworked for Zen Cart v1.3.0  03-30-2006
 *
 * Reworked again to change/add more features by yellow1912
 * Pay me a visit at RubikIntegration.com
 *
 * Add Multi Cross Sells by Gilby 2010-06-26
 */

class request_restock {

  function db_result_to_string($glue, $db_result){
  $temp_array = array();
	if($this->is_obj($db_result,'queryFactoryResult')){
		// We need to clone, because we don't want to touch the real object
		while(!$db_result->EOF){
			$temp_array[] = $db_result->fields['products_'.MXSELL_FORM_INPUT_TYPE];
			$db_result->MoveNext();
		}
		$db_result->Move(0);
		$db_result->MoveNext();
	  }
	return implode($glue,$temp_array);
  }

	// http://us3.php.net/manual/en/function.is-object.php#66370
	function is_obj( &$object, $check=null, $strict=true ){
		if (is_object($object)) {
	   	if ($check == null) {
	     	return true;
	   	} else {
	     	$object_name = get_class($object);
	     	return ($strict === true)?( $object_name == $check ):( strtolower($object_name) == strtolower($check) );
	   	}
	 	} else {
	 	return false;
    }
	}

	function add_new_cross_products($product_string, $method, $main_id){
		global $messageStack;
		// clean up the array
		$product_array = array_filter(explode(trim(MXSELL_PRODUCT_INPUT_SEPARATOR), strtoupper($product_string)));
		$product_array = array_unique($product_array);

		// re-index it
		$product_array = array_values($product_array);
		// sanitize for database query
		$product_array = zen_db_prepare_input($product_array);

		if ($method == 1)
			if(count($product_array)>0){
				$main_id = zen_db_prepare_input(strtoupper($main_id));
				if(empty($main_id))
					$messageStack->add(CROSS_SELL_NO_MAIN_FOUND, 'error');
				else
					foreach ($product_array as $id => $pid)
						$this->add_new_cross_product($main_id, $pid);
			}
			else
				$messageStack->add(sprintf(CROSS_SELL_NO_INPUT_FOUND,1), 'warning');
		else
			if (count($product_array)>1){
				foreach ($product_array as $id => $pid)
					foreach ($product_array as $id2 => $pid2)
						if ($pid2 != $pid)
							$this->add_new_cross_product($pid, $pid2);
				}
			else
				// Add error msg to stack
				$messageStack->add(sprintf(CROSS_SELL_NO_INPUT_FOUND,2), 'warning');
	}

	function add_new_cross_product($products_id, $pid) {
	  global $db, $messageStack;
		// Make sure the 2 products exist
		if (MXSELL_FORM_INPUT_TYPE == "model"){
			// For some reason the union query does not work in mysql 4.1 so we have to select 1 by 1
			$first_cross_product = $db->Execute("SELECT products_id FROM " . TABLE_PRODUCTS . " WHERE products_model = '$products_id' LIMIT 1");
			$second_cross_product = $db->Execute("SELECT products_id FROM ". TABLE_PRODUCTS . " WHERE products_model = '$pid' LIMIT 1");
			}
		else{
			$first_cross_product = $db->Execute("SELECT products_id FROM " . TABLE_PRODUCTS . " WHERE products_id = '$products_id' LIMIT 1");
			$second_cross_product = $db->Execute("SELECT products_id FROM ". TABLE_PRODUCTS . " WHERE products_id = '$pid' LIMIT 1");
			}
			// We should get back 2 products_id
		if ($first_cross_product->RecordCount() != 1)
			$messageStack->add(sprintf(CROSS_SELL_PRODUCT_NOT_FOUND, $products_id), 'error');
		elseif ($second_cross_product->RecordCount() != 1)
			$messageStack->add(sprintf(CROSS_SELL_PRODUCT_NOT_FOUND, $pid), 'error');
		else{
			$first_record = $first_cross_product->fields['products_id'];
			$second_record = $second_cross_product->fields['products_id'];
			if($first_record == $second_record)
				$messageStack->add(sprintf(CROSS_SELL_PRODUCT_DUPLICATE, $pid, $products_id), 'error');
			else{
				$check_xsell = $db->Execute("select count(products_id) as records from " . TABLE_PRODUCTS_MXSELL . MXSELL_SELECTED_TABLE . " where products_id = '" . $first_record . "' and xsell_id = '" . $second_record . "'");
				if ($check_xsell->fields['records'] > 0) {
					$messageStack->add(sprintf(CROSS_SELL_ALREADY_ADDED, $pid, $products_id), 'error');
				}
				else {
					$insert_array = array('products_id'	=>	$first_record,
										'xsell_id'		=>	$second_record,
										'sort_order'	=>	'1'
										);
					zen_db_perform(TABLE_PRODUCTS_MXSELL . MXSELL_SELECTED_TABLE, $insert_array);
					$messageStack->add(sprintf(CROSS_SELL_ADDED, $pid, $products_id), 'success');
				}
			}
		}
	}

	function update_cross_product($xsell_array){
		global $db, $messageStack;
		// clean it
		$xsell_array = zen_db_prepare_input($xsell_array);
		// Take care of the sort thing first, shall we?
		$sorted_product_array = array();
		$deleted_product_array = array();
		foreach ($xsell_array as $xsell){
			if((int)$xsell['delete'] == 1)
				$deleted_product_array[] = $xsell['id'];
			else
				if($xsell['old_sort_order'] != $xsell['new_sort_order'] && $xsell['new_sort_order'] >= 0){
					$db->Execute('UPDATE '.TABLE_PRODUCTS_MXSELL . MXSELL_SELECTED_TABLE . ' SET sort_order = ' . $xsell['new_sort_order'] . ' WHERE ID = ' . $xsell['id'] . ' LIMIT 1');
					if (MXSELL_FORM_INPUT_TYPE == "model")
						$sorted_product_array[] =  $xsell['product_model'];
					else
						$sorted_product_array[] =  $xsell['product_id'];
				}
		}
		if(count($sorted_product_array) > 0)
			$messageStack->add(sprintf(CROSS_SELL_SORT_ORDER_UPDATED, implode(',',$sorted_product_array)), 'success');
		else
			$messageStack->add(CROSS_SELL_SORT_ORDER_NOT_UPDATED, 'warning');

		if(count($deleted_product_array) > 0){
			$db->Execute('DELETE FROM '.TABLE_PRODUCTS_MXSELL  . MXSELL_SELECTED_TABLE . ' WHERE ID IN ('.implode(',',$deleted_product_array).')');
			$messageStack->add(sprintf(CROSS_SELL_PRODUCT_DELETED, mysql_affected_rows($db->link)), 'success');
		}
		else
			$messageStack->add(CROSS_SELL_PRODUCT_NOT_DELETED, 'warning');
	}

	function search_cross_product($pid, $is_easy = false) {
		$pid = zen_db_prepare_input($pid);
		global $db, $messageStack;
		$result = array('product_lookup' => null,
						'xsell_items' => null,
						'product_check' => null,
						);
		if (MXSELL_FORM_INPUT_TYPE == "model")
			$result['product_lookup'] = $db->Execute("select p.products_id from " . TABLE_PRODUCTS . " p " .
									 "where p.products_model = '$pid' LIMIT 1");
		else
			$result['product_lookup'] = $db->Execute("select p.products_id from " . TABLE_PRODUCTS . " p " .
									 "where p.products_id = '$pid' LIMIT 1");


		if ($result['product_lookup']->RecordCount() > 0) {
			$result['product_check'] = $db->Execute(  "select p.products_id, p.products_model, pd.products_name, count(p.products_id) as xsells from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_MXSELL . MXSELL_SELECTED_TABLE . " px ".
												"where p.products_id = '" . $result['product_lookup']->fields['products_id'] . "' and pd.products_id = p.products_id and px.products_id = p.products_id group by p.products_id LIMIT 1");

      if ($result['product_check']->RecordCount() > 0) {
			$result['xsell_items'] = $db->Execute("select p.products_id, p.products_model, pd.products_name, px.ID, px.sort_order from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_MXSELL . MXSELL_SELECTED_TABLE . " px " .
											"where px.products_id = '" . $result['product_lookup']->fields['products_id'] . "' and p.products_id = px.xsell_id and  pd.products_id = p.products_id group by p.products_id");
      }	else {
        if(!$is_easy){
	  		  $messageStack->add(sprintf(CROSS_SELL_PRODUCT_NOT_FOUND_IN_XSELL, $pid), 'warning');
     		  $result = array('product_lookup' => null,
				  'xsell_items' => null,
				  'product_check' => null, );
        }else{
          $result = NULL;
        }
      }
    }
		else
      if(!$is_easy){
        $messageStack->add(sprintf(CROSS_SELL_PRODUCT_NOT_FOUND, $pid), 'warning');
      }else{
        $result = NULL;
      }

		return $result;
	}

	function delete_cross_product($pid){
		global $db,$messageStack;
		$pid = zen_db_prepare_input($pid);
    if (MXSELL_FORM_INPUT_TYPE == "id")
		  $db->Execute("delete from ".TABLE_PRODUCTS_MXSELL . MXSELL_SELECTED_TABLE . " where products_id= '$pid'");
    else {
      $result = $db->Execute("SELECT products_id FROM " . TABLE_PRODUCTS . " WHERE products_model = '$pid' LIMIT 1");
      if ($result->RecordCount() > 0) {
  		  $db->Execute("delete from ".TABLE_PRODUCTS_MXSELL . MXSELL_SELECTED_TABLE . " where products_id= '" . $result->fields['products_id'] . "'");
      }
    }
		$messageStack->add(sprintf(CROSS_SELL_CLEANED_UP,mysql_affected_rows($db->link)),'success');
	}

	function list_all_cross_products(){
		global $db;
		return $db->Execute( "select p.products_id, p.products_model, pd.products_name, count(p.products_id) as xsells from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_MXSELL . MXSELL_SELECTED_TABLE . " px " .
										"where p.products_id = pd.products_id and p.products_id = px.products_id group by p.products_id");
	}

	function clean_up_cross_sell(){
		global $db, $messageStack;
		$db->Execute('DELETE FROM '.TABLE_PRODUCTS_MXSELL . MXSELL_SELECTED_TABLE . ' WHERE products_id NOT IN (SELECT products_id FROM '.TABLE_PRODUCTS.' WHERE 1=1)');
		$messageStack->add(sprintf(CROSS_SELL_CLEANED_UP,mysql_affected_rows($db->link)),'success');
	}
 }

 function get_base_product_info($pid){
       $pid = zen_db_prepare_input($pid);
    global $db;

    $result = $db->Execute("select p.products_id, p.products_image, p.products_model, p.products_price, pd.products_name, pd.products_description from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd
                   where p.products_id = '$pid'  and p.products_id = pd.products_id LIMIT 1");


    if ($result->RecordCount() > 0) {
      return $result->fields;
    }
    return NULL;
 }


function install_multi_cross_sell( $num_xsell = 1 ) {
	global $db;

	if(!defined('MXSELL_ENABLED') ) {
    add_mxs_table( 1 );
    }

  $db->Execute("INSERT INTO " . TABLE_CONFIGURATION_GROUP . " VALUES ('', 'Cross Sell Settings', 'Set Cross Sell Options', '1', '1')");
  $db->Execute("UPDATE " . TABLE_CONFIGURATION_GROUP . " SET sort_order = last_insert_id() WHERE configuration_group_id = last_insert_id()");

  $db->Execute("SET @t4=0");
  $db->Execute("SELECT (@t4:=configuration_group_id) as t4
  FROM " . TABLE_CONFIGURATION_GROUP . "
  WHERE configuration_group_title= 'Cross Sell Settings'");

  $db->Execute("INSERT INTO " . TABLE_CONFIGURATION ." (configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function)
VALUES ('', 'Enable the Display of Multi Cross Sell?', 'MXSELL_ENABLED', 'true', 'Enable or Disable the Display of Multi Cross Sells?', @t4, 20, now(), now(), NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),')");

  $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function)
VALUES ('', 'Number of Cross Sells', 'MXSELL_NUM_OF_TABLES', '1', 'Enter the number of Cross Sells that you want', @t4, 30, now(), now(), NULL, 'zen_cfg_select_option(array(\'1\', \'2\', \'3\', \'4\', \'5\'), ')");

  $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function)
VALUES ('', 'Selected Cross Sell to edit', 'MXSELL_SELECTED_TABLE', '1', 'Currently Selected Cross Sell to edit. Change this in Admin->Catalog->Multi Cross-Sell by selecting the Cross Sell to edit from the dropdown selection', @t4, 40, now(), now(), NULL, 'zen_cfg_select_option(array(\'1\'), ')");

  $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function)
VALUES ('', 'XSell Input Type To Be Used In Form', 'MXSELL_FORM_INPUT_TYPE', 'model', 'Choose to use product ID or MODEL as your input type. Check readme file for more info', @t4, 50, now(), now(), NULL, 'zen_cfg_select_option(array(\'id\', \'model\'),')");

  $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function)
VALUES ('', 'XSell Product Input Separator', 'MXSELL_PRODUCT_INPUT_SEPARATOR', ',', 'You will need to insert all product id/model you want to cross-sell in 1 field, so each product id/model needs to be separated by a separator. The default is comma, choose another if you want to', @t4, 60, now(), now(), NULL, NULL)");

  add_mxs_configuration( 1 );
 }


function add_mxs_table( $num_xsell = 1 ) {
	global $db;

  $db->Execute("CREATE TABLE IF NOT EXISTS " . TABLE_PRODUCTS_MXSELL . $num_xsell . " (
    `ID` int(10) NOT NULL auto_increment,
    `products_id` int(10) unsigned NOT NULL default '1',
    `xsell_id` int(10) unsigned NOT NULL default '1',
    `sort_order` int(10) unsigned NOT NULL default '1',
    PRIMARY KEY  (`ID`),
    KEY `idx_products_id_xsell` (`products_id`) ) TYPE=MyISAM");
	}


function remove_multi_cross_sell() {
	global $db;

//  for ( $counter = 1; $counter <= MXSELL_NUM_OF_TABLES; $counter++ )
//    $db->Execute("DROP TABLE IF EXISTS " . TABLE_PRODUCTS_MXSELL . $counter );

  $db->Execute("SET @t4=0");
  $db->Execute("SELECT (@t4:=configuration_group_id) as t4
    FROM " . TABLE_CONFIGURATION_GROUP . "
    WHERE configuration_group_title= 'Cross Sell Settings'");
  $db->Execute("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_group_id = @t4");
  $db->Execute("DELETE FROM " . TABLE_CONFIGURATION_GROUP . " WHERE configuration_group_id = @t4");
  }


function add_mxs_configuration( $num_xsell = 1 ) {
	global $db;

  $offset = $num_xsell*100;
  $db->Execute("SET @t4=0");
  $db->Execute("SELECT (@t4:=configuration_group_id) as t4
  FROM " . TABLE_CONFIGURATION_GROUP . "
  WHERE configuration_group_title= 'Cross Sell Settings'");

  $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function)
VALUES ('', 'Cross Sell " . $num_xsell . " - Enable the Display of this Cross Sell?', 'MXSELL" . $num_xsell . "_ENABLED', 'true', 'Enable or Disable the Display of this Cross Sell?', @t4, $offset+210, now(), now(), NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),')");

  $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function)
VALUES ('', 'Cross Sell " . $num_xsell . " - Sort Order', 'XSELL_SORT_ORDER" . $num_xsell . "', 'sort_order', 'Sometimes you may want to display the xsell products randomly, especially if each product xsells with lots of others', @t4, $offset+220, now(), now(), NULL, 'zen_cfg_select_option(array(\'sort_order\', \'random\'),')");

  $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function)
VALUES ('', 'Cross Sell " . $num_xsell . " - Display Products - Min', 'MIN_DISPLAY_XSELL" . $num_xsell . "', 1, 'This is the minimum number of configured Cross-Sell products required in order to cause the Cross Sell information to be displayed.<br />Default: 1', @t4, $offset+230, now(), now(), NULL, NULL)");

  $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function)
VALUES ('', 'Cross Sell " . $num_xsell . " - Display Products - Max', 'MAX_DISPLAY_XSELL" . $num_xsell . "', 6, 'This is the maximum number of configured Cross-Sell products to be displayed.<br />Default: 6', @t4, $offset+240, now(), now(), NULL, NULL)");

  $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function)
VALUES ('', 'Cross Sell " . $num_xsell . " - Products Columns Per Row', 'SHOW_PRODUCT_INFO_COLUMNS_XSELL" . $num_xsell . "_PRODUCTS', '3', 'Cross-Sell Products Columns to display per Row<br />0= off or set the number of columns.<br />Default: 3', @t4, $offset+250, now(), now(), NULL, 'zen_cfg_select_option(array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9),')");

  $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function)
VALUES ('', 'Cross Sell " . $num_xsell . " - Display Product Name?', 'XSELL" . $num_xsell . "_DISPLAY_NAME', 'true', 'Cross-Sell -- Do you want to display the product name too?<br />Default: true', @t4, $offset+260, now(), now(), NULL, 'zen_cfg_select_option(array(\'true\',\'false\'),')");

  $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function)
VALUES ('', 'Cross Sell " . $num_xsell . " - Display Model Number?', 'XSELL" . $num_xsell . "_DISPLAY_MODEL', 'false', 'Cross-Sell -- Do you want to display the model number too?<br />Default: false', @t4, $offset+270, now(), now(), NULL, 'zen_cfg_select_option(array(\'true\',\'false\'),')");

  $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function)
VALUES ('', 'Cross Sell " . $num_xsell . " - Display Prices?', 'XSELL" . $num_xsell . "_DISPLAY_PRICE', 'false', 'Cross-Sell -- Do you want to display the product prices too?<br />Default: false', @t4, $offset+280, now(), now(), NULL, 'zen_cfg_select_option(array(\'true\',\'false\'),')");

  $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function)
VALUES ('', 'Cross Sell " . $num_xsell . " - Display Buy Now Button / More Info Link?', 'XSELL" . $num_xsell . "_DISPLAY_BUY_NOW', 'false', 'Cross-Sell -- Do you want to display the buy now button too?<br />Default: false', @t4, $offset+290, now(), now(), NULL, 'zen_cfg_select_option(array(\'true\',\'false\'),')");
 }

 ?>