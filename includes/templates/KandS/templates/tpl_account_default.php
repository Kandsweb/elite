<?php
/**
 * Page Template
 *
 * Loaded automatically by index.php?main_page=account.<br />
 * Displays previous orders and options to change various Customer Account settings
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_account_default.php 4086 2006-08-07 02:06:18Z ajeh $
 */
?>

<div class="centerColumn" id="accountDefault">
    <div class="listAreaTop">
        <h1 id="accountDefaultHeading"><?php echo HEADING_TITLE; ?></h1>
    </div>

    <div id="bodyWrap" style="padding: 1px 10px 10px 10px;">
<?php if ($messageStack->size('account') > 0) echo $messageStack->output('account'); ?>

<?php
    if($account_type=='b'){ ?>
<fieldset>
<legend>Account Status</legend>
<?php
    echo "<b>Business Account<br></b>";
    echo  $account_verified?'Verified Account':'Account verification pending';
    ?>
    </fieldset>
    <?php
    }
?>


<fieldset class="unified">
<legend><?php echo MY_ACCOUNT_TITLE; ?></legend>
<ul id="myAccountGen" class="leftAlign">
<li><?php echo ' <a href="' . zen_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL') . '">' . MY_ACCOUNT_INFORMATION . '</a>'; ?></li>
<li><?php echo ' <a href="' . zen_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL') . '">' . MY_ACCOUNT_ADDRESS_BOOK . '</a>'; ?></li>
<li><?php echo ' <a href="' . zen_href_link(FILENAME_ACCOUNT_PASSWORD, '', 'SSL') . '">' . MY_ACCOUNT_PASSWORD . '</a>'; ?></li>
</ul>
</fieldset>

<?php
  if (SHOW_NEWSLETTER_UNSUBSCRIBE_LINK !='false' or CUSTOMERS_PRODUCTS_NOTIFICATION_STATUS !='0') {
?>
<fieldset class="unified">
<legend><?php echo EMAIL_NOTIFICATIONS_TITLE; ?></legend>
<ul id="myAccountNotify" class="list">
<?php
  if (SHOW_NEWSLETTER_UNSUBSCRIBE_LINK=='true') {
?>
<li><?php echo ' <a href="' . zen_href_link(FILENAME_ACCOUNT_NEWSLETTERS, '', 'SSL') . '">' . EMAIL_NOTIFICATIONS_NEWSLETTERS . '</a>'; ?></li>
<?php } //endif newsletter unsubscribe ?>
<?php
  if (CUSTOMERS_PRODUCTS_NOTIFICATION_STATUS == '1') {
?>
<li><?php echo ' <a href="' . zen_href_link(FILENAME_ACCOUNT_NOTIFICATIONS, '', 'SSL') . '">' . EMAIL_NOTIFICATIONS_PRODUCTS . '</a>'; ?></li>

<?php } //endif product notification ?>
</ul>
</fieldset>
<?php } // endif don't show unsubscribe or notification ?>

<?php
    //if (zen_count_customer_orders() > 0) {
  ?>
<fieldset class="unified">
<legend><?php echo OVERVIEW_PREVIOUS_ORDERS; ?></legend>
<div class="forward"><?php echo '<a href="' . zen_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL') . '">' . OVERVIEW_SHOW_ALL_ORDERS . '</a>'; ?></div>
<table width="100%" border="0" cellpadding="0" cellspacing="0" id="prevOrders">
    <tr class="tableHeading">
    <th scope="col"><?php echo TABLE_HEADING_DATE; ?></th>
    <th scope="col"><?php echo TABLE_HEADING_ORDER_NUMBER; ?></th>
    <th scope="col"><?php echo TABLE_HEADING_SHIPPED_TO; ?></th>
    <th scope="col"><?php echo TABLE_HEADING_STATUS; ?></th>
    <th scope="col"><?php echo TABLE_HEADING_TOTAL; ?></th>
    <th scope="col"><?php echo TABLE_HEADING_VIEW; ?></th>
  </tr>
<?php
if (zen_count_customer_orders() > 0){
  foreach($ordersArray as $orders) {
?>
  <tr>
    <td width="70"><?php echo zen_date_short($orders['date_purchased']); ?></td>
    <td width="30"><?php echo TEXT_NUMBER_SYMBOL . $orders['orders_id']; ?></td>
    <td><address><?php echo zen_output_string_protected($orders['order_name']) . '<br />' . $orders['order_country']; ?></address></td>
    <td width="70"><?php echo $orders['orders_status_name']; ?></td>
    <td width="70" align="right"><?php echo $orders['order_total']; ?></td>
    <td align="right"><?php echo '<a href="' . zen_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id=' . $orders['orders_id'], 'SSL') . '"> ' . zen_image_button(BUTTON_IMAGE_VIEW_SMALL, BUTTON_VIEW_SMALL_ALT) . '</a>'; ?></td>
  </tr>

<?php
  }
}else{
  ?>
  <tr><td colspan="7">&nbsp;</td></tr>
  <tr>
  <td colspan="7" align="center">You have no previous orders to view</td>
  </tr>
  <?php
}
?>
</table>
</fieldset>

<?php
 // }
?>
<?php
// only show when there is a GV balance
 // if ($customer_has_gv_balance ) {
?>
<fieldset class="unified">
<legend><?php echo BOX_HEADING_GIFT_VOUCHER; ?></legend>
<?php if ($customer_has_gv_balance ) {
 require($template->get_template_dir('tpl_modules_send_or_spend.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_send_or_spend.php');
 }else{
  echo "You curently have no gift vouchers";
 }?>
<?php
 // }
?>
</fieldset>
<br class="clearBoth" />
</div>
</div>