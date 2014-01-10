<?php
/**
 * Side Box Template
 *
 * @package templateSystem
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_subscribe.php,v 1.1 2006/06/16 01:46:16 Owner Exp $
 */
   $content = '';
   $content .= '<div id="' . str_replace('_', '-', $box_id . 'Content') . '" class="sideBoxContent centeredContent">';
   //$content .= 'Elite Lighting has a team of qualified electricans who can professionally install';
   $content .= '<a href="' . zen_href_link('services_page#design'). '">' . zen_image('includes/templates/KandS/images/check_green.gif'). "Design service</a><br />";
   $content .= '<a href="' . zen_href_link('services_page#delivery'). '">' . zen_image('includes/templates/KandS/images/check_green.gif'). "Delivery service</a><br />";
   $content .= '<a href="' . zen_href_link('services_page#installation'). '">' . zen_image('includes/templates/KandS/images/check_green.gif'). "Installation service</a><br />";

   $content .= '</div>';
?>
