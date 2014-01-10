<?php
//
// +----------------------------------------------------------------------+
// |zen-cart Open Source E-commerce                                       |
// +----------------------------------------------------------------------+
// | Copyright (c) 2003 The zen-cart developers                           |
// |                                                                      |
// | http://www.zen-cart.com/index.php                                    |
// |                                                                      |
// | Portions Copyright (c) 2003 osCommerce                               |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the GPL license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.zen-cart.com/license/2_0.txt.                             |
// | If you did not receive a copy of the zen-cart license and are unable |
// | to obtain it through the world-wide-web, please send a note to       |
// | license@zen-cart.com so we can mail you a copy immediately.          |
// +----------------------------------------------------------------------+
//  $Id: stats_products_viewed.php 1969 2005-09-13 06:57:21Z drbyte $
//
  require('includes/application_top.php');

  //echo var_dump($_POST);
 $action = isset($_GET['action'])?$_GET['action']:'';
 $mId = isset($_POST['mid'])?$_POST['mid']:-1;

 $is_test = isset($_POST['is_test'])?$_POST['is_test']:FALSE;
 $errors_only = isset($_POST['errors'])?$_POST['errors']:FALSE;

 $manufacturers_array = zen_get_manufacturers('', true);
 array_unshift($manufacturers_array, array('id'=>'-1','text'=>'Select Manufacture'));


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
<body onload="init()">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<a name="top"></a>
<table border="0" width="100%" cellspacing="2" cellpadding="2"><?php //Whole pg table ?>
  <tr> <td width="100%" valign="top">Manufacture:
    <?php
    echo zen_draw_form('frm_man', 'image_renamer.php', 'action=m');
    echo zen_draw_pull_down_menu('mid', $manufacturers_array, $mId);
    echo " Test Process " . zen_draw_checkbox_field('is_test',1,TRUE) . " ";
    echo zen_image_submit('button_report.gif','');
    echo " Show Errors Only (Only availabe during Test)" . zen_draw_checkbox_field('errors',1,FALSE);
    ?>
  </td>
  </tr>
</table>
<?php
  if($action='m'){
    $sql="SELECT products_id, products_model, products_image, products_status FROM " . TABLE_PRODUCTS . " WHERE manufacturers_id = $mId";
    $products_rs = $db->Execute($sql);
    //echo "<span style";
    while(!$products_rs->EOF){
      if(!$errors_only) echo "Processing ID:" . $products_rs->fields['products_id'] . " | Model:" . $products_rs->fields['products_model'] . "<br />";
      $org_file = $products_rs->fields['products_image'];
      do_additional_images($org_file);
      $orginal = substr($products_rs->fields['products_image'],9);  //remove 'products/'
      $org_value = substr($orginal,0, strlen($orginal)-4);  //remove ext
      $ext = strtolower($orginal);
      $ext = substr($ext, strlen($ext)-4,4);  //get the ext
      $xRef_rs = $db->Execute("SELECT * FROM image_xref WHERE orginal = '".$org_file . "'" );

      $xRef = 'products/'.encode($org_value). $additional_image_post_fix .$ext;

      //Check Db for entry
      if(!$xRef_rs->EOF){
        //Entry found for orginal
        if(!$errors_only)echo "<span style=\"text-decoration:line-through; color:blue;\">DB Entry found for $orginal</span> - Skipping<br />";
      }else{
        //No entry found for orginal
        //$xRef = 'products/'.encode($org_value).$ext;
        if(!$is_test){
          $result = $db->Execute("INSERT INTO image_xref (orginal, xref) VALUES ('$org_file', '$xRef')" );
          if(!$errors_only) echo "$orginal changed to ". $xRef . "<br />";
        }else{
          if(!$errors_only) echo "$orginal <u>would</u> be changed to ". $xRef . "<br />";
        }
      }
      //Db check finished
      //Check image file
      if(file_exists('../'.DIR_WS_IMAGES.$org_file)){
        if(!$is_test){
          $rnr = rename('../'.DIR_WS_IMAGES.$org_file, '../'.DIR_WS_IMAGES.$xRef);
          if(!rnr){
            echo "<b>RENAME FAILED $org_file<br/>";
          }else{
            if(!$errors_only) echo "File renamed.... $xRef<br/>";
          }
        }else{
          if(!$errors_only) echo "File <u>would</u> be renamed.... $xRef<br/>";
        }
      }else{
        //File does not exist so check to see if the renamed image exists
        $xrefName = get_image_xref($org_file);
        if(file_exists('../'.DIR_WS_IMAGES.$xrefName)){
          if($errors_only)echo "Processing ID:" . $products_rs->fields['products_id'] . " | Model:" . $products_rs->fields['products_model'] . "<br />";
         echo "File not found for ".DIR_WS_IMAGES."$org_file as it is <b>renamed</b> to $xrefName {Status:". $products_rs->fields{'products_status'}." }<br/>";
         if($errors_only) echo "-----------------------------------<br/>";
        }else{
          if($errors_only)echo "Processing ID:" . $products_rs->fields['products_id'] . " | Model:" . $products_rs->fields['products_model'] . "<br />";
         echo "<b>File not found for ".DIR_WS_IMAGES."$org_file & No renamed file</b> {Status:". $products_rs->fields{'products_status'}." }<br/>";
         if($errors_only) echo "-----------------------------------<br/>";
        }
      }
      $products_rs->MoveNext();
      if(!$errors_only) echo "-----------------------------------<br/>";
    }
  }
?>
<!-- body_eof //-->
<a href="#top">Top</a>
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php');


function do_additional_images($products_image){
  global $is_test;
  $products_image_directory = str_replace($products_image, '', substr($products_image, strrpos($products_image, '/')));
  if ($products_image_directory != '') {
    $products_image_directory = DIR_WS_IMAGES . str_replace($products_image_directory, '', $products_image) . "/";
  } else {
    $products_image_directory = DIR_WS_IMAGES;
  }
  //$products_image_directory = "../" . $products_image_directory;
  //Get list of additional images - images will be in array $images_array
  $flag_show_product_info_additional_images = 1;  //this is required to be set for the additional_images module
  require(DIR_WS_MODULES . 'additional_images.php');
  if(sizeof($images_array)>0){
    echo  "<b>".sizeof($images_array)." Additional image(s) found </b><br/>";
    foreach($images_array as $key => $image){
      $ending  = substr($image, strrpos($image, '_'));
      $image_base = str_replace($ending, '', $image);
      $encode_name = encode($image_base);
      if(!$is_test){
        if(!file_exists('../' . $products_image_directory .$encode_name . $ending)){
          $rnm = rename($products_image_directory .$image_base . $ending, $products_image_directory .$encode_name . $ending);
          if(!rnr){
            echo "RENAME FAILED $org_file<br/>";
          }else{
            if(!$errors_only) echo "Additional image file renamed.... $encode_name$ending<br/>";
          }
        }else{
          echo "Additional image $encode_name . $ending exists for $image_base$ending<br/>";
        }
      }else{
        if(file_exists('../' . $products_image_directory .$encode_name . $ending)){
          echo "Additional image $encode_name . $ending exists for $image_base$ending and won't be renamed<br/>";
        }else{
          echo "Additional image $encode_name . $ending for $image_base$ending would be renamed<br/>";
        }
      }
    }
  }
}
?>
