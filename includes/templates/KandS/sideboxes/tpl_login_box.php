<?php
//
// +----------------------------------------------------------------------+
// |zen-cart Open Source E-commerce                                       |
// +----------------------------------------------------------------------+
// | Copyright (c) 2003 The zen-cart developers                           |
// |                                                                      |
// | http://www.zen-cart.com/index.php                                    |
// |                                                                      |
// | Portions Copyright (c) 2003 osCommerce                               |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the GPL license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.zen-cart.com/license/2_0.txt.                             |
// | If you did not receive a copy of the zen-cart license and are unable |
// | to obtain it through the world-wide-web, please send a note to       |
// | license@zen-cart.com so we can mail you a copy immediately.          |
// +----------------------------------------------------------------------+
// $Id: tpl_login_box.php,v 1.0 2003/11/21 19:16:29 ajeh Exp $
//
// Designed for Zen Cart v1.00 Alpha
// Created by: Linda McGrath ZenCart@WebMakers.com
// http://www.thewebmakerscorner.com

// Updated to 1.3 standard (XHTML compliant) 2006/06/07  Rick Suffolk
// Edited by: Ian Manson thor@paradise.net.nz 2006 08 13 to include some my account links when actually logged in
// Updated 2007 12 10 for compatibility with v1.3.8

$content = "<!-- loginSideBox -->" . "\n\n";
$content .= '<div id="' . str_replace('_', '-', $box_id . 'Content') . '" class="sideBoxContent">';

if(!$_SESSION['customer_id']) {

   $content .=zen_draw_form('login_box', zen_href_link(FILENAME_LOGIN, 'action=process', 'SSL'), 'post', 'id="loginFormSidebox"');
   $content .=LOGIN_BOX_EMAIL_ADDRESS . zen_draw_separator('pixel_trans.gif','29','1') . zen_draw_input_field('email_address', '', 'size="34" style="font-size:10px;"').'<br />';
   $content .=LOGIN_BOX_PASSWORD . '&nbsp;' . zen_draw_password_field('password', '', 'size="34" style="margin: 5px 0px; font-size:10px;"') . '<br />';
   $content .= zen_draw_hidden_field('securityToken', $_SESSION['securityToken']);
   $content .='<div class="centeredContent" id="loginBoxButtons">'.zen_image_submit(BUTTON_IMAGE_LOGIN, BUTTON_LOGIN_ALT);
   //$content .='<a href="' . zen_href_link(FILENAME_PASSWORD_FORGOTTEN, '', 'SSL') . '">' . LOGIN_BOX_PASSWORD_FORGOTTEN . '</a>' . '<br />';
   $content .='</div>';
   $content .='</form>';
}  else {

   $content .= '<ul id="myAccountGen">';
   if (UN_MODULE_WISHLISTS_ENABLED) {
        $content .= '<li><a href="'. zen_href_link(UN_FILENAME_WISHLIST, '', 'SSL').'">'. UN_HEADER_TITLE_WISHLIST.'</a></li>';
   }
   $content .= '<li><a class="loginBoxLinks" href="' . zen_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . LOGIN_BOX_ACCOUNT . '</a></li>';
   //$content .= '<li><a class="loginBoxLinks" href="' . zen_href_link(FILENAME_SHOPPING_CART, '', 'SSL') . '">' . LOGIN_BOX_SHOPPING_CART . '</a></li>';
   $content .= '<li><a class="loginBoxLinks" href="' . zen_href_link(FILENAME_LOGOFF, '', 'SSL') . '">' . LOGIN_BOX_LOGOFF . '</a></li>';
   $content .= '</ul>';
}

$content .= '</div>';

