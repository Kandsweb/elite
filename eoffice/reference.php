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
// 2004-10-05 / Benjamin Bellamy
//


require('includes/application_top.php');

$search = (isset($_GET['search']) ? $_GET['search'] : '');

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
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="init()">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>


<?php
	// The request that returns the configuration keys:
	// pour la deuxième partie (products_type_layout) on limite au type 1 = general
	if (zen_not_null($search))
	{
		$requete="(select configuration_id, c.configuration_group_id as configuration_group_id, configuration_group_title, configuration_title, configuration_description, configuration_value, 'conf' as src from " . TABLE_CONFIGURATION . " as c, " . TABLE_CONFIGURATION_GROUP . " as g where c.configuration_group_id=g.configuration_group_id and (configuration_title like '%" . $search . "%' or configuration_description like '%" . $search . "%') order by configuration_title, configuration_group_id)
		union
		(select configuration_id, p.product_type_id as configuration_group_id, type_name as configuration_group_title, configuration_title, configuration_description, configuration_value, 'type' as src from ". TABLE_PRODUCT_TYPE_LAYOUT . " as p, " . TABLE_PRODUCT_TYPES . " as t where p.product_type_id=t.type_id and t.type_id=1 and (configuration_title like '%" . $search . "%' or configuration_description like '%" . $search . "%') order by configuration_title, configuration_group_id)";
	}
	else
	{
		$requete="(select configuration_id, c.configuration_group_id as configuration_group_id, configuration_group_title, configuration_title, configuration_description, configuration_value, 'conf' as src from " . TABLE_CONFIGURATION . " as c, " . TABLE_CONFIGURATION_GROUP . " as g where c.configuration_group_id=g.configuration_group_id order by configuration_title, configuration_group_id)
		union
		(select configuration_id, p.product_type_id as configuration_group_id, type_name as configuration_group_title, configuration_title, configuration_description, configuration_value, 'type' as src from ". TABLE_PRODUCT_TYPE_LAYOUT . " as p, " . TABLE_PRODUCT_TYPES . " as t where p.product_type_id=t.type_id and t.type_id=1 order by configuration_title, configuration_group_id)";
	}

	// When mySql 4.1 is released it will be possible to sort the result of the union request:
	/*
	$requete="select * from (
	(select configuration_id, c.configuration_group_id as configuration_group_id, configuration_group_title, configuration_title, configuration_description, configuration_value, 'conf' as src from " . TABLE_CONFIGURATION . " as c, " . TABLE_CONFIGURATION_GROUP . " as g where c.configuration_group_id=g.configuration_group_id and (configuration_title like '%" . $search . "%' or configuration_description like '%" . $search . "%') order by configuration_title, configuration_group_id)
	union
	(select configuration_id, p.product_type_id as configuration_group_id, type_name as configuration_group_title, configuration_title, configuration_description, configuration_value, 'type' as src from ". TABLE_PRODUCT_TYPE_LAYOUT . " as p, " . TABLE_PRODUCT_TYPES . " as t where p.product_type_id=t.type_id and (configuration_title like '%" . $search . "%' or configuration_description like '%" . $search . "%') order by configuration_title, configuration_group_id)
	) order by configuration_title, configuration_group_id";
	*/

	$configuration = $db->Execute($requete);

	echo "<br /><div class=\"pageHeading\">". HEADING_TITLE . "</div>\n";
	echo "<br /><form action=\"" .$_SERVER['PHP_SELF'] ."\" method=\"get\">";
	echo "<div class=\"messageBox\">";
	echo "Search in title and description: <input type=\"text\" name=\"search\" value=\"" . $search . "\"> <input type=\"submit\" value=\"search\"> <input type=\"button\" value=\"view all\" onClick=\"document.location.href='" . $_SERVER['PHP_SELF'] ."';\"> ";
	if($configuration->RecordCount() > 0)
	{
		echo $configuration->RecordCount() . " configuration key(s) found.";
	}
	else
	{
		echo "No configuration key found.";
	}
	echo "</div>";
	echo "</form><br/ >";

	if($configuration->RecordCount() > 0)
	{

		echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n";
		echo "\t<tr class=\"dataTableHeadingRow\">\n";
		echo "\t\t<td class=\"dataTableHeadingContent\">src</td>\n";
		echo "\t\t<td class=\"dataTableHeadingContent\">title</td>\n";
		echo "\t\t<td class=\"dataTableHeadingContent\">group</td>\n";
		echo "\t\t<td class=\"dataTableHeadingContent\">description</td>\n";
		echo "\t\t<td class=\"dataTableHeadingContent\">value</td>\n";
		echo "\t\t<td class=\"dataTableHeadingContent\"></td>\n";
		echo "\t</tr>\n";

		while (!$configuration->EOF)
		{
			if($configuration->fields['src']=='type')
			{
				$editlink = zen_href_link(FILENAME_PRODUCT_TYPES, 'ptID=' .  $configuration->fields['configuration_group_id'] . '&cID=' . $configuration->fields['configuration_id'] . '&action=layout_edit');
				$viewlink = zen_href_link(FILENAME_PRODUCT_TYPES, 'ptID=' .  $configuration->fields['configuration_group_id'] . '&cID=' . $configuration->fields['configuration_id'] . '&action=layout');
			}
			else if($configuration->fields['src']=='conf')
			{
				$editlink = zen_href_link(FILENAME_CONFIGURATION, 'gID=' . $configuration->fields['configuration_group_id'] . '&cID=' . $configuration->fields['configuration_id'] . '&action=edit');
				$viewlink = zen_href_link(FILENAME_CONFIGURATION, 'gID=' . $configuration->fields['configuration_group_id'] . '&cID=' . $configuration->fields['configuration_id']);
			}
			else
			{
				$editlink = "";
				$viewlink = "";
			}
			echo "\t<tr class=\"dataTableRow\" onmouseover=\"rowOverEffect(this)\" onmouseout=\"rowOutEffect(this)\" onclick=\"document.location.href='" . $editlink . "'\">\n";
			echo "\t\t<td class=\"dataTableContent\">" . $configuration->fields['src'] . "</td>\n";
			echo "\t\t<td class=\"dataTableContent\">" . $configuration->fields['configuration_title'] . "</td>\n";
			echo "\t\t<td class=\"dataTableContent\">" . $configuration->fields['configuration_group_title'] . "</td>\n";
			echo "\t\t<td class=\"dataTableContent\">" . $configuration->fields['configuration_description'] . " &nbsp;</td>\n";
			echo "\t\t<td class=\"dataTableContent\">" . implode("<br />\n", preg_split("/[\s,.]+/", $configuration->fields['configuration_value'])) . " &nbsp;</td>\n";
			echo "\t\t<td class=\"dataTableContent\"><a href=\"" . $viewlink . "\" title=\"view\">" . zen_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . "</a></td>";

			echo "\t</tr>\n";
			$configuration->MoveNext();
		}
		echo "</table>\n";
	}

?>
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>