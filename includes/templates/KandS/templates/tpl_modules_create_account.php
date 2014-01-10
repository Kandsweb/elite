<?php
/**
 * Page Template
 *
 * Loaded automatically by index.php?main_page=create_account.<br />
 * Displays Create Account form.
 *
 * @package templateSystem
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_modules_create_account.php 4822 2006-10-23 11:11:36Z drbyte $
 */
?>

<?php if ($messageStack->size('create_account') > 0) echo $messageStack->output('create_account'); ?>

<br class="clearBoth" />

<fieldset>
<legend>Register as*</legend>
<div class="accCreateField">
<?php echo
zen_draw_radio_field('userType', 'h', '', 'class="user-type"') . ' Home User <br>'.
zen_draw_radio_field('userType', 'b', 'true', 'class="user-type"') . ' Business User'; ?>
</div>
<br class="clearBoth" />
</fieldset>

<!-- ------------------------------------------------------------ -->

<fieldset>
<legend><?php echo TABLE_HEADING_LOGIN_DETAILS; ?></legend>

<div class="accCreateLeft"> <label class="inputLabel" for="email-address">Your Email Address</label></div>
<?php echo zen_draw_input_field('email_address', '', zen_set_field_length(TABLE_CUSTOMERS, 'customers_email_address', '40') . ' id="email-address"'.' onfocus="reSetBKColor(event)"')  . (zen_not_null(ENTRY_EMAIL_ADDRESS_TEXT) ? '<span class="alert">' . ENTRY_EMAIL_ADDRESS_TEXT . '</span>': ''); ?><br class="clearBoth" />

<div class="accCreateLeft"> <label class="inputLabel" for="email-addressConf">Confirm Email Address</label></div>
<?php echo zen_draw_input_field('email_addressConf', '', zen_set_field_length(TABLE_CUSTOMERS, 'customers_email_address', '40') . ' id="email-addressConf"'.' onfocus="reSetBKColor(event)"') . (zen_not_null(ENTRY_EMAIL_ADDRESS_TEXT) ? '<span class="alert">' . ENTRY_EMAIL_ADDRESS_TEXT . '</span>': ''); ?><br class="clearBoth" />

<div class="accCreateLeft"><label class="inputLabel" for="password-new"><?php echo ENTRY_PASSWORD; ?></label></div>
<?php echo zen_draw_password_field('password', '', zen_set_field_length(TABLE_CUSTOMERS, 'customers_password', '20') . ' id="password-new"'.' onfocus="reSetBKColor(event)"') . (zen_not_null(ENTRY_PASSWORD_TEXT) ? '<span class="alert">' . ENTRY_PASSWORD_TEXT . '</span>': ''); ?><br class="clearBoth" />

<div class="accCreateLeft"><label class="inputLabel" for="password-confirm">Retype Password</label></div>
<?php echo zen_draw_password_field('confirmation', '', zen_set_field_length(TABLE_CUSTOMERS, 'customers_password', '20') . ' id="password-confirm"'.' onfocus="reSetBKColor(event)"') . (zen_not_null(ENTRY_PASSWORD_CONFIRMATION_TEXT) ? '<span class="alert">' . ENTRY_PASSWORD_CONFIRMATION_TEXT . '</span>': ''); ?>
</fieldset>


<!-- ------------------------------------------------------------ -->

