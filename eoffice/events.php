<?php
  require('includes/application_top.php');

  $action = isset($_POST['action'])?$_POST['action']:isset($_GET['action'])?$_GET['action']:'';
  $eId = isset($_POST['eID'])?$_POST['eID']:isset($_GET['eID'])?$_GET['eID']:1;

  $events_db = $db->Execute("SELECT * FROM events");
  $event_db = $db->Execute("SELECT * FROM events WHERE event_id = $eId");

  switch($action){
    case 'update':
      $sql ="UPDATE events SET
                event_name = '". $_POST['name']."',
                event_description = '" . $_POST['description']."',
                event_images = '" . $_POST['images']."',
                event_status = '" . $_POST['status']."'
             WHERE event_id = $eId LIMIT 1";
      $db->Execute($sql);
      zen_redirect('events.php?eID='.$eId);
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
<script language="javascript" src="includes/javascript/jquery-ui-1.8.16.custom.min.js"></script>
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

$(function() {
    // a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!
    $( "#dialog:ui-dialog" ).dialog( "destroy" );

    $( "#dialog-modal" ).dialog({
      height: 140,
      modal: true
    });
  });


    // -->
</script>
</head>
<body onload="init()">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<h1> Events Manager</h1>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
<tr><td width="75%">
<!-- **************  Left **************-->
<table width="100%" cellpadding="2" cellspacing="0">
<tr class="dataTableHeadingRow" valign="top">
        <th class="dataTableHeadingContent">ID</th>
        <th class="dataTableHeadingContent">Event Name</th>
        <th class="dataTableHeadingContent">Description</th>
        <th class="dataTableHeadingContent">Images</th>
        <th class="dataTableHeadingContent">Status</th>
        <th class="dataTableHeadingContent">Action</th>
      </tr>
      <?php
      while(!$events_db->EOF){
        //echo '<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link(events, 'eID=' . $events_db->fields['event_id']) . '\'">
         echo '<tr class="dataTableRow">
              <td class="dataTableContent">' . $events_db->fields['event_id'].'</td>
              <td class="dataTableContent">'.$events_db->fields['event_name'].'</td>
              <td class="dataTableContent">'.$events_db->fields['event_description'].'</td>
              <td class="dataTableContent">'.$events_db->fields['event_images'].'</td>
              <td class="dataTableContent" align="center">'.($events_db->fields['event_status']==0?zen_image(DIR_WS_IMAGES . 'icon_red_on.gif', IMAGE_ICON_STATUS_OFF):zen_image(DIR_WS_IMAGES . 'icon_green_on.gif', IMAGE_ICON_STATUS_ON)).'</td>
              <td class="dataTableContent" align="center">' . zen_image(DIR_WS_IMAGES.'icon_event_items.gif','Edit Event','','','onclick="alert(\'ee\')"'). '</td>
              </tr>';
        $events_db->MoveNext();
      }?>
      <tr><td colspan="6" align="right"><?php echo zen_image_button('button_new_event.gif','New Event');?></td></tr>
      </table>


<!-- End Left -->
</td>
<td valign="top">
<!-- ************ Right ***************-->
<table border="0" width="100%" cellpadding="2" cellspacing="0">
  <tr>
  <th class="infoBoxHeading"><?php echo $event_db->fields['event_name'];?></th>
  </tr>
  <tr>
  <td class="infoBoxContent" align="center"><br />
  <?php if($action==''){
    echo '<a href="' . zen_href_link('events', 'eID='.$event_db->fields['event_id'].'&action=edit') . '">'.  zen_image_button('button_edit.gif', IMAGE_EDIT).'</a><br /><br />';
    }elseif($action=='edit'){
      echo zen_draw_form('edit', 'events','eID='.$event_db->fields['event_id'].'&action=update') ?>
      <table>
      <tr><td>Event Name</td>
          <td><?php echo zen_draw_input_field('name', $event_db->fields['event_name']);?> </td>
      </tr>
      <tr><td>Description</td>
          <td><?php echo zen_draw_input_field('description', $event_db->fields['event_description']);?></td>
      </tr>
      <tr><td>Images</td>
          <td><?php echo zen_draw_input_field('images', $event_db->fields['event_images']);?></td>
      </tr>
      <tr><td>Status</td>
          <td><?php echo zen_draw_checkbox_field('name', $event_db->fields['event_status'], $event_db->fields['event_status']);?></td>
      </tr>
      <tr><td colspan="2" align="center"><?php echo zen_image_submit('button_update.gif', IMAGE_UPDATE);?></td></tr>
      </table>
      </form>
    <?php }
    ?>
    </td>
  </tr>
</table>

<!-- End Right -->
</td>
</tr>
</table>

<div id="dialog-modal" title="Basic modal dialog">
  <p>Adding the modal overlay screen makes the dialog look more prominent because it dims out the page content.</p>
</div>



<!-- body //-->


<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>