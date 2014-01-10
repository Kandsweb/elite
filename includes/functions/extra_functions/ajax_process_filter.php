<?php
function process_filter_return_data(){
  global $db, $cPath;
    if (isset($_POST['sendValue'])){
      $fcID = $_POST['sendValue'];
  }else{
      $value = "";
  }
  if (isset($_POST['sendFilter'])){
      $filterId = $_POST['sendFilter'];
      $filterId++;
  }

  if (isset($_POST['sendPath'])){
    $get_array = explode('&',$_POST['sendPath']);
    foreach($get_array as  $value){
      $g_array = explode('=', $value);
      if($g_array[0]){
        $_GET[$g_array[0]] = $g_array[1];
      }
    }
  }
  //if(isset($_SESSION['filter_array'])){

  //}else{
    $_SESSION['filter_array'][$filterId]=$fcID;
    for($i=$filterId+1; $i<7; $i++){
      $_SESSION['filter_array'][$i]=-1;
    }
 // }

  $current_category_id = $fcID;

  //if($value == -1){
    //echo json_encode(array("returnValue"=> array('id'=>-1, 'text'=>'All') ));
  //}else{

    //$results_array = get_filter_array($fcID);

    $ans = build_filtered_product_list($fcID);
    require('includes/templates/KandS/templates'. '/' . 'tpl_modules_filter.php');
 $filter_box_content;


//echo json_encode($ans);
    //echo json_encode(array("returnValue"=> $value ." and " . $filterId ));
    echo json_encode(array("filterbox"=> $filter_box_content, "productsdisplay" => $ans ));
  //}

}

function get_filter_array($cID){
  global $db;
  $filter_array = array();
  $filter_array[] = array('id' => -1, 'text'=> 'All');

  if($cID == -1) return $filter_array;

   $subcategories_query = "select c.parent_id, c.categories_id, cd.categories_name
                            from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd
                            where c.categories_id = cd.categories_id
                            and cd.language_id='" . (int)$_SESSION['languages_id'] . "'
                            and c.categories_status='1'
                            and c.parent_id = '" . (int)$cID . "'
                            order by sort_order, cd.categories_name";

    $subcategories = $db->Execute($subcategories_query);

    while (!$subcategories->EOF) {
      $filter_array[] = array('id' => $subcategories->fields['categories_id'], 'text'=> $subcategories->fields['categories_name']);
      $subcategories->MoveNext();
    }
    return $filter_array;
}
    /////////////////////////////////////////////////////////////////////////////////////
function build_filtered_product_list($current_category_id){
  if(!zen_not_null($current_category_id))return 'Sorry we have not found any items matching your search terms. Try broading your search';

  global $db, $template, $breadcrumb, $zco_notifier, $cPath;
    $sub_cats_array = array();
    zen_get_subcategories($sub_cats_array, $current_category_id);
    $sub_cats = implode(',',$sub_cats_array);
    $category_depth = 'products';


      //from main temp vars

        // create column list
  $define_list = array('PRODUCT_LIST_MODEL' => PRODUCT_LIST_MODEL,
  'PRODUCT_LIST_NAME' => PRODUCT_LIST_NAME,
  'PRODUCT_LIST_MANUFACTURER' => PRODUCT_LIST_MANUFACTURER,
  'PRODUCT_LIST_PRICE' => PRODUCT_LIST_PRICE,
  'PRODUCT_LIST_QUANTITY' => PRODUCT_LIST_QUANTITY,
  'PRODUCT_LIST_WEIGHT' => PRODUCT_LIST_WEIGHT,
  'PRODUCT_LIST_IMAGE' => PRODUCT_LIST_IMAGE);

  /*                         ,
  'PRODUCT_LIST_BUY_NOW' => PRODUCT_LIST_BUY_NOW);
  */
  asort($define_list);
  reset($define_list);
  $column_list = array();
  foreach ($define_list as $key => $value)
  {
    if ($value > 0) $column_list[] = $key;
  }

  $select_column_list = '';

  for ($i=0, $n=sizeof($column_list); $i<$n; $i++)
  {
    switch ($column_list[$i])
    {
      case 'PRODUCT_LIST_MODEL':
      $select_column_list .= 'p.products_model, ';
      break;
      case 'PRODUCT_LIST_NAME':
      $select_column_list .= 'pd.products_name, ';
      break;
      case 'PRODUCT_LIST_MANUFACTURER':
      $select_column_list .= 'm.manufacturers_name, ';
      break;
      case 'PRODUCT_LIST_QUANTITY':
      $select_column_list .= 'p.products_quantity, ';
      break;
      case 'PRODUCT_LIST_IMAGE':
      $select_column_list .= 'p.products_image, ';
      break;
      case 'PRODUCT_LIST_WEIGHT':
      $select_column_list .= 'p.products_weight, ';
      break;
    }
  }
  // always add quantity regardless of whether or not it is in the listing for add to cart buttons
  if (PRODUCT_LIST_QUANTITY < 1) {
    $select_column_list .= 'p.products_quantity, ';
  }

    $typefilter = 'default';
    if (isset($_GET['typefilter'])) $typefilter = $_GET['typefilter'];
      require(DIR_WS_INCLUDES . zen_get_index_filters_directory($typefilter . '_filter.php'));

      // query the database based on the selected filters
      $listing = $db->Execute($listing_sql);


    $current_categories_description = "";
// categories_description
$sql = "SELECT categories_description
        FROM " . TABLE_CATEGORIES_DESCRIPTION . "
        WHERE categories_id= :categoriesID
        AND language_id = :languagesID";

$sql = $db->bindVars($sql, ':categoriesID', $current_category_id, 'integer');
$sql = $db->bindVars($sql, ':languagesID', $_SESSION['languages_id'], 'integer');
$categories_description_lookup = $db->Execute($sql);
if ($categories_description_lookup->RecordCount() > 0) {
  $current_categories_description = $categories_description_lookup->fields['categories_description'];
}
  $tpl_page_body = 'tpl_index_product_list.php';
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////// KandS  Gallery/List view
if(!isset($_SESSION['current_view']))$_SESSION['current_view'] = GALLERY_DEFAULT_VIEW;
if(isset($_GET['view'])){
  $_SESSION['current_view'] = $_GET['view'];
  unset($_SESSION['per_page']);
}
if(isset($_POST['per_page']))$_SESSION['per_page']= $_POST['per_page'];

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
    $cols_in_view = 5;
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
//////Eof KandS
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////


require( 'includes/templates/KandS/templates'. '/' . 'tpl_modules_product_listing_ajax.php');
 //require($template->get_template_dir('tpl_modules_product_listing.php', DIR_WS_TEMPLATE, $current_page_base,'templates'). '/' . 'tpl_modules_product_listing_ajax.php');
return $content;
}
?>
