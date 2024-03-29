<?php
/**
 * Common Template - tpl_header.php
 *
 * this file can be copied to /templates/your_template_dir/pagename<br />
 * example: to override the privacy page<br />
 * make a directory /templates/my_template/privacy<br />
 * copy /templates/templates_defaults/common/tpl_footer.php to /templates/my_template/privacy/tpl_header.php<br />
 * to override the global settings and turn off the footer un-comment the following line:<br />
 * <br />
 * $flag_disable_header = true;<br />
 *
 * @package templateSystem
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_header.php 4813 2006-10-23 02:13:53Z drbyte $
 */
//include_once 'includes/cookie_law/php/cookies.inc.php';
//include_once 'includes/cookie_law/php/functions.inc.php';
//always_include_plugin(false);
/*echo '<script type="text/javascript">
		$(document).ready( function () {
			$.ws.jpecr({
				displayButtonSelector: \'.jpecrDisplayButton\',
				growlerType: \'bar\',
				popupType: \'modal\',
				debug: false,
			});
		});
	</script>';
*/
?>

<?php
  // Display all header alerts via messageStack:
  if ($messageStack->size('header') > 0) {
    echo $messageStack->output('header');
  }
  if (isset($_GET['error_message']) && zen_not_null($_GET['error_message'])) {
  echo htmlspecialchars(urldecode($_GET['error_message']));
  }
  if (isset($_GET['info_message']) && zen_not_null($_GET['info_message'])) {
   echo htmlspecialchars($_GET['info_message']);
} else {

}
?>


<!--bof-header logo and navigation display-->
<?php
if (!isset($flag_disable_header) || !$flag_disable_header) {
?>

<div id="headerWrapper">


  <div id="logoWrapper">
    <div id="logo">
      <?php echo '<a href="' . HTTP_SERVER . DIR_WS_CATALOG . '">' . zen_image($template->get_template_dir(HEADER_LOGO_IMAGE, DIR_WS_TEMPLATE, $current_page_base,'images'). '/' . HEADER_LOGO_IMAGE, HEADER_ALT_TEXT) . '</a>'; ?></div>
  <?php if (HEADER_SALES_TEXT != '' || (SHOW_BANNERS_GROUP_SET2 != '' && $banner = zen_banner_exists('dynamic', SHOW_BANNERS_GROUP_SET2))) { ?>
      <div id="taglineWrapper">
<?php   if (HEADER_SALES_TEXT != '') {  ?>
        <div id="tagline"><?php echo HEADER_SALES_TEXT;?></div>
<?php         }
              if (SHOW_BANNERS_GROUP_SET2 != '' && $banner = zen_banner_exists('dynamic', SHOW_BANNERS_GROUP_SET2)) {
                if ($banner->RecordCount() > 0) { ?>
        <div id="bannerTwo" class="banners"><?php echo zen_display_banner('static', $banner);?></div>
<?php          }
              }?>
      </div>
<?php } // no HEADER_SALES_TEXT or SHOW_BANNERS_GROUP_SET2
  //Close div id logo ?>
 <br class="clearBoth" />
  </div>


  <div class="headerRight" id="rightLogoArea">
    <?php  require($template->get_template_dir('tpl_drop_menu_top.php',DIR_WS_TEMPLATE, $current_page_base,'common'). '/tpl_drop_menu_top.php');  ?>

    <br class="clearBoth" />

    <div class="headerLogin">
        <?php
            //Set up some vars first
            $column_width='250px';
            $box_id='loginBox';
            require(DIR_WS_MODULES . 'sideboxes/KandS/login_box.php' );
        ?>
    </div>

  </div>


  <br class="clearBoth" />

  <?php if (EZPAGES_STATUS_HEADER == '1' or (EZPAGES_STATUS_HEADER == '2' and (strstr(EXCLUDE_ADMIN_IP_FOR_MAINTENANCE, $_SERVER['REMOTE_ADDR'])))) { ?>
  <?php require($template->get_template_dir('tpl_ezpages_bar_header.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_ezpages_bar_header.php'); ?>
  <?php } ?>


<div class="menu_box">
  <?php }
    require($template->get_template_dir('tpl_menu_tabs.php',DIR_WS_TEMPLATE, $current_page_base,'common'). '/tpl_menu_tabs.php');
  ?>

<br class="clearBoth"/>
</div>
</div>
<br class="clearBoth"/>