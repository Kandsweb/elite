<?php
/**
 * Page Template
 *
 * Loaded automatically by index.php?main_page=product_info.<br />
 * Displays details of a typical product
 *
 * @package templateSystem
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_product_info_display.php 16242 2010-05-08 16:05:40Z ajeh $
 */
 //require(DIR_WS_MODULES . '/debug_blocks/product_info_prices.php');
?>
<div class="centerColumn" id="productGeneral">

<?php echo zen_draw_form('cart_quantity', zen_href_link(zen_get_info_page($_GET['products_id']), zen_get_all_get_params(array('action')) . 'action=add_product', $request_type), 'post', 'enctype="multipart/form-data"') . "\n"; ?>

<?php if ($messageStack->size('product_info') > 0) echo $messageStack->output('product_info');

 if (PRODUCT_INFO_PREVIOUS_NEXT == 1 or PRODUCT_INFO_PREVIOUS_NEXT == 3) {

/**
 * display the product previous/next helper
 */
require($template->get_template_dir('/tpl_products_next_previous.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_products_next_previous.php'); ?>
<?php } ?>

<?php require(DIR_WS_MODULES . zen_get_module_directory('family_count')); ?>

<div id="productName" class="productGeneral"><?php echo $products_name; ?></div>

<div id="productWrap">

<?php
  if (zen_not_null($products_image)) {
  ?>
<?php
/**
 * display the main product image
 */
  $products_image = get_image_xref($products_image);

   require($template->get_template_dir('/tpl_modules_main_product_image.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_main_product_image.php'); ?>
<?php
  }
?>

<?php     //<!--bof Product description -->
//if ($products_description != '') { ?>
<div id="productDescriptionBox">
  <div id="productDescriptionHead"> Product Description</div>
  <div id="productDescription" class="productGeneral biggerText">
  <?php echo stripslashes($products_description);
        echo '<div id="productDescriptionBS">'. get_bulb_string((int)$_GET['products_id']).'</div>';

        echo product_promotion((int)$_GET['products_id'], $current_page);
   ?>
  </div></div>
<?php //}
        //<!--eof Product description -->
 ?>
  <br class="clearBoth" />
<?php
/**
 * display the products additional images
 */
  require($template->get_template_dir('/tpl_modules_additional_images.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_additional_images.php');

//////////////////////////////Pricing/////////////////////////////////////////////////////////////////////////

// The following section is for the pricing information box. It has been heavyly edited and many lines removed as they are not relevent to Elite. You may need to refair to an orginal copy of this section for missing parts
?>

<div id="productInfoMidBox">
  <div id="productOrderHeading">Pricing and Availability</div>
  <div id="productOrderDetails">
    <?php
        //KandS - get_trade_price takes a pID and returns an array which contains the following information based on customer logged in, customer verfied,
        //and the status of the ABCD in the show_price field for the product
        //$trade_price[0] = PUBLIC or TRADE
        //            [1] = The rrp value
        //            [2] = 'RRP' - This is not needed at present
        //            [3] = The your price value
/*        $trade_price = get_trade_price($_GET['products_id']);
        if(is_array($trade_price)){
            //$trade_price = vat_split($trade_price);
            //if($products_rrp>0)$prices = '<div class="rrp">Recomended Retail Price '.$currencies->format($products_rrp).'</div>';
            //$prices .= '<div class="tradePrice">Your trade price<br />'.$currencies->format($trade_price['net']).'*<span class="txtSmall">  Exc VAT</span></div>';
            //$prices .= '<br />Vat '.$currencies->format($trade_price['vat']).'<br />';
        }
        //Build the product stock code. Produces a string = "<b>Stock Code: A18 14872</b><br />\n"
        $text_model = (($flag_show_product_info_model == 1 and $products_model !='') ? '<b>' . TEXT_PRODUCT_MODEL . $products_model . '</b><br />' : '') . "\n";
        //This next line is the one that produces the contact us button and its alt text
        $display_button = zen_get_buy_now_button($_GET['products_id'], '');
        //Build a contact us button
        $contact_us_button = '<br /><a href="' . zen_href_link(FILENAME_CONTACT_US, 'pId='.$_GET['products_id']) . '">' .  zen_image_button('button_contact_us.gif','Enquire about this item') . '</a>';

        $output = '';

        if($trade_price[0]=='PUBLIC'){
            if($trade_price[1]>0){
                $output.= '<div class="rrp">Recomended Retail Price '.$currencies->format($trade_price[1]).'</div>';
                $output.= '<div class="yp">Our price '.$currencies->format($trade_price[3]).'</div>';
                $output.= '</br >Contact us for availability information.<br /><div id="productStockCode"><b>Please quote the following</b><br>';
            }else{
                $output.= '<div class="rrp">Retail Price '.$currencies->format($trade_price[3]).'</div>';
                $output.= '<br />Contact us for your price and availability information.<br /><br /><div id="productStockCode"><b>Please quote the following</b><br>';
            }
        }elseif($trade_price[0]=='TRADE'){
            //Trade
            if($trade_price[1]>0){
                $output.= '<div class="rrp">Recomended Retail Price '.$currencies->format($trade_price[1]).'</div>';
            }else{
                if($trade_price[3]>0){
                    $output.= '<div class="rrp">Retail Price '.$currencies->format($trade_price[3]).'</div>';
                }
            }

            if($display_button==TEXT_AUTHORIZATION_PENDING_BUTTON_REPLACE){// APPROVAL PENDING
                $output .= '</br >Your account is pending approval please <br />contact us for your price and availability information.<br /><br /><div id="productStockCode"><b>Please quote the following</b><br>';
            }else{
                if($trade_price[1]==''){
                    if($trade_price[3]>0){
                        $output .= '<div class="yp">Your Price '.$currencies->format($trade_price[3]).'</div>';
                    }
                    $output .= '</br >Contact us for availability information.<br /><div id="productStockCode"><br>';
                }
            }
        }else{
            //Dont show any prices
            $output.= '</br >Contact us for pricing and availability information.<br /><div id="productStockCode"><b>Please quote the following</b><br>';
        }
*/
    ////////////////////////////////////////////////////////////////////////////////////////////////
    $pricing_letter='';
    $account_status=0;
    $output='';
    switch(TRUE){
        case $_SESSION['customer_id']==NULL:
            //Not logged in
            $pricing_letter='A';
            $account_status=0;
            break;
        case $_SESSION['customers_authorization']==2:
            //Trade account but not verified
            $pricing_letter='B';
            $account_status=0;
            break;
        case $_SESSION['customers_authorization']==0:
            switch($_SESSION['customer_trade_type']){
            case $trade_types_array[1]:
                $pricing_letter='B';
                $account_status=1;
                break;
            case $trade_types_array[2]:
                $pricing_letter='C';
                $account_status=1;
                break;
            case '99':
                $pricing_letter='D';
                $account_status=1;
                break;
            default:
                $account_status=1;
                break;
        }
    }
    if($pricing_letter==''){
        //$output .= '{'.$pricing_letter.'.'.$account_status.'}';
        $output.= '</br >Contact us for pricing and availability information.<br />';
    }else{
        //$output .= '{'.$pricing_letter.'.'.$account_status.'}';
        $output.= get_price($pricing_letter, $_GET['products_id'],$account_status);
    }

    $contact_us_button = '<br /><a href="' . zen_href_link(FILENAME_CONTACT_US, 'pId='.$_GET['products_id']) . '">' .  zen_image_button('button_enquire.gif','Enquire about this item') . '</a>';
    $text_model = (($flag_show_product_info_model == 1 and $products_model !='') ? '<b>' . TEXT_PRODUCT_MODEL . $products_model . '</b><br />' : '')

        ?>
        <div id="cartAdd">
        <?php
           echo $output;
            echo '<div id="productStockCode"><b>Please quote the following</b><br>'.$text_model.'</div>';
            echo $contact_us_button;
        ?>

        <!--bof Wishlist button -->

            <?php
            if (UN_MODULE_WISHLISTS_ENABLED) { ?>
            <div id="productWishlistLink" class="buttonRow back">
            <?php
            echo zen_image_submit(UN_BUTTON_IMAGE_WISHLIST_ADD, UN_BUTTON_WISHLIST_ADD_ALT, 'name="wishlist" value="yes"');
            //print_r($_REQUEST);
            ?></div>

        <?php } ?>
        </div>
    </div>
    <br class="clearBoth" />


<?php
  /////////////PRODUCT VARIATIONS
  $box_title='';
  $variant_string = get_variant_string($_GET['products_id']);
  if($variant_string != NULL){
?>
 <br class="clearBoth" />
<div id="productvariantsHeading"> <?php echo $box_title; ?></div>
<div id="productvariants">
<?php echo $variant_string; ?>
</div>
<?php } ?>
</div>

<div id="tecSpecBox">
<?php
  require($template->get_template_dir('/tpl_modules_specification.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_specification.php');
?>
</div>

<br class="clearBoth">
<?php
/////////////////FAMILY ITEMS
require($template->get_template_dir('/tpl_modules_family_items.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_family_items.php');
 ?>
<br class="clearBoth">
    <?php
    ///////////////RELATED ITEMS - XSELL
     if(defined('MXSELL_ENABLED') && MXSELL_ENABLED == 'true') {
        for ( $mxsell = 1; $mxsell <= MXSELL_NUM_OF_TABLES; $mxsell++ ) { // show all cross sells
          require($template->get_template_dir('tpl_modules_multi_xsell_products.php', DIR_WS_TEMPLATE, $current_page_base,'templates'). '/' . 'tpl_modules_multi_xsell_products.php');
        }
      }
    ?>




<?php //<!--bof Prev/Next bottom position -->
if (PRODUCT_INFO_PREVIOUS_NEXT == 2 or PRODUCT_INFO_PREVIOUS_NEXT == 3) { ?>
<?php
/**
 * display the product previous/next helper
 */
 require($template->get_template_dir('/tpl_products_next_previous.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_products_next_previous.php'); ?>
<?php }
//<!--eof Prev/Next bottom position -->
?>
<?php
//<!--bof Tell a Friend button -->
  if ($flag_show_product_info_tell_a_friend == 1) { ?>
<div id="productTellFriendLink" class="buttonRow forward"><?php echo ($flag_show_product_info_tell_a_friend == 1 ? '<a href="' . zen_href_link(FILENAME_TELL_A_FRIEND, 'products_id=' . $_GET['products_id']) . '">' . zen_image_button(BUTTON_IMAGE_TELLAFRIEND, BUTTON_TELLAFRIEND_ALT) . '</a>' : ''); ?></div>
<?php
  }

//}
?>


<?php //<!--bof Product date added/available-->
  if ($products_date_available > date('Y-m-d H:i:s')) {
    if ($flag_show_product_info_date_available == 1) {
?>
  <p id="productDateAvailable" class="productGeneral centeredContent"><?php echo sprintf(TEXT_DATE_AVAILABLE, zen_date_long($products_date_available)); ?></p>
<?php
    }
  } else {
    if ($flag_show_product_info_date_added == 1) {
?>
      <p id="productDateAdded" class="productGeneral centeredContent"><?php echo sprintf(TEXT_DATE_ADDED, zen_date_long($products_date_added)); ?></p>
<?php
    } // $flag_show_product_info_date_added
  }
//<!--eof Product date added/available -->
?>

   <?php
   require($template->get_template_dir ('tpl_modules_recent_products.php',DIR_WS_TEMPLATE, $current_page_base,'templates') . '/tpl_modules_recent_products.php'); ?>

<?php require($template->get_template_dir('tpl_modules_also_purchased_products.php', DIR_WS_TEMPLATE, $current_page_base,'templates'). '/' . 'tpl_modules_also_purchased_products.php');?>
</div>
</form>
</div>