<?php
  if (ACCOUNT_COMPANY == 'true') {
?>
<fieldset class="businessShow">
<legend><?php echo CATEGORY_COMPANY; ?></legend>

<div class="accCreateLeft"><label class="inputLabel" for="company"><?php echo ENTRY_COMPANY; ?></label></div>
<?php echo zen_draw_input_field('company', '', zen_set_field_length(TABLE_ADDRESS_BOOK, 'entry_company', '40') . ' id="company"'.' onfocus="reSetBKColor(event)"') . '*' . (zen_not_null(ENTRY_COMPANY_TEXT) ? '<span class="alert">' . ENTRY_COMPANY_TEXT . '</span>': ''); ?><br class="clearBoth" />

<div class="accCreateLeft">VAT Number:</div>
<?php echo zen_draw_input_field('vatNum', '', 'maxlength="120" size="20" ' . ' id="vatNum"'.' onfocus="reSetBKColor(event)"'); ?><br class="clearBoth" />

<div class="accCreateLeft"></div>
<?php echo zen_draw_radio_field('trading-type', 's', '', 'id="trading-type-sole"'.' onfocus="reSetBKColor(event)"'); ?>Sole Trader &nbsp;

<?php echo zen_draw_radio_field('trading-type', 'c', '', 'id="trading-type-company"'.' onfocus="reSetBKColor(event)"'); ?>Limited Company*<br class="clearBoth" />

<div class="accCreateLeft">Business Type</div>
<?php echo zen_draw_pull_down_menu('trade-type',trades_pulldown_array()
,'0','class="trade-type" id="trade_type"'.' onfocus="reSetBKColor(event)"').zen_draw_input_field('trade-other','','class="tradeOther" id=""tradeOther'.' onfocus="reSetBKColor(event)"');
?>*
</fieldset>
<?php
  }
?>

<!-- ------------------------------------------------------------ -->

<fieldset>
<legend>About You</legend>
<?php  if (ACCOUNT_GENDER == 'true') {?>
<div class="accCreateLeft"></div>
<?php echo
zen_draw_radio_field('gender', 'm', '', 'id="gender-male"') . '<label class="radioButtonLabel" for="gender-male">' . MALE . '</label>' .
zen_draw_radio_field('gender', 'f', '', 'id="gender-female"') . '<label class="radioButtonLabel" for="gender-female">' . FEMALE . '</label>' . (zen_not_null(ENTRY_GENDER_TEXT) ? '<span class="alert">' . ENTRY_GENDER_TEXT . '</span>': ''); ?>
<br class="clearBoth" />
<?php  } ?>

<div class="accCreateLeft"><label class="inputLabel" for="firstname"><?php echo ENTRY_FIRST_NAME; ?></label></div>
<?php echo zen_draw_input_field('firstname', '', zen_set_field_length(TABLE_CUSTOMERS, 'customers_firstname', '40') . ' id="firstname"'.' onfocus="reSetBKColor(event)"') . (zen_not_null(ENTRY_FIRST_NAME_TEXT) ? '<span class="alert">' . ENTRY_FIRST_NAME_TEXT . '</span>': ''); ?><br class="clearBoth" />

<div class="accCreateLeft"><label class="inputLabel" for="lastname"><?php echo ENTRY_LAST_NAME; ?></label></div>
<?php echo zen_draw_input_field('lastname', '', zen_set_field_length(TABLE_CUSTOMERS, 'customers_lastname', '40') . ' id="lastname"'.' onfocus="reSetBKColor(event)"') . (zen_not_null(ENTRY_LAST_NAME_TEXT) ? '<span class="alert">' . ENTRY_LAST_NAME_TEXT . '</span>': ''); ?><br class="clearBoth" />
</fieldset>

<!-- ------------------------------------------------------------ -->

<fieldset>
<legend><?php echo TABLE_HEADING_ADDRESS_DETAILS; ?></legend>

<div class="accCreateLeft"><label class="inputLabel" for="country"><?php echo ENTRY_COUNTRY; ?></label></div>
<?php echo zen_get_country_list('zone_country_id', $selected_country, 'id="country" ' . ($flag_show_pulldown_states == true ? 'onchange="update_zone(this.form);"' : '')) . (zen_not_null(ENTRY_COUNTRY_TEXT) ? '<span class="alert">' . ENTRY_COUNTRY_TEXT . '</span>': ''); ?>
<br class="clearBoth" />

<div class="accCreateLeft"><label class="inputLabel" for="street-address">Address</label></div>
<?php echo zen_draw_input_field('street_address', '', zen_set_field_length(TABLE_ADDRESS_BOOK, 'entry_street_address', '40') . ' id="street-address"'.' onfocus="reSetBKColor(event)"') . (zen_not_null(ENTRY_STREET_ADDRESS_TEXT) ? '<span class="alert">' . ENTRY_STREET_ADDRESS_TEXT . '</span>': ''); ?><br class="clearBoth" />

