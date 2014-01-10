<?php
/**
 * Cross Sell products
 *
 * Derived from:
 * Original Idea From Isaac Mualem im@imwebdesigning.com <mailto:im@imwebdesigning.com>
 * Portions Copyright (c) 2002 osCommerce
 * Complete Recoding From Stephen Walker admin@snjcomputers.com
 * license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 *
 * Adapted to Zen Cart by Merlin - Spring 2005
 * Reworked for Zen Cart v1.3.0  03-30-2006
 *
 * Reworked again to change/add more features by yellow1912
 * Pay me a visit at RubikIntegration.com
 *
 * Add Multi Cross Sells by Gilby 2010-06-26
 */

require('includes/application_top.php');

$pmId = pId_to_mId($_GET['products_filter']);

$request_restock  = new request_restock();
$xsell_string="";
$main_product=null;

$base_product = get_base_product_info($_GET['products_filter']);

switch($_GET['action']){
	// Unlike the original version, we cross sell each and every PAIR from the given list
	case 'newcross_sell':
		$request_restock->add_new_cross_products($_POST['exist_product_array'] . $_POST['product_array'], $_POST['cross_sell_one_way'], $_POST['main_product_id']);
	break;

	case 'editcross_sell':
		$search_result = $request_restock->search_cross_product($_POST['cID']);
		$main_product = $_POST['cID'];
		$xsell_array = $search_result['product_check'];
		$xsell_string = $main_product.','.$request_restock->db_result_to_string(',',$search_result['xsell_items'],'products_'.MXSELL_FORM_INPUT_TYPE);
	break;

	case 'deletecross_sell':
		$request_restock->delete_cross_product($_POST['cID']);

	break;

	case 'update':
		$search_result = $request_restock->update_cross_product( $_POST['xsell']);
		$search_result = $request_restock->search_cross_product($_POST['cID']);
    break;

	case 'cleancross_sell':
		$request_restock->clean_up_cross_sell();
	break;

	case 'list_all':
		$xsell_array = $request_restock->list_all_cross_products();
	break;
}

if ($pmId != NULL){
  $search_result = $request_restock->search_cross_product($pmId, true);
  $main_product = $pmId;
  $xsell_array = $search_result['product_check'];
  $xsell_string = $main_product.','.$request_restock->db_result_to_string(',',$search_result['xsell_items'],'products_'.MXSELL_FORM_INPUT_TYPE);
}


