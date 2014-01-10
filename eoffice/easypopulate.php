<?php
//
// +----------------------------------------------------------------------+
// |zen-cart Open Source E-commerce                                       |
// +----------------------------------------------------------------------+
// | Copyright (c) 2004 The zen-cart developers                           |
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
// $Id:easypopulate.php,v1.2.5.4 2005/09/26 langer $
//

//*******************************
//*******************************
// C O N F I G U R A T I O N
// V A R I A B L E S
//*******************************
//*******************************


/**
* Advanced Smart Tags - activated/de-activated in Zencart Admin
*/

// only activate advanced tags if you really know what you are doing, and understand regular expressions. Disable if things go awry.
// If you wish to add your own smart-tags below, please ensure that you understand the following:
// 1) ensure that the expressions you use avoid repetitive behaviour from one upload to the next using existing data, as you may end up with this sort of thing:
//   <b><b><b><b>thing</b></b></b></b> ...etc for each update. This is caused for each output that qualifies as an input for any expression..
// 2) remember to place the tags in the order that you want them to occur, as each is done in turn and may remove characters you rely on for a later tag
// 3) the $smart_tags array above is the last to be executed, so you have all of your carriage-returns and line-breaks to play with below
// 4) make sure you escape the following metacharacters if you are using them as string literals: ^  $  \  *  +  ?  (  )  |  .  [  ]  / etc..
// The following examples should get your blood going... comment out those you do not want after enabling $strip_advanced_smart_tags = true above
// for regex help see: http://www.quanetic.com/regex.php or http://www.regular-expressions.info
$advanced_smart_tags = array(
										// replaces "Description:" at beginning of new lines with <br /> and same in bold
										"\r\nDescription:|\rDescription:|\nDescription:" => '<br /><b>Description:</b>',

										// replaces at beginning of description fields "Description:" with same in bold
										"^Description:" => '<b>Description:</b>',

										// just make "Description:" bold wherever it is...must use both lines to prevent duplicates!
										//"<b>Description:<\/b>" => 'Description:',
										//"Description:" => '<b>Description:</b>',

										// replaces "Specification:" at beginning of new lines with <br /> and same in bold.
										"\r\nSpecifications:|\rSpecifications:|\nSpecifications:" => '<br /><b>Specifications:</b>',

										// replaces at beginning of descriptions "Specifications:" with same in bold
										"^Specifications:" => '<b>Specifications:</b>',

										// just make "Specifications:" bold wherever it is...must use both lines to prevent duplicates!
										//"<b>Specifications:<\/b>" => 'Specifications:',
										//"Specifications:" => '<b>Specifications:</b>',

										// replaces in descriptions any asterisk at beginning of new line with a <br /> and a bullet.
										"\r\n\*|\r\*|\n\*" => '<br />&bull;',

										// replaces in descriptions any asterisk at beginning of descriptions with a bullet.
										"^\*" => '&bull;',

										// returns/newlines in description fields replaced with space, rather than <br /> further below
										//"\r\n|\r|\n" => ' ',

										// the following should produce paragraphs between double breaks, and line breaks for returns/newlines
										"^<p>" => '', // this prevents duplicates
										"^" => '<p>',
										//"^<p style=\"desc-start\">" => '', // this prevents duplicates
										//"^" => '<p style="desc-start">',
										"<\/p>$" => '', // this prevents duplicates
										"$" => '</p>',
										"\r\n\r\n|\r\r|\n\n" => '</p><p>',
										// if not using the above 5(+2) lines, use the line below instead..
										//"\r\n\r\n|\r\r|\n\n" => '<br /><br />',
										"\r\n|\r|\n" => '<br />',

										// ensures "Description:" followed by single <br /> is fllowed by double <br />
										"<b>Description:<\/b><br \/>" => '<br /><b>Description:</b><br /><br />',
										);


//*******************************
//*******************************
// E N D
// C O N F I G U R A T I O N
// V A R I A B L E S
//*******************************
//*******************************


//*******************************
//*******************************
// S T A R T
// INITIALIZATION
//*******************************

require_once ('includes/application_top.php');
global $messageStack;
if(isset($_POST['action'])&&$_POST['action']=='delete'){
  //Delete uploaded sheet from server
  //echo HTTP_SERVER.DIR_WS_ADMIN.'/tempEP/'.$_POST['localfile'];
  if(unlink(DIR_FS_ADMIN.'tempEP/'.$_POST['localfile'])){
    $messageStack->add_session('File ' . $_POST['localfile'] . ' has been successfully deleted', 'success');
  }else{
    $messageStack->add_session('Failed to delete the file ' . $_POST['localfile'], 'warning');
  }

  header('Location:easypopulate.php');

  exit;
}

@set_time_limit(300); // if possible, let's try for 5 minutes before timeouts

//KandS - Format strings for data error msgs
$errMsgStart = '<span style="color: red;"><b>';
$errMsgEnd = '</b></span>';
//EOE KandS

/**
* Config translation layer..
*/
// note - not all config defines are in below...
$tempdir = EASYPOPULATE_CONFIG_TEMP_DIR;
$ep_date_format = EASYPOPULATE_CONFIG_FILE_DATE_FORMAT;
$ep_raw_time = EASYPOPULATE_CONFIG_DEFAULT_RAW_TIME;
$ep_debug_logging = ((EASYPOPULATE_CONFIG_DEBUG_LOGGING == 'true') ? true : false);
$maxrecs = EASYPOPULATE_CONFIG_SPLIT_MAX;
$price_with_tax = ((EASYPOPULATE_CONFIG_PRICE_INC_TAX == 'true') ? true : false);
$max_categories = EASYPOPULATE_CONFIG_MAX_CATEGORY_LEVELS;
$strip_smart_tags = ((EASYPOPULATE_CONFIG_SMART_TAGS == 'true') ? true : false);
// may make it optional for user to use their own names for these EP tasks..

/**
* Test area start
*/
//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);//test purposes only
//register_globals_vars_check ();// test purposes only
//$maxrecs = 4; // for testing
// usefull stuff: mysql_affected_rows(), mysql_num_rows().
$ep_debug_logging_all = true; // do not comment out.. make false instead
//$sql_fail_test == true; // used to cause an sql error on new product upload - tests error handling & logs
/*
* Test area end
**/

/**
* Initialise vars
*/

// Current EP Version
$curver = '1.2.5.4';

$display_output = '';
$ep_dltype = NULL;
$ep_dlmethod = NULL;
$chmod_check = true;
$ep_stack_sql_error = false; // function returns true on any 1 error, and notifies user of an error
$specials_print = EASYPOPULATE_SPECIALS_HEADING;
$featured_print = EASYPOPULATE_FEATURED_HEADING;
$replace_quotes = false; // langer - this is probably redundant now...retain here for now..
$products_with_attributes = false; // langer - this will be redundant after html renovation
// maybe below can go in array eg $ep_processed['attributes'] = true, etc.. cold skip all post-upload tasks on check if isset var $ep_processed.
$has_attributes == false;
$has_specials == false;
$xsell_master_array = array();

// define(EASYPOPULATE_CONFIG_COL_DELIMITER, "\t");
$separator = "\t"; // only tab allowed at present

// all mods go in this array as 'name' => 'true' if exist. eg $ep_supported_mods['psd'] => true means it exists.
// langer - scan array in future to reveal if any mods for inclusion in downloads
$ep_supported_mods = array();

// config keys array - must contain any expired keys to ensure they are deleted on install or removal
$ep_keys = array('EASYPOPULATE_CONFIG_TEMP_DIR',
								'EASYPOPULATE_CONFIG_FILE_DATE_FORMAT',
								'EASYPOPULATE_CONFIG_DEFAULT_RAW_TIME',
								'EASYPOPULATE_CONFIG_SPLIT_MAX',
								'EASYPOPULATE_CONFIG_MAX_CATEGORY_LEVELS',
								'EASYPOPULATE_CONFIG_PRICE_INC_TAX',
								'EASYPOPULATE_CONFIG_ZERO_QTY_INACTIVE',
								'EASYPOPULATE_CONFIG_SMART_TAGS',
								'EASYPOPULATE_CONFIG_ADV_SMART_TAGS',
								'EASYPOPULATE_CONFIG_DEBUG_LOGGING',
								);

// default smart-tags setting when enabled. This can be added to.
$smart_tags = array("\r\n|\r|\n" => '<br />',
										);

if (substr($tempdir, -1) != '/') $tempdir .= '/';
if (substr($tempdir, 0, 1) == '/') $tempdir = substr($tempdir, 1);

$ep_debug_log_path = DIR_FS_CATALOG . $tempdir;

if ($ep_debug_logging_all == true) {
$fp = fopen($ep_debug_log_path . 'ep_debug_log.txt','w'); // new blank log file on each page impression for full testing log (too big otherwise!!)
fclose($fp);
}

/**
* Pre-flight checks start here
*/

// temp folder exists & permissions check & adjust if we can
// lets check our config is installed 1st..
// when installing, we skip these tests..
if (EASYPOPULATE_CONFIG_TEMP_DIR == 'EASYPOPULATE_CONFIG_TEMP_DIR' && ($_GET['langer'] != 'install' or $_GET['langer'] != 'installnew')) {
	// admin area config not installed
	$messageStack->add(sprintf(EASYPOPULATE_MSGSTACK_INSTALL_KEYS_FAIL, '<a href="' . zen_href_link(FILENAME_EASYPOPULATE, 'langer=installnew') . '">', '</a>'), 'warning');
} elseif ($_GET['langer'] != 'install' && $_GET['langer'] != 'installnew') {
	ep_chmod_check ($tempdir);
}

// installation start
if ($_GET['langer'] == 'install' or $_GET['langer'] == 'installnew') {
	if ($_GET['langer'] == 'installnew') {
		// remove any old config..
		remove_easypopulate();
		// install new config
		install_easypopulate();
		zen_redirect(zen_href_link(FILENAME_EASYPOPULATE, 'langer=install'));
	}

	$chmod_check = ep_chmod_check($tempdir);
	if ($chmod_check == false) {
		// no temp dir, so template download wont work..
		$messageStack->add(EASYPOPULATE_MSGSTACK_INSTALL_CHMOD_FAIL, 'caution');
	} else {
		// chmod success
		if (defined('EASYPOPULATE_MSGSTACK_LANGER') && strpos(EASYPOPULATE_MSGSTACK_LANGER, 'paypal@portability.com.au') == true) {
			$messageStack->add(EASYPOPULATE_MSGSTACK_LANGER, 'caution');
		} else {
			$messageStack->add('EasyPopulate support & development by <b>langer</b>. Donations are always appreciated to support continuing development: paypal@portability.com.au', 'caution');
		}
		// lets do a full download to the temp file
		$ep_dltype = 'full';
		$ep_dlmethod = 'tempfile';
		$messageStack->add(EASYPOPULATE_MSGSTACK_INSTALL_CHMOD_SUCCESS, 'success');
	}
	//zen_redirect(zen_href_link(FILENAME_EASYPOPULATE));

	// attempt to delete redundant files from previous versions v1.2.5.2 and lower
	// delete easypopulate_functions from admin dir
	$return = @unlink('easypopulate_functions.php');
	if($return == true) $messageStack->add(sprintf(EASYPOPULATE_MSGSTACK_INSTALL_DELETE_SUCCESS, 'easypopulate_functions.php', 'ADMIN'), 'success');
	$return = @unlink('includes/boxes/extra_boxes/populate_tools_dhtml.php');
	if($return == true) {
		$messageStack->add(sprintf(EASYPOPULATE_MSGSTACK_INSTALL_DELETE_SUCCESS, 'populate_tools_dhtml.php', '/includes/boxes/extra_boxes/'), 'success');
	} else {
		// delete populate_tools_dhtml.php from extra boxes failed. Tell user to delete it, otherwise it shows in DHTML menu.
		if (@is_file(includes/boxes/extra_boxes/populate_tools_dhtml.php)) $messageStack->add(sprintf(EASYPOPULATE_MSGSTACK_INSTALL_DELETE_FAIL, 'populate_tools_dhtml.php', '/includes/boxes/extra_boxes/'), 'caution');
	}

} elseif ($_GET['langer'] == 'remove') {
	remove_easypopulate();
	zen_redirect(zen_href_link(FILENAME_EASYPOPULATE));
}
// end installation/removal


