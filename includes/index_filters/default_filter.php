<?php
/**
 * default_filter.php  for index filters
 *
 * index filter for the default product type
 * show the products of a specified manufacturer
 *
 * @package productTypes
 * @copyright Copyright 2003-2009 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @todo Need to add/fine-tune ability to override or insert entry-points on a per-product-type basis
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: default_filter.php 14870 2009-11-19 22:36:24Z drbyte $
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
if (isset($_GET['sort']) && strlen($_GET['sort']) > 3) {
  $_GET['sort'] = substr($_GET['sort'], 0, 3);
}
if (isset($_GET['alpha_filter_id']) && (int)$_GET['alpha_filter_id'] > 0) {
    $alpha_sort = " and pd.products_name LIKE '" . chr((int)$_GET['alpha_filter_id']) . "%' ";
  } else {
    $alpha_sort = '';
  }
  if (!isset($select_column_list)) $select_column_list = "";
   // show the products of a specified manufacturer
  if (isset($_GET['manufacturers_id']) && $_GET['manufacturers_id'] != '' ) {
    if (isset($_GET['filter_id']) && zen_not_null($_GET['filter_id'])) {
        echo __FILE__. __LINE__. ' SQL NOT DONE';
// We are asked to show only a specific category
      $listing_sql = "select distinct " . $select_column_list . " p.products_id, p.products_type, p.master_categories_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, pd.products_description, if(s.status = 1, s.specials_new_products_price, NULL) AS specials_new_products_price, IF(s.status = 1, s.specials_new_products_price, p.products_price) as final_price, p.products_sort_order, p.product_is_call, p.product_is_always_free_shipping, p.products_qty_box_status
       from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id , " .
       TABLE_PRODUCTS_DESCRIPTION . " pd, " .
       TABLE_MANUFACTURERS . " m, " .
       TABLE_PRODUCTS_TO_CATEGORIES . " p2c
       where p.products_status = 1
         and p.manufacturers_id = m.manufacturers_id
         and m.manufacturers_id = '" . (int)$_GET['manufacturers_id'] . "'
         and p.products_id = p2c.products_id
         and pd.products_id = p2c.products_id
         and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'
         ";/* bof all_sub_cats_products */
         if(!empty($sub_cats))
         	$listing_sql .=  "and p2c.categories_id IN($sub_cats)" .
         $alpha_sort;
         else
         /* eof all_sub_cats_products*/ $listing_sql .=  "
         and p2c.categories_id = '" . (int)$_GET['filter_id'] . "'" .
         $alpha_sort;
    } else {
// We show them all
        echo __FILE__. __LINE__. ' SQL NOT DONE';
      $listing_sql = "select distinct " . $select_column_list . " p.products_id, p.products_type, p.master_categories_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, pd.products_description, IF(s.status = 1, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status = 1, s.specials_new_products_price, p.products_price) as final_price, p.products_sort_order, p.product_is_call, p.product_is_always_free_shipping, p.products_qty_box_status
      from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " .
      TABLE_PRODUCTS_DESCRIPTION . " pd, " .
      TABLE_MANUFACTURERS . " m
      where p.products_status = 1
        and pd.products_id = p.products_id
        and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'
        and p.manufacturers_id = m.manufacturers_id
        and m.manufacturers_id = '" . (int)$_GET['manufacturers_id'] . "'" .
        $alpha_sort;
    }
  } else {
// show the products in a given category
    if (isset($_GET['filter_id']) && zen_not_null($_GET['filter_id'])) {
      echo __FILE__. __LINE__. ' SQL NOT DONE';
// We are asked to show only specific category
      $listing_sql = "select distinct " . $select_column_list . " p.products_id, p.products_type, p.master_categories_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, pd.products_description, IF(s.status = 1, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status = 1, s.specials_new_products_price, p.products_price) as final_price, p.products_sort_order, p.product_is_call, p.product_is_always_free_shipping, p.products_qty_box_status
      from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " .
      TABLE_PRODUCTS_DESCRIPTION . " pd, " .
      TABLE_MANUFACTURERS . " m, " .
      TABLE_PRODUCTS_TO_CATEGORIES . " p2c
      where p.products_status = 1
        and p.manufacturers_id = m.manufacturers_id
        and m.manufacturers_id = '" . (int)$_GET['filter_id'] . "'
        and p.products_id = p2c.products_id
        and pd.products_id = p2c.products_id
        and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'
        ";/* bof all_sub_cats_products */
         if(!empty($sub_cats))
         	$listing_sql .=  "and p2c.categories_id IN($sub_cats)" .
         $alpha_sort;
         else
         /* eof all_sub_cats_products*/ $listing_sql .=  "
        and p2c.categories_id = '" . (int)$current_category_id . "'" .

        $alpha_sort;
    } else {
// We show them all##########################################################################################
// This is the section that is currently in use**************************************************************
/////////////////////////////////////////////////////////////////////////////////////////////////////////////

//Ensure product name is in the sql as it is needed for my sort by
if(strpos($select_column_list, 'products_name')=== FALSE){
  $select_column_list .= 'products_name, ';
}

$listing_sql ="SELECT " . $select_column_list . "
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
  pd.products_viewed,
  pef.product_style,
  pef.product_priority,
  p.products_last_modified
 FROM
  products_to_categories p2c
   INNER JOIN products_description pd
    ON pd.products_id = p2c.products_id
   INNER JOIN products p
    ON p.products_id = p2c.products_id
   INNER JOIN product_extra_fields pef
    ON pef.products_id = p2c.products_id
 WHERE
  p.products_status = 1 AND ";
  if(!empty($sub_cats))
    $listing_sql .=  " p2c.categories_id IN($sub_cats)";
  else
  $listing_sql .=  " p2c.categories_id = " . (int)$current_category_id ;

         /////Add Filters
         if($_SESSION['OptionFilter']->is_filter_on()){
           $f_string = $_SESSION['OptionFilter']->get_filter_options_on('Style');
           if(sizeof($f_string)>0){
             $filter = implode(',',$f_string);
             $listing_sql .= " AND pef.product_style IN($filter) ";
           }

           $f_string = $_SESSION['OptionFilter']->get_filter_options_on('Colour');
           if(sizeof($f_string)>0){
             $filter = implode(',',$f_string);
             $listing_sql .= " AND pef.product_colour IN($filter) ";
           }

           $f_string = $_SESSION['OptionFilter']->get_filter_options_on('Material');
           if(sizeof($f_string)>0){
             $filter = implode(',',$f_string);
             $listing_sql .= " AND pef.product_material IN($filter) ";
           }

           $f_string = $_SESSION['OptionFilter']->get_filter_options_on('Finish');
           if(sizeof($f_string)>0){
             $filter = implode(',',$f_string);
             $listing_sql .= " AND pef.product_finish IN($filter) ";
           }

         }

         $sql_common = $listing_sql;

         $grouping = " GROUP BY LEFT(p.products_model,8) ";

         $orderby = " order by ";//"product_priority desc,";

         $listing_sql = '('.$sql_common . $grouping . ') UNION (' . $sql_common . ' AND pef.product_priority = 1)';

         $count_max_sql = '('.$sql_common . ') UNION (' . $sql_common . ' AND pef.product_priority = 1)';

         ////////////////////////
        //$listing_sql .= $alpha_sort;
//echo __FILE__. __LINE__. '<BR/>'. $listing_sql;
    }
  }