if(defined('MXSELL_ENABLED') ) {

  if (isset($_GET['select_cross_sell'])) {
    $cross_sell_edit = ($_GET['select_cross_sell']);
    $db->Execute("UPDATE " . TABLE_CONFIGURATION .
      " set configuration_value = $cross_sell_edit
      WHERE configuration_key = 'MXSELL_SELECTED_TABLE'" );
      zen_redirect(zen_href_link(FILENAME_EASY_XSELL));
    }

  if(!defined('MIN_DISPLAY_XSELL'. MXSELL_NUM_OF_TABLES) ) {
    for ( $counter = 2; $counter <= MXSELL_NUM_OF_TABLES; $counter++ ) {
      if(!defined('MIN_DISPLAY_XSELL'. $counter) ) {
        add_mxs_table( $counter );
        add_mxs_configuration( $counter );
        }
      }
    }
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
<script language="javascript" src="includes/javascript/dynamic_input_field.js"></script>
<style type='text/css'>
.submit_link {
 color: #0000ff;
 background-color: transparent;
 text-decoration: none;
 border: none;
}
</style>
<script type="text/javascript">
<!--
function add_product_field(){
	prefix={type:'label',text:'more',innerText:'<?php echo TEXT_PRODUCT_ID ?>: ',attributes:{className:'inputLabel'}};
	sufix={type:'br'};
	addFields('xsellProducts','product_id[',-1,prefix,sufix);
}
function add_remove_main_product_field(chk,main_product_value){
	field_area = document.getElementById('mainProductField');
	if (chk.checked == 1){
		div_field = {type:'div',attributes:{id:'mainProductFieldContent'}};
		addSingleField(field_area, div_field);
		new_field_area = document.getElementById('mainProductFieldContent');
		field1={type:'label',innerText:'Main product: ',attributes:{className:'inputLabel'}};
		field2={type:'input',attributes:{type:'text',name:'main_product_id',value:main_product_value}};
		addSingleField(new_field_area, field1);
		addSingleField(new_field_area, field2);
	}
  	else{
  		new_field_area = document.getElementById('mainProductFieldContent');
  		field_area.removeChild(new_field_area);
  	}
}

function init()
{
  cssjsmenu('navbar');
  if (document.getElementById)
  {
    var kill = document.getElementById('hoverJS');
    kill.disabled = true;
  }
}
function delete_confirmation() {
	var theForm = document.forms['update_cross'];
	var numOfCheckedBox = 0;

	for(i=0; i<theForm.elements.length; i++){
	if(theForm.elements[i].type == "checkbox" && theForm.elements[i].checked){
			numOfCheckedBox ++;
	    }

	}
	if (numOfCheckedBox > 0){
		var alertText = "You chose to deleted " + numOfCheckedBox + " cross-sell(s). Are you sure you want to delete them?";
		var answer = confirm(alertText)
		if (!answer){
			return false ;
		}
	}

	return true;
}
// -->
</script>
</head>
<body onLoad="init()">
<!-- header //-->
<div class="header_area">
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
</div>
<!-- header_eof //-->


<?php
$action = (isset($_GET['action']) ? $_GET['action'] : '');
if($action=='remove') { ?>
<p style="color:red";>
<?php echo sprintf(MXSELL_REMOVE_CONFIRM, zen_href_link(FILENAME_EASY_XSELL,'action=remove_confirmed'), zen_href_link(FILENAME_EASY_XSELL) ); ?>
</p>
<?php } ?>

<?php  $mxs_table = MXSELL_SELECTED_TABLE; ?>
  <table style="border:none;width:100%; border-spacing:0;cell-padding:0;">
    <tr style="padding-top:10px;">
      <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
      <td class="pageHeading" colspan="2" style="text-align:right;vertical-align:top;padding-top:10px;">&nbsp;

      </td>
    </tr>
  </table>

<?php if(defined('MXSELL_ENABLED') ) { ?>
  Select Cross Sell to edit:
  <?php	echo zen_draw_form('clean_cross', FILENAME_EASY_XSELL, 'action=select_cross_sell&products_filter=' . $_GET['products_filter'] . '&current_category_id=' . $_GET['current_category_id'], 'get'); ?>
<!-- <form method='get' action='FILENAME_EASY_XSELL'> -->
  <select name="select_cross_sell">
  <?php for ( $counter = 1; $counter <= MXSELL_NUM_OF_TABLES; $counter++ ) {
    if ($counter != MXSELL_SELECTED_TABLE)
      echo '<option value="' . $counter . '">' . $counter . '</option>';
    else
      echo '<option value="' . $counter . '"selected="yes">' . $counter . '</option>' ;
   }  ?>

    <input type="submit" value="Go" />
    </select></form>
    <div style="padding-left:550px;">
    <a href=" <?php echo(zen_href_link(FILENAME_CATEGORIES, 'cPath=' . $_GET['current_category_id'])) ?> "> <?php  echo(zen_image_button('button_return.jpg', 'Return to Catalogue')) ?> </a></div>
    <hr />
    <div style="padding:20px;padding-top:0px; float:left; width:40%;">
    <h3 style="color:#0066FF;">Cross Sell With Product</h3>
    <table width="100%">
      <tr>
        <td>ID</td>
        <td>Model</td>
        <td>Name</td>
        <td>Price</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td><?php echo $base_product['products_id']?></td>
        <td><?php echo $base_product['products_model']?></td>
        <td><?php echo $base_product['products_name']?></td>
        <td><?php echo $base_product['products_price']?></td>
        <td><?php echo zen_image(DIR_WS_CATALOG_IMAGES  . $base_product['products_image'], $base_product['products_name'],'' ,125);?></td>
      </tr>
    </table>
    <?php if($search_result == NULL){
      echo 'This product currently has no cross sells';
    }
    ?>
    <br /><br />
 		<h3 style="color:#0066FF;">New Cross-Sells <?php echo $mxs_table; ?></h3>
<?php	echo zen_draw_form('new_cross', FILENAME_EASY_XSELL, 'action=newcross_sell&products_filter=' . $_GET['products_filter'] . '&current_category_id=' . $_GET['current_category_id'], 'post'); ?>
		<fieldset style="width:100%;">
			<legend>New Cross Sell</legend><br />
			<label class="inputLabel">Product Cross-Sell applies to:&nbsp;</label><br />
			<div id="mainProductField" style="padding-left:15px;"></div>
			<div id="xsellProducts" style="padding-left:15px;">
			<label class="inputLabel"><?php echo TEXT_PRODUCT_ID ?>(s):</label>
			<?php echo zen_draw_hidden_field('exist_product_array', $xsell_string);
        echo zen_draw_input_field('product_array','','size="65"'); ?><br />
			</div>
			<span style="float:right"><?php echo zen_image_submit('button_insert.gif', IMAGE_INSERT); ?></span>
			<input type="checkbox" name="cross_sell_one_way" value="1" onClick="return add_remove_main_product_field(cross_sell_one_way,'<?php echo $main_product; ?>');" />Cross sell 1 way only?
		</fieldset>
<?php
		echo '</form>';
?>
		<br clear="all" />
		<br />
		<center><hr style="color:#cccccc;" size="1" width="80%" /></center>
		<br />
		</div>
</div>
		<div style="float:right; width:49%; padding-right:20px; padding-left:20px;">
    <h3 style="color:#0066FF;">Edit Cross-Sells <?php echo $mxs_table; ?></h3>
<?php

		  echo '<div style="float:left; width:100%;">';

		  if (isset ($search_result['product_lookup']) && $search_result['product_lookup']->RecordCount() > 0) {

			echo zen_draw_form('update_cross', FILENAME_EASY_XSELL, 'action=update&products_filter=' . $_GET['products_filter'] . '&current_category_id=' . $_GET['current_category_id'], 'post');
			echo zen_draw_hidden_field('cID', zen_db_prepare_input($_POST['cID']));
?>
			<fieldset>
			  <legend>Product Cross-Sell for <?php echo $_POST['cID']; ?></legend><br />
			  <span style="padding:5px;"><span style="color:#0033CC">Product Name: </span><?php echo $search_result['product_check']->fields['products_name']; ?></span><br /><br />
			  <label class="inputLabel">Current Cross-Sells:&nbsp;<br /><br /></label>
			  <div style="padding-left:15px;">
<?php
		  	  echo '<table cellspacing="0" cellpadding="5" style="border:1px solid #cccccc; border-collapse: collapse;">';
				echo '<tr style="background-color:#dddddd;">';
				  echo '<td>Product ID</td>';
				  echo '<td>Product Model</td>';
				  echo '<td>Name</td>';
				  echo '<td>Sort order</td>';
				  echo '<td>Delete?</td>';
          echo '<td>Image</td>';
				echo '</tr>';

			for ($count = 0; !$search_result['xsell_items']->EOF; $count++) {
				echo '<tr>';
				echo '<td style="border-bottom:1px dashed #cccccc;">' .
					$search_result['xsell_items']->fields['products_id'] .
					zen_draw_hidden_field("xsell[$count][id]", $search_result['xsell_items']->fields['ID']) .
					zen_draw_hidden_field("xsell[$count][product_id]", $search_result['xsell_items']->fields['products_id']) .
					'</td>';
				echo '<td style="border-bottom:1px dashed #cccccc;">' .
					$search_result['xsell_items']->fields['products_model'] .
					zen_draw_hidden_field("xsell[$count][product_model]", $search_result['xsell_items']->fields['products_model']) .
					'</td>';
				echo '<td style="border-bottom:1px dashed #cccccc;">' . $search_result['xsell_items']->fields['products_name'] . '</td>';
				echo '<td style="border-bottom:1px dashed #cccccc;">';
					echo zen_draw_input_field("xsell[$count][new_sort_order]",$search_result['xsell_items']->fields['sort_order'],"size=1");
					echo zen_draw_hidden_field("xsell[$count][old_sort_order]",$search_result['xsell_items']->fields['sort_order']);
				echo '</td>';
				echo '<td style="border-bottom:1px dashed #cccccc;">';
					echo zen_draw_checkbox_field("xsell[$count][delete]", 1, false);
				echo '</td>';
        echo '<td>';
        $xsell_image = zen_get_products_image($search_result['xsell_items']->fields['products_id']);
        echo zen_image(DIR_WS_CATALOG_IMAGES  . $xsell_image, $xsell_image,'' ,65);
        echo '</td>';
				echo '</tr>';
			  $search_result['xsell_items']->MoveNext();
			}
			echo '<tr><td colspan="5">' .
					zen_image_submit('button_update.gif', IMAGE_UPDATE,'onClick="return delete_confirmation()"') .
				 '</td></tr>';
			echo '</table>';
?>
			</div>
		  </fieldset>
<?php
		  echo '</form>';
		  }
?>

<?php /*
		 if(!isset($xsell_array)){
		  echo '<a href="'.zen_href_link(FILENAME_EASY_XSELL, 'action=list_all').'"><h3 style="color:#0066FF;">List All Cross-Sells ' . $mxs_table . '</h3></a>';
		 }
		 else{
		  echo '<h3 style="color:#0066FF;">Current Cross-Sells ' . $mxs_table . '</h3>';
		  echo '<table cellspacing="0" cellpadding="5" style="border-collapse: collapse; border:1px solid #cccccc; width:100%;">';

			echo '<tr style="background-color:#dddddd;">';
			echo '<td>Product id</td>';
			echo '<td>Product Model</td>';
			echo '<td>Product Name</td>';
			echo '<td># of Current Cross-Sells</td>';
			echo '<td colspan="2" align="center">Action</td>';
			echo '</tr>';
			  while (!$xsell_array->EOF) {
				echo '<tr>';
				echo '<td style="border-bottom:1px dashed #cccccc;">' . $xsell_array->fields['products_id'] . '</td>';
				echo '<td style="border-bottom:1px dashed #cccccc;">' . $xsell_array->fields['products_model'] . '</td>';
				echo '<td style="border-bottom:1px dashed #cccccc;">' . $xsell_array->fields['products_name'] . '</td>';
				echo '<td align="center" style="border-bottom:1px dashed #cccccc;">' . $xsell_array->fields['xsells'] . '</td>';
				echo '<td style="border-bottom:1px dashed #cccccc;">';
					echo zen_draw_form('edit_cross', FILENAME_EASY_XSELL,'action=editcross_sell', 'post');
					if (MXSELL_FORM_INPUT_TYPE == "id")
						echo zen_draw_hidden_field("cID", $xsell_array->fields['products_id']);
					else
						echo zen_draw_hidden_field("cID", $xsell_array->fields['products_model']);
					echo zen_draw_input_field('Edit', 'Edit', 'class="submit_link"', false, 'submit');
					echo '</form>';
				echo '</td>';
				echo '<td style="border-bottom:1px dashed #cccccc;">';
					echo zen_draw_form('edit_cross', FILENAME_EASY_XSELL,'action=deletecross_sell', 'post');
					if (MXSELL_FORM_INPUT_TYPE == "id")
						echo zen_draw_hidden_field("cID", $xsell_array->fields['products_id']);
					else
						echo zen_draw_hidden_field("cID", $xsell_array->fields['products_model']);
					echo zen_draw_input_field('Delete', 'Delete', 'class="submit_link"', false, 'submit');
					echo '</form>';
				echo '</td>';
				echo '</tr>';
				$xsell_array->MoveNext();
			  }
			  echo '</table>';
		  }
*/
		 ?></div>
<br clear="all" />
<?php } ?>
<!-- body_eof //-->
<!-- footer //-->
<div class="footer-area">
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
</div>
<!-- footer_eof //-->
<br />
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>