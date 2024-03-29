<?php
/**
 * Page Template
 *
 * Loaded automatically by index.php?main_page=account_edit.<br />
 * View or change Customer Account Information
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_account_edit_default.php 3848 2006-06-25 20:33:42Z drbyte $
 * @copyright Portions Copyright 2003 osCommerce
 */
?>
<div class="centerColumn" id="accountEditDefault">
<div class="listAreaTop"><h1 id="pgTopHeading">Edit Account</h1></div>
<div id="bodyWrap" style="padding: 1px 10px 10px 10px; text-align: left;">
<?php echo zen_draw_form('account_edit', zen_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL'), 'post', 'onsubmit="return check_form(account_edit);"') . zen_draw_hidden_field('action', 'process'); ?>

<?php if ($messageStack->size('account_edit') > 0) echo $messageStack->output('account_edit'); ?>

<fieldset>
<legend><?php echo HEADING_TITLE; ?></legend>
<div class="alert forward"><?php echo FORM_REQUIRED_INFORMATION; ?></div>
<br class="clearBoth" />

<?php
  if (ACCOUNT_GENDER == 'true') {
?>
<label class="accCreateLeft">Saluation</label>
<?php echo zen_draw_radio_field('gender', 'm', $male, 'id="gender-male"') . '<label class="radioButtonLabel" for="gender-male">' . MALE . '</label>' . zen_draw_radio_field('gender', 'f', $female, 'id="gender-female"') . '<label class="radioButtonLabel" for="gender-female">' . FEMALE . '</label>' . (zen_not_null(ENTRY_GENDER_TEXT) ? '<span class="alert">' . ENTRY_GENDER_TEXT . '</span>': ''); ?>
<br class="clearBoth" />
<?php
  }
?>

<label class="accCreateLeft" for="firstname"><?php echo ENTRY_FIRST_NAME; ?></label>
<?php echo zen_draw_input_field('firstname', $account->fields['customers_firstname'], 'id="firstname"'.' onfocus="reSetBKColor(event)"') . (zen_not_null(ENTRY_FIRST_NAME_TEXT) ? '<span class="alert">' . ENTRY_FIRST_NAME_TEXT . '</span>': ''); ?>
<br class="clearBoth" />

<label class="accCreateLeft" for="lastname"><?php echo ENTRY_LAST_NAME; ?></label>
<?php echo zen_draw_input_field('lastname', $account->fields['customers_lastname'], 'id="lastname"'.' onfocus="reSetBKColor(event)"') . (zen_not_null(ENTRY_LAST_NAME_TEXT) ? '<span class="alert">' . ENTRY_LAST_NAME_TEXT . '</span>': ''); ?>
<br class="clearBoth" />

<?php
  if (ACCOUNT_DOB == 'true') {
?>
<label class="accCreateLeft" for="dob"><?php echo ENTRY_DATE_OF_BIRTH; ?></label>
<?php echo zen_draw_input_field('dob', zen_date_short($account->fields['customers_dob']), 'id="dob"') . (zen_not_null(ENTRY_DATE_OF_BIRTH_TEXT) ? '<span class="alert">' . ENTRY_DATE_OF_BIRTH_TEXT . '</span>': ''); ?>
<br class="clearBoth" />
<?php
  }
?>

<label class="accCreateLeft" for="email-address"><?php echo ENTRY_EMAIL_ADDRESS; ?></label>
<?php echo zen_draw_input_field('email_address', $account->fields['customers_email_address'], 'id="email_address"'.' onfocus="reSetBKColor(event)"') . (zen_not_null(ENTRY_EMAIL_ADDRESS_TEXT) ? '<span class="alert">' . ENTRY_EMAIL_ADDRESS_TEXT . '</span>': ''); ?>
<br class="clearBoth" />

<label class="accCreateLeft" for="email_addressConf">Confirm Email</label>
<?php echo zen_draw_input_field('email_addressConf', $account->fields['customers_email_address'], 'id="email_addressConf"'.' onfocus="reSetBKColor(event)"') . (zen_not_null(ENTRY_EMAIL_ADDRESS_TEXT) ? '<span class="alert">' . ENTRY_EMAIL_ADDRESS_TEXT . '</span>': ''); ?>
<br class="clearBoth" />

<label class="accCreateLeft" for="telephone"><?php echo ENTRY_TELEPHONE_NUMBER; ?></label>
<?php echo zen_draw_input_field('telephone', $account->fields['customers_telephone'], 'id="telephone"'.' onfocus="reSetBKColor(event)"') . (zen_not_null(ENTRY_TELEPHONE_NUMBER_TEXT) ? '<span class="alert">' . ENTRY_TELEPHONE_NUMBER_TEXT . '</span>': ''); ?>
<br class="clearBoth" />

<label class="accCreateLeft" for="mobile">Mobile Number:</label>
<?php echo zen_draw_input_field('mobile', $mobile, 'id="mobile"'.' onfocus="reSetBKColor(event)"') . (zen_not_null(ENTRY_TELEPHONE_NUMBER_TEXT) ? '<span class="alert">' . ENTRY_TELEPHONE_NUMBER_TEXT . '</span>': ''); ?>
<br class="clearBoth" />

<label class="accCreateLeft" for="fax"><?php echo ENTRY_FAX_NUMBER; ?></label>
<?php echo zen_draw_input_field('fax', $account->fields['customers_fax'], 'id="fax"') . (zen_not_null(ENTRY_FAX_NUMBER_TEXT) ? '<span class="alert">' . ENTRY_FAX_NUMBER_TEXT . '</span>': ''); ?>
<br class="clearBoth" />

<?php
  if (CUSTOMERS_REFERRAL_STATUS == 2 and $customers_referral == '') {
?>
<label class="accCreateLeft" for="customers-referral"><?php echo ENTRY_CUSTOMERS_REFERRAL; ?></label>
<?php echo zen_draw_input_field('customers_referral', '', zen_set_field_length(TABLE_CUSTOMERS, 'customers_referral', 15), 'id="customers-referral"'); ?>
<br class="clearBoth" />
<?php } ?>

<?php
  if (CUSTOMERS_REFERRAL_STATUS == 2 and $customers_referral != '') {
?>
<label for="customers-referral-readonly"><?php echo ENTRY_CUSTOMERS_REFERRAL; ?></label>
<?php echo $customers_referral; zen_draw_hidden_field('customers_referral', $customers_referral,'id="customers-referral-readonly"'); ?>
<br class="clearBoth" />
<?php } ?>
</fieldset>

<?php
    if($account_type=='b'){
?>
<fieldset>
<legend>My Company Information</legend>
<div class="accCreateLeft"><label class="inputLabel" for="company"><?php echo ENTRY_COMPANY; ?></label></div>
<?php echo zen_draw_input_field('company', $company, zen_set_field_length(TABLE_ADDRESS_BOOK, 'entry_company', '40') . ' id="company"'.' onfocus="reSetBKColor(event)"') . '*' . (zen_not_null(ENTRY_COMPANY_TEXT) ? '<span class="alert">' . ENTRY_COMPANY_TEXT . '</span>': ''); ?><br class="clearBoth" />

<div class="accCreateLeft">VAT Number:</div>
<?php echo zen_draw_input_field('vatNum', $vat_num, 'maxlength="120" size="20" ' . ' id="vatNum"'); ?><br class="clearBoth" />

<div class="accCreateLeft">Business Type:</div>
<?php echo zen_draw_input_field('trade-type', $trade_type, 'maxlength="120" size="20" ' . ' id="trade-type" disabled'); ?><br class="clearBoth" />
</fieldset>
<?php
    }
?>

<fieldset style="text-align: center;">
<legend><?php echo ENTRY_EMAIL_PREFERENCE; ?></legend>
<?php echo zen_draw_radio_field('email_format', 'HTML', $email_pref_html,'id="email-format-html"') . '<label class="radioButtonLabel" for="email-format-html">' . ENTRY_EMAIL_HTML_DISPLAY . '</label>' . zen_draw_radio_field('email_format', 'TEXT', $email_pref_text, 'id="email-format-text"') . '<label  class="radioButtonLabel" for="email-format-text">' . ENTRY_EMAIL_TEXT_DISPLAY . '</label>'; ?>
<br class="clearBoth" />
</fieldset>

<div class="buttonRow back"><?php echo '<a href="' . zen_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . zen_image_button(BUTTON_IMAGE_BACK , BUTTON_BACK_ALT) . '</a>'; ?></div>
<div class="buttonRow forward"><?php echo zen_image_submit(BUTTON_IMAGE_UPDATE , BUTTON_UPDATE_ALT); ?></div>
<br class="clearBoth" />

</form>
</div>
</div>