<?php
/**
 * blank sidebox - allows a blank sidebox to be added to your site
 *
 * @package templateSystem
 * @copyright 2007 Kuroi Web Design
  * @copyright Portions Copyright 2003-2007 Zen Cart Development Team
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: blank_sidebox.php 2007-05-26 kuroi $
 */

  $content = '';
  $content .= '<div id="' . str_replace('_', '-', $box_id . 'Content') . '" class="sideBoxContent">';

  // Replace the text and HTML tags between the apostophes on lines 19 and 20.
  // Use as many or as few lines using this model as you need for your custom content.
  // If you have a multilingual site define your text in the languages/YOUR_LANGUAGE/extra_definitions/blank_sidebox_defines.php and include it as shown in line 19.
  // If your site is monolingual, you can put the text right here as shown on line 20 (and nobody will know!)
  $content .= '<p>' . TEXT_BLANK_SIDEBOX . '</p>';
  $content .= '<p>You can include text, links, images, HTML markup and even PHP code</p>';

  $content .= '</div>';
?>