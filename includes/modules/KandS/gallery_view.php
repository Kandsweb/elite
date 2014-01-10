<?php
  if(!isset($_SESSION['current_view']))$_SESSION['current_view'] = GALLERY_DEFAULT_VIEW;
if(isset($_GET['view'])){
  $_SESSION['current_view'] = $_GET['view'];
  unset($_SESSION['per_page']);
}
if(isset($_GET['pp']))$_SESSION['per_page']= $_GET['pp'];

$image_button_list = IMAGE_GALLERY_LIST;
$image_button_medium = IMAGE_GALLERY_MEDIUM;
$image_button_small = IMAGE_GALLERY_SMALL;

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

switch($current_page){
  case 'index':

    break;
  case 'products_all':

    break;
}
//MAX_DISPLAY_PRODUCTS
?>
