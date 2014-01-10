<?php
  require('includes/application_top.php');

  $action = isset($_POST['action'])?'':$_POST['action'];


  switch($action){
    case '':
      $events_db = $db->Execute("SELECT * FROM events");
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

    // -->
</script>
</head>
<body onload="init()">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<h1> Events Manager</h1>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
<tr>
<?php switch($action){
  case 'add':

    break;
  case '':
      ?>
      <td>
      <table>
      <tr class="dataTableHeadingRow">
        <th class="dataTableHeadingContent">ID</th>
        <th class="dataTableHeadingContent">Event Name</th>
        <th class="dataTableHeadingContent">Description</th>
        <th class="dataTableHeadingContent">Images</th>
        <th class="dataTableHeadingContent">Status</th>
        <th class="dataTableHeadingContent">Action</th>
      </tr>
      <?php
      while(!$events_db->EOF){
        echo '<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">
              <td class="dataTableContent">' . $events_db->fields['event_id'].'</td>
              <td class="dataTableContent">'.$events_db->fields['event_name'].'</td>
              <td class="dataTableContent">'.$events_db->fields['event_description'].'</td>
              <td class="dataTableContent">'.$events_db->fields['event_images'].'</td>
              <td class="dataTableContent" align="center">'.($events_db->fields['event_status']==0?zen_image(DIR_WS_IMAGES . 'icon_red_on.gif', IMAGE_ICON_STATUS_OFF):zen_image(DIR_WS_IMAGES . 'icon_green_on.gif', IMAGE_ICON_STATUS_ON)).'</td>
              <td class="dataTableContent" align="center">' . '</td>
              </tr>';
        $events_db->MoveNext();
      }?>
      </table>
      </td>
      <td>
        <table border="1">
          <tr><td>234</td></tr>
        </table>
      </td>
      <?php
    break;
}
?>
</tr>
</table>






<!-- body //-->


<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>