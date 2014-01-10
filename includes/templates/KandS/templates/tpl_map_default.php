<?php
/**
 * Page Template
 *
 * Loaded automatically by index.php?main_page=contact_us.<br />
 * Displays contact us page form.
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_contact_us_default.php 3651 2006-05-22 05:18:52Z ajeh $
 */
?>
<div class="centerColumn">
  <div class="listAreaTop">
    <h1 id="siteMapHeading">Store Location</h1>
  </div>
  <div id="bodyWrap">
    <div id="map_define"><?php
      include $define_page; ?>
    </div>
    <div id="map" style="width: <?php echo GOOGLE_MAP_WIDTH; ?>px; height: <?php echo GOOGLE_MAP_HEIGHT; ?>px"></div>
  </div>
</div>