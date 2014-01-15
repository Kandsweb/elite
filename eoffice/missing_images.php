<?php
/**
 * @package admin
 * @copyright Copyright 2003-2009 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: stats_products_purchased.php 15014 2009-12-01 21:24:50Z drbyte $
 */
 define('KAS_DEBUG',false);

  require('includes/application_top.php');

$sql = "SELECT DISTINCT
  p.products_id,
  p.products_model,
  p.products_image,
  pd.products_name
FROM
  products_to_categories p2c
  INNER JOIN products_description pd
    ON pd.products_id = p2c.products_id
  INNER JOIN products p
    ON p.products_id = p2c.products_id
WHERE
  p.products_status = 1
";

/*$sql = "SELECT DISTINCT
  p.products_id,
  p.products_model,
  p.products_image,
  pd.products_name
FROM
  products_to_categories p2c
  INNER JOIN products_description pd
    ON pd.products_id = p2c.products_id
  INNER JOIN products p
    ON p.products_id = p2c.products_id
  INNER JOIN product_extra_fields pef
    ON pef.products_id = p2c.products_id
WHERE
  p.products_status = 1
";*/

$res = $db->Execute($sql);

$num_of_fields = sizeof($res->fields);
$num_of_rows = $res->RecordCount();


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
            <td  class="pageHeading">Missing Images</td>
          </tr>
          <tr><td>
<?php
//Table headings
echo "<table border='1'><tr><td></td>";
  foreach($res->fields as $key => $value){
    echo "<td>" . $key . '</td>';
  }
  echo "<td> xRef </td>";
echo "</tr>";
/////////////////////////////////////////////////
$f_array = array();
for ($r = 0; $r < $num_of_rows; $r++){
  foreach($res->fields as $key => $value){
    $f_array[$r][] = $value;
  }
  $res->MoveNext();
}
$count=0;
for ($r = 0; $r < sizeof($f_array); $r++){
  if(!file_exists('../images/'.$f_array[$r][2])){
 		if(KAS_DEBUG)echo '../images/'.$f_array[$r][2].' -- '.!file_exists('../images/'.$f_array[$r][2]).'<br/>';
    $xref = get_image_xref($f_array[$r][2]);
    if(!file_exists('../images/' . $xref)){
	 		 if(KAS_DEBUG)echo 'S2../images/' . $xref.' -== '.!file_exists('../images/' . $xref).'<br/>';
       $count++;
       echo '<tr>';
       echo '<td>' . $count . '</td>';
       echo '<td>' . $f_array[$r][0] . '</td>';
       echo '<td>' . $f_array[$r][1] . '</td>';
       echo '<td>' . $f_array[$r][2] . '</td>';
       echo '<td>' . $f_array[$r][3] . '</td>';
       echo '<td>' . $xref . '</td>';
       //echo '<td>' . $f_array[$r][5] . '</td>';
       echo '</tr>';
    }
  }
}
///////////////////////////////////////////////////
echo "</table>";
?>
            </td>
          </tr>
        </table></td>
      </tr>
   </table>