/**
* END check for existance of various mods
*/

if (EASYPOPULATE_CONFIG_ADV_SMART_TAGS == 'true') $smart_tags = array_merge($advanced_smart_tags,$smart_tags);

// maximum length for a category in this database
$category_strlen_max = zen_field_length(TABLE_CATEGORIES_DESCRIPTION, 'categories_name');

// model name length error handling
$model_varchar = zen_field_length(TABLE_PRODUCTS, 'products_model');
if (!isset($model_varchar)) {
	$messageStack->add(EASYPOPULATE_MSGSTACK_MODELSIZE_DETECT_FAIL, 'warning');
	$modelsize = 32;
} else {
	$modelsize = $model_varchar;
}
//echo $modelsize;

/**
* Pre-flight checks finish here
*/

// now to create the file layout for each download type..

//elari check default language_id from configuration table DEFAULT_LANGUAGE
$epdlanguage_query = ep_query("select languages_id, name from " . TABLE_LANGUAGES . " where code = '" . DEFAULT_LANGUAGE . "'");
if (mysql_num_rows($epdlanguage_query)) {
	$epdlanguage = mysql_fetch_array($epdlanguage_query);
	$epdlanguage_id   = $epdlanguage['languages_id'];
	$epdlanguage_name = $epdlanguage['name'];
} else {
	//$messageStack->add('', 'warning'); // langer - this will never occur..
	echo 'Strange but there is no default language to work... That may not happen, just in case...';
}

$langcode = array();
$languages_query = ep_query("select languages_id, code from " . TABLE_LANGUAGES . " order by sort_order");
// start array at one, the rest of the code expects it that way
$ll =1;
while ($ep_languages = mysql_fetch_array($languages_query)) {
	//will be used to return language_id en language code to report in product_name_code instead of product_name_id
	$ep_languages_array[$ll++] = array(
				'id' => $ep_languages['languages_id'],
				'code' => $ep_languages['code']
				);
}
$langcode = $ep_languages_array;

$ep_dltype = (isset($_GET['dltype'])) ? $_GET['dltype'] : $ep_dltype;

if (zen_not_null($ep_dltype)) {

	// if dltype is set, then create the filelayout.  Otherwise it gets read from the uploaded file
	// ep_create_filelayout($dltype); // get the right filelayout for this download. langer - redundant function call..

	// depending on the type of the download the user wanted, create a file layout for it.
	$fieldmap = array(); // default to no mapping to change internal field names to external.
	switch($ep_dltype){
	case 'full':
		// The file layout is dynamically made depending on the number of languages
		$iii = 0;
		$filelayout = array(
			'v_products_model'    => $iii++,
      'v_manufactures_code'      => $iii++,
			'v_products_image'    => $iii++,
			);

		foreach ($langcode as $key => $lang){
			$l_id = $lang['id'];
			// uncomment the head_title, head_desc, and head_keywords to use
			// Linda's Header Tag Controller 2.0
			$filelayout  = array_merge($filelayout , array(
					'v_products_name_' . $l_id    => $iii++,
					'v_products_description_' . $l_id => $iii++,
					));
		}
		// uncomment the customer_price and customer_group to support multi-price per product contrib

			$header_array = array(
			'v_products_price'    => $iii++,
			'v_products_weight'   => $iii++,
			//'v_date_added'      => $iii++,
			'v_products_quantity'   => $iii++,
			);

		$header_array['v_manufacturers_name'] = $iii++;
		$filelayout = array_merge($filelayout, $header_array);

		// shafiq featured array merge
		//$filelayout = array_merge($filelayout,$featured_array);

		// build the categories name section of the array based on the number of categores the user wants to have
		//for($i=1;$i<$max_categories+1;$i++){
		//	$filelayout = array_merge($filelayout, array('v_categories_name_' . $i => $iii++));
		//}
    $filelayout = array_merge($filelayout, array('v_categories_id' => $iii++));

		$filelayout = array_merge($filelayout, array(
			//'v_tax_class_title'   => $iii++,
			'v_status'      => $iii++,
			));

    $extra_fields_array = array(
    'v_manufactures_code' => $iii++,
    'v_product_style'  => $iii++,
    'v_product_finish'  => $iii++,
    'v_product_material'  => $iii++,
    'v_product_colour'    =>$iii++,
    'v_bulbs_qty'       => $iii++,
    'v_bulbs_watage'    => $iii++,
    'v_bulbs_type'       => $iii++,
    'v_bulbs_cap'        => $iii++,
    'v_bulbs_included'       => $iii++,
    'v_dimensions_height'       => $iii++,
    'v_dimensions_width' => $iii++,
    'v_dimensions_depth'  => $iii++,
    'v_product_dia'  => $iii++,
    'v_product_min_drop'  => $iii++,
    'v_product_max_drop'  => $iii++,
    'v_product_length'  => $iii++,
    'v_product_recess'  => $iii++,
    'v_product_shade_inc' =>$iii++,
    'v_ip_rating' =>$iii++,
    'v_product_voltage' =>$iii++,
    'v_product_guarantee' =>$iii++,
    'v_product_options' =>$iii++,
    'v_product_saftey_class' =>$iii++,
    'v_product_transformer' =>$iii++,
    'v_product_driver' =>$iii++,
    'v_product_cut_out' =>$iii++,
    'v_product_surface_temp' =>$iii++,
    'v_product_cable' =>$iii++,
    'v_product_carriage' =>$iii++,
    'v_product_statements' =>$iii++,
    'v_product_tilt'  =>$iii++,
    'v_product_variant' =>$iii++,
    'v_xsell' =>$iii++,
    'v_now_price' =>$iii++,
    'v_common_images' =>$iii++,
    'v_show_price' =>$iii++,
    'v_rrp' =>$iii++,
    'v_rate_1' =>$iii++,
    'v_rate_2' =>$iii++,
    'v_rate_3' =>$iii++,
    'v_bulbs_s1' =>$iii++,
    'v_bulbs_s2' =>$iii++
    );
    $filelayout = array_merge($filelayout, $extra_fields_array);

		$filelayout_sql = "SELECT
			p.products_id as v_products_id,
			p.products_model as v_products_model,
			p.products_image as v_products_image,
			p.products_price as v_products_price,
			p.products_weight as v_products_weight,
			p.products_date_available as v_date_avail,".
			//p.products_date_added as v_date_added,
			"p.products_tax_class_id as v_tax_class_id,
			p.products_quantity as v_products_quantity,
			p.manufacturers_id as v_manufacturers_id,
			subc.categories_id as v_categories_id,
			p.products_status as v_status
			FROM
			".TABLE_PRODUCTS." as p,
			".TABLE_CATEGORIES." as subc,
			".TABLE_PRODUCTS_TO_CATEGORIES." as ptoc
			WHERE
			p.products_id = ptoc.products_id AND
			ptoc.categories_id = subc.categories_id
			";

		break;
	/*case 'priceqty':
		$iii = 0;
		// uncomment the customer_price and customer_group to support multi-price per product contrib
		$filelayout = array(
			'v_products_model'    => $iii++,
			'v_specials_price'    => $iii++,
			'v_specials_date_avail'     => $iii++,
			'v_specials_expires_date'     => $iii++,
			'v_products_price'    => $iii++,
			'v_products_quantity'   => $iii++,
			//'v_customer_price_1'    => $iii++,
			//'v_customer_group_id_1'   => $iii++,
			//'v_customer_price_2'    => $iii++,
			//'v_customer_group_id_2'   => $iii++,
			//'v_customer_price_3'    => $iii++,
			//'v_customer_group_id_3'   => $iii++,
			//'v_customer_price_4'    => $iii++,
			//'v_customer_group_id_4'   => $iii++,
				);
		$filelayout_sql = "SELECT
			p.products_id as v_products_id,
			p.products_model as v_products_model,
			p.products_price as v_products_price,
			p.products_tax_class_id as v_tax_class_id,
			p.products_quantity as v_products_quantity
			FROM
			".TABLE_PRODUCTS." as p
			";

		break;*/

	case 'category':
		// The file layout is dynamically made depending on the number of languages
		$iii = 0;
		$filelayout = array(
			'v_products_model'    => $iii++,
		);

		// build the categories name section of the array based on the number of categores the user wants to have
		for($i=1;$i<$max_categories+1;$i++){
			$filelayout = array_merge($filelayout, array('v_categories_name_' . $i => $iii++));
		}


		$filelayout_sql = "SELECT
			p.products_id as v_products_id,
			p.products_model as v_products_model,
			subc.categories_id as v_categories_id
			FROM
			".TABLE_PRODUCTS." as p,
			".TABLE_CATEGORIES." as subc,
			".TABLE_PRODUCTS_TO_CATEGORIES." as ptoc
			WHERE
			p.products_id = ptoc.products_id AND
			ptoc.categories_id = subc.categories_id
			";
		break;


	}
	$filelayout_count = count($filelayout);

}

//*******************************
//*******************************
// E N D
// INITIALIZATION
//*******************************
//*******************************

