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

if ($products_image != '') {
  // prepare image name
  $products_image_extension = substr($products_image, strrpos($products_image, '.'));
  $products_image_base = str_replace($products_image_extension, '', $products_image);

  // if in a subdirectory
  if (strrpos($products_image, '/')) {
    $products_image_match = substr($products_image, strrpos($products_image, '/')+1);
    //echo 'TEST 1: I match ' . $products_image_match . ' - ' . $file . ' -  base ' . $products_image_base . '<br>';
    $products_image_match = str_replace($products_image_extension, '', $products_image_match) . '_';
    $products_image_base = $products_image_match;
  }

  $products_image_directory = str_replace($products_image, '', substr($products_image, strrpos($products_image, '/')));
  if ($products_image_directory != '') {
    $products_image_directory = DIR_WS_IMAGES . str_replace($products_image_directory, '', $products_image) . "/";
  } else {
    $products_image_directory = DIR_WS_IMAGES;
  }

  // Check for additional matching images
  $file_extension = $products_image_extension;
  $products_image_match_array = array();
  if ($dir = @dir($products_image_directory)) {
    while ($file = $dir->read()) {
      if (!is_dir($products_image_directory . $file)) {
        if(preg_match("/" . $products_image_base . "/i", $file) == '1') {
          if (substr($file, 0, strrpos($file, '.')) != substr($products_image, 0, strrpos($products_image, '.'))) {
            if ($products_image_base . str_replace($products_image_base, '', $file) == $file) {
                //echo 'I AM A MATCH ' . $file . '<br />';
              $images_array[] = $file;
            } else {
                //echo 'I AM NOT A MATCH ' . $file . '<br />';
            }
          }
        }
      }
    }
    //KandS - -Common images
    $sql = "SELECT * FROM common_images WHERE products_id = ".$_GET['products_id'];
    $common_images = $db->Execute($sql);
    while(!$common_images->EOF){
      $images_array[] = $common_images->fields['image'];
      $common_images->MoveNext();
    }
    //EOE KandS

    if (sizeof($images_array)) {
      sort($images_array);
    }
    $dir->close();
  }
}

// Build output based on images found
$num_images = sizeof($images_array);
$list_box_contents = '';
$title = '';
//echo var_dump($images_array);

if ($num_images) {
  $row = 0;
  $col = 0;
  if ($num_images < IMAGES_AUTO_ADDED || IMAGES_AUTO_ADDED == 0 ) {
    $col_width = floor(100/$num_images);
  } else {
    $col_width = floor(100/IMAGES_AUTO_ADDED);
  }
//echo $num_images;
  for ($i=0, $n=$num_images; $i<$n; $i++) {
    $file = $images_array[$i];
    $file_extension = substr($file, strrpos($file, '.'));
    $products_image_large = ereg_replace('^' . DIR_WS_IMAGES, DIR_WS_IMAGES . 'large/', $products_image_directory) . ereg_replace($file_extension . '$', '', $file) . IMAGE_SUFFIX_LARGE . $file_extension;
    $flag_has_large = true;
    $flag_display_large = (IMAGE_ADDITIONAL_DISPLAY_LINK_EVEN_WHEN_NO_LARGE == 'Yes' || $flag_has_large);
    $base_image = $products_image_directory . $file;
//echo $base_image;
//echo "Q!1<br>";
    $thumb_slashes = zen_image($base_image, addslashes($products_name), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT);
//echo "dd32";
    // remove additional single quotes from image attributes (important!)
    $thumb_slashes = preg_replace("/([^\\\\])'/", '$1\\\'', $thumb_slashes);
    $thumb_regular = zen_image($base_image, $products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT);
//echo "Z3D76AE";
    $large_link = zen_href_link(FILENAME_POPUP_IMAGE_ADDITIONAL, 'pID=' . $_GET['products_id'] . '&pic=' . $i . '&products_image_large_additional=' . $products_image_large);
//echo "333";

    // Link Preparation:
  // bof Zen Lightbox v1.4 aclarke 2007-09-22
  if (ZEN_LIGHTBOX_STATUS == 'true' || FUAL_SLIMBOX == 'true') {

    $script_link = '<script language="javascript" type="text/javascript"><!--' . "\n" . 'document.write(\'' . ($flag_display_large ? '<a href="' . zen_lightbox($products_image_large, addslashes($products_name), LARGE_IMAGE_WIDTH, LARGE_IMAGE_HEIGHT) . '" rel="lightbox[gallery]" title="' . addslashes($products_name) . '">' . $thumb_slashes . '<br />' . TEXT_CLICK_TO_ENLARGE . '</a>' : $thumb_slashes) . '\');' . "\n" . '//--></script>';

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
//echo "222";

?>