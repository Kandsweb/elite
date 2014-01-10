<?php
/**
 * Page Template
 *
 * Main index page<br />
 * Displays greetings, welcome text (define-page content), and various centerboxes depending on switch settings in Admin<br />
 * Centerboxes are called as necessary
 *
 * @package templateSystem
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_index_default.php 3464 2006-04-19 00:07:26Z ajeh $
 */
?>
<div class="centerColumn" id="indexDefault">
<h1 id="indexDefaultHeading"><?php echo HEADING_TITLE; ?></h1>

<?php if (SHOW_CUSTOMER_GREETING == 1) { ?>
<h2 class="greeting"><?php echo zen_customer_greeting(); ?></h2>
<?php } ?>

<?php
  if (SHOW_BANNERS_GROUP_SET3 != '' && $banner = zen_banner_exists('dynamic', SHOW_BANNERS_GROUP_SET3)) {
    if ($banner->RecordCount() > 0) {
?>
<div id="bannerThree" class="banners"><?php echo zen_display_banner('static', $banner); ?></div>
<?php
    }
  }
?>

<?php if (DEFINE_MAIN_PAGE_STATUS >= 1 and DEFINE_MAIN_PAGE_STATUS <= 2) { ?>
<?php
/**
 * get the Define Main Page Text
 */

?>

<div id="slideBox">
<?php }

put_slideshow();
?>
</div>

<div id="indexDefaultMainContent" class="content">



<img src="/images/January-Sale.jpg" border="0" width="375" height="115" alt="Sale now on in store">
<?php ////////////////////////////////Manual Banner position/////////////////////////////////////
?>
<a href="https://selfbuild.ticketbud.com/belfast2014?pc=ELITE" target="_blank">
<?php

 echo '<div class="bancol back">'.zen_image('images/selfbuild14.png','','','','class="forward" style="padding:0 0px 0 12px;"').'<br><span style="font-size:17px;">Visit us at the Belfast Self Build show.</span><br><br>Claim your FREE tickets, complements of Elite Lighting by clicking the Self build logo.<br><br><br></div>';
 //echo "<br class='clearBoth'/>";
//echo zen_image('images/Renovation_Sale.jpg','','735');
//echo '<a href="' . zen_href_link('promotions') . '">'. zen_image('images/sept_11_promotion.gif','View our September Promotion items') .'</a><br/>';
//echo '<a href="' . zen_href_link('events&event=6') . '">'. zen_image('images/Belfast_show_11.png','View a selection of items we have on display at the Improve Your Home exhibition').'</a><br /><br />';
//echo '<a href="' . zen_href_link('events&event=6') . '">'.  zen_image('images/dublin_11.jpg','Visit us at the Ideal Home show in Dublin').'</a><br /><br />';
?>
</a>
<?php
/////////////////////////////////////End Manual Banner position//////////////////////////////////




require($define_page); ?></div>

<?php
  if (SHOW_BANNERS_GROUP_SET1 != '' && $banner = zen_banner_exists('dynamic', SHOW_BANNERS_GROUP_SET1)) {
    if ($banner->RecordCount() > 0) {
?>
<div id="bannerOne" class="banners"><?php echo zen_display_banner('static', $banner); ?></div>
<?php
    }
  }
?>

<br class="clearBoth" />
<?php
  $show_display_category = $db->Execute(SQL_SHOW_PRODUCT_INFO_MAIN);
  while (!$show_display_category->EOF) {
?>

<?php if ($show_display_category->fields['configuration_key'] == 'SHOW_PRODUCT_INFO_MAIN_FEATURED_PRODUCTS') { ?>
<?php
/**
 * display the Featured Products Center Box
 */
?>
<?php require($template->get_template_dir('tpl_modules_featured_products.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_featured_products.php'); ?>
<?php } ?>

<?php if ($show_display_category->fields['configuration_key'] == 'SHOW_PRODUCT_INFO_MAIN_SPECIALS_PRODUCTS') { ?>
<?php
/**
 * display the Special Products Center Box
 */
?>
<?php require($template->get_template_dir('tpl_modules_specials_default.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_specials_default.php'); ?>
<?php } ?>

<?php if ($show_display_category->fields['configuration_key'] == 'SHOW_PRODUCT_INFO_MAIN_NEW_PRODUCTS') { ?>
<?php
/**
 * display the New Products Center Box
 */
?>
<?php require($template->get_template_dir('tpl_modules_whats_new.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_whats_new.php'); ?>
<?php } ?>

<?php if ($show_display_category->fields['configuration_key'] == 'SHOW_PRODUCT_INFO_MAIN_UPCOMING') { ?>
<?php
/**
 * display the Upcoming Products Center Box
 */
?>
<?php include(DIR_WS_MODULES . zen_get_module_directory(FILENAME_UPCOMING_PRODUCTS)); ?><?php } ?>


<?php
  $show_display_category->MoveNext();
} // !EOF
?>
</div>