$ep_dlmethod = isset($_GET['download']) ? $_GET['download'] : $ep_dlmethod;
if ($ep_dlmethod == 'stream' or  $ep_dlmethod == 'tempfile'){
	//*******************************
	//*******************************
	// DOWNLOAD FILE
	//*******************************
	//*******************************
	$filestring = ""; // this holds the csv file we want to download

	$result = ep_query($filelayout_sql);
	$row =  mysql_fetch_array($result);

	// Here we need to allow for the mapping of internal field names to external field names
	// default to all headers named like the internal ones
	// the field mapping array only needs to cover those fields that need to have their name changed
	if (count($fileheaders) != 0 ) {
		$filelayout_header = $fileheaders; // if they gave us fileheaders for the dl, then use them; langer - (froogle only??)
	} else {
		$filelayout_header = $filelayout; // if no mapping was spec'd use the internal field names for header names
	}
	//We prepare the table heading with layout values
	foreach( $filelayout_header as $key => $value ){
		$filestring .= $key . $separator;
	}
	// now lop off the trailing tab
	$filestring = substr($filestring, 0, strlen($filestring)-1);

	// default to normal end of row
	$endofrow = $separator . 'EOREOR' . "\n";

	$filestring .= $endofrow;

	$num_of_langs = count($langcode);
	while ($row){

		// if the filelayout says we need a products_name, get it
		// build the long full froogle image path

		// check for a large image else use medium else use small else no link
		// thanks to Tim Kroeger - www.breakmyzencart.com
	  $products_image = (($row['v_products_image'] == PRODUCTS_IMAGE_NO_IMAGE) ? '' : $row['v_products_image']);
	  $products_image_extension = substr($products_image, strrpos($products_image, '.'));
	  $products_image_base = ereg_replace($products_image_extension . '$', '', $products_image);
	  $products_image_medium = $products_image_base . IMAGE_SUFFIX_MEDIUM . $products_image_extension;
	  $products_image_large = $products_image_base . IMAGE_SUFFIX_LARGE . $products_image_extension;
	  if (!file_exists(DIR_FS_CATALOG_IMAGES . 'large/' . $products_image_large)) {
	    if (!file_exists(DIR_FS_CATALOG_IMAGES . 'medium/' . $products_image_medium)) {
	     $image_url = (($products_image == '') ? '' : DIR_WS_CATALOG_IMAGES . $products_image);
	    } else {
	      $image_url = DIR_WS_CATALOG_IMAGES . 'medium/' . $products_image_medium;
	    }
	  } else {
	    $image_url = DIR_WS_CATALOG_IMAGES . 'large/' . $products_image_large;
	  }
		$row['v_products_fullpath_image'] = $image_url;

		// names and descriptions require that we loop thru all languages that are turned on in the store
		foreach ($langcode as $key => $lang){
			$lid = $lang['id'];

			// for each language, get the description and set the vals
			$sql2 = "SELECT *
				FROM ".TABLE_PRODUCTS_DESCRIPTION."
				WHERE
					products_id = " . $row['v_products_id'] . " AND
					language_id = '" . $lid . "'
				";
			$result2 = ep_query($sql2);
			$row2 =  mysql_fetch_array($result2);

			$row['v_products_name_' . $lid]   = $row2['products_name'];
			$row['v_products_description_' . $lid]  = $row2['products_description'];
		}


		// for the categories, we need to keep looping until we find the root category

		// start with v_categories_id
		// Get the category description
		// set the appropriate variable name
		// if parent_id is not null, then follow it up.
		// we'll populate an aray first, then decide where it goes in the
		//$thecategory_id = $row['v_categories_id'];

		// if the filelayout says we need a manufacturers name, get it
		if (isset($filelayout['v_manufacturers_name'])){
			if ($row['v_manufacturers_id'] != ''){
				$sql2 = "SELECT manufacturers_name
					FROM ".TABLE_MANUFACTURERS."
					WHERE
					manufacturers_id = " . $row['v_manufacturers_id']
					;
				$result2 = ep_query($sql2);
				$row2 =  mysql_fetch_array($result2);
				$row['v_manufacturers_name'] = $row2['manufacturers_name'];
			}
		}

		// If you have other modules that need to be available, put them here

    $extras_sql = 'SELECT * FROM ' . TABLE_PRODUCTS_EXTRA_FIELDS . ' WHERE products_id = ' . $row['v_products_id'];
    $erow = ep_query($extras_sql);

    while($extras_row = mysql_fetch_array($erow)){
      $isIndex=true;
      foreach($extras_row as $key =>   $value){
        if(!$isIndex){
          if($key == 'product_id')continue;
          $row['v_'.$key]=$value;
        }
        $isIndex = !$isIndex;
      }
    }


		//We check the value of tax class and title instead of the id
		//Then we add the tax to price if $price_with_tax is set to 1
		$row_tax_multiplier     = ep_get_tax_class_rate($row['v_tax_class_id']);
		$row['v_tax_class_title']   = zen_get_tax_class_title($row['v_tax_class_id']);
		$row['v_products_price']  = round($row['v_products_price'] + ($price_with_tax * $row['v_products_price'] * $row_tax_multiplier / 100),2);


		// remove any bad things in the texts that could confuse EasyPopulate
		$therow = '';
		foreach( $filelayout as $key => $value ){

			$thetext = $row[$key];
			// kill the carriage returns and tabs in the descriptions, they're killing me!
			$thetext = str_replace("\r",' ',$thetext);
			$thetext = str_replace("\n",' ',$thetext);
			$thetext = str_replace("\t",' ',$thetext);
			// and put the text into the output separated by tabs
			$therow .= $thetext . $separator;
		}

		// lop off the trailing tab, then append the end of row indicator
		$therow = substr($therow,0,strlen($therow)-1) . $endofrow;

		$filestring .= $therow;
		// grab the next row from the db
		$row =  mysql_fetch_array($result);
	}

	//$EXPORT_TIME=time();
	$EXPORT_TIME = strftime('%Y%b%d-%H%I');
	switch ($ep_dltype) {
		case 'full':
		$EXPORT_TIME = "Full-EP" . $EXPORT_TIME;
		break;
		case 'priceqty':
		$EXPORT_TIME = "PriceQty-EP" . $EXPORT_TIME;
		break;
		case 'category':
		$EXPORT_TIME = "Category-EP" . $EXPORT_TIME;
		break;
		case 'froogle':
		$EXPORT_TIME = "Froogle-EP" . $EXPORT_TIME;
		break;
		case 'attrib':
		$EXPORT_TIME = "Attributes-EP" . $EXPORT_TIME;
		break;
	}

	// now either stream it to them or put it in the temp directory
	if ($ep_dlmethod == 'stream'){
		//*******************************
		// STREAM FILE
		//*******************************
		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: attachment; filename=$EXPORT_TIME.xls");
		// Changed if using SSL, helps prevent program delay/timeout (add to backup.php also)
		//  header("Pragma: no-cache");
		if ($request_type== 'NONSSL'){
			header("Pragma: no-cache");
		} else {
			header("Pragma: ");
		}
		header("Expires: 0");
		echo $filestring;
		die();
	} else {
		//*******************************
		// PUT FILE IN TEMP DIR
		//*******************************
		$tmpfpath = DIR_FS_CATALOG . '' . $tempdir . "$EXPORT_TIME.txt";
		//unlink($tmpfpath);
		$fp = fopen( $tmpfpath, "w+");
		fwrite($fp, $filestring);
		fclose($fp);
		$messageStack->add(sprintf(EASYPOPULATE_MSGSTACK_FILE_EXPORT_SUCCESS, $EXPORT_TIME, $tempdir), 'success');
	}
}

//*******************************
//*******************************
// DOWNLOADING ENDS HERE
//*******************************
//*******************************

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//*****************************************************************************************************************************************************
//*****************************************************************************************************************************************************
//*************** UPLOADING OF FILES STARTS HERE
//*****************************************************************************************************************************************************
//*****************************************************************************************************************************************************

if ((isset($_FILES['usrfl']) or isset($GLOBALS[$usrfl . '_name'])) && ($_GET['split']==1 or $_GET['split']==2)) {


	//*******************************
	//*******************************
	// UPLOAD AND SPLIT FILE
	//*******************************
	//*******************************
	// move the file to where we can work with it

	$file = ep_get_uploaded_file('usrfl');

	if (is_uploaded_file($file['tmp_name'])) {
		ep_copy_uploaded_file($file, DIR_FS_CATALOG . '' . $tempdir);
	}

	$infp = fopen(DIR_FS_CATALOG . '' . $tempdir . $file['name'], "r");

	//toprow has the field headers
	$toprow = fgets($infp,32768);

	$filecount = 1;


	$tmpfname = EASYPOPULATE_FILE_SPLITS_PREFIX . $filecount . "-" . $file['name'];
	//$display_output .= 'Creating file ' . $tmpfname . '...';
	$tmpfpath = DIR_FS_CATALOG  . $tempdir . $tmpfname;
	$fp = fopen( $tmpfpath, "w+");
	fwrite($fp, $toprow);

	$linecount = 0;
	$line = fgets($infp,32768);
	while ($line){
		// walking the entire file one row at a time
		// but a line is not necessarily a complete row, we need to split on rows that have "EOREOR" at the end
		$line = str_replace('"EOREOR"', 'EOREOR', $line);
		fwrite($fp, $line);
		if (strpos($line, 'EOREOR')){
			// we found the end of a line of data, store it
			$linecount++; // increment our line counter
			if ($linecount >= $maxrecs){
				//$display_output .= "Added $linecount records and closing file... <Br>";
				$linecount = 0; // reset our line counter
				// close the existing file and open another;
				fclose($fp);
				$filecount++;
				$tmpfname = EASYPOPULATE_FILE_SPLITS_PREFIX . $filecount . "-" . $file['name'];
				//$display_output .= 'Creating file ' . $tmpfname . '...';
				$tmpfpath = DIR_FS_CATALOG  . $tempdir . $tmpfname;
				//Open next file name
				$fp = fopen( $tmpfpath, "w+");
				fwrite($fp, $toprow);
			}
		}
		$line=fgets($infp,32768);
	}
	//$display_output .= "Added $linecount records and closing file...<br><br> ";
	fclose($fp);
	fclose($infp);

	$display_output .= sprintf(EASYPOPULATE_DISPLAY_SPLIT_LOCATION, $tempdir);
}

