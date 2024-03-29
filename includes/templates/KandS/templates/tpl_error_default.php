<?php
/**
 * Page Template
 *
 * Loaded automatically by index.php?main_page=conditions.<br />
 * Displays conditions page.
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_conditions_default.php 3464 2006-04-19 00:07:26Z ajeh $
 */
?>
<div class="centerColumn" id="conditions">
  <div class="listAreaTop">
    <h1 id="conditionsHeading">
    <?php echo $e_title; ?></h1>
  </div>

  <div id="errorContent">

  <?php echo $e_mgs;
  /**
   * require the html_define for the conditions page
   */
    //require($define_page);
  ?>

  <br /><br class="clearBoth" /><br />
  <div class="buttonRow back"><?php
    echo zen_back_link() . zen_image_button(BUTTON_IMAGE_CONTINUE, BUTTON_CONTINUE_ALT) . '</a>'; ?></div>
    <br /><br />
  </div>
</div>