<?php
/**
 * recent products sidebox
 * includes/modules/sideboxes/recent_products.php
 */

// test if box should display
if(zen_not_null($_GET['products_id'])) {
	$productid_array = array();
	$product_id1 = $_GET['products_id'];
	$_SESSION['recent_products1'][] = $product_id1;
}

	if (isset($_SESSION['recent_products1'])) {
	$productid_array = array_unique(array_reverse($_SESSION['recent_products1']));

	if (RECENT_VIEWED_PRODUCTS_MAXIMUM < 1) {
	//set the maximum number of recently viewed products here
	 $maximum_recent = 10;
	 } else {
	 $maximum_recent = RECENT_VIEWED_PRODUCTS_MAXIMUM;
	}

	$recent = array_slice($productid_array, 0, $maximum_recent);

$sub_query = " where p.products_id  IN (";
foreach($recent as $value){
	$sub_query .= "'" . $value . "', ";
}
$sub_query = substr($sub_query , 0, (strlen($sub_query)-2));
$sub_query .= ")";

  $show_recent_products= true;

  if ($show_recent_products == true) {

  $recent_products_query = "select p.products_id, p.master_categories_id, p.products_image, pd.products_name
  					from " . TABLE_PRODUCTS . " as p, " . TABLE_PRODUCTS_DESCRIPTION . " as pd "
					. $sub_query ."
					and p.products_id = pd.products_id";



  $recent_products = $db->Execute($recent_products_query);

  $row = 0;
  $col = 0;
  $list_box_contents = array();
  $title = '';

  $num_products_count = ($recent_products_query == '') ? 0 : $recent_products->RecordCount();

  // show only when 1 or more
 if ($num_products_count > 0) {
  if ($num_products_count < SHOW_PRODUCT_INFO_COLUMNS_FEATURED_PRODUCTS || SHOW_PRODUCT_INFO_COLUMNS_FEATURED_PRODUCTS == 0) {
    $col_width = floor(100/$num_products_count);
  } else {
    $col_width = floor(100/SHOW_PRODUCT_INFO_COLUMNS_FEATURED_PRODUCTS);
  }
  while (!$recent_products->EOF) {

    $recent_products->fields['products_image']=get_image_xref($recent_products->fields['products_image']);

    $products_price = zen_get_products_display_price($recent_products->fields['products_id']);
    if (!isset($productsInCategory[$recent_products->fields['products_id']])) $productsInCategory[$recent_products->fields['products_id']] = zen_get_generated_category_path_rev($recent_products->fields['master_categories_id']);

	 $list_box_contents[$row][$col] = array('params' =>'class="centerBoxContentsFeatured centeredContent back"' . ' ' . 'style="width:' . $col_width . '%;"',
    'text' => (($recent_products->fields['products_image'] == '' and PRODUCTS_IMAGE_NO_IMAGE_STATUS == 0) ? '' : '<a href="' . zen_href_link(zen_get_info_page($recent_products->fields['products_id']), 'cPath=' . $productsInCategory[$recent_products->fields['products_id']] . '&products_id=' . $recent_products->fields['products_id']) . '">' . zen_image(DIR_WS_IMAGES . $recent_products->fields['products_image'], $recent_products->fields['products_name'], IMAGE_FEATURED_PRODUCTS_LISTING_WIDTH, IMAGE_FEATURED_PRODUCTS_LISTING_HEIGHT) . '</a><br />') . '<a href="' . zen_href_link(zen_get_info_page($recent_products->fields['products_id']), 'cPath=' . $productsInCategory[$recent_products->fields['products_id']] . '&products_id=' . $recent_products->fields['products_id']) . '"><div class="familyViewItem">' . $recent_products->fields['products_name'] . '</div></a>');

	 $col ++;
    if ($col > (SHOW_PRODUCT_INFO_COLUMNS_FEATURED_PRODUCTS - 1)) {
      $col = 0;
      $row ++;
    }
    $recent_products->MoveNext();
  }

  if ($recent_products->RecordCount() > 0) {
    /*if (isset($new_products_category_id) && $new_products_category_id !=0) {
      $category_title = zen_get_categories_name((int)$new_products_category_id);
      $title = '<h2 class="centerBoxHeading">1' . TABLE_HEADING_FEATURED_PRODUCTS . ($category_title != '' ? ' - ' . $category_title : '') . '</h2>';
    } else {
      $title = '<h2 class="centerBoxHeading">2' . TABLE_HEADING_FEATURED_PRODUCTS . '</h2>';
    }*/
    $zc_show_featured = true;
  }
  }
  }
  }
?>