// $split = 2 means let's process the split files now..
//if (isset($_POST['localfile']) or isset($GLOBALS['HTTP_POST_FILES'][$localfile]) or ((isset($_FILES['usrfl']) or isset($GLOBALS[$usrfl . '_name'])) && $_GET['split']==0)) {
if ((isset($_POST['localfile']) or $_GET['split']==2) or ((isset($_FILES['usrfl']) or isset($GLOBALS[$usrfl . '_name'])) && $_GET['split']==0)) {

	$display_output .= EASYPOPULATE_DISPLAY_HEADING;

//*****************************************************************************************************************************************************
//*****************************************************************************************************************************************************
//*************** UPLOAD AND INSERT FILE
//*****************************************************************************************************************************************************
//*****************************************************************************************************************************************************

	if ((isset($_FILES['usrfl']) or isset($GLOBALS[$usrfl . '_name'])) && $_GET['split']!=2) {
		// move the uploaded file to where we can work with it
		$file = ep_get_uploaded_file('usrfl'); // populate our array $file

		// langer - this copies the file to our temp dir. This is required so it can be read into file array.
		// add option to change name so it does not over-write any downloads there in same hour?
		// maybe just add option for seconds to all filenames - problem solved.
		// our uploads don't have any time stamp, but users can manage this..

		//$new_file_prefix = 'uploaded-'.strftime('%y%m%d-%H%I%S').'-';
		if (is_uploaded_file($file['tmp_name'])) {
			ep_copy_uploaded_file($file, DIR_FS_CATALOG . $tempdir);
		}
		$display_output .= sprintf(EASYPOPULATE_DISPLAY_UPLOADED_FILE_SPEC, $file['tmp_name'], $file['name'], $file['size']);

		// get the entire file into an array
		$readed = file(DIR_FS_CATALOG . $tempdir . $file['name']);
	}


	if (isset($_POST['localfile']) && $_GET['split']!=2){// dont think $_GET['split']!=2) is rwd, but hey...
		// move the file to where we can work with it
		$file = ep_get_uploaded_file('localfile');

		//$file['size'] = filesize(DIR_FS_CATALOG . $tempdir . $filename);

		$display_output .= sprintf(EASYPOPULATE_DISPLAY_LOCAL_FILE_SPEC, $file['name']);

		// get the entire file into an array
		$readed = file(DIR_FS_CATALOG . $tempdir . $file['name']);
	}

	// split = 2 means we are using ep to create page for uploading each split file in turn
	if ($_GET['split'] == 2) {

		$printsplit = EASYPOPULATE_FILE_SPLITS_HEADING; //'';
		// let's set our variables for each pass...
		if (isset($_FILES['usrfl']) or isset($GLOBALS[$usrfl . '_name'])) {
			// the 1st pass.. could make it not upload 1st split file I guess (looks nicer?)
			//easy test would be to change $thisiteration = 1; to 0 instead.

			$maxcount = $filecount; // last file set this to max
			$splitfname = $file['name'];
			$thisiteration = 0; // let's begin with no file
		} else {
			$maxcount = $_GET['fc'];
			$splitfname = $_GET['fn'];
			$thisiteration = $_GET['fi'];
		}
		$nextiteration = $thisiteration + 1;

		$this_file = EASYPOPULATE_FILE_SPLITS_PREFIX . $thisiteration . "-" . $splitfname;
		$next_file = EASYPOPULATE_FILE_SPLITS_PREFIX . $nextiteration . "-" . $splitfname;

		for ($i=1, $n=$thisiteration; $i<=$n; $i++) {
			$printsplit .= EASYPOPULATE_FILE_SPLIT_COMPLETED . EASYPOPULATE_FILE_SPLITS_PREFIX . $i . "-" . $splitfname . '<br />';
		}
		if ($thisiteration == $maxcount)  {
			$printsplit .= EASYPOPULATE_FILE_SPLITS_DONE;
		} else {
			$printsplit .= '<a href="easypopulate.php?fc=' . $maxcount . '&fn=' . $splitfname . '&split=2&fi=' . $nextiteration . '">' . EASYPOPULATE_FILE_SPLIT_ANCHOR_TEXT . $next_file . '</a><br />';
		}
		for ($i=$nextiteration+1, $n=$maxcount; $i<=$n; $i++) {
			$printsplit .= EASYPOPULATE_FILE_SPLIT_PENDING . EASYPOPULATE_FILE_SPLITS_PREFIX . $i . "-" . $splitfname . '<br />';
		}

		if ($thisiteration == 0) {
			$readed = array();// don't start until user says..
		} else {
		// get the entire file into an array
		$readed = file(DIR_FS_CATALOG . $tempdir . $this_file);
		$display_output .= sprintf(EASYPOPULATE_DISPLAY_LOCAL_FILE_SPEC, $this_file);
		}
	}


//*****************************************************************************************************************************************************
//*****************************************************************************************************************************************************
//*************** PROCESS UPLOAD FILE
//*****************************************************************************************************************************************************
//*****************************************************************************************************************************************************

	// these are the fields that will be defaulted to the current values in the database if they are not found in the incoming file
	// langer - why not qry products table and use result array??
	$default_these = array(
		'v_products_image',
		'v_categories_id',
		'v_products_price',
		'v_products_quantity',
		'v_products_weight',
		'v_date_added',
		'v_date_avail',
		'v_instock',
		'v_tax_class_title',
		'v_manufacturers_name',
		'v_manufacturers_id',
    'v_manufactures_code',
    'v_product_style',
    'v_product_finish',
    'v_product_material',
    'v_product_colour',
    'v_bulbs_qty',
    'v_bulbs_watage',
    'v_bulbs_type',
    'v_bulbs_cap',
    'v_bulbs_included',
    'v_dimensions_height',
    'v_dimensions_width',
    'v_dimensions_depth',
    'v_product_dia',
    'v_product_min_drop',
    'v_product_max_drop',
    'v_product_length',
    'v_product_recess',
    'v_product_shade_inc',
    'v_ip_rating',
    'v_product_voltage',
    'v_product_guarantee',
    'v_product_options',
    'v_product_saftey_class',
    'v_product_transformer',
    'v_product_driver',
    'v_product_cut_out',
    'v_product_surface_temp',
    'v_product_cable',
    'v_product_carriage',
    'v_product_statements',
    'v_product_tilt',
    'v_product_variant',
    'v_xsell',
    'v_priority'
	);

	// now we string the entire thing together in case there were carriage returns in the data
	$newreaded = "";
	foreach ($readed as $read){
		$newreaded .= $read;
	}

	// now newreaded has the entire file together without the carriage returns
	// if for some reason we get quotes around our EOREOR, adjust our row delimiter to suit
	// this assumes the only change from \tEOREOR would be \t"EOREOR" (other text delimiters won't work yet)
	if (strpos($newreaded, '"EOREOR"') == false) {
		$row_separator = $separator . 'EOREOR';
	} else {
		$row_separator = $separator . '"EOREOR"';
	}

	$readed = explode($row_separator,$newreaded);
	//$readed = explode('EOREOR',$newreaded);

	// Now we'll populate the filelayout based on the header row.
	$theheaders_array = explode($separator, $readed[0]); // explode the first row, it will be our filelayout (column headings)
	$lll = 0;
	$filelayout = array();
	foreach($theheaders_array as $header){
		$cleanheader = trim(str_replace('"', '', $header)); // remove any added quotes
		// are all of our headings prefixed by v_? if not, fail upload!!
		if (substr($cleanheader,0, 2) != 'v_') {
			// we probably do not have a tab file, or 1 or more of our headings are missing..
			// error msg & fail
			// need an error var to change "Upload Complete" to "Upload Failed" or some such
			// continue;
		}
		$filelayout[$cleanheader] = $lll++; // $filelayout['1st_header'] = 1, etc..
	}
	// END CREATE HEADER LAYOUT
	// langer - output $filelayout array; $readed

	unset($readed[0]); //  we don't want to process the headers with the data
	// unset($readed[(count($readed))]); // the last row is always blank, so let's drop it too (or is it?? maybe not for non-windows..)
	// now we've got the array broken into parts by the expicit end-of-row marker.

//*****************************************************************************************************************************************************
//*****************************************************************************************************************************************************
//***************  BEGIN PROCESSING DATA
//*****************************************************************************************************************************************************
//*****************************************************************************************************************************************************
	foreach ($readed as $file_row) {
		// first we clean up the row of data // chop any blanks from each end
		$file_row = trim($file_row," ");

    if ($file_row=="\r\n")continue;
		// this does not account for instances where our column separator (tab, comma, etc) may exist in a text-delimited field
		// how can we explode on these, but only if not delimited??
		// eg model => url => "decription - this tab is part of description => and should not explode" => etc =>
		// the assumption is that a blank field would not have a text delimiter, so only delimited fields will satisfy the regex
		// maybe instead use preg_split on $separator where NOT /\t\".*[$separator].*\"\t/ - this leaves the delimiter in the data, only cleaning out problem ones below!
		// blow it into an array, splitting on $separator (tabs only at the mo..)

		// lets replace any $separator within our text delimiters, and put them back after perhaps...
		// /\t\".*?[$separator].*?\"\t/
		// $ep_replace = '/'.$separator.$txt_delim.'.*'.$separator.'.*'.$txt_delim.$separator.'/';

		//$items = str_replace($ep_replace,'EP~REPLACE~EP',$items);
		$items = explode($separator, $file_row);

//*****************************************************************************************************************************************************
//*****************************************************************************************************************************************************
//*************** IMAGE PROCESSING
//*****************************************************************************************************************************************************
//*****************************************************************************************************************************************************
    $ext = strtolower($items[$filelayout['v_products_image']]);
    $ext = substr($ext, strlen($ext)-4,4);

    $items[$filelayout['v_products_image']] = trim($items[$filelayout['v_products_image']]);
    //Check extension
    if($ext != '.jpg' && $ext != '.gif'){
        $items[$filelayout['v_products_image']] .= '.jpg';
    }
    //Check for illegal chars - at present only + is not allowed. This is a requirement of image handler
    if (ereg("\+", $items[$filelayout['v_products_image']])) {
        $oifn = $items[$filelayout['v_products_image']];
        $items[$filelayout['v_products_image']] = ereg_replace("\+", "-", $items[$filelayout['v_products_image']]);
        $display_output .= sprintf(EASYPOPULATE_DISPLAY_INVALID_IMAGENAME, $items[$filelayout['v_products_model']], $oifn, $items[$filelayout['v_products_image']]);
    }

//*****************************************************************************************************************************************************
//*****************************************************************************************************************************************************
//*************** PROCESS MY FIELDS WITH SPECIAL CHARS
//*****************************************************************************************************************************************************
//*****************************************************************************************************************************************************
    if(strpos($items[$filelayout['v_product_variant']],'*') > 0){
      $items[$filelayout['v_product_variant']] = str_replace('*', '<br />', $items[$filelayout['v_product_variant']]);
    }

//*****************************************************************************************************************************************************
//*****************************************************************************************************************************************************
//*************** HANDLE MY FIELDS - Set empty to null etc
//*****************************************************************************************************************************************************
//*****************************************************************************************************************************************************
    //Now price
    $now_price = $items[$filelayout['v_now_price']];
    if($now_price == '' || $now_price=='0' ){
      $items[$filelayout['v_now_price']] = NULL;
    }
    //family caption
    if($items[$filelayout['v_family_caption']]=='')$items[$filelayout['v_family_caption']]=NULL;


//*****************************************************************************************************************************************************
//*****************************************************************************************************************************************************
//*************** DATA CLEANING
//*****************************************************************************************************************************************************
//*****************************************************************************************************************************************************
		// make sure all non-set things are set to '';
		// and strip the quotes from the start and end of the stings.
		// escape any special chars for the database.
		// langer - $key is heading name, $value is column number
		foreach($filelayout as $key => $value) {
			$i = $filelayout[$key];// langer - i is our column number, $key the name of our column heading. Check exist using if (array_key_exists($filelayout['v_model'])) ??
			if (zen_not_null($items[$i]) == false) {
				// let's make our null item data an empty string
				$items[$i]='';
			} else {
				// Check to see if either of the magic_quotes are turned on or off;
				// And apply filtering accordingly.
				if (function_exists('ini_get') && ini_get('magic_quotes_runtime') == 1) {
					//echo 'magic quotes on<br />';
					$items[$i] = trim($items[$i]);
					// The magic_quotes_runtime are on, so lets account for them
					// check if the 2nd & last character is a quote (/"xxxx/");
					// if it is, chop off the quotes and slashes.
					while (substr($items[$i],-1) == '"' && substr($items[$i],1,1) == '"') {
						$items[$i] = substr($items[$i],2,strlen($items[$i])-4);
					}
					// now any remaining doubled double quotes should be converted to one doublequote
					$items[$i] = str_replace('\"\"','"',$items[$i]);
					if ($replace_quotes == true){
						$items[$i] = str_replace('\"',"&#34",$items[$i]);
						$items[$i] = str_replace("\'","&#39",$items[$i]); // is this right? maybe "\\\'","&#39", as we are checking for literal escape and literal apostrophe??
						//$items[$i] = str_replace("\\\'","&#39",$items[$i]); // try if errors reported, though handling of db updates should be ok.
					}
				} else {
					// check if the 1st & last character are quotes if yes, chop off the 1st and last character of the string.
					$items[$i] = trim($items[$i]);
					//$debug_log = debug_log($items[$i],'item');
					while (substr($items[$i],-1) == '"' && substr($items[$i],0,1) == '"') {
						$items[$i] = substr($items[$i],1,strlen($items[$i])-2);
					}
					// now any remaining doubled double quotes should be converted to one doublequote
					$items[$i] = str_replace('""','"',$items[$i]);
					if ($replace_quotes == true){
						$items[$i] = str_replace('"',"&#34",$items[$i]);
						$items[$i] = str_replace("'","&#39",$items[$i]);
					}
				}
			}
		}


//*****************************************************************************************************************************************************
//*****************************************************************************************************************************************************
//*************** DEAL WITH MULIPLE CATEGORIES
//*****************************************************************************************************************************************************
//*****************************************************************************************************************************************************
      $additional_categories = explode('*', $items[$filelayout['v_categories_id']]);
      $items[$filelayout['v_categories_id']] = $additional_categories[0];


//*****************************************************************************************************************************************************
//*****************************************************************************************************************************************************
//*************** now do a query to get the record's current contents
//*****************************************************************************************************************************************************
//*****************************************************************************************************************************************************
		$sql = "SELECT
			p.products_id as v_products_id,
			p.products_model as v_products_model,
			p.products_image as v_products_image,
			p.products_price as v_products_price,
			p.products_weight as v_products_weight,
			p.products_date_added as v_date_added,
			p.products_date_available as v_date_avail,
			p.products_tax_class_id as v_tax_class_id,
			p.products_quantity as v_products_quantity,
			p.manufacturers_id as v_manufacturers_id,
			subc.categories_id as v_categories_id
			FROM
			".TABLE_PRODUCTS." as p,
			".TABLE_CATEGORIES." as subc,
			".TABLE_PRODUCTS_TO_CATEGORIES." as ptoc
			WHERE
			p.products_id = ptoc.products_id AND
			p.products_model = '" . zen_db_input($items[$filelayout['v_products_model']]) . "' AND
			ptoc.categories_id = subc.categories_id
			";
		$result = ep_query($sql);
		$row =  mysql_fetch_array($result);
		$product_is_new = true;

		while ($row){
			$product_is_new = false;
			// Get current products descriptions and categories for this model from database
			// $row at present consists of current product data for above fields only (in $sql)
			// since we have a row, the item already exists.

//*****************************************************************************************************************************************************
//*****************************************************************************************************************************************************
//*************** CHECK IF ITEM IS TO BE DELETED
//*****************************************************************************************************************************************************
//*****************************************************************************************************************************************************
			if ($items[$filelayout['v_status']] == 9) {
				$display_output .= sprintf(EASYPOPULATE_DISPLAY_RESULT_DELETED, $items[$filelayout['v_products_model']]);
				ep_remove_product($items[$filelayout['v_products_model']]);
                ///Remove xsell references
                $xpid = ep_pID_mID($items[$filelayout['v_products_model']]);
                if($xpid != NULL){
                  $sql = "DELETE FROM " . TABLE_PRODUCTS_MXSELL . "1 WHERE (products_id = $xpid  OR xsell_id = $xpid )";
                  $db->Execute($sql);
                }else{
                  $display_output .= " - Unable to delete ".$items[$filelayout['v_products_model']]." from xsell as there is no entry for it<br/>";
                }
				continue 2;
			}

//*****************************************************************************************************************************************************
//*****************************************************************************************************************************************************
//***************  Let's get all the data we need and fill in all the fields that need to be defaulted to the current values, get the description and set the vals
//*****************************************************************************************************************************************************
//*****************************************************************************************************************************************************
	        $sql2 = "SELECT *
		            FROM ".TABLE_PRODUCTS_DESCRIPTION."
		            WHERE products_id = " . $row['v_products_id'] . " AND language_id = '1'";
	        $result2 = ep_query($sql2);
	        $row2 =  mysql_fetch_array($result2);
	        // Need to report from ......_name_1 not ..._name_0
	        $row['v_products_name_1']    = $row2['products_name'];// name assigned
	        $row['v_products_description_1']   = $row2['products_description'];// description assigned

//*****************************************************************************************************************************************************
//*****************************************************************************************************************************************************
//*************** retrieve current manufacturer name from db for this product if exist
//*****************************************************************************************************************************************************
//*****************************************************************************************************************************************************
			if ($row['v_manufacturers_id'] != ''){
				$sql2 = "SELECT manufacturers_name
					FROM ".TABLE_MANUFACTURERS."
					WHERE
					manufacturers_id = " . $row['v_manufacturers_id'];
				$result2 = ep_query($sql2);
				$row2 =  mysql_fetch_array($result2);
				$row['v_manufacturers_name'] = $row2['manufacturers_name'];
			}else{
                $display_output .= sprintf(EASYPOPULATE_DISPLAY_RESULT_NO_MANUFACTURE, $items[$filelayout['v_products_model']]);
            }

			// the $$thisvar is on purpose: it creates a variable named what ever was in $thisvar and sets the value
			// sets them to $row value, which is the existing value for these fields in the database
			foreach ($default_these as $thisvar){
				$$thisvar = $row[$thisvar];
			}

			$row =  mysql_fetch_array($result);// langer - reset our array for next stage??
		}

//*****************************************************************************************************************************************************
//*****************************************************************************************************************************************************
//***************  Categories start.
//*****************************************************************************************************************************************************
//*****************************************************************************************************************************************************
        $thecategory_id = $additional_categories[0];// master category id

		/**
		* langer - We have now set our PRODUCT_TABLE vars for existing products, and got our default descriptions & categories in $row still
		* new products start here!
		*/

		/**
		* langer - let's have some data error checking..
		* inputs: $items; $filelayout; $product_is_new (no reliance on $row)
		*/
		if ($items[$filelayout['v_status']] == 9 && zen_not_null($items[$filelayout['v_products_model']])) {
			// new delete got this far, so cant exist in db. Cant delete what we don't have...
			$display_output .= sprintf(EASYPOPULATE_DISPLAY_RESULT_DELETE_NOT_FOUND, $items[$filelayout['v_products_model']]);
			continue;
		}
		if ($product_is_new == true) {
			if (!zen_not_null(trim($items[$filelayout['v_categories_id']])) && zen_not_null($items[$filelayout['v_products_model']])) {
			// let's skip this new product without a master category..
			$display_output .= sprintf(EASYPOPULATE_DISPLAY_RESULT_CATEGORY_NOT_FOUND, $items[$filelayout['v_products_model']], ' new');
			continue;
			} else {
				// minimum test for new product - model(already tested below), name, price, category, taxclass(?), status (defaults to active)
				// to add
			}
		} else { // not new product
			if (!zen_not_null(trim($items[$filelayout['v_categories_id']])) && isset($filelayout['v_categories_name_1'])) {
				// let's skip this existing product without a master category but has the column heading
				// or should we just update it to result of $row (it's current category..)??
				$display_output .= sprintf(EASYPOPULATE_DISPLAY_RESULT_CATEGORY_NOT_FOUND, $items[$filelayout['v_products_model']], '');
				foreach ($items as $col => $langer) {
					if ($col == $filelayout['v_products_model']) continue;
					$display_output .= print_el($langer);
				}
				continue;
			}
		}
		/*
		* End data checking
		**/
		// this is an important loop.  What it does is go thru all the fields in the incoming file and set the internal vars.
		// Internal vars not set here are either set in the loop above for existing records, or not set at all (null values)
		// the array values are handled separately, although they will set variables in this loop, we won't use them.
		// $key is column heading name, $value is column number for the heading..
		// langer - this would appear to over-write our defaults with null values in $items if they exist
		// in other words, if we have a file heading, then you want all listed models updated in this field
		// add option here - update all null values, or ignore null values???
		foreach($filelayout as $key => $value){
			$$key = $items[$value];
		}

		// so how to handle these?  we shouldn't build the array unless it's been giving to us.
		// The assumption is that if you give us names and descriptions, then you give us name and description for all applicable languages
		//foreach ($langcode as $lang){
		//	$l_id = $lang['id'];
			if (isset($filelayout['v_products_name_1'])){ // do for each language in our upload file if exist
				// we set dynamically the language vars
				$v_products_name[1] = smart_tags($items[$filelayout['v_products_name_1']],$smart_tags,$cr_replace,false);
				$v_products_description[1] = smart_tags($items[$filelayout['v_products_description_1']],$smart_tags,$cr_replace,$strip_smart_tags);
			}
		//}
		//elari... we get the tax_clas_id from the tax_title - from zencart??
		//on screen will still be displayed the tax_class_title instead of the id....
		//if (isset($v_tax_class_title)){
		//	$v_tax_class_id = ep_get_tax_title_class_id($v_tax_class_title);
		//}
		//we check the tax rate of this tax_class_id
		//$row_tax_multiplier = ep_get_tax_class_rate($v_tax_class_id);

		//And we recalculate price without the included tax...
		//Since it seems display is made before, the displayed price will still include tax
		//This is same problem for the tax_clas_id that display tax_class_title
		//if ($price_with_tax == true){
		//	$v_products_price = round( $v_products_price / (1 + ( $row_tax_multiplier * $price_with_tax/100) ), 4);
		//}

		// if they give us one category, they give us all 6 categories
		// langer - this does not appear to support more than 7 categories??
		//unset ($v_categories_name); // default to not set.

		// langer - if null, make products qty = 1. Why?? make it 0
		//if (trim($v_products_quantity) == '') {
		//	$v_products_quantity = 0;
		//}

		if ($sql_fail_test == true) {
			// The following original code causes new product to fail - useful for testing
			// I keep it because I think something I changed introduced the error.. )-:
			if ($v_date_avail == '') {
				$v_date_avail = "NULL";
			} else {
				$v_date_avail = '"' . $v_date_avail . '"';
			}

		} else {
			// the (new) good code...
			$v_date_avail = zen_not_null(trim($v_date_avail,"\"")) ? '"' . trim($v_date_avail,"\"") . '"' : "NULL";
		}
		$v_date_added = zen_not_null(trim($v_date_added,"\"")) ? '"' . trim($v_date_added,"\"") . '"' : "NULL";

		// default the stock if they spec'd it or if it's blank
		$v_db_status = '1'; // default to active
		if ($v_status == '0'){
			// they told us to deactivate this item
			$v_db_status = '0';
		}
		//if (EASYPOPULATE_CONFIG_ZERO_QTY_INACTIVE == 'true' && $v_products_quantity == 0) {
			// if they said that zero qty products should be deactivated, let's deactivate if the qty is zero
		//	$v_db_status = '0';
		//}

		if ($v_manufacturer_id == '') {
			$v_manufacturer_id = "NULL";
		}

		if (trim($v_products_image) == '') {
			$v_products_image = PRODUCTS_IMAGE_NO_IMAGE;
		}

    ///Check field lengths
		if (strlen($v_products_model) > $modelsize ){
			$display_output .= sprintf(EASYPOPULATE_DISPLAY_RESULT_MODEL_NAME_LONG, $v_products_model);
			continue;
		}
    if (strlen($v_now_price) > 100 ){
      $display_output .= sprintf('<br /><font size="15px" color="red" ><b>SKIPPED! - Now Price: </b>%s -  name too long</font>', $v_now_price);
      continue;
    }
    ////////////////////////////////////////////////////////////////////////////
    //                                                                        //
    //    MANUFACTURE                                                         //
    //                                                                        //
    ////////////////////////////////////////////////////////////////////////////
    if ($ep_debug_logging == true)  write_debug_log("\nDEBUG Line:" .__LINE__."\n");

		// OK, we need to convert the manufacturer's name into id's for the database
		if ( isset($v_manufacturers_name) && $v_manufacturers_name != '' ){
			$sql = "SELECT man.manufacturers_id
				FROM ".TABLE_MANUFACTURERS." as man
				WHERE
					man.manufacturers_name = '" . zen_db_input($v_manufacturers_name) . "'";
			$result = ep_query($sql);
			$row =  mysql_fetch_array($result);
			if ( $row != '' ){
				foreach( $row as $item ){
					$v_manufacturer_id = $item;
				}
			} else {
				$sql = "SELECT MAX( manufacturers_id) max FROM ".TABLE_MANUFACTURERS;
				$result = ep_query($sql);
				$row =  mysql_fetch_array($result);
				$max_mfg_id = $row['max']+1;
				// default the id if there are no manufacturers yet
				if (!is_numeric($max_mfg_id) ){
					$max_mfg_id=1;
				}

				$sql = "INSERT INTO " . TABLE_MANUFACTURERS . "(
					manufacturers_id,
					manufacturers_name,
					date_added,
					last_modified
					) VALUES (
					$max_mfg_id,
					'" . zen_db_input($v_manufacturers_name) . "',
					CURRENT_TIMESTAMP,
					CURRENT_TIMESTAMP
					)";
				$result = ep_query($sql);
				$v_manufacturer_id = $max_mfg_id;
			}
		}

		// insert new, or update existing, product
		if ($v_products_model != "") {
			//   products_model exists so we can continue

			// First we check to see if this is a product in the current db.
      if ($ep_debug_logging == true)  write_debug_log("\nDEBUG Line:" .__LINE__."\n");
			$result = ep_query("SELECT products_id FROM ".TABLE_PRODUCTS." WHERE (products_model = '" . zen_db_input($v_products_model) . "')");

			if (mysql_num_rows($result) == 0)  {
        ////////////////////////////////////////////////////////////////////////////
        //                                                                        //
        //    NEW PRODUCT ACTUAL INSERT INTO DB TABLE PRODUCTS                    //
        //                                                                        //
        ////////////////////////////////////////////////////////////////////////////

				if ($v_categories_id == ''){
					// check category exists - return without adding new if not
					$display_output .= "<font size=\"15px\" color='red'> <b> !No Category for New Product - Rejected!</b></font><br>";
					break;
				}

				$v_date_added = ($v_date_added == 'NULL') ? CURRENT_TIMESTAMP : $v_date_added;

				$sql = "SHOW TABLE STATUS LIKE '".TABLE_PRODUCTS."'";
				$result = ep_query($sql);
				$row =  mysql_fetch_array($result);
				$max_product_id = $row['Auto_increment'];
				//echo 'next id '.$max_product_id.'<br />';
				if (!is_numeric($max_product_id) ){
					$max_product_id=1;
				}
				$v_products_id = $max_product_id;

				$query = "INSERT INTO ".TABLE_PRODUCTS." (
						products_image,
						products_model,
						products_price,
						products_status,
						products_last_modified,
						products_date_added,
						products_date_available,
						products_tax_class_id,
						products_weight,
						products_quantity,
						manufacturers_id, master_categories_id)
							VALUES (
								'".zen_db_input($v_products_image)."',";
				// redundant image mods removed
				$query .="'".zen_db_input($v_products_model)."',
									'".zen_db_input($v_products_price)."',
									'".zen_db_input($v_db_status)."',
									CURRENT_TIMESTAMP,
									$v_date_added,
									$v_date_avail,
									'".zen_db_input($v_tax_class_id)."',
									'".zen_db_input($v_products_weight)."',
									'1',
									'$v_manufacturer_id', " . zen_db_input($thecategory_id) . ")";
								//echo 'New product SQL:'.$query.'<br />';


           if ($ep_debug_logging == true)  write_debug_log("\nDEBUG Line:" .__LINE__."\n");
					$result = ep_query($query);
					if ($result == true) {
						$display_output .= sprintf(EASYPOPULATE_DISPLAY_RESULT_NEW_PRODUCT, $v_products_model);
					} else {
						$display_output .= sprintf(EASYPOPULATE_DISPLAY_RESULT_NEW_PRODUCT_FAIL, $v_products_model);
						continue; // langer - any new categories however have been created by now..Adding into product table needs to be 1st action?
					}
					foreach ($items as $col => $langer) {
						if ($col == $filelayout['v_products_model']) continue;
						$display_output .= print_el($langer);
					}

			} else {
        ////////////////////////////////////////////////////////////////////////////
        //                                                                        //
        //    UPDATE EXISTING PRODUCT FOR TABLE PRODUCTS                          //
        //                                                                        //
        ////////////////////////////////////////////////////////////////////////////

				// existing product, get the id from the query and update the product data

				// if date added is null, let's keep the existing date in db..
				$v_date_added = ($v_date_added == 'NULL') ? $row['v_date_added'] : $v_date_added; // if NULL, let's try to use date in db
				$v_date_added = zen_not_null($v_date_added) ? $v_date_added : CURRENT_TIMESTAMP; // if updating, but date added is null, we use today's date

        if ($ep_debug_logging == true)  write_debug_log("\nDEBUG Line:" .__LINE__."\n");
				$row =  mysql_fetch_array($result);
				$v_products_id = $row['products_id'];
				$row =  mysql_fetch_array($result); // langer - is this to reset array, or an accidental duplication?!?
				$query = 'UPDATE '.TABLE_PRODUCTS.'
						SET
						products_price="'.zen_db_input($v_products_price).
            '" ,products_image="'.zen_db_input($v_products_image);
            $query .= '", products_weight="'.zen_db_input($v_products_weight). '"' .
						//'", products_tax_class_id="'.zen_db_input($v_tax_class_id) . '"' .
						//'", products_date_available= ' . $v_date_avail .
						//', products_date_added= ' . $v_date_added .
						', products_last_modified=CURRENT_TIMESTAMP' .
						', products_quantity="' . zen_db_input($v_products_quantity) .
						'" ,manufacturers_id=' . $v_manufacturer_id .
						' , products_status=' . zen_db_input($v_db_status) .
            ', master_categories_id= ' . zen_db_input($thecategory_id) . '
						WHERE
							(products_id = "'. $v_products_id . '")';
        if ($ep_debug_logging == true)  write_debug_log("\nDEBUG Line:" .__LINE__."\n");
				$result = ep_query($query);

					if ($result == true) {
						$display_output .= sprintf(EASYPOPULATE_DISPLAY_RESULT_UPDATE_PRODUCT, $v_products_model);
						foreach ($items as $col => $langer) {
							if ($col == $filelayout['v_products_model']) continue;
							$display_output .= print_el($langer);
						}
					} else {
						$display_output .= sprintf(EASYPOPULATE_DISPLAY_RESULT_UPDATE_PRODUCT_FAIL, $v_products_model);
					}
			}


      $e_sql = "SELECT products_id FROM " . TABLE_PRODUCTS_EXTRA_FIELDS . " WHERE products_id = $v_products_id";
      if ($ep_debug_logging == true)  write_debug_log("\nDEBUG Line:" .__LINE__."\n");
      $e_res = ep_query($e_sql);
      if (mysql_num_rows($e_res) == 0) {

        ////////////////////////////////////////////////////////////////////////////
        //                                                                        //
        //    INSERT NEW INTO PRODUCTS EXTRA FIELDS                               //
        //                                                                        //
        ////////////////////////////////////////////////////////////////////////////

        if($v_now_price=='')$v_now_price=NULL;

        $e_query = "INSERT INTO ".TABLE_PRODUCTS_EXTRA_FIELDS." (
        products_id, manufactures_code, product_style, product_finish, product_material, product_colour,
        bulbs_qty, bulbs_watts, bulbs_type, bulbs_cap, bulbs_included,
        dimensions_height, dimensions_width, dimensions_depth, product_dia, product_min_drop, product_max_drop, product_length, product_recess,
        product_shade_inc, ip_rating, product_voltage, product_guarantee, product_options, product_safety_class,
        product_transformer, product_driver, product_cut_out, product_surface_temp, product_cable, product_carriage,
        product_statements, product_tilt, product_variant, product_priority, family_caption, now_price,
        show_price, rrp, rate_1, rate_2, rate_3, bulbs_s1, bulbs_s2, web_price) VALUES (
          '".zen_db_input($v_products_id)."',
          '".zen_db_input($v_manufactures_code)."',
          '".zen_db_input($v_product_style)."',
          '".zen_db_input($v_product_finish)."',
          '".zen_db_input($v_product_material)."',
          '".zen_db_input($v_product_colour)."',
          '".zen_db_input($v_bulbs_qty)."',
          '".zen_db_input($v_bulbs_watage)."',
          '".zen_db_input($v_bulb_type)."',
          '".zen_db_input($v_bulb_cap)."',
          '".zen_db_input($v_bulb_inc)."',
          '".zen_db_input($v_dimensions_height)."',
          '".zen_db_input($v_dimensions_width)."',
          '".zen_db_input($v_dimensions_depth)."',
          '".zen_db_input($v_product_dia)."',
          '".zen_db_input($v_product_min_drop)."',
          '".zen_db_input($v_product_max_drop)."',
          '".zen_db_input($v_product_length)."',
          '".zen_db_input($v_product_recess)."',
          '".zen_db_input($v_product_shade_inc)."',
          '".zen_db_input($v_ip_rating)."',
          '".zen_db_input($v_product_voltage)."',
          '".zen_db_input($v_product_guarantee)."',
          '".zen_db_input($v_product_options)."',
          '".zen_db_input($v_product_saftey_class)."',
          '".zen_db_input($v_product_transformer)."',
          '".zen_db_input($v_product_driver)."',
          '".zen_db_input($v_product_cut_out)."',
          '".zen_db_input($v_product_surface_temp)."',
          '".zen_db_input($v_product_cable)."',
          '".zen_db_input($v_product_carriage)."',
          '".zen_db_input($v_product_statements)."',
          '".zen_db_input($v_product_tilt)."',
          '".zen_db_input($v_product_variant)."',
          '".zen_db_input($v_priortity)."',
          '".zen_db_input($v_family_caption)."',
          '".zen_db_input($v_now_price)."',
          '".zen_db_input($v_show_price)."',
          '".zen_db_input($v_rrp)."',
          '".zen_db_input($v_rate_1)."',
          '".zen_db_input($v_rate_2)."',
          '".zen_db_input($v_rate_3)."',
          '".zen_db_input($v_bulbs_s1)."',
          '".zen_db_input($v_bulbs_s2)."',
          '".zen_db_input($v_web_price)."'
          )";
          //insert extra fields
          if ($ep_debug_logging == true)  write_debug_log("\nDEBUG Line:" .__LINE__."\n");
          $result = ep_query($e_query);
      }else{

        //********************************************
        //Update record in products extra fields
        //********************************************

        if($v_now_price=='')$v_now_price=NULL;

        $e_query = 'UPDATE ' . TABLE_PRODUCTS_EXTRA_FIELDS . ' SET '.
          'manufactures_code ="'. zen_db_input($v_manufactures_code) . '"' .
          ', product_style ="' . zen_db_input($v_product_style) . '"' .
          ', product_finish ="' . zen_db_input($v_product_finish) . '"' .
          ', product_material ="' . zen_db_input($v_product_material) . '"' .
          ', product_colour ="' . zen_db_input($v_product_colour) . '"' .
          ', bulbs_qty ="' . zen_db_input($v_bulbs_qty) . '"' .
          ', bulbs_watts ="' . zen_db_input($v_bulbs_watage)  . '"' .
          ', bulbs_type ="' . zen_db_input($v_bulb_type) . '"' .
          ', bulbs_cap ="' . zen_db_input($v_bulb_cap) . '"' .
          ', bulbs_included ="' . zen_db_input($v_bulbs_inc) . '"' .
          ', dimensions_height ="' . zen_db_input($v_dimensions_height) . '"' .
          ', dimensions_width ="' . zen_db_input($v_dimensions_width) . '"' .
          ', dimensions_depth ="' . zen_db_input($v_dimensions_depth) . '"' .
          ', product_dia ="' . zen_db_input($v_product_dia) . '"' .
          ', product_min_drop ="' . zen_db_input($v_product_min_drop) . '"' .
          ', product_max_drop ="' . zen_db_input($v_product_max_drop) . '"' .
          ', product_length ="' . zen_db_input($v_product_length) . '"' .
          ', product_recess ="' . zen_db_input($v_product_recess) . '"' .
          ', product_shade_inc ="' . zen_db_input($v_product_shade_inc) . '"' .
          ', ip_rating ="' . zen_db_input($v_ip_rating) . '"' .
          ', product_voltage ="' . zen_db_input($v_product_voltage) . '"' .
          ', product_guarantee ="' . zen_db_input($v_product_guarantee)  . '"' .
          ', product_options ="' . zen_db_input($v_product_materials) . '"' .
          ', product_safety_class ="' . zen_db_input($v_product_saftey_class) . '"' .
          ', product_transformer ="' . zen_db_input($v_product_transformer) . '"' .
          ', product_driver ="' . zen_db_input($v_product_driver) . '"' .
          ', product_cut_out ="' . zen_db_input($v_product_cut_out) . '"' .
          ', product_surface_temp ="' . zen_db_input($v_product_surface_temp) . '"' .
          ', product_cable ="' . zen_db_input($v_product_cable) . '"' .
          ', product_tilt ="' . zen_db_input($v_product_tilt) . '"' .
          ', product_variant ="' . zen_db_input($v_product_variant) . '"' .
          ', product_priority ="' . zen_db_input($v_priortity) . '"' .
          ', family_caption ="' . zen_db_input($v_family_caption) . '"' .
          ', now_price ="' . zen_db_input($v_now_price) . '"' .
          ', product_options ="' . zen_db_input($v_product_options) . '"' .
          ', product_carriage ="' . zen_db_input($v_product_carrage) . '"' .
          ', product_statements ="' . zen_db_input($v_product_statements) . '"' .
          ', show_price ="' . zen_db_input($v_show_price) . '"' .
          ', rrp ="' . zen_db_input($v_rrp) . '"' .
          ', rate_1 ="' . zen_db_input($v_rate_1) . '"' .
          ', rate_2 ="' . zen_db_input($v_rate_2) . '"' .
          ', rate_3 ="' . zen_db_input($v_rate_3) . '"' .
          ', bulbs_s1 ="' . zen_db_input($v_bulbs_s1) . '"' .
          ', bulbs_s2 ="' . zen_db_input($v_bulbs_s2) . '"' .
          ', web_price ="' . zen_db_input($v_web_price) . '"
            WHERE
              (products_id = "'. $v_products_id . '")';

          if ($ep_debug_logging == true)  write_debug_log("\nDEBUG Line:" .__LINE__."\n");
          $result = ep_query($e_query);
      }


      //////////////////////////////////////////////////////////////////////////////////////////////
      //
      // the following is common in both the updating an existing product and creating a new product
      //
      //////////////////////////////////////////////////////////////////////////////////////////////


      //*************************
      // Products Descriptions Start
      //*************************
			if (isset($v_products_name)){
				foreach( $v_products_name as $key => $name){
					if ($name!=''){
						$sql = "SELECT * FROM ".TABLE_PRODUCTS_DESCRIPTION." WHERE
								products_id = $v_products_id AND
								language_id = " . $key;
            if ($ep_debug_logging == true)  write_debug_log("\nDEBUG Line:" .__LINE__."\n");
						$result = ep_query($sql);
						if (mysql_num_rows($result) == 0) {
              ////////////////////////////////////////////////////////////////////////////
              //                                                                        //
              //    NEW PRODUCT DESCRIPTION                                             //
              //                                                                        //
              ////////////////////////////////////////////////////////////////////////////

              format_description($v_products_description[$key]);

							$sql =
								"INSERT INTO ".TABLE_PRODUCTS_DESCRIPTION."
									(products_id,
									language_id,
									products_name,
									products_description)
									VALUES (
										'" . $v_products_id . "',	1,
										'" . zen_db_input($name) . "',
										'" . zen_db_input($v_products_description[1]) . "')";

							//echo 'New product desc:'.$sql.'<br />';
              if ($ep_debug_logging == true)  write_debug_log("\nDEBUG Line:" .__LINE__."\n");
							$result = ep_query($sql);
						} else {
              ////////////////////////////////////////////////////////////////////////////
              //                                                                        //
              //    UPDATE PRODUCT DESCRIPTION                                          //
              //                                                                        //
              ////////////////////////////////////////////////////////////////////////////
							// already in the description, let's just update it

              format_description($v_products_description[1]);

							$sql =
								"UPDATE ".TABLE_PRODUCTS_DESCRIPTION." SET products_name='" . zen_db_input($name) . "',	products_description='" . zen_db_input($v_products_description[$key]) . "'	WHERE	products_id = '$v_products_id' AND language_id = '1'";


							//echo 'existing product desc:'.$sql.'<br />';
              if ($ep_debug_logging == true)  write_debug_log("\nDEBUG Line:" .__LINE__."\n");
							$result = ep_query($sql);
						}
					}
				}
			}

			//*************************
			// Products Descriptions End
			//*************************

			// langer - Assign product to category if linked

			if (isset($v_categories_id)){
				//delete all entries in the products to categories table for the product id
        ep_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . "
                  where products_id = '" . $v_products_id . "'");

        $display_output .= 'In categories ';

        foreach($additional_categories as $key => $value){
					$res1 = ep_query('INSERT INTO '.TABLE_PRODUCTS_TO_CATEGORIES.' (products_id, categories_id)
								  VALUES ("' . $v_products_id . '", "' . $value . '")');
          $display_output .= ', '. $value;
        }
			}

      /////////////////////////////////////////////////////////////////////////////////////////////
      //      Common Images                                                                      //
      /////////////////////////////////////////////////////////////////////////////////////////////
      if(isset($v_common_images)&& $v_common_images!=''){
        $display_output .= ' [Common Img ';
        $common_images = explode(';',$v_common_images );
        $sql = "DELETE FROM common_images WHERE products_id = $v_products_id";
        ep_query($sql);
        if(is_array($common_images)){
          foreach($common_images as $col => $image){
            //$ext = strtolower($v_common_images);
            $ext = pathinfo($v_common_images,PATHINFO_EXTENSION );
            if($ext =='')$image .= '.jpg';
            $image = 'family_common/'.$image;
            $sql = "INSERT INTO common_images (products_id, image) VALUES ($v_products_id, '$image')";
            ep_query($sql);
            $display_output .= $image . ',';
            if(strlen($image)>48)$display_output .= $errMsgStart.'COMMON IMAGE '.$image.' IS TO LONG (MAX 30chars filename)'.$errMsgEnd;
          }
        }else{
          //Single image
          $sq = "INSERT INTO common_images (products_id, image) VALUES ($v_products_model, '$v_common_images')";
          ep_query($sql);
          $display_output .= $v_common_images;
        }
        $display_output .= ']';
      }

      /////////////////////////////////////////////////////////////////////////////////////////////
      ////    Cross Sell - Store values for processing after the file loop has finished         ///
      /////////////////////////////////////////////////////////////////////////////////////////////
      if($v_xsell != ''){                                                                       ///
        $xsell_master_array[$v_products_model]=zen_db_prepare_input($v_xsell);                  ///
      }                                                                                         ///
      /////////////////////////////////////////////////////////////////////////////////////////////

		} else {
			// this record is missing the product_model
			$display_output .= EASYPOPULATE_DISPLAY_RESULT_NO_MODEL;
			foreach ($items as $col => $langer) {
			if ($col == $filelayout['v_products_model']) continue;
				$display_output .= print_el($langer);
			}
		}
		// end of row insertion code
	}

	/*************************************************************************************************
	* Post-upload tasks end
	**************************************************************************************************/

  $display_output .= "<br /><br />";


  /////////////////////////////////////////////////////////////////////////////////////////////
  ////    Cross Sell - process                                                              ///
  /////////////////////////////////////////////////////////////////////////////////////////////

  //Delete any entries in xsell table for all items
  //  ob_implicit_flush(true);
  // ob_end_flush();
  foreach($xsell_master_array as $xOrgModel => $xvalues){
    $values = NULL;
    //echo 'D Loop'.$xOrgModel.'-----'.$xvalues.'<br />';
    $xvalues_array = explode(';',$xvalues);
    foreach($xvalues_array as $key => $xvalue){
      if(strpos($xvalue, '*')>0){
        ///wildcard in family code, look up family items and insert them all
        $c_xvalue = trim($xvalue, '*');
        $rs_family = $db->Execute("SELECT products_model FROM " . TABLE_PRODUCTS . " WHERE products_model LIKE '" . $c_xvalue . "%'");
        while(!$rs_family->EOF){
          $values[] = $rs_family->fields['products_model'];
          $rs_family->MoveNext();
        }
      }else{
        //No wildcard so just use the value as given
        $values[] = $xvalue;
      }
    }
    //Get the pID for the base model
    $xpid = ep_pID_mID($xOrgModel);
    if(is_array($values)){
      foreach($values as $value){
        //Get the pID for the item to xsell with base NOTE here $mId is not model id but product id
        $mId = ep_pID_mID($value);
  //echo 'Del Loop -'.$value.'<br />';
  //echo '.';
        //Check product to xsell with exists
        if($mId != NULL){
          //Query db to see if an entry for this xsell exists in the xsell table
          $result = ep_query("SELECT * FROM " . TABLE_PRODUCTS_MXSELL . "1 WHERE (products_id = $xpid AND xsell_id = $mId) OR (products_id = $mId AND xsell_id = $xpid)");
          $row =  mysql_fetch_array($result);
          if($row){
            //xsell exists in table so delete it
            $sql = "DELETE FROM " . TABLE_PRODUCTS_MXSELL . "1 WHERE (products_id = $xpid  AND xsell_id = $mId )OR (products_id = $mId AND xsell_id = $xpid)";
  //echo 'Deleting - '.$sql.'<br />';
  //echo '.';
            ep_query($sql);
          }
        }
      }
    }
  }//end Delete
