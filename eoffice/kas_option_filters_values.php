<?php
require('includes/application_top.php');
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

isset($_GET['action'])?$action= $_GET['action']:(isset($_POST['action'])?$action=$_POST['action']:$action='');
isset($_GET['filter_id'])?$filter_id= $_GET['filter_id']:(isset($_POST['filter_id'])?$filter_id=$_POST['filter_id']:$filter_id='');
isset($_GET['oID'])?$oID = $_GET['oID']:$oID='';
if(isset($_GET['so'])){
  $sort_order = 'foptions_'.$_GET['so'];
}else{
  $sort_order = 'foptions_value';
}

switch($action){
  case 'lookup_name':
    $action ='';
    break;
  case 'value_edit':
  break;
  case 'delete_confirm':
    $db->Execute("DELETE FROM filter_options_values WHERE foptions_group = '$filter_id' AND foptions_name = '" . $_POST['name'] . "' LIMIT 1");
    //echo '<div class="messageStackSuccess">'.$_POST['name'].' has been deleted</div>';
    zen_redirect(zen_href_link('kas_option_filters_values.php', 'action=lookup_name&filter_id=' . $filter_id));
    break;
  case 'update':
    $db->Execute("UPDATE filter_options_values SET foptions_value = '" .$_POST['new_value'] . "', foptions_name = '" . $_POST['new_name'] . "' WHERE foptions_group = $filter_id AND foptions_value = " . $_POST['last_value'] . " LIMIT 1");
    //echo '<div class="messageStackSuccess">'.$_POST['new_name'].' has been updated with the value of '.$_POST['new_value'].'</div>';
    zen_redirect(zen_href_link('kas_option_filters_values.php', 'action=lookup_name&filter_id=' . $filter_id));
    break;
  case 'new':
    $db->Execute("INSERT INTO filter_options_values (foptions_group, foptions_name, foptions_value) VALUES (". $_POST['filter_id'] . ", '" .$_POST['new_name'] . "', ".  $_POST['new_value'] .')');
    //echo '<div class="messageStackSuccess">New item '.$_POST['new_name'].' has been created with the value of '.$_POST['new_value'] . '</div>';
    zen_redirect(zen_href_link('kas_option_filters_values.php', 'action=lookup_name&filter_id=' . $filter_id));
  break;
}

if($filter_id !=''){
  $rs_values = $db->Execute("SELECT * FROM filter_options_values WHERE foptions_group = $filter_id ORDER BY $sort_order");
  if($oID !=''){
    $res = $db->Execute("SELECT * FROM filter_options_values WHERE foptions_group = $filter_id AND foptions_value = $oID");
    $value_name = $res->fields['foptions_name'];
    $value_value = $res->fields['foptions_value'];
  }
}

