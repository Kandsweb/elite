<?php
  require('includes/application_top.php');

  $action = NULL;
  if($_GET['action']!= NULL){
      $action = $_GET['action'];
  }

  switch ($action){
    case NULL:
        break;
    case 'delete_promotion_item':
        $db->Execute("UPDATE ".TABLE_PRODUCTS_EXTRA_FIELDS." SET now_price =0 WHERE products_id = ". $_GET['pID']." LIMIT 1");
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

<!-- body //-->
<table border="0" cellpadding="3px">
<?php
$sql = "SELECT p.products_id, p.products_model, pd.products_name, pef.now_price FROM ".TABLE_PRODUCTS_EXTRA_FIELDS." pef INNER JOIN ".TABLE_PRODUCTS." p ON p.products_id = pef.products_id INNER JOIN ".TABLE_PRODUCTS_DESCRIPTION." pd ON pd.products_id = pef.products_id WHERE pef.now_price > 0";
$promotions = $db->Execute($sql);

while(!$promotions->EOF){
  echo '<tr><td>'.$promotions->fields['products_id'].'</td><td>'.$promotions->fields['products_model'].'</td><td>'.$promotions->fields['products_name'].'</td><td>'.$promotions->fields['now_price'].'</td>
  <td>';
 echo '<a href="' . zen_href_link('promotions.php', 'pID=' . $promotions->fields['products_id'] . '&action=delete_promotion_item') . '">' . zen_image(DIR_WS_IMAGES . 'icon_delete.gif', ICON_DELETE) . '</a>';
  echo '</td>
  </tr>';

  $promotions->MoveNext();
}
?>
</table>
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>