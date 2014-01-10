<?php
/**
 * Page Template
 *
 * Loaded automatically by index.php?main_page=contact_us.<br />
 * Displays contact us page form.
 *
 * @package templateSystem
 * @copyright Copyright 2003-2009 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_contact_us_default.php 16307 2010-05-21 21:50:06Z wilt $
 */

  echo ' <script type="text/javascript">
                   var RecaptchaOptions = {
                      theme : \'clean\'
                   };
                   </script>';
 $publickey = CAPTCHA_PUBLIC_KEY;
?>
<!--The overlay and message box-->
<div id="fuzz">
  <div class="msgbox">
    <h4>Sending request<br />Please wait....</h4>
  <img src="images/loading-gif-animation.gif" width="80" height="80" /><br />
  </div>
</div>
<!--End the overlay and message box-->

<div class="centerColumn" id="contactUsDefault">


<?php echo zen_draw_form('contact_us', zen_href_link(FILENAME_CONTACT_US, 'action=send')); ?>
<div class="listAreaTop"><h1 id="conditionsHeading"><?php echo $breadcrumb->last(); ?></h1></div>
<div id="bodyWrap">

<?php
  if (isset($_GET['action']) && ($_GET['action'] == 'success')) {
?>

<div class="mainContent success"><?php echo TEXT_SUCCESS; ?></div>

<div class="buttonRow"><a href="<?php echo zen_href_link(FILENAME_DEFAULT). '">' . zen_image_button(BUTTON_IMAGE_BACK, BUTTON_BACK_ALT) . '</a>'; ?></div>

<?php
  } else {
?>

<?php if (DEFINE_CONTACT_US_STATUS >= '1' and DEFINE_CONTACT_US_STATUS <= '2') { ?>
<div id="contactUsNoticeContent" class="content">
<?php
/**
 * require html_define for the contact_us page
 */
  require($define_page);
?>
</div>
<?php } ?>

<?php if ($messageStack->size('contact') > 0) echo $messageStack->output('contact'); ?>

<fieldset id="contactUsForm">
<legend><?php echo HEADING_TITLE; ?></legend>
<div class="alert forward"><?php echo FORM_REQUIRED_INFORMATION; ?></div>
<br class="clearBoth" />
<div id="err_msg"></div>
<?php
// show dropdown if set
    if (CONTACT_US_LIST !=''){
?>
<label class="inputLabel" for="send-to"><?php echo SEND_TO_TEXT; ?></label>
<?php echo zen_draw_pull_down_menu('send_to',  $send_to_array, 0, 'id="send-to"') . '<span class="alert">' . ENTRY_REQUIRED_SYMBOL . '</span>'; ?>
<br class="clearBoth" />
<?php
    }
?>

<?php /////////////Elite only///////////////////////
if($product_model){
  ?>
<label class="inputLabel" for="mId">Product Model</label>
<?php echo zen_draw_input_field('mId', $product_model, ' size="10" id="mId" disabled="disabled"') ;
      echo zen_draw_hidden_field('modelId',$product_model); ?>
<br class="clearBoth" /><br />
<?php
}
////////////////////////////////////////// ?>

<div id="name_wrap">
<img class="err_icon" src="/images/icons/icn_fielderror.png"/><label class="inputLabel" for="contactname"><?php echo ENTRY_NAME . '<span class="alert">' . ENTRY_REQUIRED_SYMBOL . '</span>'; ?> &nbsp; &nbsp; &nbsp; </label>
<?php echo zen_draw_input_field('contactname', $name, ' size="40" id="contactname"'); ?>
<br class="clearBoth" />
</div>

<div id="email_wrap">
<img class="err_icon" src="/images/icons/icn_fielderror.png"/><label class="inputLabel" for="email-address"><?php echo ENTRY_EMAIL. '<span class="alert">' . ENTRY_REQUIRED_SYMBOL . '</span>'; ?></label>
<?php echo zen_draw_input_field('email', ($email_address), ' size="40" id="email-address"') ; ?>
<br class="clearBoth" />
</div>

<div id="message_wrap" style="vertical-align: top">
<img class="err_icon" src="/images/icons/icn_fielderror.png"/><label for="enquiry" style="vertical-align: top"> <?php echo ENTRY_ENQUIRY . '<span class="alert">' .ENTRY_REQUIRED_SYMBOL. '</span>'; ?> &nbsp; &nbsp; &nbsp;  &nbsp;</label>
<?php echo zen_draw_textarea_field('enquiry', '30', '5', $enquiry, 'id="enquiry"') .'<br />';?>
</div>

<div id="captcha_wrap">
<img class="err_icon" src="/images/icons/icn_fielderror.png"/><label class="inputLabel" for="captcha" style="text-align:left">Security Code*</label>
<?php echo '<p>' . recaptcha_get_html($publickey) . '</p>'; ?>
 </div>
 <br />

<div class="buttonRow forward">
<script type="text/javascript">
document.write('<?php echo zen_image_button(BUTTON_IMAGE_SEND, BUTTON_SEND_ALT, 'onClick="checkForm()"'); ?>');
</script>

<noscript>
<?php echo zen_image_submit(BUTTON_IMAGE_SEND, BUTTON_SEND_ALT); ?>
</noscript>
</div>

</fieldset>
<br />
<div class="buttonRow back"><?php echo zen_back_link() . zen_image_button(BUTTON_IMAGE_BACK, BUTTON_BACK_ALT) . '</a>'; ?></div><br />
<?php if (CONTACT_US_STORE_NAME_ADDRESS== '1') { ?>
<address><?php echo nl2br(STORE_NAME_ADDRESS); ?></address>
<?php } ?>
<?php
  }
?>

</form>
</div>
</div>