//echo '<br />';
  //Now insert xsell values
  ob_implicit_flush(true);
  ob_end_flush();

  foreach($xsell_master_array as $xOrgModel => $xvalues){
    $values = NULL;
    //Get the pID for the base model
    $xpid = ep_pID_mID($xOrgModel);
    //Put list of xsell values into array
    //echo 'D Loop'.$xOrgModel.'-----'.$xvalues.'<br />';
    $xsell_array = explode(';', $xvalues);
    foreach($xsell_array as $value){
      $one_way = false;
      $values = array();
      //Check for one way indicator
      if(strpos($value, '!')>0){
        $one_way = true;
        $value = trim($value, '!');
      }
      if(strpos($value, '*')>0){
        ///wildcard in family code, look up family items and insert them all
        $value = trim($value, '*');
        $rs_family = $db->Execute("SELECT products_model FROM " . TABLE_PRODUCTS . " WHERE products_model LIKE '" . $value . "%'");
        while(!$rs_family->EOF){
          $values[] = $rs_family->fields['products_model'];
          $rs_family->MoveNext();
        }
        if(sizeof($values)==0){
          //No family found
           $display_output .= "|<span style='color:Red'><b> xsell fail No family $value found</b></span>";
        }
      }else{
        //No wildcard so just use the value as given
        $values[] = $value;
      }
      //Now loop through all the values, if it is not xselling to a family this will be only 1 item
      foreach($values as $value){
        //Get the pID for the item to xsell with base NOTE here $mId is not model id but product id
        $mId = ep_pID_mID($value);
//echo 'Add '.$value .'('.$mId.') with '.$xOrgModel.'('.$xpid.')<br />';
//echo '.';
        //Avoid xselling the base item with itself
        if($v_products_id == $mId)continue;
        //Check product to xsell with exists
        if($mId != NULL){
          //Check to ensure item has not already been added in this run
          $result = ep_query("SELECT * FROM " . TABLE_PRODUCTS_MXSELL . "1 WHERE (products_id = $xpid AND xsell_id = $mId)");
          $row =  mysql_fetch_array($result);
          if(!$row){
             //Add xsell
             $sql_1 = "INSERT INTO " . TABLE_PRODUCTS_MXSELL . "1 (products_id, xsell_id, sort_order) VALUES ($xpid, $mId, 1)";
//echo $sql_1.'<br />';
//echo '.';
            ep_query($sql_1);
            $xsup = 1;
            $display_output .= "$xOrgModel xsell with $value<br/>";
          }
          //If xsell is two way
          if(!$one_way){
            //Check to ensure item has not already been added in this run
            $result = ep_query("SELECT * FROM " . TABLE_PRODUCTS_MXSELL . "1 WHERE (products_id = $mId AND xsell_id = $xpid)");
            $row =  mysql_fetch_array($result);
            if(!$row){
              //Add xsell
              $sql_2 = "INSERT INTO " . TABLE_PRODUCTS_MXSELL . "1 (products_id, xsell_id, sort_order) VALUES ($mId, $xpid, 1)";
//echo $sql_2.'<br />';
//echo '.';
              ep_query($sql_2);
              $xsup++;
              $display_output .= "$value xsell with $xOrgModel<br/>";
            }
          }
        }else{
          //No xsell item to add base item to
          $display_output .= "<span style='color:Red'><b>$xOrgModel xsell fail No product $value </b></span><br/>";
        }
      }//end foreach($values as $value)
      $display_output .="-------------------------<br/>";
    }//eol foreach($xsell_array as $value)

  }// eof xsell

	$display_output .= EASYPOPULATE_DISPLAY_RESULT_UPLOAD_COMPLETE;
}

