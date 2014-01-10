<?php
/**
 * additional_images module
 *
 * Prepares list of additional product images to be displayed in template
 *
 * @package templateSystem
 * @copyright Copyright 2005-2006 breakmyzencart.com
 * @copyright Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: additional_images.php 5369 2006-12-23 10:55:52Z drbyte $
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
if (!defined('IMAGE_ADDITIONAL_DISPLAY_LINK_EVEN_WHEN_NO_LARGE')) define('IMAGE_ADDITIONAL_DISPLAY_LINK_EVEN_WHEN_NO_LARGE','Yes');
$images_array = array();

$family_array = array();
$res = $db->Execute("SELECT products_model FROM " . TABLE_PRODUCTS . " WHERE products_id = '" . $_GET['products_id'] . "'");
if(!$res->EOF){
  $model_code = $res->fields['products_model'];
  $base_model = substr($model_code,0,8);
  $res = $db->Execute("SELECT
          products.products_id,
          products.products_image,
          products_description.products_name
        FROM
          products
          INNER JOIN products_description
            ON products.products_id = products_description.products_id
        WHERE
          products.products_model LIKE '". $base_model ."%' AND
          products.products_model <> '" . $model_code . "'");

  //SELECT products_id, products_image FROM " . TABLE_PRODUCTS . " WHERE products_model LIKE '" . $base_model . "%' AND products_model <> '" . $model_code . "'");
  while(!$res->EOF){
    $images_array[] = array('image' => ereg_replace('products/', '', $res->fields['products_image']), 'id'=>$res->fields['products_id'], 'name'=>$res->fields['products_name']);
    $res->MoveNext();
  }
}


// Build output based on images found
$num_images = sizeof($images_array);
$list_box_contents = '';
$title = '';

if ($num_images) {
  $row = 0;
  $col = 0;
  if ($num_images < IMAGES_AUTO_ADDED || IMAGES_AUTO_ADDED == 0 ) {
    $col_width = floor(100/$num_images);
  } else {
    $col_width = floor(100/IMAGES_AUTO_ADDED);
  }

  for ($i=0, $n=$num_images; $i<$n; $i++) {
    $file = $images_array[$i]['image'];
    $file_extension = substr($file, strrpos($file, '.'));
    $products_image_large = ereg_replace('^' . DIR_WS_IMAGES, DIR_WS_IMAGES . 'large/', $products_image_directory) . ereg_replace($file_extension . '$', '', $file) . IMAGE_SUFFIX_LARGE . $file_extension;
    $flag_has_large = true;
    $flag_display_large = (IMAGE_ADDITIONAL_DISPLAY_LINK_EVEN_WHEN_NO_LARGE == 'Yes' || $flag_has_large);
    $base_image = $products_image_directory . $file;
    $thumb_slashes = zen_image($base_image, addslashes($images_array[$i]['name']), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT);
    // remove additional single quotes from image attributes (important!)
    $thumb_slashes = preg_replace("/([^\\\\])'/", '$1\\\'', $thumb_slashes);
    $thumb_regular = zen_image($base_image, $products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT);
    $large_link = zen_href_link(FILENAME_POPUP_IMAGE_ADDITIONAL, 'pID=' . $images_array[$i]['id'] . '&pic=' . $i . '&products_image_large_additional=' . $products_image_large);

    // Link Preparation:
  // bof Zen Lightbox v1.4 aclarke 2007-09-22
  if (ZEN_LIGHTBOX_STATUS == 'true' || FUAL_SLIMBOX == 'true') {

    $script_link = '<script language="javascript" type="text/javascript"><!--' . "\n" . 'document.write(\'' . ($flag_display_large ? '<a href="' . zen_lightbox($products_image_large, addslashes($images_array[$i]['name']), LARGE_IMAGE_WIDTH, LARGE_IMAGE_HEIGHT) . '" rel="lightbox[gallery]" title="' . addslashes($images_array[$i]['name']) . '">' . $thumb_slashes .  '</a>' : $thumb_slashes) . '\');' . "\n" . '//--></script>';
    $script_link .= '<br/><a href="'.  zen_href_link(zen_get_info_page($images_array[$i]['id']), ($cPath ? 'cPath=' . $cPath . '&' : '') . 'products_id=' . $images_array[$i]['id']) .'"><div class="familyViewItem">Item Details</div></a>';
  } else {

    $script_link = '<script language="javascript" type="text/javascript"><!--' . "\n" . 'document.write(\'' . ($flag_display_large ? '<a href="javascript:popupWindow(\\\'' . $large_link . '\\\')">' . $thumb_slashes . '<br />' . TEXT_CLICK_TO_ENLARGE . '</a>' : $thumb_slashes) . '\');' . "\n" . '//--></script>';

  }
  // eof Zen Lightbox v1.4 aclarke 2007-09-22

    $noscript_link = '<noscript>' . ($flag_display_large ? '<a href="' . zen_href_link(FILENAME_POPUP_IMAGE_ADDITIONAL, 'pID=' . $_GET['products_id'] . '&pic=' . $i . '&products_image_large_additional=' . $products_image_large) . '" target="_blank">' . $thumb_regular . '<br /><span class="imgLinkAdditional">' . TEXT_CLICK_TO_ENLARGE . '</span></a>' : $thumb_regular ) . '</noscript>';

    //      $alternate_link = '<a href="' . $products_image_large . '" onclick="javascript:popupWindow(\''. $large_link . '\') return false;" title="' . $products_name . '" target="_blank">' . $thumb_regular . '<br />' . TEXT_CLICK_TO_ENLARGE . '</a>';

    $link = $script_link . "\n      " . $noscript_link;
    //      $link = $alternate_link;

    // List Box array generation:
    $list_box_contents[$row][$col] = array('params' => 'class="additionalImages centeredContent back"' . ' ' . 'style="width:' . $col_width . '%;"',
    'text' => "\n      " . $link);
    $col ++;
    if ($col > (IMAGES_AUTO_ADDED -1)) {
      $col = 0;
      $row ++;
    }
  } // end for loop
} // endif

?>