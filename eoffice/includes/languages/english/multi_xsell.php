<?php
/**
 * Cross Sell products
 *
 * Derived from:
 * Original Idea From Isaac Mualem im@imwebdesigning.com <mailto:im@imwebdesigning.com>
 * Portions Copyright (c) 2002 osCommerce
 * Complete Recoding From Stephen Walker admin@snjcomputers.com
 * license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 *
 * Adapted to Zen Cart by Merlin - Spring 2005
 * Reworked for Zen Cart v1.3.0  03-30-2006
 *
 * Reworked again to change/add more features by yellow1912
 * Pay me a visit at RubikIntegration.com
 *
 * Add Multi Cross Sells by Gilby 2010-06-26
 */
define('HEADING_TITLE', 'Multi Cross Sell Admin v1.00');
define('TEXT_PRODUCT_ID', 'Product '.MXSELL_FORM_INPUT_TYPE);

define('CROSS_SELL_SORT_ORDER_UPDATED', 'Updated sort order of the following products: %s');
define('CROSS_SELL_SORT_ORDER_NOT_UPDATED', 'No sort order updated!');
define('CROSS_SELL_NO_INPUT_FOUND', 'Please input at least %d products ids/models to cross-sell');
define('CROSS_SELL_NO_MAIN_FOUND', 'Please input main product\'s '.MXSELL_FORM_INPUT_TYPE);
define('CROSS_SELL_ALREADY_ADDED', 'Product %s has already been added to Product %s');
define('CROSS_SELL_ADDED', 'Product %s has been added as a Cross-Sell to Product %s');
define('CROSS_SELL_PRODUCT_DELETED', '%s Cross-Sell(s) Successfully Removed.');
define('CROSS_SELL_PRODUCT_NOT_DELETED', 'No Cross-Sell Removed.');
define('CROSS_SELL_PRODUCT_NOT_FOUND', 'No Product was Found with the '.MXSELL_FORM_INPUT_TYPE.': %s');
define('CROSS_SELL_PRODUCT_NOT_FOUND_IN_XSELL', 'No Product was Found with the '.MXSELL_FORM_INPUT_TYPE.': %s in this Cross-Sell table');
define('CROSS_SELL_CLEANED_UP', '%s cross-sell(s) cleaned up');
define('CROSS_SELL_PRODUCT_DUPLICATE','%s and %s have the same product id');

define('MXSELL_REMOVE_CONFIRM','Warning! This will remove all Multi Cross Sell data!! Continue? <a href="%s">Yes</a>&nbsp;&nbsp;<a href="%s">No</a>');

?>