//BOF KaS
if((!isset($_GET['disp_order'])) || (isset($_GET['disp_order']))){
  $_GET['disp_order'] = ereg_replace("[^0-9*]", "", $_GET['disp_order']);
  if($_GET['disp_order']< 0 || $_GET['disp_order']>8)$_GET['dips_order']=0;
}else{
  $_GET['disp_order']=0;
}

//These values must match up with those on tpl_module_gallery_view.php
  switch ($_GET['disp_order']) {
    case 8:
      $listing_sql .=  $orderby . " RAND() ";
      break;
    case 0:
      $listing_sql .=  $orderby . " products_viewed desc";
      break;
    case 1:
      $listing_sql .=  $orderby . " products_name ";
      break;
    case 2:
      $listing_sql .=  $orderby . " products_name desc ";
      break;
    case 3:
      $listing_sql .=  $orderby . " products_price ";
      break;
    case '4':
      $listing_sql .=  $orderby . " products_price desc ";
      break;
    case '5':
      $listing_sql .=  $orderby . " products_model ";
      break;
    case '6':
      $listing_sql .=  $orderby . " products_last_modified desc, products_name ";
      break;
    case '7':
      $listing_sql .=  $orderby . " products_last_modified, products_name ";
      break;
    //case '8':
      //$listing_sql .= " order by p.products_model " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
      //break;

  }