// END FILE UPLOADS

// if we had an SQL error anywhere, let's tell the user..maybe they can sort out why
if ($ep_stack_sql_error == true) $messageStack->add(EASYPOPULATE_MSGSTACK_ERROR_SQL, 'caution');

/**
* this is a rudimentary date integrity check for references to any non-existant product_id entries
* this check ought to be last, so it checks the tasks just performed as a quality check of EP...
* langer - to add: data present in table products, but not in descriptions.. user will need product info, and decide to add description, or delete product
*/
if ($_GET['dross'] == 'delete') {
	// let's delete data debris as requested...
	ep_purge_dross();
	// now check it is really gone...
	$dross = ep_get_dross();
	if (zen_not_null($dross)) {
		$string = "Product debris corresponding to the following product_id(s) cannot be deleted by EasyPopulate:\n";
		foreach ($dross as $products_id => $langer) {
			$string .= $products_id . "\n";
		}
		$string .= "It is recommended that you delete this corrupted data using phpMyAdmin.\n\n";
		write_debug_log($string);
		$messageStack->add(EASYPOPULATE_MSGSTACK_DROSS_DELETE_FAIL, 'caution');
	} else {
		$messageStack->add(EASYPOPULATE_MSGSTACK_DROSS_DELETE_SUCCESS, 'success');
	}
} else { // elseif ($_GET['dross'] == 'check')
	// we can choose a config option: check always, or only on clicking a button
	// default action when not deleting existing debris is to check for it and alert when discovered..
	$dross = ep_get_dross();
	if (zen_not_null($dross)) {
		$messageStack->add(sprintf(EASYPOPULATE_MSGSTACK_DROSS_DETECTED, count($dross), zen_href_link(FILENAME_EASYPOPULATE, 'dross=delete')), 'caution');
	}
}