//Build values for drop down
$res =  $db->Execute("SELECT * FROM filter_options_names");
$names[] = array('id'=>0, 'text'=>'Select Filter');
while(!$res->EOF){
  $names[] = array('id'=>$res->fields['foptions_id'], 'text'=>$res->fields['foptions_name']);
  $res->MoveNext();
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
<h1>&nbsp;&nbsp;Filter Options Values</h1>
<?php echo zen_draw_form('option_name', 'kas_option_filters_values.php','','get')?>
<table border="0" width="100%" cellspacing="2" cellpadding="5">
  <tr>
    <td>Filter Option Name: <?php
      echo zen_draw_hidden_field('action', 'lookup_name');
      echo zen_draw_pull_down_menu('filter_id',$names, $filter_id ,'onchange="this.form.submit();"');?>
    </td>
  </tr>

</table>
<br />
</form>
<?php if($filter_id !=''){?>
<table border="0" width="100%" cellspacing="2" cellpadding="6">
  <tr><td width="60%">
<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr class="dataTableHeadingRow">
    <td><a href="<?php echo zen_href_link('kas_option_filters_values.php', 'action=lookup_name&so=name&filter_id=' . $filter_id)?>">Name</a></td>
    <td align="center"><a href="<?php echo zen_href_link('kas_option_filters_values.php', 'action=lookup_name&so=value&filter_id=' . $filter_id)?>">Value</a></td>
    <td align="center">Action</td>
    <?php
    while(!$rs_values->EOF){
      echo '<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">';
      echo '<td class="dataTableContent">'.$rs_values->fields['foptions_name'].'</td>';
      echo '<td class="dataTableContent" align="center">'.$rs_values->fields['foptions_value'].'</td>';
      echo '<td class="dataTableContent" align="center">';
      echo '<a href="' . zen_href_link('kas_option_filters_values', '&filter_id=' . $filter_id . '&oID=' . $rs_values->fields['foptions_value']) . '&action=value_edit' . '">' . zen_image(DIR_WS_IMAGES . 'icon_edit.gif', ICON_EDIT) . '</a>' . '&nbsp; &nbsp;';
      echo '<a href="' . zen_href_link('kas_option_filters_values', '&filter_id=' . $filter_id . '&oID=' . $rs_values->fields['foptions_value']) . '&action=value_delete' . '">' . zen_image(DIR_WS_IMAGES . 'icon_delete.gif', ICON_DELETE) . '</a>';
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
  echo zen_draw_form('delete', 'kas_option_filters_values.php','','post');
  echo zen_draw_hidden_field('action', 'anew');
  echo zen_draw_hidden_field('filter_id', $filter_id);
  echo zen_draw_hidden_field('name', $value_name);
  ?>
<table width="50%" border="1">
<tr align="center">
  <td><?php echo 'Add New Item '. zen_image_submit('button_insert.gif', 'Add a new item', 'onclick="this.form.submit();"');
           // echo '&nbsp;&nbsp;&nbsp; <a href="'. zen_href_link('kas_option_filters_values.php', 'filter_id='.$filter_id) .'>'.zen_image_button('button_cancel.gif','Cancel delete').'</a>';
  ?></td>
</tr>
</table>
</form>
<?php
}

////EDIT
if($action == 'value_edit'){
  echo zen_draw_form('update', 'kas_option_filters_values.php','action=update&filter_id='.$filter_id.'&oID='.$oID,'post');
  echo zen_draw_hidden_field('action', 'update');
  echo zen_draw_hidden_field('filter_id', $filter_id);
  echo zen_draw_hidden_field('oID', $oID);
  echo zen_draw_hidden_field('last_value', $value_value);?>
<table width="50%" border="1">
<tr>
  <td class="infoBoxHeading">Edit value</td>
</tr>
<tr>
  <td>Name:&nbsp;&nbsp;&nbsp;<?php echo zen_draw_input_field('new_name', $value_name);?> </td>
</tr>
<tr>
  <td>Value:&nbsp;&nbsp;&nbsp;<?php echo zen_draw_input_field('new_value', $value_value);?> </td>
</tr>
<tr align="center">
  <td><?php echo zen_image_submit('button_update.gif', IMAGE_UPDATE, 'onclick="this.form.submit();"');?></td>
</tr>
</table>
</form>
<?php }

////ADD NEW
if($action == 'anew'){
  echo zen_draw_form('update', 'kas_option_filters_values.php','action=new&filter_id='.$filter_id,'post');
  echo zen_draw_hidden_field('action', 'new');
  echo zen_draw_hidden_field('filter_id', $filter_id);
  ?>
<table width="50%" border="1">
<tr>
  <td class="infoBoxHeading">Add New Item</td>
</tr>
<tr>
  <td>Name:&nbsp;&nbsp;&nbsp;<?php echo zen_draw_input_field('new_name', $value_name);?> </td>
</tr>
<tr>
  <td>Value:&nbsp;&nbsp;&nbsp;<?php echo zen_draw_input_field('new_value', $value_value);?> </td>
</tr>
<tr align="center">
  <td><?php echo zen_image_submit('button_insert.gif', 'Insert new item', 'onclick="this.form.submit();"');?></td>
</tr>
</table>
</form>
<?php }

///DELETE
if($action == 'value_delete'){
  echo zen_draw_form('delete', 'kas_option_filters_values.php','','post');
  echo zen_draw_hidden_field('action', 'delete_confirm');
  echo zen_draw_hidden_field('filter_id', $filter_id);
  echo zen_draw_hidden_field('name', $value_name);
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
            echo '&nbsp;&nbsp;&nbsp; <a href="'. zen_href_link('kas_option_filters_values.php', 'filter_id='.$filter_id) .'>'.zen_image_button('button_cancel.gif','Cancel delete').'</a>';
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

<?php } ?>

<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>

<br />
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
