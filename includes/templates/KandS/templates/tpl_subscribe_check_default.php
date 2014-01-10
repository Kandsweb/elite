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
// $Id: tpl_subscribe_default.php,v 1.1 2006/06/16 01:46:16 Owner Exp $  dmcl1
//
?>
<!-- body_text //-->
<div class="centerColumn" id="maintenanceDefault">
<div class="listAreaTop">
<h1 id="conditionsHeading"><?php echo HEADING_TITLE; ?></h1>
</div>
<div id="bodyWrap">
<?php

  echo ' <script type="text/javascript">
                   var RecaptchaOptions = {
                      theme : \'clean\'
                   };
                   </script>';
   echo zen_draw_form('subscribe', zen_href_link(FILENAME_SUBSCRIBE_CHECK, '', 'SSL'), 'post', 'id="form_id2" onSubmit="javascript:return emailCheck(\'form_id2\',\'email2\',\'email2\');"');
   echo zen_draw_hidden_field('act', 'subscribe');
   echo zen_draw_hidden_field('main_page',FILENAME_SUBSCRIBE);
   echo SUBSCRIBE_CAPTCHA_INSTRUCTIONS;
   echo SUBSCRIBE_EMAIL_ENTERED;
   echo '<label>' . zen_draw_input_field('email', $emailaddress, 'id="email2" size="18" maxlength="90" style="width: ' .
               ($column_width-30) . 'px" value="' . HEADER_SUBSCRIBE_DEFAULT_TEXT .
               '" onfocus="if (this.value == \'' . HEADER_SUBSCRIBE_DEFAULT_TEXT . '\') this.value = \'\';"');
   echo '</label><br/><br/>';
   echo zen_draw_radio_field('email_format', 'HTML', ($email_format == 'HTML' ? true : false),'id="email-format-html"') . '<label class="radioButtonLabel" for="email-format-html">' . ENTRY_EMAIL_HTML_DISPLAY . '</label>' .  zen_draw_radio_field('email_format', 'TEXT', ($email_format == 'TEXT' ? true : false), 'id="email-format-text"') . '<label class="radioButtonLabel" for="email-format-text">' . ENTRY_EMAIL_TEXT_DISPLAY . '</label>';

   $publickey = CAPTCHA_PUBLIC_KEY;

if ($messageStack->size('invalidemail') > 0){
     echo '<br/>';
     echo $messageStack->output('invalidemail');
   }
   if ($messageStack->size('captcha') > 0){
     echo '<br/><br/>';
     echo $messageStack->output('captcha');
   }
   if ($messageStack->size('subscribe') > 0){
     echo '<br/><br/>';
     echo $messageStack->output('subscribe');
   }


   echo '<p>' . recaptcha_get_html($publickey) . '</p>';
   echo zen_draw_hidden_field('pass','check');
   if(EMAIL_USE_HTML == 'true') {
    //Remove the choice of HTML or TEXT and set to HTML
    //$content .= ' <br /> <label>' . zen_draw_radio_field('email_format', 'HTML', true) . ENTRY_EMAIL_HTML_DISPLAY . '</label>';
    //$content .= ' <label style="white-space:nowrap">' . zen_draw_radio_field('email_format', 'TEXT', false) . ENTRY_EMAIL_TEXT_DISPLAY . '</label>';
    echo zen_draw_hidden_field('email_format', 'HTML');
   }
   echo ' <br /><p class="main">' . zen_image_submit (BUTTON_IMAGE_SUBSCRIBE,HEADER_SUBSCRIBE_BUTTON, 'value="' . HEADER_SUBSCRIBE_BUTTON . '" ');
   echo '</p></form>';

 //if(!empty($error)) { echo $messageStack->output('subscribe'); }
 ?>

  <p class="main"><br /><?php echo zen_back_link() . zen_image_button(BUTTON_IMAGE_BACK, BUTTON_BACK_ALT) . '</a>'; ?></p>
</div>
</div>