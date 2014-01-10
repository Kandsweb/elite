<?php
require('includes/application_top.php');
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

isset($_GET['action'])?$action= $_GET['action']:(isset($_POST['action'])?$action=$_POST['action']:$action='');
isset($_GET['id'])?$filter_id= $_GET['id']:(isset($_POST['id'])?$filter_id=$_POST['id']:$filter_id='');
if(isset($_GET['so'])){
  $sort_order = $_GET['so'];
}else{
  $sort_order = 'id';
}

switch($action){
  case 'lookup_name':
    $action ='';
    break;
  case 'delete_confirm':
    $db->Execute("DELETE FROM family_captions WHERE id = '$filter_id'  LIMIT 1");
    zen_redirect(zen_href_link('family_captions.php', 'action=lookup_name&filter_id=' . $filter_id));
    break;
  case 'update':
    $db->Execute("UPDATE family_captions SET caption = '" .$_POST['new_name'] . "' WHERE id = " . $_POST['id'] . " LIMIT 1");
    zen_redirect(zen_href_link('family_captions.php', 'action=lookup_name&filter_id=' . $filter_id));
    break;
  case 'new':
    $db->Execute("INSERT INTO family_captions (caption) VALUES ('". $_POST['new_name'] . "')");
    zen_redirect(zen_href_link('family_captions.php', 'action=lookup_name&id=' . $filter_id));
  break;
}


  $rs_values = $db->Execute("SELECT * FROM family_captions ORDER BY $sort_order");
  if($filter_id !=''){
    $res = $db->Execute("SELECT * FROM family_captions WHERE id = $filter_id");
    $value_id = $res->fields['id'];
    $value_caption = $res->fields['caption'];
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
<h1>&nbsp;&nbsp;Family Captions</h1>

<table border="0" width="100%" cellspacing="2" cellpadding="6">
  <tr><td width="60%" valign="top">
<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr class="dataTableHeadingRow">
    <td><a href="<?php echo zen_href_link('family_captions.php', 'so=id')?>">ID</a></td>
    <td align="center"><a href="<?php echo zen_href_link('family_captions.php', 'so=caption')?>">Caption</a></td>
    <td align="center">Action</td>
    <?php
    while(!$rs_values->EOF){
      echo '<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">';
      echo '<td class="dataTableContent">'.$rs_values->fields['id'].'</td>';
      echo '<td class="dataTableContent" align="center">'.$rs_values->fields['caption'].'</td>';
      echo '<td class="dataTableContent" align="center">';
      echo '<a href="' . zen_href_link('family_captions.php', '&id=' . $rs_values->fields['id']) . '&action=value_edit' . '">' . zen_image(DIR_WS_IMAGES . 'icon_edit.gif', ICON_EDIT) . '</a>' . '&nbsp; &nbsp;';
      echo '<a href="' . zen_href_link('family_captions.php', '&id=' . $rs_values->fields['id']) . '&action=value_delete' . '">' . zen_image(DIR_WS_IMAGES . 'icon_delete.gif', ICON_DELETE) . '</a>';
      echo '</td></tr>';
      $rs_values->MoveNext();
    }
    ?>
  </tr>
</table>
</td>

<td valign="top">


<?php //////////////// Side pannel ////////////////////////////////

//echo $action;

////Default
if($action == ''){
  echo zen_draw_form('delete', 'family_captions.php','','post');
  echo zen_draw_hidden_field('action', 'anew');
  echo zen_draw_hidden_field('filter_id', $filter_id);
  echo zen_draw_hidden_field('name', $value_name);
  ?>
<table width="50%" border="1">
<tr align="center">
  <td><?php echo 'Add New Caption '. zen_image_submit('button_insert.gif', 'Add a new item', 'onclick="this.form.submit();"');
            echo '&nbsp;&nbsp;&nbsp; <a href="'. zen_href_link('family_captions.php') .'>'.zen_image_button('button_cancel.gif','Cancel').'</a>';
  ?></td>
</tr>
</table>
</form>
<?php
}

////EDIT
if($action == 'value_edit'){
  echo zen_draw_form('update', 'family_captions.php','action=update&id='.$filter_id);
  echo zen_draw_hidden_field('action', 'update');
  echo zen_draw_hidden_field('id', $filter_id);?>
<table width="50%" border="1">
<tr>
  <td class="infoBoxHeading">Edit caption</td>
</tr>
<tr>
  <td>Caption:&nbsp;&nbsp;&nbsp;<?php echo zen_draw_textarea_field('new_name', 'soft', 50, '8', $value_caption);?> </td>
</tr>
<tr align="center">
  <td><?php echo zen_image_submit('button_update.gif', IMAGE_UPDATE, 'onclick="this.form.submit();"');
            echo '&nbsp;&nbsp;&nbsp; <a href="'. zen_href_link('family_captions.php', 'filter_id='.$filter_id) .'">'.zen_image_button('button_cancel.gif','Cancel delete').'</a>';?></td>

</tr>
</table>
</form>
<?php }

////ADD NEW
if($action == 'anew'){
  echo zen_draw_form('update', 'family_captions.php');
  echo zen_draw_hidden_field('action', 'new');
  //echo zen_draw_hidden_field('filter_id', $filter_id);
  ?>
<table width="50%" border="1">
<tr>
  <td class="infoBoxHeading">Add New Item</td>
</tr>
<tr>
  <td>Caption:<br /><?php echo zen_draw_textarea_field('new_name', 'soft', 30, '3', $value_name);?> </td>
</tr>
<tr align="center">
  <td><?php echo zen_image_submit('button_insert.gif', 'Insert new item', 'onclick="this.form.submit();"');
            echo '&nbsp;&nbsp;&nbsp; <a href="'. zen_href_link('family_captions.php', 'filter_id='.$filter_id) .'">'.zen_image_button('button_cancel.gif','Cancel delete').'</a>';?></td>
</tr>
</table>
</form>
<?php }

///DELETE
if($action == 'value_delete'){
  echo zen_draw_form('delete', 'family_captions.php','','post');
  echo zen_draw_hidden_field('action', 'delete_confirm');
  echo zen_draw_hidden_field('id', $filter_id);
  //echo zen_draw_hidden_field('name', $value_name);
  ?>
<table width="50%" border="1">
<tr>
  <td class="infoBoxHeading">Delete Item</td>
</tr>
<tr>
  <td>Confirm you wish to delete this item?</td>
</tr>
<tr align="center">
  <td><?php echo zen_image_submit('button_confirm.gif', 'Delete this item', 'onclick="this.form.submit();"');
            echo '&nbsp;&nbsp;&nbsp; <a href="'. zen_href_link('family_captions.php', 'filter_id='.$filter_id) .'">'.zen_image_button('button_cancel.gif','Cancel delete').'</a>';
  ?></td>
</tr>
</table>
</form>
<?php
}
?>
</td>
</tr>
</table>


<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>

<br />
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
