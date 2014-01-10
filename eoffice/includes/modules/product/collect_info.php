<?php
/**
 * @package admin
 * @copyright Copyright 2003-2010 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: collect_info.php 17947 2010-10-13 20:29:41Z drbyte $
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

  $pulldown = array(array('id'=>'9', 'text'=>'N/A'),array('id'=>0,'text'=>'No'),array('id'=>1, 'text'=>'Yes'));
  $pulldown1 = array(array('id'=>'9', 'text'=>'N/A'),array('id'=>'A','text'=>'Public'),array('id'=>'B', 'text'=>'Electrican'),array('id'=>'C', 'text'=>'Int. Designer'),array('id'=>'D', 'text'=>'Other'));
  $parameters = array('products_name' => '',
                     'products_description' => '',
                     'products_url' => '',
                     'products_id' => '',
                     'products_quantity' => '',
                     'products_model' => '',
                     'products_image' => '',
                     'products_price' => '',
                     'products_virtual' => DEFAULT_PRODUCT_PRODUCTS_VIRTUAL,
                     'products_weight' => '',
                     'products_date_added' => '',
                     'products_last_modified' => '',
                     'products_date_available' => '',
                     'products_status' => '',
                     'products_tax_class_id' => DEFAULT_PRODUCT_TAX_CLASS_ID,
                     'manufacturers_id' => '',
                     'products_quantity_order_min' => '',
                     'products_quantity_order_units' => '',
                     'products_priced_by_attribute' => '',
                     'product_is_free' => '',
                     'product_is_call' => '',
                     'products_quantity_mixed' => '',
                     'product_is_always_free_shipping' => DEFAULT_PRODUCT_PRODUCTS_IS_ALWAYS_FREE_SHIPPING,
                     'products_qty_box_status' => PRODUCTS_QTY_BOX_STATUS,
                     'products_quantity_order_max' => '0',
                     'products_sort_order' => '0',
                     'products_discount_type' => '0',
                     'products_discount_type_from' => '0',
                     'products_price_sorter' => '0',
                     'master_categories_id' => '',
                     'dimensions_height' => '',
                     'dimensions_width' => '',
                     'dimensions_width' => '',
                     'bulbs_qty' => '',
                     'bulbs_type' => '',
                     'bulbs_watts' => '',
                     'bulbs_cap' => '',
                     'ip_rating' => '',
                     'manufactures_code' => '',
                     'product_colour' => '',
                     'product_finish' => '',
                     'product_material' => '',
                     'product_style' => '',
                     'bulbs_included'  => '',
                     'product_dia' => '',
                     'product_max_drop' => '',
                     'product_min_drop' => '',
                     'product_length' => '',
                     'product_safety_class' => '',
                     'product_materials' => '',
                     'product_voltage' => '',
                     'product_guarantee' => '',
                     'product_shade_inc' => '',
                     'product_tramsformer' => '',
                     'product_driver' => '',
                     'product_cut_out' => '',
                     'product_recess' => '',
                     'product_surface_temp' => '',
                     'product_cable' => '',
                     'product_application' => '',
                     'product_weight_limit' => '',
                     'product_tilt' => '',
                     'product_variant' => '',
                     'family_caption' => '',
                     'bulbs_s1' =>'',
                     'bulbs_s2' =>'',
                     'rrp' =>'',
                     'rate_1' =>'',
                     'rate_2' =>'',
                     'rate_3' =>'',
                     'show_price' =>'',
                     'web_price' =>'',
                     'now_price' =>''
                     );

    $pInfo = new objectInfo($parameters);

    if (isset($_GET['pID']) && empty($_POST)) {
      $product = $db->Execute("select pd.products_name, pd.products_description, pd.products_url,
                                      p.products_id, p.products_quantity, p.products_model,
                                      p.products_image, p.products_price, p.products_virtual, p.products_weight,
                                      p.products_date_added, p.products_last_modified,
                                      date_format(p.products_date_available, '%Y-%m-%d') as
                                      products_date_available, p.products_status, p.products_tax_class_id,
                                      p.manufacturers_id,
                                      p.products_quantity_order_min, p.products_quantity_order_units, p.products_priced_by_attribute,
                                      p.product_is_free, p.product_is_call, p.products_quantity_mixed,
                                      p.product_is_always_free_shipping, p.products_qty_box_status, p.products_quantity_order_max,
                                      p.products_sort_order,
                                      p.products_discount_type, p.products_discount_type_from,
                                      p.products_price_sorter, p.master_categories_id, ".
                                      EXTRA_FIELDS. "
                              from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_EXTRA_FIELDS . " pdex
                              where p.products_id = '" . (int)$_GET['pID'] . "'
                              and p.products_id = pd.products_id and pdex.products_id = p.products_id
                              and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'");
//var_dump($product->fields);
      $pInfo->objectInfo($product->fields);
    } elseif (zen_not_null($_POST)) {
      $pInfo->objectInfo($_POST);
      $products_name = $_POST['products_name'];
      $products_description = $_POST['products_description'];
      $products_url = $_POST['products_url'];
    }
//var_dump($pInfo);

    if($pInfo->bulbs_included=='')$pInfo->bulbs_included=9;
    if($pInfo->product_shade_inc=='')$pInfo->product_shade_inc=9;
    if($pInfo->product_driver=='')$pInfo->product_driver=9;
    if($pInfo->product_transformer=='')$pInfo->product_transformer=9;
    if($pInfo->show_price==0)$pInfo->show_price='';


    $manufacturers_array = array(array('id' => '', 'text' => TEXT_NONE));
    $manufacturers = $db->Execute("select manufacturers_id, manufacturers_name
                                   from " . TABLE_MANUFACTURERS . " order by manufacturers_name");
    while (!$manufacturers->EOF) {
      $manufacturers_array[] = array('id' => $manufacturers->fields['manufacturers_id'],
                                     'text' => $manufacturers->fields['manufacturers_name']);
      $manufacturers->MoveNext();
    }

    $tax_class_array = array(array('id' => '0', 'text' => TEXT_NONE));
    $tax_class = $db->Execute("select tax_class_id, tax_class_title
                                     from " . TABLE_TAX_CLASS . " order by tax_class_title");
    while (!$tax_class->EOF) {
      $tax_class_array[] = array('id' => $tax_class->fields['tax_class_id'],
                                 'text' => $tax_class->fields['tax_class_title']);
      $tax_class->MoveNext();
    }

    $languages = zen_get_languages();

    if (!isset($pInfo->products_status)) $pInfo->products_status = '1';
    switch ($pInfo->products_status) {
      case '0': $in_status = false; $out_status = true; break;
      case '1':
      default: $in_status = true; $out_status = false;
        break;
    }
// set to out of stock if categories_status is off and new product or existing products_status is off
    if (zen_get_categories_status($current_category_id) == '0' and $pInfo->products_status != '1') {
      $pInfo->products_status = 0;
      $in_status = false;
      $out_status = true;
    }

// Virtual Products
    if (!isset($pInfo->products_virtual)) $pInfo->products_virtual = PRODUCTS_VIRTUAL_DEFAULT;
    switch ($pInfo->products_virtual) {
      case '0': $is_virtual = false; $not_virtual = true; break;
      case '1': $is_virtual = true; $not_virtual = false; break;
      default: $is_virtual = false; $not_virtual = true;
    }
// Always Free Shipping
    if (!isset($pInfo->product_is_always_free_shipping)) $pInfo->product_is_always_free_shipping = DEFAULT_PRODUCT_PRODUCTS_IS_ALWAYS_FREE_SHIPPING;
    switch ($pInfo->product_is_always_free_shipping) {
      case '0': $is_product_is_always_free_shipping = false; $not_product_is_always_free_shipping = true; $special_product_is_always_free_shipping = false; break;
      case '1': $is_product_is_always_free_shipping = true; $not_product_is_always_free_shipping = false; $special_product_is_always_free_shipping = false; break;
      case '2': $is_product_is_always_free_shipping = false; $not_product_is_always_free_shipping = false; $special_product_is_always_free_shipping = true; break;
      default: $is_product_is_always_free_shipping = false; $not_product_is_always_free_shipping = true; $special_product_is_always_free_shipping = false;
    }
// products_qty_box_status shows
    if (!isset($pInfo->products_qty_box_status)) $pInfo->products_qty_box_status = PRODUCTS_QTY_BOX_STATUS;
    switch ($pInfo->products_qty_box_status) {
      case '0': $is_products_qty_box_status = false; $not_products_qty_box_status = true; break;
      case '1': $is_products_qty_box_status = true; $not_products_qty_box_status = false; break;
      default: $is_products_qty_box_status = true; $not_products_qty_box_status = false;
    }
// Product is Priced by Attributes
    if (!isset($pInfo->products_priced_by_attribute)) $pInfo->products_priced_by_attribute = '0';
    switch ($pInfo->products_priced_by_attribute) {
      case '0': $is_products_priced_by_attribute = false; $not_products_priced_by_attribute = true; break;
      case '1': $is_products_priced_by_attribute = true; $not_products_priced_by_attribute = false; break;
      default: $is_products_priced_by_attribute = false; $not_products_priced_by_attribute = true;
    }
// Product is Free
    if (!isset($pInfo->product_is_free)) $pInfo->product_is_free = '0';
    switch ($pInfo->product_is_free) {
      case '0': $in_product_is_free = false; $out_product_is_free = true; break;
      case '1': $in_product_is_free = true; $out_product_is_free = false; break;
      default: $in_product_is_free = false; $out_product_is_free = true;
    }
// Product is Call for price
    if (!isset($pInfo->product_is_call)) $pInfo->product_is_call = '0';
    switch ($pInfo->product_is_call) {
      case '0': $in_product_is_call = false; $out_product_is_call = true; break;
      case '1': $in_product_is_call = true; $out_product_is_call = false; break;
      default: $in_product_is_call = false; $out_product_is_call = true;
    }
// Products can be purchased with mixed attributes retail
    if (!isset($pInfo->products_quantity_mixed)) $pInfo->products_quantity_mixed = '0';
    switch ($pInfo->products_quantity_mixed) {
      case '0': $in_products_quantity_mixed = false; $out_products_quantity_mixed = true; break;
      case '1': $in_products_quantity_mixed = true; $out_products_quantity_mixed = false; break;
      default: $in_products_quantity_mixed = true; $out_products_quantity_mixed = false;
    }

// set image overwrite
  $on_overwrite = true;
  $off_overwrite = false;
// set image delete
  $on_image_delete = false;
  $off_image_delete = true;

  $foptions_array = array();
  //get_filter_option_array(1, $foptions_array);
  //get_filter_option_array(2, $foptions_array);

?>
<link rel="stylesheet" type="text/css" href="includes/javascript/spiffyCal/spiffyCal_v2_1.css">
<script language="JavaScript" src="includes/javascript/spiffyCal/spiffyCal_v2_1.js"></script>
<script language="javascript"><!--
  var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "new_product", "products_date_available","btnDate1","<?php echo $pInfo->products_date_available; ?>",scBTNMODE_CUSTOMBLUE);
//--></script>
<script language="javascript"><!--
var tax_rates = new Array();
<?php
    for ($i=0, $n=sizeof($tax_class_array); $i<$n; $i++) {
      if ($tax_class_array[$i]['id'] > 0) {
        echo 'tax_rates["' . $tax_class_array[$i]['id'] . '"] = ' . zen_get_tax_rate_value($tax_class_array[$i]['id']) . ';' . "\n";
      }
    }
?>

function doRound(x, places) {
  return Math.round(x * Math.pow(10, places)) / Math.pow(10, places);
}


function updateNet() {
  var taxRate = 0; //getTaxRate();
  var netValue = document.forms["new_product"].products_price_gross.value;

  if (taxRate > 0) {
    netValue = netValue / ((taxRate / 100) + 1);
  }

  document.forms["new_product"].products_price.value = doRound(netValue, 4);
}
//--></script>
    <?php
//  echo $type_admin_handler;
echo zen_draw_form('new_product', $type_admin_handler , 'cPath=' . $cPath . (isset($_GET['product_type']) ? '&product_type=' . $_GET['product_type'] : '') . (isset($_GET['pID']) ? '&pID=' . $_GET['pID'] : '') . '&action=new_product_preview' . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') . ( (isset($_GET['search']) && !empty($_GET['search'])) ? '&search=' . $_GET['search'] : '') . ( (isset($_POST['search']) && !empty($_POST['search']) && empty($_GET['search'])) ? '&search=' . $_POST['search'] : ''), 'post', 'enctype="multipart/form-data"');
    ?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo sprintf(TEXT_NEW_PRODUCT, zen_output_generated_category_path($current_category_id)); ?></td>
            <td class="pageHeading" align="right"><?php echo zen_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="main" align="right"><?php echo zen_draw_hidden_field('products_date_added', (zen_not_null($pInfo->products_date_added) ? $pInfo->products_date_added : date('Y-m-d'))) . zen_image_submit('button_preview.gif', IMAGE_PREVIEW) . '&nbsp;&nbsp;<a href="' . zen_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . (isset($_GET['pID']) ? '&pID=' . $_GET['pID'] : '') . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') . ( (isset($_GET['search']) && !empty($_GET['search'])) ? '&search=' . $_GET['search'] : '') . ( (isset($_POST['search']) && !empty($_POST['search']) && empty($_GET['search'])) ? '&search=' . $_POST['search'] : '')) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>'; ?></td>
      </tr>
      <tr>
        <td><table border="0" cellspacing="0" cellpadding="2">
<?php
// show when product is linked
if (zen_get_product_is_linked($_GET['pID']) == 'true' and $_GET['pID'] > 0) {
?>
          <tr>
            <td class="main"><?php echo TEXT_MASTER_CATEGORIES_ID; ?></td>
            <td class="main">
              <?php
                // echo zen_draw_pull_down_menu('products_tax_class_id', $tax_class_array, $pInfo->products_tax_class_id);
                echo zen_image(DIR_WS_IMAGES . 'icon_yellow_on.gif', IMAGE_ICON_LINKED) . '&nbsp;&nbsp;';
                echo zen_draw_pull_down_menu('master_category', zen_get_master_categories_pulldown($_GET['pID']), $pInfo->master_categories_id); ?>
            </td>
          </tr>
<?php } else { ?>
          <tr>
            <td class="main"><?php echo TEXT_MASTER_CATEGORIES_ID; ?></td>
            <td class="main"><?php echo TEXT_INFO_ID . ($_GET['pID'] > 0 ? $pInfo->master_categories_id  . ' ' . zen_get_category_name($pInfo->master_categories_id, $_SESSION['languages_id']) : $current_category_id  . ' ' . zen_get_category_name($current_category_id, $_SESSION['languages_id'])); ?></td>
          </tr>
<?php } ?>
          <tr>
            <td colspan="2" class="main"><?php echo TEXT_INFO_MASTER_CATEGORIES_ID; ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo zen_draw_separator('pixel_trans.gif', '100%', '2'); ?></td>
          </tr>
<?php
// hidden fields not changeable on products page
echo zen_draw_hidden_field('master_categories_id', $pInfo->master_categories_id);
echo zen_draw_hidden_field('products_discount_type', $pInfo->products_discount_type);
echo zen_draw_hidden_field('products_discount_type_from', $pInfo->products_discount_type_from);
echo zen_draw_hidden_field('products_price_sorter', $pInfo->products_price_sorter);
echo zen_draw_hidden_field('product_date_available','');
echo zen_draw_hidden_field('product_is_free', 0);
echo zen_draw_hidden_field('product_is_call', 0);
echo zen_draw_hidden_field('products_priced_by_attribute', 0);
echo zen_draw_hidden_field('products_virtual', 0);
echo zen_draw_hidden_field('product_is_always_free_shipping', 0);
echo zen_draw_hidden_field('products_qty_box_status', 0);
echo zen_draw_hidden_field('products_quantity_order_min', 1);
echo zen_draw_hidden_field('products_quantity_order_max', 0);
echo zen_draw_hidden_field('products_quantity_order_units', 1);
echo zen_draw_hidden_field('products_quantity_mixed', 0);
echo zen_draw_hidden_field('products_tax_class_id', 0);
echo zen_draw_hidden_field('products_price', $pInfo->products_price);
echo zen_draw_hidden_field('products_url', '');
echo zen_draw_hidden_field('products_weight', 0);
echo zen_draw_hidden_field('products_quantity', 1);
echo zen_draw_hidden_field('overwrite', 1);
echo zen_draw_hidden_field('products_image_manual', '');
?>
          <tr>
            <td colspan="2" class="main" align="center"><?php echo (zen_get_categories_status($current_category_id) == '0' ? TEXT_CATEGORIES_STATUS_INFO_OFF : '') . ($out_status == true ? ' ' . TEXT_PRODUCTS_STATUS_INFO_OFF : ''); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>

          <tr>
            <td colspan="2"><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main"><?php echo TEXT_PRODUCTS_STATUS; ?></td>
                <td class="main"><?php echo zen_draw_radio_field('products_status', '1', $in_status) . '&nbsp;' . TEXT_PRODUCT_AVAILABLE . '&nbsp;' . zen_draw_radio_field('products_status', '0', $out_status) . '&nbsp;' . TEXT_PRODUCT_NOT_AVAILABLE; ?></td>
                <td width="20%" class="main"></td>
                <td class="main"><?php echo TEXT_PRODUCTS_MODEL; ?></td>
                <td class="main"><?php echo zen_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . zen_draw_input_field('products_model', htmlspecialchars(stripslashes($pInfo->products_model), ENT_COMPAT, CHARSET), zen_set_field_length(TABLE_PRODUCTS, 'products_model').'style="width:130px"'); ?></td>
              </tr>
              <tr>
                <td colspan="5"><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo TEXT_PRODUCTS_MANUFACTURER; ?></td>
                <td class="main"><?php echo  zen_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . zen_draw_pull_down_menu('manufacturers_id', $manufacturers_array, $pInfo->manufacturers_id); ?></td>
                <td width="20%" class="main"></td>
                <td class="main">Manufactures Code:</td>
                <td class="main"><?php echo zen_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . zen_draw_input_field('products_manufacture_code', $pInfo->manufactures_code, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'manufactures_code')); ?></td>
              </tr>
              <tr>
                <td colspan="5"><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo TEXT_PRODUCTS_QUANTITY; ?></td>
                <td class="main" colspan="4"><?php echo zen_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . zen_draw_input_field('products_quantity', $pInfo->products_quantity); ?></td>
              </tr>
            </table></td>
          </tr>

          <tr>
            <td colspan="2"><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main"><?php if ($i == 0) echo TEXT_PRODUCTS_NAME; //echo zen_get_products_name($pInfo->products_id, $languages[$i]['id']); ?></td>
            <td class="main"><?php echo zen_draw_input_field('products_name[' . $languages[$i]['id'] . ']', (isset($products_name[$languages[$i]['id']]) ? htmlspecialchars(stripslashes($products_name[$languages[$i]['id']]), ENT_COMPAT, CHARSET) : htmlspecialchars(zen_get_products_name($pInfo->products_id, $languages[$i]['id']), ENT_COMPAT, CHARSET)), zen_set_field_length(TABLE_PRODUCTS_DESCRIPTION, 'products_name'). 'style="width:550px"'); ?></td>
          </tr>
<?php
    }
?>

          <tr>
            <td colspan="2"><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>

<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) echo TEXT_PRODUCTS_DESCRIPTION; ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main" width="100%">
        <?php if ($_SESSION['html_editor_preference_status']=="FCKEDITOR") {
                $oFCKeditor = new FCKeditor('products_description[' . $languages[$i]['id'] . ']') ;
                $oFCKeditor->Value = (isset($products_description[$languages[$i]['id']])) ? stripslashes($products_description[$languages[$i]['id']]) : zen_get_products_description($pInfo->products_id, $languages[$i]['id']);
                $oFCKeditor->Width  = '99%' ;
                $oFCKeditor->Height = '50' ;
//                $oFCKeditor->Config['ToolbarLocation'] = 'Out:xToolbar' ;
//                $oFCKeditor->Create() ;
                $output = $oFCKeditor->CreateHtml() ;  echo $output;
          } else { // using HTMLAREA or just raw "source"

          echo zen_draw_textarea_field('products_description[' . $languages[$i]['id'] . ']', 'soft', '110%', '10', (isset($products_description[$languages[$i]['id']])) ? htmlspecialchars(stripslashes($products_description[$languages[$i]['id']]), ENT_COMPAT, CHARSET, TRUE) : htmlspecialchars(zen_get_products_description($pInfo->products_id, $languages[$i]['id']), ENT_COMPAT, CHARSET)); //,'id="'.'products_description' . $languages[$i]['id'] . '"');
          } ?>
        </td>
              </tr>
            </table></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2"><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo zen_draw_separator('pixel_black.gif', '100%', '1'); ?></td>
          </tr>
           <tr>
            <td colspan="2"><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>

          <tr>
            <td class="main">Pricing:</td>
            <td class="main">Shop Price: <?php echo '&nbsp;' . '£' . zen_draw_input_field('products_price_gross', $pInfo->products_price, 'OnKeyUp="updateNet()"').
              zen_draw_separator('pixel_trans.gif', '24', '15') . 'RRP:&nbsp;£'.zen_draw_input_field('product_rrp', $pInfo->product_rrp, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'rrp')).
              zen_draw_separator('pixel_trans.gif', '24', '15') . 'Web Price:&nbsp;£'.zen_draw_input_field('product_web_price', $pInfo->product_web_price, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'web_price')).
              zen_draw_separator('pixel_trans.gif', '24', '15') . 'NOW PRICE:&nbsp;'.zen_draw_input_field('product_now_price', $pInfo->product_now_price, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'now_price')).'<br><br>'.
              'Rate 1:&nbsp;£'.zen_draw_input_field('product_rate_1', $pInfo->product_rate_1, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'rate_1')).
              zen_draw_separator('pixel_trans.gif', '24', '15') .'Rate 2:&nbsp;£'.zen_draw_input_field('product_rate_2', $pInfo->product_rate_2, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'rate_2')).
              zen_draw_separator('pixel_trans.gif', '24', '15') .'Rate 3:&nbsp;£'.zen_draw_input_field('product_rate_3', $pInfo->product_rate_3, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'rate_3')).
              zen_draw_separator('pixel_trans.gif', '24', '15') .'Show Price:&nbsp;'.zen_draw_input_field('product_show_price', $pInfo->show_price, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'show_price'));
              ?>
            </td>
            </tr>


          <tr>
            <td colspan="2"><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo zen_draw_separator('pixel_black.gif', '100%', '1'); ?></td>
          </tr>
           <tr>
            <td colspan="2"><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>

            <td colspan="2" class="main">Colour:<?php echo zen_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
             zen_draw_input_field('product_colour', $pInfo->product_colour, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'product_colour').'style="width:50px"') .
             zen_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
             'Style:' . zen_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .  zen_draw_input_field('product_style', $pInfo->product_style, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'product_style').'style="width:50px"') .
             zen_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
             'Finish:' . zen_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .  zen_draw_input_field('product_finish', $pInfo->product_finish, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'product_finish').'style="width:50px"').
             zen_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
             'Material:' . zen_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .  zen_draw_input_field('product_material', $pInfo->product_material, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'product_material').'style="width:50px"')
             ?></td>
           </tr>
          <tr>
            <td colspan="2"><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>


          <tr>
            <td class="main"><?php echo TEXT_PRODUCTS_DIMENSIONS; ?></td>
            <td class="main"><?php echo zen_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
            TEXT_PRODUCTS_DIMENSIONS_HEIGHT . '&nbsp;' . zen_draw_input_field('products_dimensions_height', $pInfo->dimensions_height, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'dimensions_height').'style="width:50px"') .
            TEXT_PRODUCTS_CM . zen_draw_separator('pixel_trans.gif', '30', '15') . '&nbsp;' .
            TEXT_PRODUCTS_DIMENSIONS_WIDTH  . '&nbsp;' . zen_draw_input_field('products_dimensions_width', $pInfo->dimensions_width, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'dimensions_width').'style="width:50px"') .
            TEXT_PRODUCTS_CM . zen_draw_separator('pixel_trans.gif', '30', '15') . '&nbsp;' .
             'Length'  . '&nbsp;' . zen_draw_input_field('products_dimensions_length', $pInfo->product_length, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'product_length').'style="width:50px"') .
             TEXT_PRODUCTS_CM . zen_draw_separator('pixel_trans.gif', '30', '15') . '&nbsp;' .
             TEXT_PRODUCTS_DIMENSIONS_DEPTH  . '&nbsp;' . zen_draw_input_field('products_dimensions_depth', $pInfo->dimensions_depth, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'dimensions_depth').'style="width:50px"') . TEXT_PRODUCTS_CM; ?></td>
          </tr>


          <tr>
            <td class="main"></td>
            <td class="main"> <?php echo zen_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
            'Diameter:'. '&nbsp;' . zen_draw_input_field('product_dia', $pInfo->product_dia, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'product_dia').'style="width:50px"') . TEXT_PRODUCTS_CM . zen_draw_separator('pixel_trans.gif', '30', '15') . '&nbsp;' .
             'Min. Drop:'  . '&nbsp;' . zen_draw_input_field('product_min_drop', $pInfo->product_min_drop, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'product_min_drop').'style="width:50px"') . TEXT_PRODUCTS_CM . zen_draw_separator('pixel_trans.gif', '30', '15') . '&nbsp;' .
             'Max. Drop:'  . '&nbsp;' . zen_draw_input_field('product_max_drop', $pInfo->product_max_drop, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'product_max_drop').'style="width:50px"') . TEXT_PRODUCTS_CM . zen_draw_separator('pixel_trans.gif', '30', '15') . '&nbsp;' .
              'Recess:'  . '&nbsp;' . zen_draw_input_field('product_recess', $pInfo->product_recess, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'product_recess').'style="width:50px"') . TEXT_PRODUCTS_CM; ?> </td>
          </tr>

          <tr>
            <td colspan="2"><?php echo zen_draw_separator('pixel_black.gif', '100%', '1'); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>

          <tr>
            <td class="main">Bulb Details:</td>
            <td class="main">
            <?php
            echo zen_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
            TEXT_PRODUCTS_BULBS_QTY . '&nbsp;' . zen_draw_input_field('products_bulbs_qty', $pInfo->bulbs_qty, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'bulbs_qty') . 'style="width:50px"') .zen_draw_separator('pixel_trans.gif', '30', '15') . '&nbsp;' .
            'Included' . '&nbsp;' . zen_draw_pull_down_menu('bulbs_included', $pulldown, $pInfo->bulbs_included).'<br><br>'.
            'Bulb Statement 1' . '&nbsp;' . zen_draw_input_field('product_bulbs_s1', $pInfo->bulbs_s1, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'bulbs_s1')) . zen_draw_separator('pixel_trans.gif', '30', '15') . '&nbsp;' .
            'Bulb Statement 2' . '&nbsp;' . zen_draw_input_field('product_bulbs_s2', $pInfo->bulbs_s2, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'bulbs_s2')) . zen_draw_separator('pixel_trans.gif', '30', '15') . '&nbsp;';
            ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo zen_draw_separator('pixel_black.gif', '100%', '1'); ?></td>
          </tr>
           <tr>
            <td colspan="2"><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr class="main">
            <td> </td>
            <td>
            <table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td>Shade Inc.:<?php echo '&nbsp;' . zen_draw_pull_down_menu('product_shade_inc',$pulldown, $pInfo->product_shade_inc).zen_draw_separator('pixel_trans.gif', '30', '15');?></td>
                <td>Voltage:<?php echo  '&nbsp;' . zen_draw_input_field('product_voltage', $pInfo->product_voltage, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'product_voltage') . 'style="width:50px"').zen_draw_separator('pixel_trans.gif', '30', '15')?></td>
                <td>Transformer Inc.:<?php echo '&nbsp;' . zen_draw_pull_down_menu('product_transformer',$pulldown, $pInfo->product_transformer).zen_draw_separator('pixel_trans.gif', '30', '15');?></td>
                <td>Driver Inc.:<?php echo '&nbsp;' . zen_draw_pull_down_menu('product_driver',$pulldown, $pInfo->product_driver).zen_draw_separator('pixel_trans.gif', '30', '15');?></td>
                <td>Guarantee:<?php echo  '&nbsp;' . zen_draw_input_field('product_guarantee', $pInfo->product_guarantee, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'product_guarantee') . 'style="width:50px"').'Yrs'.zen_draw_separator('pixel_trans.gif', '30', '15')?></td>
                <td>Surface Temp.:<?php echo  '&nbsp;' . zen_draw_input_field('product_surface_temp', $pInfo->product_surface_temp, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'product_surface_temp') . 'style="width:50px"').'&deg;C'.zen_draw_separator('pixel_trans.gif', '30', '15')?></td>
              </tr>
              <tr>
                <td colspan="6"><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo TEXT_PRODUCTS_IP_RATING . '&nbsp;'. zen_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;IP' . zen_draw_input_field('products_ip_rating', $pInfo->ip_rating, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'ip_rating')) . zen_draw_separator('pixel_trans.gif', '30', '15'); ?></td>
                <td>Cable Length:<?php echo  '&nbsp;' . zen_draw_input_field('product_cable', $pInfo->product_cable, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'product_cable') . 'style="width:50px"').'Mtrs'.zen_draw_separator('pixel_trans.gif', '30', '15')?></td>
                <td>Weight Limit:<?php echo  '&nbsp;' . zen_draw_input_field('product_weight_limit', $pInfo->product_weight_limit, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'product_weight_limit') . 'style="width:50px"').'Kgs'.zen_draw_separator('pixel_trans.gif', '30', '15')?></td>
                <td>Cut Out:<?php echo  '&nbsp;' . zen_draw_input_field('product_cut_out', $pInfo->product_cut_out, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'product_cut_out') . 'style="width:50px"').TEXT_PRODUCTS_CM.zen_draw_separator('pixel_trans.gif', '30', '15')?></td>
                <td>Tilt:<?php echo  '&nbsp;' . zen_draw_input_field('product_tilt', $pInfo->product_tilt, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'product_tilt') . 'style="width:50px"').'&deg;'.zen_draw_separator('pixel_trans.gif', '30', '15')?></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="6"><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <td>
              <tr>
                <td colspan="6">Application:<?php echo  '&nbsp;' . zen_draw_input_field('product_application', $pInfo->product_application, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'product_application') . 'style="width:500px"').zen_draw_separator('pixel_trans.gif', '30', '15')?></td>
              </tr>
              <tr>
                <td colspan="6"><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td colspan="6">Variant:<?php echo  '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . zen_draw_input_field('product_variant', $pInfo->product_variant, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'product_variant') . 'style="width:500px"').zen_draw_separator('pixel_trans.gif', '30', '15')?></td>
              </tr>
              <tr>
                <td colspan="6">Family Caption Number:<?php echo  '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . zen_draw_input_field('family_caption', $pInfo->family_caption, zen_set_field_length(TABLE_PRODUCTS_EXTRA_FIELDS, 'family_caption') . 'style="width:50px"').zen_draw_separator('pixel_trans.gif', '30', '15')?></td>
              </tr>
            </table>
            </td>
          </tr>

          <tr>
            <td class="main"></td>
            <td class="main"></td>
          </tr>
          <?php
          for($g=1; $g<=sizeof($foptions_array); $g++){
            ?>
            <tr>
            <td class="main"><?php echo get_filter_option_name($g); ?></td>
            <td><table border="0" style="padding: 1px 5px 1px 5px"><tr style="padding: 1px 5px 1px 5px">
            <?php
            $group_array = $foptions_array[$g];
            for($gg=0; $gg<sizeof($group_array);$gg++){
              echo '<td ><input type=checkbox name=foptions_' . get_filter_option_name($g) .'[] value=' . $group_array[$gg]['value'] . '>' .  $group_array[$gg]['name'] . '&nbsp;&nbsp;</td>';
            }
            ?>
            </tr></table></td></tr>
            <?php
          }
          ?>
          <tr>
            <td colspan="2"><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
  $dir = @dir(DIR_FS_CATALOG_IMAGES);
  $dir_info[] = array('id' => '', 'text' => "Main Directory");
  while ($file = $dir->read()) {
    if (is_dir(DIR_FS_CATALOG_IMAGES . $file) && strtoupper($file) != 'CVS' && $file != "." && $file != "..") {
      $dir_info[] = array('id' => $file . '/', 'text' => $file);
    }
  }
  $dir->close();
  sort($dir_info);

  $default_directory = substr( $pInfo->products_image, 0,strpos( $pInfo->products_image, '/')+1);
?>

          <tr>
            <td colspan="2"><?php echo zen_draw_separator('pixel_black.gif', '100%', '3'); ?></td>
          </tr>

          <tr>
            <td class="main" colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main"><?php echo TEXT_PRODUCTS_IMAGE; ?></td>
                <td class="main"><?php echo zen_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . zen_draw_file_field('products_image') . '&nbsp;' . ($pInfo->products_image !='' ? TEXT_IMAGE_CURRENT . $pInfo->products_image : TEXT_IMAGE_CURRENT . '&nbsp;' . NONE) . zen_draw_hidden_field('products_previous_image', $pInfo->products_image); ?></td>
                <td valign = "center" class="main"><?php if ($xs == 0){
                  echo TEXT_PRODUCTS_IMAGE_DIR; ?>&nbsp;<?php echo zen_draw_pull_down_menu('img_dir', $dir_info, $default_directory);
                } ?></td>
						  </tr>
              <tr>
                <td class="main"><?php echo zen_draw_separator('pixel_trans.gif', '24', '15'); ?></td>
                <td class="main" valign="top"><?php echo TEXT_IMAGES_DELETE . ' ' . zen_draw_radio_field('image_delete', '0', $off_image_delete) . '&nbsp;' . TABLE_HEADING_NO . ' ' . zen_draw_radio_field('image_delete', '1', $on_image_delete) . '&nbsp;' . TABLE_HEADING_YES; ?></td>
	  	    	  </tr>
            </table></td>
          </tr>

          <tr>
            <td colspan="2"><?php echo zen_draw_separator('pixel_black.gif', '100%', '10'); ?></td>
          </tr>

          <tr>
            <td class="main"><?php echo TEXT_PRODUCTS_SORT_ORDER; ?></td>
            <td class="main"><?php echo zen_draw_separator('pixel_trans.gif', '24', '10') . '&nbsp;' . zen_draw_input_field('products_sort_order', $pInfo->products_sort_order); ?></td>
          </tr>

          <tr>
            <td colspan="2"><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>

        </table></td>
      </tr>
      <tr>
        <td><?php echo zen_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="main" align="right"><?php echo zen_draw_hidden_field('products_date_added', (zen_not_null($pInfo->products_date_added) ? $pInfo->products_date_added : date('Y-m-d'))) . ( (isset($_GET['search']) && !empty($_GET['search'])) ? zen_draw_hidden_field('search', $_GET['search']) : '') . ( (isset($_POST['search']) && !empty($_POST['search']) && empty($_GET['search'])) ? zen_draw_hidden_field('search', $_POST['search']) : '') . zen_image_submit('button_preview.gif', IMAGE_PREVIEW) . '&nbsp;&nbsp;<a href="' . zen_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . (isset($_GET['pID']) ? '&pID=' . $_GET['pID'] : '') . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') . ( (isset($_GET['search']) && !empty($_GET['search'])) ? '&search=' . $_GET['search'] : '') . ( (isset($_POST['search']) && !empty($_POST['search']) && empty($_GET['search'])) ? '&search=' . $_POST['search'] : '')) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>'; ?></td>
      </tr>
    </table></form>
