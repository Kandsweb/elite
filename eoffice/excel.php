<?php 
  ini_set ( 'memory_limit', '64M' );
  require('includes/application_top.php');
  zen_set_time_limit(0);
  $action = (isset($_GET['action']) ? $_GET['action'] : '');
  if ($action == 'upload') {
        if ($upl_file = new upload('upl_file')) {
          $upl_file->set_destination( DIR_FS_ADMIN.'temp/');
	  			if ($upl_file->parse() && $upl_file->save()) {}
        }
  }

  function zen_output_generated_category_path_excel($id, $from = 'category') {
    $calculated_category_path_string = '';
    $calculated_category_path = zen_generate_category_path($id, $from);
    for ($i=0, $n=sizeof($calculated_category_path); $i<$n; $i++) {
      for ($j=sizeof($calculated_category_path[$i])-1; $j>=0; ($j=$j-1)) {
        $calculated_category_path_string .= $calculated_category_path[$i][$j]['text'] . ' => ';
      }
    }
    $calculated_category_path_string = substr($calculated_category_path_string, 0, -4);

    if (strlen($calculated_category_path_string) < 1) $calculated_category_path_string = TEXT_TOP;

    return $calculated_category_path_string;
  }

  function zen_get_category_id_from_path($str) {
		global $languages_id, $db;
    $str_arr = explode('=>',$str);
		$parent_id = 0;
		for ($i=0;$i<sizeof($str_arr);$i++) {
	  	$str_arr[$i] = trim($str_arr[$i]);
	  	$parent_id_ar = $db->Execute('select c.categories_id, cd.categories_name from '.TABLE_CATEGORIES.' c, '.TABLE_CATEGORIES_DESCRIPTION.' cd where c.categories_id=cd.categories_id and c.parent_id='.$parent_id.' and cd.categories_name=\''.zen_db_prepare_input($str_arr[$i]).'\' and cd.language_id='.$languages_id);
	  	$parent_id = (int)$parent_id_ar->fields['categories_id'];
	  }
    return $parent_id;
  }

// languages definition --------------------------------------
  $languages = $db->Execute("select languages_id, name, code, image, directory from " . TABLE_LANGUAGES . " order by languages_id");
  $first_languages_id = -1;
  while (!$languages->EOF) {
    $languages_array[$languages->fields['languages_id']] = array(
                               'name' => $languages->fields['name'],
                               'code' => $languages->fields['code'],
		                       		 'languages_id' => $languages->fields['languages_id'],
                               'image' => $languages->fields['image'],
                               'directory' => $languages->fields['directory']);
	if ($first_languages_id == -1) $first_languages_id = $languages->fields['languages_id'];
	$languages->MoveNext();
  }
  $languages_array_keys = array_keys($languages_array);

/// columns definition --------------------------------------
//  products --------------------------------------
  $products_fields = array('products_id' => 0,
                           'products_price' => 5,
						   						 'products_image' => 6,
                           'products_quantity' => 7,
                           'products_quantity_new' => 8,
                           'products_model' => 9,
                           'products_weight' => 10,
                           'products_status' => 11,
                           'manufacturers_id' => 12,
       	                   'categories_id' => 13,
                           'products_date_added' => 14,
                           'products_last_modified' => 15,
                           'products_date_available' => 16,
                           'products_tax_class_id' => 17,
                           'products_ordered' => 18
                           );
  $products_description_fields = array('products_id' => 0,
                                       'products_name' => 1,
                                       'products_description' => 2,
                                       'products_url' => 3,
                                       'language_id' => 4);
// categories --------------------------------------
  $categories_fields = array('categories_id' => 0,
                             'categories_image' => 3,
                             'parent_id' => 4,
                             'sort_order' => 5,
                             'date_added' => 6,
                             'last_modified' => 7
                             );
  $categories_description_fields = array('categories_id' => 0,
                                         'categories_name' => 1,
                                         'language_id' => 2);