/**
* Changes planned for below
* 1) 1 input field for local and server updating
* 2) default to update directly from HDD, with option to upload to temp, or update from temp
* 3) List temp files with upload, delete, etc options
* 4) Auto detecting of mods - display list of (only) installed mods, with check-box to include in download
* 5) may consider an auto-splitting feature if it can be done.
*     Will detect speed of server, safe_mode etc and determine what splitting level is required (can be over-ridden of course)
*/

// all html templating is now below here.
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
<body onLoad="init()">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
	<table border="0" width="100%" cellspacing="2" cellpadding="2">
		<tr>
<!-- body_text //-->
			<td width="100%" valign="top">
<?php
				echo zen_draw_separator('pixel_trans.gif', '1', '10');
?>
				<table border="0" width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td class="pageHeading"><?php echo "Easy Populate $curver"; ?></td>
					</tr>
				</table>
<?php
				echo zen_draw_separator('pixel_trans.gif', '1', '10');
?>
				<table border="0" width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td valign="top">

							<table width="100%" border="0" cellpadding="8" valign="top">
								<tr>
									<td width="100%">
										<p>
											<form ENCTYPE="multipart/form-data" ACTION="easypopulate.php?split=0" METHOD="POST">
												<div align = "left">
													<b>Upload EP File</b><br />
													<input TYPE="hidden" name="MAX_FILE_SIZE" value="100000000">
													<input name="usrfl" type="file" size="50">
													<input type="submit" name="buttoninsert" value="Insert into db">
													<br />
												</div>
											</form>

											<form ENCTYPE="multipart/form-data" ACTION="easypopulate.php?split=2" METHOD="POST">
												<div align = "left">
													<b>Split EP File</b><br />
													<input TYPE="hidden" name="MAX_FILE_SIZE" value="1000000000">
													<input name="usrfl" type="file" size="50">
													<input type="submit" name="buttonsplit" value="Split file">
													<br />
												</div>
											</form>

											<form ENCTYPE="multipart/form-data" ACTION="easypopulate.php" METHOD="POST">
												<div align = "left">
													<b>Import from Temp Dir (<? echo $tempdir; ?>)</b><br />
													<input TYPE="text" name="localfile" size="50">
													<input type="submit" name="buttoninsert" value="Insert into db">
													<br />
												</div>
											</form>
										</p>