<?php if (ACCOUNT_SUBURB == 'true') { ?>
<div class="accCreateLeft"><label class="inputLabel" for="suburb"><?php echo ENTRY_SUBURB; ?></label></div>
<?php echo zen_draw_input_field('suburb', '', zen_set_field_length(TABLE_ADDRESS_BOOK, 'entry_suburb', '40') . ' id="suburb"'.' onfocus="reSetBKColor(event)"') . (zen_not_null(ENTRY_SUBURB_TEXT) ? '<span class="alert">' . ENTRY_SUBURB_TEXT . '</span>': ''); ?><br class="clearBoth" />
<?php } ?>

<div class="accCreateLeft"><label class="inputLabel" for="city"><?php echo ENTRY_CITY; ?></label></div>
<?php echo zen_draw_input_field('city', '', zen_set_field_length(TABLE_ADDRESS_BOOK, 'entry_city', '40') . ' id="city"'.' onfocus="reSetBKColor(event)"') . (zen_not_null(ENTRY_CITY_TEXT) ? '<span class="alert">' . ENTRY_CITY_TEXT . '</span>': ''); ?><br class="clearBoth" />

<?php if (ACCOUNT_STATE == 'true') { if ($flag_show_pulldown_states == true) {?>
<div class="accCreateLeft"><label class="inputLabel" for="stateZone" id="zoneLabel"><?php echo ENTRY_STATE; ?></label></div>
<?php  echo zen_draw_pull_down_menu('zone_id', zen_prepare_country_zones_pull_down($selected_country), $zone_id, 'id="stateZone"'.' onfocus="reSetBKColor(event)"');
if (zen_not_null(ENTRY_STATE_TEXT)) echo '&nbsp;<span class="alert">' . ENTRY_STATE_TEXT . '</span>';
}?>

<?php if ($flag_show_pulldown_states == true) { ?>
<br class="clearBoth" id="stBreak" />
<?php } ?>
<div class="accCreateLeft" id="stDiv"><label class="inputLabel" for="state" id="stateLabel"><?php echo $state_field_label; ?></label></div>
<?php
    echo zen_draw_input_field('state', '', zen_set_field_length(TABLE_ADDRESS_BOOK, 'entry_state', '40') . ' id="state"'.' onfocus="reSetBKColor(event)"');
    if (zen_not_null(ENTRY_STATE_TEXT)) echo '&nbsp;<span class="alert" id="stText">' . ENTRY_STATE_TEXT . '</span>';
    if ($flag_show_pulldown_states == false) {
      echo zen_draw_hidden_field('zone_id', $zone_name, ' ');
    }
?>
<br class="clearBoth" />
<?php
  }
?>

<div class="accCreateLeft" id="pcDiv"><label class="inputLabel" id="pcText" for="postcode"><?php echo ENTRY_POST_CODE; ?></label></div>
<?php echo zen_draw_input_field('postcode', '', zen_set_field_length(TABLE_ADDRESS_BOOK, 'entry_postcode', '40') . ' id="postcode"'.' onfocus="reSetBKColor(event)"') . (zen_not_null(ENTRY_POST_CODE_TEXT) ? '<span class="alert" id="pcAlert">' . ENTRY_POST_CODE_TEXT . '</span>': ''); ?>
<br class="clearBoth" />
</fieldset>

<!-- ------------------------------------------------------------ -->

<?php
  if (DISPLAY_PRIVACY_CONDITIONS == 'true') {
?>
<fieldset>
<legend><?php echo TABLE_HEADING_PRIVACY_CONDITIONS; ?></legend>
<div class="information"><?php echo TEXT_PRIVACY_CONDITIONS_DESCRIPTION;?></div>
<?php echo zen_draw_checkbox_field('privacy_conditions', '1', false, 'id="privacy"');?>
<label class="checkboxLabel" for="privacy"><?php echo TEXT_PRIVACY_CONDITIONS_CONFIRM;?></label>
</fieldset>
<?php
  }
?>

