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

   $content .= 'We are always looking for ways to improve our website to give you the best possible experience while browsing, and would appreciate if you would spare a few moments to answer a few simple questions to help us achieve this.';

   $content .= '<br/><br/>';
   $content .= zen_draw_separator('pixel_trans.gif',40,1);
   $content .= '<a href="' . zen_href_link('feedback').'">' . zen_image_button('button_leave_feedback.gif','Leave Feedback').'</a>';
   $content .= '</div>';
?>