<?php
              echo '<br />' . $printsplit; // our files splitting matrix
              echo $display_output; // upload results
              if (strlen($specials_print) > strlen(EASYPOPULATE_SPECIALS_HEADING)) {
                echo '<br />' . $specials_print . EASYPOPULATE_SPECIALS_FOOTER; // specials summary
              }
              if (strlen($featured_print) > strlen(EASYPOPULATE_FEATURED_HEADING)) {
                echo '<br />' . $featured_print . EASYPOPULATE_FEATURED_FOOTER; // featured product summary
              }
?>										<b>Download Files</b>
										<br /><br />
										<!-- Download file links -  Add your custom fields here -->
										<a href="easypopulate.php?download=stream&dltype=full">Download <b>Complete</b> tab-delimited .txt file to edit</a>

										<br />
										<!--<a href="easypopulate.php?download=stream&dltype=priceqty">Download <b>Model/Price/Qty</b> tab-delimited .txt file to edit</a><br />
										<a href="easypopulate.php?download=stream&dltype=category">Download <b>Model/Category</b> tab-delimited .txt file to edit</a><br />
										<a href="easypopulate.php?download=stream&dltype=attrib">Download <b>Model/Attributes</b> tab-delimited .txt file</a> -->
										<br /><br />
										<b>Create Files in Temp Dir <? echo $tempdir; ?></b>
										<br /><br />
										<a href="easypopulate.php?download=tempfile&dltype=full">Create <b>Complete</b> tab-delimited .txt file in temp dir</a>
<?php if ($products_with_attributes == true) { ?>
										<span class="fieldRequired"> (Attributes Included)</span>
<?php } else { ?>
										<span class="fieldRequired"> (Attributes Not Included)</span>
<?php } ?>
										<br />
										<a href="easypopulate.php?download=tempfile&dltype=priceqty">Create <b>Model/Price/Qty</b> tab-delimited .txt file in temp dir</a><br />
										<a href="easypopulate.php?download=tempfile&dltype=category">Create <b>Model/Category</b> tab-delimited .txt file in temp dir</a><br />
									</td>
								</tr>
							</table>
<?php

              include(DIR_FS_CATALOG . $tempdir . 'fileList.php');
?>

						</td>
					</tr>
				</table>

			</td>
<!-- body_text_eof //-->
		</tr>
	</table>
<!-- body_eof //-->
	<br />
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>