<!-- ------------------------------------------------------------ -->
<?php
  if (ACCOUNT_DOB == 'true') {
?>
<fieldset>
<legend><?php echo TABLE_HEADING_DATE_OF_BIRTH; ?></legend>
<label class="inputLabel" for="dob"><?php echo ENTRY_DATE_OF_BIRTH; ?></label>
<?php echo zen_draw_input_field('dob','', 'id="dob"') . (zen_not_null(ENTRY_DATE_OF_BIRTH_TEXT) ? '<span class="alert">' . ENTRY_DATE_OF_BIRTH_TEXT . '</span>': ''); ?>
<br class="clearBoth" />
</fieldset>
<?php
  }
?>
<fieldset>
<legend>Communication preferences</legend>

<div class="accCreateLeft"><label class="inputLabel" for="telephone"><?php echo ENTRY_TELEPHONE_NUMBER; ?></label></div>
<?php echo zen_draw_input_field('telephone', '', zen_set_field_length(TABLE_CUSTOMERS, 'customers_telephone', '40') . ' id="telephone"'.' onfocus="reSetBKColor(event)"') . (zen_not_null(ENTRY_TELEPHONE_NUMBER_TEXT) ? '<span class="alert">' . ENTRY_TELEPHONE_NUMBER_TEXT . '</span>': ''); ?><br class="clearBoth" />

<div class="accCreateLeft"><label class="inputLabel" for="mobile">Mobile Number:</label></div>
<?php echo zen_draw_input_field('mobile', '', zen_set_field_length(TABLE_CUSTOMERS, 'customers_telephone', '40') . ' id="mobile"'.' onfocus="reSetBKColor(event)"') . (zen_not_null(ENTRY_TELEPHONE_NUMBER_TEXT) ? '<span class="alert">' . ENTRY_TELEPHONE_NUMBER_TEXT . '</span>': ''); ?><br class="clearBoth" />

<?php
  if (ACCOUNT_FAX_NUMBER == 'true') { ?>
<div class="accCreateLeft businessShow"><label class="inputLabel" for="fax"><?php echo ENTRY_FAX_NUMBER; ?></label></div>
<?php echo zen_draw_input_field('fax', '', zen_set_field_length(TABLE_CUSTOMERS, 'customers_fax', '40'). 'id="fax" class="businessShow"'.' onfocus="reSetBKColor(event)"') . (zen_not_null(ENTRY_FAX_NUMBER_TEXT) ? '<span class="alert">' . ENTRY_FAX_NUMBER_TEXT . '</span>': ''); ?> <br class="clearBoth" />
<?php  } ?>

<?php
  if (ACCOUNT_NEWSLETTER_STATUS != 0) {
?>
<div class="accCreateLeft"></div><?php echo zen_draw_checkbox_field('newsletter', '1', $newsletter, 'id="newsletter-checkbox"') . '<label class="checkboxLabel" for="newsletter-checkbox">' . ENTRY_NEWSLETTER . '</label>' . (zen_not_null(ENTRY_NEWSLETTER_TEXT) ? '<span class="alert">' . ENTRY_NEWSLETTER_TEXT . '</span>': ''); ?>
<br class="clearBoth" />
<?php } ?>

<div class="accCreateLeft"></div><?php echo zen_draw_radio_field('email_format', 'HTML', ($email_format == 'HTML' ? true : false),'id="email-format-html"') . '<label class="radioButtonLabel" for="email-format-html">' . ENTRY_EMAIL_HTML_DISPLAY . '</label>' .  zen_draw_radio_field('email_format', 'TEXT', ($email_format == 'TEXT' ? true : false), 'id="email-format-text"') . '<label class="radioButtonLabel" for="email-format-text">' . ENTRY_EMAIL_TEXT_DISPLAY . '</label>'; ?>
<br class="clearBoth" />
</fieldset>

<?php
  if (CUSTOMERS_REFERRAL_STATUS == 2) {
?>
<fieldset>

<legend><?php echo TABLE_HEADING_REFERRAL_DETAILS; ?></legend>
<label class="inputLabel" for="customers_referral"><?php echo ENTRY_CUSTOMERS_REFERRAL; ?></label>
<?php echo zen_draw_input_field('customers_referral', '', zen_set_field_length(TABLE_CUSTOMERS, 'customers_referral', '15') . ' id="customers_referral"'); ?>
<br class="clearBoth" />
</fieldset>
<?php } ?>
