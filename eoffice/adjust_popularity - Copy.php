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

  switch ($action){
    case 'add': //insert
      $action = '';
      if(isset($_POST['additem'])){
        $array = $_POST['additem'];
        foreach($array as $item){
          $sql = "SELECT * FROM " . TABLE_PRODUCTS_EXTRA_FIELDS . " WHERE products_id = $item";
          $res = $db->Execute($sql);
          if($res->EOF){
            //insert
          }else{
            //update
            $sql = "UPDATE " . TABLE_PRODUCTS_EXTRA_FIELDS . " SET product_priority = '1' WHERE products_id = $item LIMIT 1";
            $res = $db->Execute($sql);
          }
        }
      }
      zen_redirect('adjust_popularity.php');
      break;

    case 'new':
      $catID = isset($_GET['new_category_id'])?$_GET['new_category_id']:'';
      $view_category = zen_output_generated_category_path($catID);
      $products_array = array();
      $products_array = zen_get_categories_products_list($catID,true,false);
      //zen_redirect('adjust_popularity.php');
      break;

    case 'update':
      $action = '';
      foreach($_POST['new_value'] as $id => $new_value){
        if($_POST['old_value'][$id] != $new_value){
          $new_offset = $new_value - $_POST['old_value'][$id];
          $last_value = $_POST['old_value'][$id];
          $sql = "SELECT viewed_offset FROM products_viewed_adjustment WHERE products_id = $id";
          $res = $db->Execute($sql);
          if($res->EOF){
            //not existing entry so add now
            $db->Execute("INSERT INTO products_viewed_adjustment (products_id, viewed_offset) VALUES ($id, $new_offset)");
          }else{
            //already an offset so calculate the new offset. First get any existing offset
            $current_offset = $res->fields['viewed_offset'];
            $final_offset = $current_offset + $new_offset;
            $db->Execute("UPDATE products_viewed_adjustment SET viewed_offset = $final_offset WHERE products_id = $id");
          }
          //$db->Execute("UPDATE product_extra_fields SET products_viewed = 1 WHERE products_id = $id");
          $db->Execute("UPDATE products_description SET products_viewed = $new_value WHERE products_id = $id");
        }
      }
      zen_redirect('adjust_popularity.php');
      break;

    case 'reset':
      $action = '';
      $offset = get_viewed_offset($_GET['pid']);
      $current_viewed = get_viewed_count($_GET['pid']);
      $new_count = $current_viewed - $offset;
      update_viewed_count($_GET['pid'], $new_count);
      update_viewed_offset($_GET['pid'], 0);
      zen_redirect('adjust_popularity.php');
      break;

    case 'remove':
      $action = '';
      //reset count
      $offset = get_viewed_offset($_GET['pid']);
      $current_viewed = get_viewed_count($_GET['pid']);
      $new_count = $current_viewed - $offset;
      update_viewed_count($_GET['pid'], $new_count);
      //remove from products_extra_fields
      $db->Execute("UPDATE " . TABLE_PRODUCTS_EXTRA_FIELDS . " SET product_priority = NULL WHERE products_id = " . $_GET['pid']);
      //remove from products_viewed_adjustment table
      $db->Execute("DELETE FROM products_viewed_adjustment WHERE products_id = " . $_GET['pid']);
      zen_redirect('adjust_popularity.php');
      break;
  }

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
<table border="0" width="100%" cellspacing="2" cellpadding="2"><?php //Whole pg table ?>
  <tr> <td width="100%" valign="top">
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE;
            if($action == ''){
              echo zen_draw_separator('pixel_trans.gif', 150, 1);
              echo '<a href="' . zen_href_link('adjust_popularity.php','action=new') . '">' . zen_image_button('button_insert.gif','').'</a>';
              echo zen_draw_separator('pixel_trans.gif', '800', '1');
              echo zen_image_button('button_update.gif','Update values', 'onclick="javascript:document.update.submit();"');
            }
            ?></td>
            <td class="pageHeading" align="right"><?php echo zen_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>

  <?php ///////////////////////////// Add new item //////////////////////////////////////////////

  if($action == 'new'){?>
    <td width="70%"><?php
    echo zen_draw_separator('pixel_trans.gif', 25, 1);
    echo zen_draw_form('category', 'adjust_popularity.php', '' , 'get');
    echo zen_draw_hidden_field('action', 'new');
    $cat_array = zen_get_category_tree('', '', '0', '', '', true);
    array_unshift($cat_array, array(id=>-1, text=>'* * Select Category * *'));
    echo zen_draw_pull_down_menu('new_category_id', $cat_array, $new_category_id, 'onChange="this.form.submit();"');
    echo '</form>';
    $view_category = zen_output_generated_category_path($_GET['new_category_id']);
    //echo zen_draw_products_pull_down_categories('catList','','',true,true);
    ?></td>
    </tr>
    <tr><td>&nbsp;</td></tr>
    <?php if(!empty($products_array)){
      ?>
      <tr>
      <td>
      <?php
      echo zen_draw_form('items', 'adjust_popularity.php', 'action=add' , 'post');
      ?>
      <table width="80%" cellspacing="0" cellpadding="0">
      <tr><td colspan="5"><b><?php echo $view_category; ?></b></td></tr>
      <tr><td colspan="5">&nbsp;</td></tr>
      <tr class="dataTableHeadingRow">
        <th class="dataTableHeadingContent" align="center" width="8%">ID</th>
        <th class="dataTableHeadingContent" align="left" width="67%">Name</th>
        <th class="dataTableHeadingContent" align="center" width="12%">Code</th>
        <th class="dataTableHeadingContent" align="center" width="5%">Actual</th>
        <th class="dataTableHeadingContent" align="center" width="4%"></th>
        <th class="dataTableHeadingContent" align="center" width="4%"></th>
      </tr>
      <?php
        $last_basecode = '';
        foreach($products_array as $pID){
          $flag=false;
          $products_sql = 'SELECT  p.products_id, p.products_model, p.products_image, products_description.products_name, products_description.products_viewed FROM ' . TABLE_PRODUCTS . ' p INNER JOIN ' . TABLE_PRODUCTS_DESCRIPTION . ' products_description ON p.products_id = products_description.products_id WHERE p.products_id = ' .  $pID .' AND products_description.language_id = ' . (int)$_SESSION['languages_id'] . ' ORDER BY p.products_model';
          //echo $products_sql;
          $products = $db->Execute($products_sql);
          $base_code = substr($products->fields['products_model'],0,8);
          $family_code = substr($products->fields['products_model'],8);
          //Family split line
          if($last_basecode != $base_code){
            if($last_basecode != ''){
              echo '<tr><td colspan="6">' . zen_draw_separator('pixel_black.gif','100%', '1') . '</tr>';
              $flag = true;
            }else $flag=true;
          }
          $last_basecode = $base_code;
          ?>
          <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">
          <td class="dataTableContent" align="center"><?php
          echo $products->fields['products_id'];
          ?> </td>
          <td class="dataTableContent" align="left"><?php
          echo $products->fields['products_name'];
          ?> </td>
          <td class="dataTableContent" align="center"><?php
          echo $base_code . ' - ' . $family_code;
          ?> </td>
          <td class="dataTableContent" align="center"><?php
          echo $products->fields['products_viewed'];
          ?> </td>
          <td class="dataTableContent" align="center"><?php
          echo zen_draw_checkbox_field('additem[]',$products->fields['products_id']);
          ?></td>
          <td class="dataTableContent" align="center"><?php
          echo ($flag?zen_image(DIR_WS_IMAGES . 'icon_status_green.gif', 'Default family item'):'&nbsp;').'&nbsp;';
          if (is_priority_item($products->fields['products_id']))
            echo zen_image(DIR_WS_IMAGES . 'icon_status_red.gif', 'Poriority family item');
          ?> </td>
          </tr>
          <?php
        }
        //space and buttons at bottom
        echo '<tr><td colspan=5>&nbsp;</td></tr>';
        echo '<tr><td colspan=2>&nbsp;</td><td>&nbsp;&nbsp;';
        echo '<a href="adjust_popularity.php">'.zen_image_button('button_cancel.gif','Cancel','').'</a>';
        echo '</td>';
        echo '<td>&nbsp;</td><td>';
        echo zen_image_submit('button_insert.gif','Insert selected');
        echo '</td>';
        echo '</tr>';
        echo '</form>';
      ?>
      </table>
      </td>
      </tr><tr>
      <?php }
    }elseif($action == ''){


    //////////////////////////////// Default action - Show list ////////////////////////////////////// ?>


<!-- body_text //-->

        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
        <?php echo zen_draw_form('update', 'adjust_popularity.php', 'action=update' , 'post'); ?>
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_NUMBER; ?></td>
                <td class="dataTableHeadingContent" align="center">Code</td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
                <td class="dataTableHeadingContent">Actual</td>
                <td class="dataTableHeadingContent">Offset</td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_VIEWED; ?>&nbsp;</td>
                <td class="dataTableHeadingContent" align="center">&nbsp;</td>
              </tr>
<?php
  if (isset($_GET['page']) && ($_GET['page'] > 1)) $rows = $_GET['page'] * MAX_DISPLAY_SEARCH_RESULTS_REPORTS - MAX_DISPLAY_SEARCH_RESULTS_REPORTS;
  $rows = 0;
  $products_query_raw = "select p.products_id, p.products_model, p.master_categories_id, pd.products_name, pd.products_viewed, l.name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_LANGUAGES . " l, " . TABLE_PRODUCTS_EXTRA_FIELDS . " pef where p.products_id = pd.products_id and pef.products_id = p.products_id and l.languages_id = pd.language_id and pef.product_priority IS NOT NULL  order by p.master_categories_id, pd.products_viewed DESC";
  $products_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS_REPORTS, $products_query_raw, $products_query_numrows);
  $products = $db->Execute($products_query_raw);
  $last_cat = array();

  while (!$products->EOF) {

// only show low stock on products that can be added to the cart
    if ($zc_products->get_allow_add_to_cart($products->fields['products_id']) == 'Y') {
      $rows++;

      if (strlen($rows) < 2) {
        $rows = '0' . $rows;
      }
      $cPath = zen_get_product_path($products->fields['products_id']);
      $viewed_offset = get_viewed_offset($products->fields['products_id']);
      $actual_viewed = $products->fields['products_viewed'] - $viewed_offset;

      $current_cat = zen_generate_category_path($products->fields['master_categories_id']);

      /// Output category path if diff from last item's
      if($last_cat != $products->fields['master_categories_id']){
        $last_cat = $products->fields['master_categories_id']; ?>
      <tr><td colspan="6"><b><i><?php //echo $output;
      echo zen_output_generated_category_path($products->fields['master_categories_id']);?>
      </i></b>`</td></tr>
      <?php } ?>

      <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">
        <td class="dataTableContent" align="center"><?php echo $products->fields['products_id']; ?></td>
        <td class="dataTableContent" align="center"><?php echo $products->fields['products_model']; ?></td>
        <td class="dataTableContent"><?php echo '<a href="' . zen_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $products->fields['products_id']) . '">' . $products->fields['products_name'] . '</a> '; ?></td>
        <td class="dataTableContent"><?php echo $actual_viewed; ?></td>
        <td class="dataTableContent"><?php echo $viewed_offset; ?></td>
        <td class="dataTableContent" align="center"><?php echo zen_draw_input_field('new_value['.$products->fields['products_id'].']', $products->fields['products_viewed'],'size=5');
        echo zen_draw_hidden_field('old_value['.$products->fields['products_id'].']', $products->fields['products_viewed']); ?>&nbsp;</td>
        <td class="dataTableContent"><?php echo '<a href="' . zen_href_link('adjust_popularity.php', 'action=reset&pid='.$products->fields['products_id']) . '">' . zen_image(DIR_WS_IMAGES . 'icon_reset.gif', 'Reset') . '</a>&nbsp;&nbsp;&nbsp;';
        echo '<a href="' . zen_href_link('adjust_popularity.php', 'action=remove&pid='.$products->fields['products_id']) . '">' . zen_image(DIR_WS_IMAGES . 'icon_green_off.gif', 'Remove') . '</a>';
        ?></td>
      </tr>
<?php
    }
    $products->MoveNext();
  }
  echo '</form>'
  ?>
            </table></td>
          </tr>
          <tr>
            <td colspan="3"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td class="smallText" valign="top"><?php echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS_REPORTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
                <td class="smallText" align="right"><?php echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS_REPORTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
    </table>
<!-- body_text_eof //-->
  <?php ///////////////////////////////////////////////////////////////////////////////////////////////
  }?>
  </td>
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>