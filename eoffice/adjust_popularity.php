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
    case 'cc':  //Change Category
      if(isset($_GET['c']))$_GET['new_category_id1'] = $_GET['c'];
      if(isset($_GET['new_category_id1']) && $_GET['new_category_id1'] != -1) $cCat = $_GET['new_category_id1'];
      if(isset($_GET['new_category_id2']) && $_GET['new_category_id2'] != -1) $cCat = $_GET['new_category_id2'];
      $sub_cats_array = array();
      kas_get_subcategories($sub_cats_array, $cCat);
      $sub_cats = implode(',',$sub_cats_array);
      if($sub_cats == '')$sub_cats = $cCat;
      $category_name = zen_output_generated_category_path($cCat);

     $products_query_raw = "SELECT DISTINCT  p.products_id, p.products_model, pd.products_viewed
                           FROM
                            products_to_categories p2c
                             JOIN products_description pd
                              ON pd.products_id = p2c.products_id
                             JOIN products p
                              ON p.products_id = p2c.products_id
                           WHERE
                            p.products_status = 1 AND p2c.categories_id IN($sub_cats)
                           GROUP BY LEFT(p.products_model,8)
                            ORDER BY
                             pd.products_viewed DESC";

      //if (isset($_GET['page']) && ($_GET['page'] > 1)) $rows = $_GET['page'] * MAX_DISPLAY_SEARCH_RESULTS_REPORTS - MAX_DISPLAY_SEARCH_RESULTS_REPORTS;
      if (isset($_GET['page']) && ($_GET['page'] > 1)) $rows = $_GET['page'] * 50 - 50;
      //$products_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS_REPORTS, $products_query_raw, $products_query_numrows);
      $products_split = new splitPageResults($_GET['page'], 50, $products_query_raw, $products_query_numrows);
      $products = $db->Execute($products_query_raw);
      break;

    case 'set':
      if(isset($_GET['pid'])&&isset($_GET['nv'])){
          $sql = "SELECT * FROM " . TABLE_PRODUCTS_EXTRA_FIELDS . " WHERE products_id = " . $_GET['pid'];
          $res = $db->Execute($sql);
          if($res->EOF){
            //insert
          }else{
            //update
            $sql = "UPDATE " . TABLE_PRODUCTS_EXTRA_FIELDS . " SET product_priority = '1' WHERE products_id = " . $_GET['pid'] . " LIMIT 1";
            $res = $db->Execute($sql);
          }
          //now do offset
          $org_value = get_viewed_actual($_GET['pid']);
          $new_offset = $_GET['nv'] - $org_value;
          $sql = "SELECT viewed_offset FROM products_viewed_adjustment WHERE products_id = " . $_GET['pid'];
          $res = $db->Execute($sql);
          if($res->EOF){
            //not existing entry so add now
            $db->Execute("INSERT INTO products_viewed_adjustment (products_id, viewed_offset) VALUES (" . $_GET['pid'] . ", $new_offset)");
          }else{
            //existing entry, do update
            $db->Execute("UPDATE products_viewed_adjustment SET viewed_offset = $new_offset WHERE products_id = " . $_GET['pid']);
          }
          $db->Execute("UPDATE products_description SET products_viewed = " . $_GET['nv'] . " WHERE products_id = " . $_GET['pid']);
      }
      zen_redirect(zen_href_link('adjust_popularity.php', 'action=cc&new_category_id1=' . $_GET['c']));
      break;

    case 'reset':
      $offset = get_viewed_offset($_GET['pid']);
      $current_viewed = get_viewed_count($_GET['pid']);
      $new_count = $current_viewed - $offset;
      update_viewed_count($_GET['pid'], $new_count);
      update_viewed_offset($_GET['pid'], 0);
      zen_redirect(zen_href_link('adjust_popularity.php', 'action=cc&new_category_id1=' . $_GET['c']));
      break;

    case 'remove':
      //reset count
      $offset = get_viewed_offset($_GET['pid']);
      $current_viewed = get_viewed_count($_GET['pid']);
      $new_count = $current_viewed - $offset;
      update_viewed_count($_GET['pid'], $new_count);
      //remove from products_extra_fields
      $db->Execute("UPDATE " . TABLE_PRODUCTS_EXTRA_FIELDS . " SET product_priority = NULL WHERE products_id = " . $_GET['pid']);
      //remove from products_viewed_adjustment table
      $db->Execute("DELETE FROM products_viewed_adjustment WHERE products_id = " . $_GET['pid']);
      zen_redirect(zen_href_link('adjust_popularity.php', 'action=cc&new_category_id1=' . $_GET['c']));
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
<script language="javascript" src="includes/javascript/jscript_a.jquery-1.5.2.min.js"></script>
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

  function vUpdate(pid, vOld){
    vNew = $('#newValue_' + pid).val();
    if(vOld == vNew){
      alert("You have not made any changes");
      return;
    }
    var lc = "<?php echo zen_href_link('adjust_popularity.php', 'page=' . $_GET['page'] . '&action=set&c=' . $cCat . '&pid=');?>";
    lc = lc +  pid + "&nv=" + vNew;
    //alert(lc);
    document.location.href = lc;
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

            ?></td>
            <td class="pageHeading" align="right"><?php echo zen_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr><td>
        <?php if(isset($cCat))
        echo '<a href="adjust_popularity.php">'.zen_image_button('button_back.gif','Cancel','').'</a>'; ?>
      </td></tr>
      <tr>
      </table>
    </td>
  </tr>
  </table>


  <?php /////////////////// Display the cat selector ////////////////////////////////////////////////
  if($action==''){?>
  <table border="0" width="50%">
    <tr><td>Select category to adjust product popularity</td></tr>
    <tr><td><?php
    echo zen_draw_form('category', 'adjust_popularity.php', '' , 'get');
    echo zen_draw_hidden_field('action', 'cc');
    $cat_array = zen_get_category_tree(1, '', '0', '', '', true);
    array_unshift($cat_array, array(id=>-1, text=>'Select LIGHTING Category'));
    echo zen_draw_pull_down_menu('new_category_id1', $cat_array, $new_category_id, 'onChange="this.form.submit();"') . ' or ';
    $cat_array = zen_get_category_tree(2, '', '0', '', '', true);
    array_unshift($cat_array, array(id=>-1, text=>'Select INTERIORS Category'));
    echo zen_draw_pull_down_menu('new_category_id2', $cat_array, $new_category_id, 'onChange="this.form.submit();"');
    echo '</form>';
    ?></td></tr>
  </table>
  <?php }


  //////////////////////////// Display the category list /////////////////////////
  if($action == 'cc'){?>
  <h3><?php echo $category_name ?></h3>
  <table border="0" width="95%" cellpadding="0">
  <tr class="dataTableHeadingRow">
    <th class="dataTableHeadingContent" align="center" width="5%">ID</th>
    <th class="dataTableHeadingContent" align="center" width="4%">Base</th>
    <th class="dataTableHeadingContent" align="center" width="10%">Code</th>
    <th class="dataTableHeadingContent" align="left" width="30%">Image Name</th>
    <th class="dataTableHeadingContent" align="left" width="30%">Name</th>
    <th class="dataTableHeadingContent" align="center" width="5%">Actual</th>
    <th class="dataTableHeadingContent" align="center" width="4%">Offset</th>
    <th class="dataTableHeadingContent" align="center" width="4%">New Value</th>
    <th class="dataTableHeadingContent" align="center" width="18%">Action</th>
  </tr>
  <?php while(!$products->EOF){
    $base_code = substr($products->fields['products_model'],0,8);
    $on_display_code = $products->fields['products_model'];
    $family_code = substr($products->fields['products_model'],8);
    //Family split line
    if($last_basecode != $base_code){
      if($last_basecode != ''){
        echo '<tr><td colspan="11">' . zen_draw_separator('pixel_black.gif','100%', '1') . '</tr>';
        $flag = true;
      }else $flag=true;
    }else $flag = false;
    $last_basecode = $base_code;
    $family = $db->Execute("SELECT DISTINCT p.products_id, p.products_model, pd.products_name, pd.products_viewed, pva.viewed_offset, p.products_image
                           FROM
                            products_to_categories p2c
                             JOIN products_description pd
                              ON pd.products_id = p2c.products_id
                             JOIN products p
                              ON p.products_id = p2c.products_id
                             JOIN product_extra_fields pef
                              ON pef.products_id = p2c.products_id
                             LEFT JOIN products_viewed_adjustment pva
                              ON pva.products_id = p2c.products_id
                           WHERE
                            p.products_status = 1 AND p.products_model LIKE '" . $base_code . "%'
                            ORDER BY
                             pd.products_viewed DESC");




    while(!$family->EOF){


    echo '<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">';
    echo '<td class="dataTableContent" align="center">' . $family->fields['products_id'] . '</td>';
    echo '<td class="dataTableContent" align="center">';
          echo ($on_display_code == $family->fields['products_model']?zen_image(DIR_WS_IMAGES . 'icon_status_green.gif', 'Default family item'):'&nbsp;').'&nbsp;';
          if (is_priority_item($family->fields['products_id']))
            echo zen_image(DIR_WS_IMAGES . 'icon_status_red.gif', 'Poriority family item');
    echo '<td class="dataTableContent" align="center">' . $family->fields['products_model'] . '</td>';
    echo '<td class="dataTableContent" align="left">' . $family->fields['products_image'] . '</td>';
    echo '<td class="dataTableContent">' . $family->fields['products_name'] . '</td>';
    echo '<td class="dataTableContent" align="center">' . ($family->fields['products_viewed']- $family->fields['viewed_offset']) . '</td>';
    echo '<td class="dataTableContent" align="center">' . $family->fields['viewed_offset'] . '</td>';
    echo '<td class="dataTableContent" align="center">' . zen_draw_input_field('new_value_'.$family->fields['products_id'], $family->fields['products_viewed'],'size=5 onkeydown="if (event.keyCode == 13)vUpdate(\'' . $family->fields['products_id'] . '\',\'' . $family->fields['products_viewed'] . '\')" id="newValue_'.$family->fields['products_id'].'"') .zen_draw_hidden_field('old_value_'.$family->fields['products_id'], $family->fields['products_viewed'])  . '</td>';
            echo '</td>';
    //---------------
    echo '<td class="dataTableContent" align="left">';
    echo '&nbsp;&nbsp;' . zen_image(DIR_WS_IMAGES . 'icon_save.gif', 'Update','','','onclick="javascript:vUpdate(\'' . $family->fields['products_id'] . '\',\'' . $family->fields['products_viewed'] . '\')"');
    echo '&nbsp;&nbsp;&nbsp';
    //--------------
      if($family->fields['viewed_offset'] !=NULL &&  $family->fields['viewed_offset'] > 0){
        echo '<a href="' . zen_href_link('adjust_popularity.php', 'action=reset&pid='.$family->fields['products_id']). '&c=' . $cCat . '">' . zen_image(DIR_WS_IMAGES . 'icon_reset.gif', 'Reset') . '</a>';
      }else{
         echo '&nbsp;&nbsp;&nbsp;';
      }
      echo '&nbsp;&nbsp;&nbsp;';
    //---------------
      if($family->fields['viewed_offset'] !=NULL){
        echo '<a href="' . zen_href_link('adjust_popularity.php', 'action=remove&pid='.$family->fields['products_id']). '&c=' . $cCat . '">' . zen_image(DIR_WS_IMAGES . 'icon_delete.gif', 'Remove') . '</a>';
      }
    echo '&nbsp;&nbsp;&nbsp;&nbsp;';
    echo '</td>';
    //--------------
    echo '</tr>';


    $family->MoveNext();
    }
    $products->MoveNext();
   } ?>

  </table>
  <table border="0" width="95%" cellspacing="0" cellpadding="2">
    <tr>
      <td class="smallText" valign="top"><?php echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS_REPORTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
      <td class="smallText" align="right"><?php echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_SEARCH_RESULTS_REPORTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
    </tr>
    <tr><td align="right" colspan="2">
      <?php if(isset($cCat))
        echo '<a href="adjust_popularity.php">'.zen_image_button('button_back.gif','Cancel','').'</a>'; ?>
    </td></tr>
  </table>
  <?php } ?>

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>