//EOF Kas
// set the default sort order setting from the Admin when not defined by customer
/*  if (!isset($_GET['sort']) and PRODUCT_LISTING_DEFAULT_SORT_ORDER != '') {
    $_GET['sort'] = PRODUCT_LISTING_DEFAULT_SORT_ORDER;
  }

  if (isset($column_list)) {
    if ((!isset($_GET['sort'])) || (isset($_GET['sort']) && !preg_match('/[1-8][ad]/', $_GET['sort'])) || (substr($_GET['sort'], 0, 1) > sizeof($column_list)) ) {
      for ($i=0, $n=sizeof($column_list); $i<$n; $i++) {
        if (isset($column_list[$i]) && $column_list[$i] == 'PRODUCT_LIST_NAME') {
          $_GET['sort'] = $i+1 . 'a';
          $listing_sql .= " order by p.products_sort_order, pd.products_name";
          break;
        } else {
// sort by products_sort_order when PRODUCT_LISTING_DEFAULT_SORT_ORDER is left blank
// for reverse, descending order use:
//       $listing_sql .= " order by p.products_sort_order desc, pd.products_name";
          //$listing_sql .= " order by p.products_sort_order, pd.products_name, RAND()";
          break;
        }
      }
// if set to nothing use products_sort_order and PRODUCTS_LIST_NAME is off
      if (PRODUCT_LISTING_DEFAULT_SORT_ORDER == '') {
        $_GET['sort'] = '20a';
      }
    } else {
      $sort_col = substr($_GET['sort'], 0 , 1);
      $sort_order = substr($_GET['sort'], 1);
      switch ($column_list[$sort_col-1]) {
        case 'PRODUCT_LIST_MODEL':
          $listing_sql .= " order by p.products_model " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
          break;
        case 'PRODUCT_LIST_NAME':
          $listing_sql .= " order by pd.products_name " . ($sort_order == 'd' ? 'desc' : '');
          break;
        case 'PRODUCT_LIST_MANUFACTURER':
          $listing_sql .= " order by m.manufacturers_name " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
          break;
        case 'PRODUCT_LIST_QUANTITY':
          $listing_sql .= " order by p.products_quantity " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
          break;
        case 'PRODUCT_LIST_IMAGE':
          $listing_sql .= " order by pd.products_name";
          break;
        case 'PRODUCT_LIST_WEIGHT':
          $listing_sql .= " order by p.products_weight " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
          break;
        case 'PRODUCT_LIST_PRICE':
          $listing_sql .= " order by p.products_price_sorter " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
          break;
      }
    }
  }
  */
// optional Product List Filter
/*
  if (PRODUCT_LIST_FILTER > 0) {
    if (isset($_GET['manufacturers_id']) && $_GET['manufacturers_id'] != '') {
      $filterlist_sql = "select distinct c.categories_id as id, cd.categories_name as name
      from " . TABLE_PRODUCTS . " p, " .
      TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " .
      TABLE_CATEGORIES . " c, " .
      TABLE_CATEGORIES_DESCRIPTION . " cd
      where p.products_status = 1
        and p.products_id = p2c.products_id
        and p2c.categories_id = c.categories_id
        and p2c.categories_id = cd.categories_id
        and cd.language_id = '" . (int)$_SESSION['languages_id'] . "'
        and p.manufacturers_id = '" . (int)$_GET['manufacturers_id'] . "'
      order by cd.categories_name";
    } else {
      $filterlist_sql= "select distinct m.manufacturers_id as id, m.manufacturers_name as name
      from " . TABLE_PRODUCTS . " p, " .
      TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " .
      TABLE_MANUFACTURERS . " m
      where p.products_status = 1
        and p.manufacturers_id = m.manufacturers_id
        and p.products_id = p2c.products_id
        and p2c.categories_id = '" . (int)$current_category_id . "'
      order by m.manufacturers_name";
    }
    $do_filter_list = false;
    $filterlist = $db->Execute($filterlist_sql);
    if ($filterlist->RecordCount() > 1) {
        $do_filter_list = true;
      if (isset($_GET['manufacturers_id'])) {
        $getoption_set =  true;
        $get_option_variable = 'manufacturers_id';
        $options = array(array('id' => '', 'text' => TEXT_ALL_CATEGORIES));
      } else {
        $options = array(array('id' => '', 'text' => TEXT_ALL_MANUFACTURERS));
      }
      while (!$filterlist->EOF) {
        $options[] = array('id' => $filterlist->fields['id'], 'text' => $filterlist->fields['name']);
        $filterlist->MoveNext();
      }
    }
  } */
  $max_sql='';
  if(strlen($sub_cats)>0){
    $max_sql = "SELECT COUNT(*) AS total FROM products_to_categories WHERE categories_id IN($sub_cats)";
  }else if($current_category_id>2){
    $max_sql = "SELECT COUNT(*) AS total FROM products_to_categories WHERE categories_id = " . (int)$current_category_id;
  }
  if($max_sql !=''){
    $mcr = $db->Execute($max_sql);
    $max_product_count =  $mcr->fields['total'];
    //echo $max_product_count.'<br/>';
  }
//echo $listing_sql;
  //$my_count = $db->Execute($listing_sql);
  //$my_count = $my_count->RecordCount();
//echo __FILE__. __LINE__. '<BR/>CCID '.$current_category_id . ' --- SC ' . $sub_cats . ' --- MPC ' . $max_product_count;
//echo __FILE__. __LINE__. '<BR/>'.$listing_sql;
//var_dump($db->Execute('EXPLAIN '.$listing_sql));