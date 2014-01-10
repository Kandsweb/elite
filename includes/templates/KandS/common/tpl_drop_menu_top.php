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
// $Id: tpl_drop_menu.php  2005/06/15 15:39:05 DrByte Exp $
//

?>
<div id="smoothmenu1" class="kasmenu1">
<ul>
<li><a href="<?php echo zen_href_link(FILENAME_DEFAULT); ?>"><?php echo HEADER_TITLE_CATALOG; ?></a>
  <ul>
    <?php if(count_promotion_items()>0){?>
      <li><a href="<?php echo zen_href_link('promotions'); ?>"><?php echo 'Promotion Items'; ?></a></li>
    <?php }?>
    <li><a href="<?php echo zen_href_link(FILENAME_PRODUCTS_ALL); ?>"><?php echo HEADER_TITLE_ALL_PRODUCTS; ?></a></li>
    <li><a href="<?php echo zen_href_link('products_new'); ?>"><?php echo HEADER_TITLE_NEW_PRODUCTS; ?></a></li>
    <li><a href="<?php echo zen_href_link(FILENAME_ADVANCED_SEARCH); ?>"> <?php echo HEADER_TITLE_SEARCH; ?></a></li>
    <li><a href="<?php echo zen_href_link(FILENAME_SITE_MAP); ?>"><?php echo HEADER_TITLE_SITE_MAP; ?></a></li>
  </ul>
</li>

<li><a href="#">My Elite</a>
<?php
    if(!$_SESSION['customer_id']){?>
    <ul>
        <li><a href="<?php echo zen_href_link(FILENAME_LOGIN); ?>"><?php echo HEADER_TITLE_LOGIN; ?></a></li>
        <li><a href="<?php echo zen_href_link(FILENAME_CREATE_ACCOUNT); ?>"><?php echo HEADER_TITLE_CREATE_ACCOUNT; ?></a></li>
    </ul>
<?php }else{ ?>
    <ul>
    <?php
        if($_SESSION['cart']->count_contents()>0){    ?>
            <li><a href="<?php echo zen_href_link(FILENAME_CHECKOUT_SHIPPING); ?>"><?php echo HEADER_TITLE_CHECKOUT; ?></a></li>
        <?php  }   ?>
        <?php
        if (UN_MODULE_WISHLISTS_ENABLED) { ?>
            <li><a href="<?php echo zen_href_link(UN_FILENAME_WISHLIST, '', 'SSL'); ?>"><?php echo UN_HEADER_TITLE_WISHLIST; ?></a></li>
        <?php } ?>
        <li><a href="<?php echo zen_href_link(FILENAME_ACCOUNT); ?>"><?php echo HEADER_TITLE_MY_ACCOUNT; ?></a></li>
        <!--<li><a href="<?php echo zen_href_link(FILENAME_SHOPPING_CART); ?>"><?php echo HEADER_TITLE_CART_CONTENTS; ?></a></li>-->
        <li><a href="<?php echo zen_href_link(FILENAME_LOGOFF); ?>"><?php echo HEADER_TITLE_LOGOFF; ?></a></li>
    </ul>
<?php } ?>
</li>

<li><a href="#"><?php echo HEADER_TITLE_INFORMATION; ?></a>
  <ul>
  	<!--<li><a href="<?php echo zen_href_link('set_cookie');?>" class="jpecrDisplayButton">Cookie Settings</a></li>-->
    <li><a href="<?php echo zen_href_link('map'); ?>"><?php echo HEADER_TITLE_MAP; ?></a></li>
    <?php if (DEFINE_SHIPPINGINFO_STATUS <= 1) { ?>
    <li><a href="<?php echo zen_href_link(FILENAME_SHIPPING); ?>"><?php echo HEADER_TITLE_SHIPPING_INFO; ?></a></li>
  <?php } ?>
  <li><a href="<?php echo zen_href_link(FILENAME_ABOUT_US); ?>"><?php echo HEADER_TITLE_ABOUT_US; ?></a></li>
  <?php if (DEFINE_PRIVACY_STATUS <= 1)  { ?>
      <li><a href="<?php echo zen_href_link(FILENAME_PRIVACY); ?>"><?php echo HEADER_TITLE_PRIVACY_POLICY; ?></a></li>
  <?php } ?>
  <?php if (DEFINE_CONDITIONS_STATUS <= 1) { ?>
      <li><a href="<?php echo zen_href_link(FILENAME_CONDITIONS); ?>"><?php echo HEADER_TITLE_CONDITIONS_OF_USE; ?></a></li>
  <?php } ?>
  <li><a href="<?php echo zen_href_link(FILENAME_UNSUBSCRIBE, '', 'NONSSL'); ?>"><?php echo HEADER_TITLE_UNSUBSCRIBE; ?></a></li>
  <li><a href="<?php echo zen_href_link('links_page'); ?>">Links</a></li>
  <li><a href="<?php echo zen_href_link(FILENAME_COOKIE_USAGE,'a');?>"><?php echo 'Cookie Usage';?> </a></li>
  <li><a id="cookie" href="<?php echo zen_href_link(FILENAME_COOKIE_USAGE,'a');?>"><?php echo 'Cookie Policy';?> </a></li>
  </ul>
</li>

<li><a href="#">Services</a>
  <ul>
    <li><a href="<?php echo zen_href_link('services_page#design');?>">Lighting Design</a></li>
    <li><a href="<?php echo zen_href_link('services_page#delivery');?>">Delivery Service</a></li>
    <li><a href="<?php echo zen_href_link('services_page#design');?>">Installation Service</a></li>
  </ul>
</li>

<li><a href="<?php echo zen_href_link(FILENAME_CONTACT_US, '', 'NONSSL'); ?>"><?php echo HEADER_TITLE_CONTACT_US; ?></a></li>
<li><a href="<?php echo zen_href_link('events', '', 'NONSSL'); ?>"><?php echo 'Events'; ?></a>
<?php
  $res = $db->Execute("SELECT * FROM events WHERE event_status = 1 ORDER BY event_id DESC");
  echo '<ul>';
  while(!$res->EOF){
    echo '<li><a href="'. zen_href_link('events','event='.$res->fields['event_id']).'">' . $res->fields['event_name'] .'</a></li>';
    //$events_list[]=array('id'=>$res->fields['event_id'],'name'=>$res->fields['event_name']);
    $res->MoveNext();
  }
  echo '</ul>';
?>
</li>
</ul>
</div>