// manufacturers --------------------------------------
  $manufacturers_fields = array('manufacturers_id' => 0,
                                'manufacturers_name' => 3,
                                'manufacturers_image' => 4,
                                'date_added' => 5,
                                'last_modified' => 6
                                );
  $manufacturers_description_fields = array('manufacturers_id' => 0,
                                            'manufacturers_url' => 1,
                                            'languages_id' => 2);
    switch ($action) {
      case 'cr_file':
      require_once "includes/excel/Spreadsheet/Writer.php";
      $dop_filename = 'products_'.Date('YmdHis').'.xls';
	  	$xls = new Spreadsheet_Excel_Writer('temp/'.$dop_filename);
	  	$xls->setVersion(8);
	  	$titleFormat =& $xls-> addFormat();
	  	$titleFormat->setBgColor('');
	  	$titleFormat->setColor('navy');
	  	$titleFormat->setBold();
	  	$titleFormat->setPattern(18);
	  	$sheet =& $xls->addWorksheet('Products');
// set columns width --------------------------------------
	  	$sheet->setColumn(0, 0, 3);
	  	if (isset($products_description_fields['products_name'])){
				$i = $products_description_fields['products_name'];
				$sheet->setColumn($i, $i, 25);
	  	}
	  	if (isset($products_description_fields['products_description'])){
				$i = $products_description_fields['products_description'];
				$sheet->setColumn($i, $i, 25);
	  	}
	  	if (isset($products_description_fields['language_id'])){
				$i = $products_description_fields['language_id'];
				$sheet->setColumn($i, $i, 3);
	  	}
	  	if (isset($products_fields['products_quantity'])){
				$i = $products_fields['products_quantity'];
				$sheet->setColumn($i, $i, 4);
	  	}
	  	if (isset($products_fields['products_quantity_new'])){
				$i = $products_fields['products_quantity_new'];
				$sheet->setColumn($i, $i, 4);
	  	}
	  	if (isset($products_fields['products_weight'])){
				$i = $products_fields['products_weight'];
				$sheet->setColumn($i, $i, 4);
	  	}
	  	if (isset($products_fields['products_status'])){
				$i = $products_fields['products_status'];
				$sheet->setColumn($i, $i, 4);
	  	}
	  	$sheet->freezePanes(array(1, 2));

// generating products --------------------------------------
	  	if (isset($_POST['gn_products'])&&trim($_POST['gn_products'])<>'') {
	  		$ins_arr = array_merge($products_fields,$products_description_fields);
	  		array_multisort($ins_arr);
	  		while (list($key, $value) = each($ins_arr)) {
					$sheet->write(0,$value,str_replace('products_','',$key),$titleFormat); 
	  		}

			  $s = 'p.'.implode(", p.", array_keys($products_fields)).', ';
	  		$s .= 'pd.'.implode(", pd.", array_keys($products_description_fields));
	  		$s = str_replace(', p.categories_id','',$s);
	  		$s = str_replace(', p.products_quantity_new','',$s);
	  		$product_categories_id = (int)$_POST['categories_id'];
	  		$dop_where = '';
	  		if ($product_categories_id>0) {
		  		$from_str = ' from '.TABLE_PRODUCTS.' p, '.TABLE_PRODUCTS_DESCRIPTION.' pd , '.TABLE_PRODUCTS_TO_CATEGORIES.' ptc ';
		  		$cat_ar = zen_get_category_tree($product_categories_id,'',0,'',true);
		  		for ($i=0;$i<sizeof($cat_ar);$i++) $dop_st .= ' ptc.categories_id='.$cat_ar[$i]['id'].' or ';
		  		$dop_st = substr($dop_st, 0, -3);
		  		if (trim($dop_st)<>'')
		  		$dop_where = ' and (p.products_id=ptc.products_id) and ('.$dop_st.')';
		  		else $dop_where = ' and (p.products_id=ptc.products_id) and (ptc.categories_id=-1234)';
	  		} else $from_str = ' from '.TABLE_PRODUCTS.' p, '.TABLE_PRODUCTS_DESCRIPTION.' pd ';
	  		
	  		$products_sql = 'select '.$s.
	  		$from_str . ' 
	  		where (p.products_id=pd.products_id) '.$dop_where.' and (p.products_id<='.(int)$_POST['max_products'].' and p.products_id>='.(int)$_POST['min_products'].') 
	  		order by '.((sizeof($languages_array_keys)>1) ? 'p.products_id, pd.language_id ':'pd.products_name' );
 	  		$products = $db->Execute($products_sql) ;

	  		$i=1; 
	  		while (!$products->EOF) {
					reset($ins_arr);
	    		while (list($key, $value) = each($ins_arr)) {
		  			if (($products->fields['language_id'] == $first_languages_id) || (array_key_exists($key,$products_description_fields)))
		  			$sheet->write($i,$value,$products->fields[$key]);
	    		}

	    		$sheet->write($i,$products_description_fields['language_id'],$languages_array[$products->fields['language_id']]['code']);
	    		if ($products->fields['language_id'] == $first_languages_id) {
	      		$dop_cat_name = '';
	      		$prod_to_cat = $db->Execute('select categories_id from '.TABLE_PRODUCTS_TO_CATEGORIES.' where products_id='.$products->fields['products_id']);
	      		while (!$prod_to_cat->EOF) {
							$categ_names = zen_output_generated_category_path_excel($prod_to_cat->fields['categories_id']);
	        		if ($prod_to_cat->fields['categories_id'] == 0) $dop_cat_name1 = 'Top';
	          	else $dop_cat_name1 = $categ_names;
	        		$dop_cat_name .= (($dop_cat_name<>'')?"\n":'').$dop_cat_name1;
	        		$prod_to_cat->MoveNext();
	      		}
	      		$sheet->writeString($i,$products_fields['categories_id'],$dop_cat_name);
	      		$manuf_name = $db->Execute('select manufacturers_name from '.TABLE_MANUFACTURERS.' where manufacturers_id='.(int)$products->fields['manufacturers_id']);
	      		$sheet->writeString($i,$products_fields['manufacturers_id'],trim($manuf_name->fields['manufacturers_name']));
	    		}
	    		$i++;
	    		$products->MoveNext();
	  		}
	  	}

// generating categories --------------------------------------
	  	$sheet1 =& $xls->addWorksheet('Categories');
	  	$sheet1->setColumn(0, 0, 3);
	  	if (isset($categories_description_fields['categories_name'])){
				$i = $categories_description_fields['categories_name'];
				$sheet1->setColumn($i, $i, 25);
	  	}
	  	if (isset($categories_description_fields['language_id'])){
				$i = $categories_description_fields['language_id'];
				$sheet1->setColumn($i, $i, 3);
	  	}
	  	if (isset($categories_fields['categories_image'])){
				$i = $categories_fields['categories_image'];
				$sheet1->setColumn($i, $i, 10);
	  	}
	  	if (isset($categories_fields['parent_id'])){
				$i = $categories_fields['parent_id'];
				$sheet1->setColumn($i, $i, 25);
	  	}
	  	if (isset($categories_fields['sort_order'])){
				$i = $categories_fields['sort_order'];
				$sheet1->setColumn($i, $i, 3);
	  	}
	  	$sheet1->freezePanes(array(1, 0));
	  
	  	if (isset($_POST['gn_categories'])&&trim($_POST['gn_categories'])<>'') {
	  		$ins_arr = array_merge($categories_fields,$categories_description_fields);
	  		array_multisort($ins_arr);
	  		$s = 'c.'.implode(", c.", array_keys($categories_fields)).', ';
	  		$s .= 'cd.'.implode(", cd.", array_keys($categories_description_fields));
	  		$categories = $db->Execute('select '.$s.'
	  		from '.TABLE_CATEGORIES.' c, '.TABLE_CATEGORIES_DESCRIPTION.' cd 
	  		where c.categories_id=cd.categories_id and c.categories_id<='.(int)$_POST['max_categories'].' and c.categories_id>='.(int)$_POST['min_categories'].' order by c.categories_id');
	  		while (list($key, $value) = each($ins_arr)) {
					$sheet1->write(0,$value,str_replace('categories_','',$key),$titleFormat);
	  		}
	  		$i = 1;
	  		while (!$categories->EOF) {
					reset($ins_arr);
	    		while (list($key, $value) = each($ins_arr)) {
		  			if (($categories->fields['language_id'] == $first_languages_id) || (array_key_exists($key,$categories_description_fields)))
		  			$sheet1->write($i,$value,$categories->fields[$key]);
	    		}
					$sheet1->write($i,$categories_description_fields['language_id'],$languages_array[$categories->fields['language_id']]['code']);
					if ($categories->fields['language_id'] == $first_languages_id) {
		  			$categ_name = zen_output_generated_category_path_excel($categories->fields['parent_id']);
		  			$sheet1->write($i,$categories_fields['parent_id'],$categ_name);
					}
	    		$i++;
	    		$categories->MoveNext();
	  		}
	  	}

// generating manufacturers --------------------------------------
	  	$sheet2 =& $xls->addWorksheet('Manufacturers');
	  	$sheet2->setColumn(0, 0, 3);
	  	if (isset($manufacturers_description_fields['languages_id'])){
				$i = $manufacturers_description_fields['languages_id'];
				$sheet2->setColumn($i, $i, 3);
	  	}
	  	if (isset($manufacturers_fields['manufacturers_name'])){
				$i = $manufacturers_fields['manufacturers_name'];
				$sheet2->setColumn($i, $i, 25);
	  	}
	  	if (isset($manufacturers_fields['manufacturers_image'])){
				$i = $manufacturers_fields['manufacturers_image'];
				$sheet2->setColumn($i, $i, 15);
	  	}
	  	$sheet2->freezePanes(array(1, 0));
	  
	  	if (isset($_POST['gn_manufacturers'])&&trim($_POST['gn_manufacturers'])<>'') {
	  		$ins_arr = array_merge($manufacturers_fields,$manufacturers_description_fields);
	  		array_multisort($ins_arr);
	  		$s = 'm.'.implode(", m.", array_keys($manufacturers_fields)).', ';
	  		$s .= 'md.'.implode(", md.", array_keys($manufacturers_description_fields));

	  		$manufacturers = $db->Execute('select '.$s.' 
	  		from '.TABLE_MANUFACTURERS.' m, '.TABLE_MANUFACTURERS_INFO.' md where m.manufacturers_id=md.manufacturers_id and m.manufacturers_id<='.(int)$_POST['max_manufacturers'].' and m.manufacturers_id>='.(int)$_POST['min_manufacturers'].' order by m.manufacturers_name');
	  		while (list($key, $value) = each($ins_arr)) {
					$sheet2->write(0,$value,str_replace('manufacturers_','',$key),$titleFormat);
	  		}
	  		$i = 1;
	  		if (isset($_POST['gn_manufacturers'])&&trim($_POST['gn_manufacturers'])<>'')
	  		while (!$manufacturers->EOF) {
					reset($ins_arr);
	    		while (list($key, $value) = each($ins_arr)) {
		  			if (($manufacturers->fields['languages_id'] == $first_languages_id) || (array_key_exists($key,$manufacturers_description_fields)))
		  			$sheet2->write($i,$value,$manufacturers->fields[$key]);
	    		}

	    		$sheet2->write($i,$manufacturers_description_fields['languages_id'],$languages_array[$manufacturers->fields['languages_id']]['code']);
	    		$i++;
	    		$manufacturers->MoveNext();
	  		}
	  	}
		  $xls->close();
		  $messageStack->add_session('File '.$dop_filename.' has been generated', 'success');
		  zen_redirect(FILENAME_EXCEL);
      break;

/// POPULATE --------------------------------------
	  case 'populate':
		require_once 'includes/excel/reader.php';
	    $data = new Spreadsheet_Excel_Reader();
			$data->setRowColOffset(0);
	    //$data->setOutputEncoding('CP1251');
	    $data->read('temp/'.urldecode($_GET['filename']));

// populate categories --------------------------------------
	    if (strstr($data->sheets[1]['cells'][0][0],'PPL')) {
        $messageStack->add_session('Categories populated', 'success');
        $i = 1;
        while ($i <= $data->sheets[1]['numRows']) {
          if (trim($data->sheets[1]['cells'][$i][1])<>'') {
          	reset($categories_fields);
          	unset($sql_data_array);
          	while (list($key, $value) = each($categories_fields)) {
    					switch ($key) {
      					case 'manufacturers_id':
      					case 'categories_id':

      						break;
      					case 'last_modified':
      						if (trim($data->sheets[1]['cells'][$i][$value])<>'')
									$sql_data_array[$key] = zen_db_prepare_input($data->sheets[1]['cells'][$i][$value]);
									else $sql_data_array[$key] = 'null';
      						break;
      					default:
      						$sql_data_array[$key] = zen_db_prepare_input($data->sheets[1]['cells'][$i][$value]);
      						break;
    					}
          		
          	}
          	$categories_id = (int)zen_db_prepare_input($data->sheets[1]['cells'][$i][0]);
          	if ($categories_id > 0) {
          		zen_db_perform(TABLE_CATEGORIES, $sql_data_array, 'update', "categories_id = '" . $categories_id . "'");
          		$action = 'update';
          	} else {
          		zen_db_perform(TABLE_CATEGORIES, $sql_data_array);
          		$categories_id = zen_db_insert_id();
          		$action = 'insert';
          	}
          	for ($j = 0; $j < sizeof($languages_array_keys); $j++) {
		        	reset($categories_description_fields);
          		unset($sql_data_array);
			  
		        	while (list($key, $value) = each($categories_description_fields)) {
		      	    if (($key<>'language_id') && ($key<>'categories_id'))
			  	      $sql_data_array[$key] = zen_db_prepare_input($data->sheets[1]['cells'][$i+$j][$value]);
		  	      }
    	      	if ($action == 'update') {
				      	zen_db_perform(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array, 'update', "categories_id = '" . (int)$categories_id . "' and language_id = '" . (int)$languages_array_keys[$j] . "'");
	          	} else {
          			$sql_data_array['categories_id'] = $categories_id;
				  			$sql_data_array['language_id'] = $languages_array_keys[$j];
				  			zen_db_perform(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array);
          		}
          	}

          }
          $i = $i+sizeof($languages_array_keys);
        }
        if (array_key_exists('parent_id',$categories_fields)) {
        	$i = 1;
        	while ($i <= $data->sheets[1]['numRows']) {
        		if (trim($data->sheets[1]['cells'][$i][1])<>'') {
			 				$parent_id = (int)zen_get_category_id_from_path($data->sheets[1]['cells'][$i][$categories_fields['parent_id']]);
      	      if (($parent_id>0) || zen_db_prepare_input($data->sheets[1]['cells'][$i][$categories_fields['parent_id']])=='Top' || zen_db_prepare_input($data->sheets[1]['cells'][$i][$categories_fields['parent_id']])=='') {
				   			$db->Execute('update '.TABLE_CATEGORIES.' set parent_id=\''.(int)$parent_id.'\' where categories_id=\''.zen_db_prepare_input($data->sheets[1]['cells'][$i][0]).'\'');
				 			} elseif (($parent_id == 0) && zen_db_prepare_input($data->sheets[1]['cells'][$i][$categories_fields['parent_id']])<>'Top')
				 			$messageStack->add_session('Categories sheet: Do not find parent category for category with id='.$data->sheets[1]['cells'][$i][0].' ', 'warning');
        		}
        		$i = $i+sizeof($languages_array_keys);
        	}
        }
	    }
	    
// populate manufacturers --------------------------------------
	    if (strstr($data->sheets[2]['cells'][0][0],'PPL')) {
	    	$messageStack->add_session('Manufacturers populated', 'success');
	    	$i = 1;
	    	while ($i <= $data->sheets[2]['numRows']) {
	    		if (trim($data->sheets[2]['cells'][$i][1])<>'') {
		      	reset($manufacturers_fields);
		      	unset($sql_data_array);
		      	while (list($key, $value) = each($manufacturers_fields)) {
    					switch ($key) {
      					case 'manufacturers_id':

      						break;
      					case 'last_modified':
      						if (trim($data->sheets[2]['cells'][$i][$value])<>'')
									$sql_data_array[$key] = zen_db_prepare_input($data->sheets[2]['cells'][$i][$value]);
									else $sql_data_array[$key] = 'null';
      						break;
      					default:
      						$sql_data_array[$key] = zen_db_prepare_input($data->sheets[2]['cells'][$i][$value]);
      						break;
    					}

			      }
			      $manufacturers_id = (int)zen_db_prepare_input($data->sheets[2]['cells'][$i][0]);
			      if ($manufacturers_id > 0) {
			    		zen_db_perform(TABLE_MANUFACTURERS, $sql_data_array, 'update', "manufacturers_id = '" . $manufacturers_id . "'");
			    		$action = 'update';
			  		} else {
							zen_db_perform(TABLE_MANUFACTURERS, $sql_data_array);
							$manufacturers_id = zen_db_insert_id();
							$action = 'insert';
				  	}
			  		for ($j = 0; $j < sizeof($languages_array_keys); $j++) {
		      	  reset($manufacturers_description_fields);
			  	  	unset($sql_data_array);
			  
		  	      while (list($key, $value) = each($manufacturers_description_fields)) {
			          if (($key<>'languages_id') && ($key<>'manufacturers_id'))
				        $sql_data_array[$key] = zen_db_prepare_input($data->sheets[2]['cells'][$i+$j][$value]);
			        }
			    		if ($action == 'update') {
			    	  	zen_db_perform(TABLE_MANUFACTURERS_INFO, $sql_data_array, 'update', "manufacturers_id = '" . (int)$manufacturers_id . "' and languages_id = '" . (int)$languages_array_keys[$j] . "'");
							} else {
					  		$sql_data_array['manufacturers_id'] = $manufacturers_id;
					  		$sql_data_array['languages_id'] = $languages_array_keys[$j];
					  		zen_db_perform(TABLE_MANUFACTURERS_INFO, $sql_data_array);
							}
			  		}

	    		}
	    		$i = $i+sizeof($languages_array_keys);
	    	}
	    }

// populate products --------------------------------------
	    if (strstr($data->sheets[0]['cells'][0][0],'PPL')) {
	    	$messageStack->add_session('Products populated', 'success');
		  	$i = 1;
        while ($i <= $data->sheets[0]['numRows']) {
        	if (((isset($products_description_fields['products_url']))&&strtolower(trim($data->sheets[0]['cells'][$i][$products_description_fields['products_url']]))<>'d')||(empty($products_description_fields['products_url']))) {
  	      	if (trim($data->sheets[0]['cells'][$i][1])<>'') {
	        		reset($products_fields);
        			unset($sql_data_array);
        			while (list($key, $value) = each($products_fields)) {
    						switch ($key) {
    	  					case 'products_id':
  	    					case 'manufacturers_id':
	      					case 'categories_id':
      						case 'products_quantity':

      							break;
      						case 'products_quantity_new':
      							if (trim($data->sheets[0]['cells'][$i][$products_fields['products_quantity_new']])<>'')
										$sql_data_array['products_quantity'] = zen_db_prepare_input($data->sheets[0]['cells'][$i][$products_fields['products_quantity_new']]);
      							break;
    	  					case 'products_last_modified':
  	    					case 'products_date_available':
	      						if (trim($data->sheets[0]['cells'][$i][$value])<>'')
										$sql_data_array[$key] = zen_db_prepare_input($data->sheets[0]['cells'][$i][$value]);
										else $sql_data_array[$key] = 'null';
      							break;
      						case 'products_status':
      							if ((zen_db_prepare_input($data->sheets[0]['cells'][$i][$value])==1)||(zen_db_prepare_input($data->sheets[0]['cells'][$i][$value])=='')) $products_status = 1;
      							  else $products_status = zen_db_prepare_input($data->sheets[0]['cells'][$i][$value]);
      							$sql_data_array[$key] = $products_status;
      							break;
      						default:
      							$sql_data_array[$key] = zen_db_prepare_input($data->sheets[0]['cells'][$i][$value]);
      							break;
    						}
    	    		}
  	      		$products_id = (int)zen_db_prepare_input($data->sheets[0]['cells'][$i][0]);
// product manufacturers --------------------------------------
	        		if (isset($products_fields['manufacturers_id'])) {
        				if (trim($data->sheets[0]['cells'][$i][$products_fields['manufacturers_id']])<>'') {
        					$manufacturers_id_ar = $db->Execute('select manufacturers_id from '.TABLE_MANUFACTURERS.' where manufacturers_name=\''.zen_db_prepare_input($data->sheets[0]['cells'][$i][$products_fields['manufacturers_id']]).'\'');
      	  				//$manufacturers_id_ar = tep_db_fetch_array($manufacturers_id_qu);
    	  	  			if ((int)$manufacturers_id_ar->fields['manufacturers_id']>0) $manufacturers_id = (int)$manufacturers_id_ar->fields['manufacturers_id'];
  	  	    			else $messageStack->add_session('Products sheet: wrong manufacturers name for product with id='.$products_id, 'warning');
	  	      		} else $manufacturers_id = 0;
  	      			$sql_data_array['manufacturers_id'] = $manufacturers_id;
	        		}
// product manufacturers --------------------------------------
      	  		if ((int)$products_id > 0) {
				    		zen_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '" . $products_id . "'");
				    		$action = 'update';
				  		} else {
								zen_db_perform(TABLE_PRODUCTS, $sql_data_array);
								$products_id = zen_db_insert_id();
								if (strstr($data->sheets[3]['cells'][0][0],'PPL')) $inserted_products_id[$data->sheets[0]['cells'][$i][$products_description_fields['products_name']]] = $products_id;
								$action = 'insert';
				  		}
				  		for ($j = 0; $j < sizeof($languages_array_keys); $j++) {
				  			reset($products_description_fields);
				    		unset($sql_data_array);
			  
		        		while (list($key, $value) = each($products_description_fields)) {
		      	    	if (($key<>'language_id') && ($key<>'products_id'))
			  	      	$sql_data_array[$key] = zen_db_prepare_input($data->sheets[0]['cells'][$i+$j][$value]);
		  	      	}
				    		if ($action == 'update') {
				      		zen_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', "products_id = '" . (int)$products_id . "' and language_id = '" . (int)$languages_array_keys[$j] . "'");
								} else {
				  				$sql_data_array['products_id'] = $products_id;
				  				$sql_data_array['language_id'] = $languages_array_keys[$j];
								  zen_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array);
									}
				  		}

// product categories --------------------------------------
				  		if (isset($products_fields['categories_id'])) {
				  			$dop_arr = explode("\n",$data->sheets[0]['cells'][$i][$products_fields['categories_id']]);
				  			$db->Execute('delete from '.TABLE_PRODUCTS_TO_CATEGORIES.' where products_id = '.$products_id);
			  				for ($k=0; $k<sizeof($dop_arr); $k++) {
									$sql_data_array = array('products_id' => $products_id,
      	                                  'categories_id' => zen_get_category_id_from_path($dop_arr[$k]));
									zen_db_perform(TABLE_PRODUCTS_TO_CATEGORIES, $sql_data_array);
				  			}
				  		}
// product categories --------------------------------------
        		}
        	} elseif ((isset($products_description_fields['products_url']))&&strtolower(trim($data->sheets[0]['cells'][$i][$products_description_fields['products_url']])) == 'd') {
        		$products_id = (int)zen_db_prepare_input($data->sheets[0]['cells'][$i][0]);
        		zen_remove_product($products_id);
        	}
        	$i = $i+sizeof($languages_array_keys);
        }
	    }

	    zen_redirect(FILENAME_EXCEL);
      break;
      case 'delete_confirm':
        @unlink(DIR_FS_ADMIN.'temp/' . $_GET['filename']);
        zen_redirect(FILENAME_EXCEL);
      break;
    }
?>
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="init()">

<div id="spiffycalendar" class="text"></div>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>

<!-- body_text //-->

    <td width="100%" valign="top">
<table width="100%" border="0">
<tr><td>
<?php
	echo '<font style="size:8px;">Upload file:</font><br>'.zen_draw_form('categories', FILENAME_EXCEL, 'action=upload', 'post', 'enctype="multipart/form-data"');
	echo zen_draw_file_field('upl_file').'<br>'.zen_image_submit('button_upload.gif', IMAGE_UPLOAD);
?></form>
    </td>
  </tr>


<?php    $contents = array();
$current_path = DIR_FS_ADMIN.'temp';
//echo '=='.$current_path;
    $dir = dir(DIR_FS_ADMIN.'temp');
    while ($file = $dir->read()) {
     if (strstr($file,'.xls'))
      if ( ($file != '.') && ($file != 'CVS') && ( ($file != '..') || ($current_path != DIR_FS_DOCUMENT_ROOT) ) ) {
        $file_size = number_format(filesize($current_path . '/' . $file)) . ' bytes';
        $permissions = '';
        if ($showuser) {
          $user = @posix_getpwuid(fileowner($current_path . '/' . $file));
          $group = @posix_getgrgid(filegroup($current_path . '/' . $file));
        } else {
          $user = $group = array();
        }

        $contents[] = array('name' => $file,
                            'is_dir' => is_dir($current_path . '/' . $file),
                            'last_modified' => strftime(DATE_TIME_FORMAT, filemtime($current_path . '/' . $file)),
                            'size' => $file_size,
                            'permissions' => $permissions,
                            'user' => $user['name'],
                            'group' => $group['name']);
      }
    }

    function zen_cmp($a, $b) {
      return strcmp( ($a['is_dir'] ? 'D' : 'F') . $a['name'], ($b['is_dir'] ? 'D' : 'F') . $b['name']);
    }
    usort($contents, 'zen_cmp');
?>

      <tr>
        <td align="right"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top" align="right"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent">File name</td>
                <td class="dataTableHeadingContent" align="right">File size</td>
                <td class="dataTableHeadingContent" align="center">Last Modified</td>
                <td class="dataTableHeadingContent" align="right">Action&nbsp;</td>
              </tr>
<?php
  $icon = zen_image(DIR_WS_ICONS . 'file_download.gif', ICON_FILE_DOWNLOAD);
  for ($i=0, $n=sizeof($contents); $i<$n; $i++) {
    if ((!isset($HTTP_GET_VARS['info']) || (isset($HTTP_GET_VARS['info']) && ($HTTP_GET_VARS['info'] == $contents[$i]['name']))) && !isset($fInfo) && ($action != 'upload') && ($action != 'new_folder')) {
      $fInfo = new objectInfo($contents[$i]);
    }

    if ($contents[$i]['name'] == '..') {
      $goto_link = substr($current_path, 0, strrpos($current_path, '/'));
    } else {
      $goto_link = $current_path . '/' . $contents[$i]['name'];
    }

    /*if (isset($fInfo) && is_object($fInfo) && ($contents[$i]['name'] == $fInfo->name)) {
      if ($fInfo->is_dir) {
        echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">' . "\n";
        $onclick_link = 'goto=' . $goto_link;
      } else {
        echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">' . "\n";
        $onclick_link = 'info=' . urlencode($fInfo->name) . '&action=edit';
      }
    } else {
      echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">' . "\n";
      $onclick_link = 'info=' . urlencode($contents[$i]['name']);
    }*/

    echo '<tr class="dataTableRow">';
    if ($contents[$i]['is_dir']) {
      if ($contents[$i]['name'] == '..') {
      } else {
      }
      
    } else {

    }
    $link = zen_href_link(FILENAME_EXCEL, 'goto=' . $current_path.'/'.$contents[$i]['name']);
    if ($contents[$i]['name'] <> '..') {
?>
                <td class="dataTableContent"><?php echo '<a href="' .HTTP_SERVER.DIR_WS_ADMIN.'temp/'. $contents[$i]['name'] . '">' . $icon . '&nbsp;' . $contents[$i]['name'].'</a>'; ?></td>
                <td class="dataTableContent" align="right"><?php echo ($contents[$i]['is_dir'] ? '&nbsp;' : $contents[$i]['size']); ?></td>
                <td class="dataTableContent" align="center"><?php echo $contents[$i]['last_modified']; ?></td>
                <td class="dataTableContent" align="right">
		<?php
		 echo '<a href="'.zen_href_link(FILENAME_EXCEL, 'action=start_populate&filename='.urlencode($contents[$i]['name'])).'">Populate from file</a>&nbsp;
		       <a href="'.zen_href_link(FILENAME_EXCEL, 'action=start_delete&filename='.urlencode($contents[$i]['name'])).'">'.zen_image(DIR_WS_IMAGES . 'icon_delete.gif','Delete').'</a>';
		?>
		&nbsp;</td></tr>
<?php
  }}
?>
</table>


<?php
    echo '<a href="'.zen_href_link(FILENAME_EXCEL, 'action=start_cr_file').'">Generate file</a>&nbsp;&nbsp;&nbsp;';
?>


<?php
  $heading = array();
  $contents = array();

  switch ($action) {
    case 'start_populate':
        $heading[] = array('text' => '<b>Start populate</b>');

        $contents[] = array('text' => 'Would you like to start populate of '.urldecode($_GET['filename']).'?');
	
				$contents[] = array('align' => 'center',
	      			              'text' => '<br><a href="'.zen_href_link(FILENAME_EXCEL, 'action=populate&filename='.urlencode(urldecode($_GET['filename']))).'">'.zen_image_button('button_populate.gif', 'populate'));
      break;
    case 'start_delete':
        $heading[] = array('text' => '<b>Delete file</b>');

        $contents[] = array('text' => 'Would you like to delegte '.$_GET['filename'].'?');
	
				$contents[] = array('align' => 'center',
	      			              'text' => '<br><a href="'.zen_href_link(FILENAME_EXCEL, 'action=delete_confirm&filename='.urlencode(urldecode($_GET['filename']))).'">'.zen_image_button('button_delete.gif', 'populate'));
      break;
    case 'start_cr_file':
        $heading[] = array('text' => '<b>Start populate</b>');
       
        $contents = array('form' => zen_draw_form('newcategory', FILENAME_EXCEL, 'action=cr_file', 'post'));
        $contents[] = array('text' => 'Would you like to generate new file?<br><br>');
	
        $products_range = $db->Execute('select max(products_id) as max_id, min(products_id) as min_id from '.TABLE_PRODUCTS);
        $contents[] = array('text' => zen_draw_checkbox_field('gn_products','',true).' generate products from category:<br>'.
        zen_draw_pull_down_menu('categories_id', zen_get_category_tree()).'<br>'.
        'with id<br>
        from: '.zen_draw_input_field('min_products',$products_range->fields['min_id'],' size="5"').' to: '.zen_draw_input_field('max_products',$products_range->fields['max_id'],' size="5"').'<br><br>');
	
        $categories_range = $db->Execute('select max(categories_id) as max_id, min(categories_id) as min_id from '.TABLE_CATEGORIES);
        $contents[] = array('text' => zen_draw_checkbox_field('gn_categories','',true).' generate categories with id<br>
        from: '.zen_draw_input_field('min_categories',$categories_range->fields['min_id'],' size="5"').' to: '.zen_draw_input_field('max_categories',$categories_range->fields['max_id'],' size="5"').'<br><br>');
	
        $categories_range = $db->Execute('select max(manufacturers_id) as max_id, min(manufacturers_id) as min_id from '.TABLE_MANUFACTURERS);
        	$contents[] = array('text' => zen_draw_checkbox_field('gn_manufacturers','',true).' generate manufacturers with id<br>
        from: '.zen_draw_input_field('min_manufacturers',$categories_range->fields['min_id'],' size="5"').' to: '.zen_draw_input_field('max_manufacturers',$categories_range->fields['max_id'],' size="5"').'<br><br>');



        $contents[] = array('align' => 'center',
	                          'text' => '<br>'.zen_image_submit('button_generate.gif', 'generate'));
      break;
    case 'delete':

      break;
    default:
      break;
  }

  if ( (zen_not_null($heading)) && (zen_not_null($contents)) )
  {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
?>
          </tr>
        </table></td>
      </tr>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?><!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>