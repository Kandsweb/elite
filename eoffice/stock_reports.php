<?php
/**
 * @package admin
 * @copyright Copyright 2003-2009 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: stats_products_purchased.php 15014 2009-12-01 21:24:50Z drbyte $
 */

  require('includes/application_top.php');

  $manufacturers_array=array();
  $sql = "SELECT manufacturers_id, manufacturers_name FROM " . TABLE_MANUFACTURERS;
  $man = $db->Execute($sql);
  while(!$man->EOF){
    $manufacturers_array[]=array('id'=>$man->fields['manufacturers_id'], 'text'=>$man->fields['manufacturers_name']);
    $man->MoveNext();
  }

  if(isset($_GET['action']) && $_GET['action']=='a'){
    $mID = $_POST['manufacturers_id'];
    $res = $db->Execute('SELECT p.products_id, p.products_model, p.products_status, pd.products_name, pef.manufactures_code FROM ' . TABLE_PRODUCTS . ' p, ' . TABLE_PRODUCTS_DESCRIPTION . ' pd,  ' . TABLE_PRODUCTS_EXTRA_FIELDS . ' pef WHERE manufacturers_id= '. $mID . ' AND p.products_id = pd.products_id AND p.products_id = pef.products_id');
    $products_array = array();
    while(!$res->EOF){
      $products_array[] = array('id'=>$res->fields['products_id'], 'model'=> $res->fields['products_model'], 'name'=>$res->fields['products_name'], 'man'=>$res->fields['manufactures_code'], 'status'=>$res->fields['products_status']);
      $res->MoveNext();
    }
  }
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" media="print" href="includes/stylesheet_print.css">
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
<body onLoad="init()">
<!-- header //-->
<div class="header-area">
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
</div>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE_SR; ?></td>
            <td class="pageHeading" align="right"><?php echo zen_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            <td class="smallText" align="right">
<?php
// show reset search
    echo zen_draw_form('search', FILENAME_STATS_PRODUCTS_PURCHASED, '', 'get', '', true);
    echo HEADING_TITLE_SEARCH_DETAIL_REPORTS . ' ' . zen_draw_input_field('products_filter') . zen_hide_session_id();
    if (isset($products_filter) && zen_not_null($products_filter)) {
      $products_filter = zen_db_input(zen_db_prepare_input($products_filter));
      echo '<br/ >' . TEXT_INFO_SEARCH_DETAIL_FILTER . $products_filter;
    }
    if (isset($products_filter) && zen_not_null($products_filter)) {
      echo '<br/ >' . '<a href="' . zen_href_link(FILENAME_STATS_PRODUCTS_PURCHASED, '', 'NONSSL') . '">' . zen_image_button('button_reset.gif', IMAGE_RESET) . '</a>&nbsp;&nbsp;';
    }
    echo '</form>';

// show reset search
    echo zen_draw_form('search', FILENAME_STATS_PRODUCTS_PURCHASED, '', 'get', '', true);
    echo '<br/ >' . HEADING_TITLE_SEARCH_DETAIL_REPORTS_NAME_MODEL . ' ' . zen_draw_input_field('products_filter_name_model') . zen_hide_session_id();
    if (isset($products_filter_name_model) && zen_not_null($products_filter_name_model)) {
      $products_filter_name_model = zen_db_input(zen_db_prepare_input($products_filter_name_model));
      echo '<br/ >' . TEXT_INFO_SEARCH_DETAIL_FILTER . zen_db_prepare_input($products_filter_name_model);
    }
    if (isset($products_filter_name_model) && zen_not_null($products_filter_name_model)) {
      echo '<br/ >' . '<a href="' . zen_href_link(FILENAME_STATS_PRODUCTS_PURCHASED, '', 'NONSSL') . '">' . zen_image_button('button_reset.gif', IMAGE_RESET) . '</a>&nbsp;&nbsp;';
    }
    echo '</form>';
?>
            </td>
          </tr>
        </table></td>
      </tr>
   </table>
   <?php echo zen_draw_form('select_manufacture', 'stock_reports.php','action=a');
   ?>
   <table border="0" width="80%" cellspacing="2" cellpadding="2">
    <tr>
      <td>
        Manufacture  <?php
        echo zen_draw_pull_down_menu('manufacturers_id', $manufacturers_array);
        ?>
         <?php echo zen_image_submit('button_report.gif',''); ?>
      </td>
      <td>

      </td>
    </tr>
   </table>
   <?php
   if($products_array){
     ?>
     <table border="1" width="100%" cellspacing="2" cellpadding="2">
     <tr>
     <td> pID </td>
     <td> Code</td>
     <td> Name</td>
     <td> Man. Code</td>
     <td> Status</td>
     </tr>
     <?php
     foreach($products_array as $item){
       echo '<tr>';
      foreach($item as $key => $value){
        echo '<td>';
        echo $value;
        echo '</td>';
      }
       echo '</tr>';
     }
     ?>
     </table>
     <?php
   }
   ?>
   </form>

<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>

<br />
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>