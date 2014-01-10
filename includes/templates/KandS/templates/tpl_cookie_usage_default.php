<?php
/**
 * Page Template
 *
 * Loaded automatically by index.php?main_page=cookie_usage.<br />
 * Displays information page, if cookie only is set in admin and cookies disabled in users browser.
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_cookie_usage_default.php 2540 2005-12-11 07:55:22Z birdbrain $
 */
?>
<div class="centerColumn" id="cookieUsageDefault">

<div class="listAreaTop">
<?php if(!$user_call){ ?>

<h1 id="cookieUsageDefaultHeading"><?php

echo HEADING_TITLE; ?></h1></div>
<div id="bodyWrap">
<div id="cookieUsageDefaultMainContent" class="content"><?php echo TEXT_INFORMATION; ?></div>
<fieldset>
<legend><?php echo BOX_INFORMATION_HEADING; ?></legend>

<div id="cookieUsageDefaultSecondaryContent" class="content"><?php echo BOX_INFORMATION; ?></div>
</fieldset>

<div id="cookieUsageDefaultContent2" class="content"><?php echo TEXT_INFORMATION_2; ?></div>

<div id="cookieUsageDefaultContent4" class="content"><?php echo TEXT_INFORMATION_4; ?></div>

<div id="cookieUsageDefaultContent5" class="content"><?php echo TEXT_INFORMATION_5; ?></div>

<div id="cookieUsageDefaultContent3" class="content"><?php echo TEXT_INFORMATION_3;?></div>
<br>

<div class="buttonRow back"><?php echo zen_back_link() . zen_image_button(BUTTON_IMAGE_CONTINUE, BUTTON_CONTINUE_ALT) . '</a>'; ?></div>
</div>

<?php }else{ ?>
<h1 id="cookieUsageDefaultHeading">Cookie Policy</h1></div>
<div id="bodyWrap">

<?php
$define_page = zen_get_file_directory(DIR_WS_LANGUAGES . $_SESSION['language'] . '/html_includes/', FILENAME_DEFINE_COOKIE_POLICY, 'false');
require($define_page);
?>
<div class="buttonRow back"><?php echo zen_back_link() . zen_image_button(BUTTON_IMAGE_CONTINUE, BUTTON_CONTINUE_ALT) . '</a>'; ?></div>
</div>
</div>

<?php }?>
</div>