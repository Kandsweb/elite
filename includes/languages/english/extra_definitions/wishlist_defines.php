<?php
// control multiple wishlist functionality
if(UN_DB_MODULE_WISHLISTS_ENABLED == 'true'){
	define('UN_MODULE_WISHLISTS_ENABLED', true);
} else {
	define('UN_MODULE_WISHLISTS_ENABLED', false);}
if(UN_DB_ALLOW_MULTIPLE_WISHLISTS == 'true'){
	define('UN_ALLOW_MULTIPLE_WISHLISTS', true);
} else {
	define('UN_ALLOW_MULTIPLE_WISHLISTS', false);}
if(UN_DB_DISPLAY_CATEGORY_FILTER == 'true'){
	define('UN_DISPLAY_CATEGORY_FILTER', true);
} else {
	define('UN_DISPLAY_CATEGORY_FILTER', false);}
if(UN_DB_ALLOW_MULTIPLE_PRODUCTS_CART_COMPACT == 'true'){
	define('UN_ALLOW_MULTIPLE_PRODUCTS_CART_COMPACT', true);
} else {
	define('UN_ALLOW_MULTIPLE_PRODUCTS_CART_COMPACT', false);}

// template header
define('UN_HEADER_TITLE_WISHLIST', 'Project Lists');

// wishlist sidebox
define('UN_BOX_HEADING_WISHLIST', 'Project List');
define('UN_BUTTON_IMAGE_WISHLIST_ADD', 'wishlist_add.gif');
define('UN_BUTTON_WISHLIST_ADD_ALT', 'Add to Project List');
define('UN_BOX_WISHLIST_ADD_TEXT', 'Click to add this product to your Project List.');
define('UN_BOX_WISHLIST_LOGIN_TEXT', '<p><a href="' . zen_href_link(FILENAME_LOGIN, '', 'NONSSL') . '">Log In</a> to be able to add this product to your Project List.</p>');

// control form
define('UN_TEXT_SORT', 'Sort');
define('UN_TEXT_SHOW', 'Show');
define('UN_TEXT_VIEW', 'View');
define('UN_TEXT_ALL_CATEGORIES', 'All Categories');

// more
define('UN_TEXT_ADD_WISHLIST', 'Add to Project List');
define('UN_TEXT_REMOVE_WISHLIST', 'Remove from Project List');
define('UN_BUTTON_IMAGE_SAVE', BUTTON_IMAGE_UPDATE);
define('UN_BUTTON_SAVE_ALT', BUTTON_UPDATE_ALT);
define('UN_TEXT_EMAIL_WISHLIST', 'Tell a Friend');
define('UN_TEXT_FIND_WISHLIST', 'Find a Friend\'s Project List');
define('UN_TEXT_NEW_WISHLIST', 'Create a new Project List');
define('UN_TEXT_MANAGE_WISHLISTS', 'Manage my Project Lists');
define('UN_TEXT_WISHLIST_MOVE', 'Move items between Project Lists');
define('SUCCESS_ADDED_TO_WISHLIST_PRODUCT', 'Successfully added Product to the Project List ...');

define('UN_TEXT_PRIORITY', 'Priority');
define('UN_TEXT_DATE_ADDED', 'Date Added');
define('UN_TEXT_QUANTITY', 'Quantity');
define('UN_TEXT_COMMENT', 'Comment');

define('UN_TEXT_PRIORITY_0', '0 - Don\'t buy this for me');
define('UN_TEXT_PRIORITY_1', '1 - I\'m thinking about it');
define('UN_TEXT_PRIORITY_2', '2 - Like to have');
define('UN_TEXT_PRIORITY_3', '3 - Love to have');
define('UN_TEXT_PRIORITY_4', '4 - Must have');

// product lists
define('UN_TEXT_NO_PRODUCTS', 'No products currently in list.');
define('UN_TEXT_COMPACT', 'Compact');
define('UN_TEXT_EXTENDED', 'Extended');

// general
define('UN_LABEL_DELIMITER', ': ');
define('UN_TEXT_REMOVE', 'Remove');
define('UN_EMAIL_SEPARATOR', "-------------------------------------------------------------------------------\n");
define('UN_TEXT_DATE_AVAILABLE', 'Date Available: %s');
define('UN_TEXT_FORM_FIELD_REQUIRED', '*');
define('TEXT_OPTION_DIVIDER', '&nbsp;-&nbsp;');

// tables
define('UN_TABLE_HEADING_PRODUCTS', 'Name');
define('UN_TABLE_HEADING_PRICE', 'Price');
define('UN_TABLE_HEADING_BUY_NOW', 'Cart');
define('UN_TABLE_HEADING_QUANTITY', 'Qty');
define('UN_TABLE_HEADING_WISHLIST', 'Wishlist');
define('UN_TABLE_HEADING_SELECT', 'Select');

//errors
define('UN_ERROR_GET_ID', 'Error getting default project list id.');
define('UN_ERROR_GET_CUSTDATA', 'Error getting customer data.');
define('UN_ERROR_GET_PERMISSION', 'You do not have permission.');
define('UN_ERROR_GET_WISHLIST', 'Error getting project list.');
define('UN_ERROR_GET_WISHLIST_ID', 'Error getting project list: id not set.');
define('UN_ERROR_FIND_WISHLIST', 'Error finding project lists.');
define('UN_ERROR_IS_PRIVATE', 'Error determining if project list is private.');
define('UN_ERROR_MAKE_DEFAULT', 'Error setting default.');
define('UN_ERROR_MAKE_DEFAULT_ZERO', 'Error zeroing default.');
define('UN_ERROR_MAKE_PUBLIC', 'Error making project list public.');
define('UN_ERROR_MAKE_PRIVATE', 'Error making project list private.');
define('UN_ERROR_CREATE_DEFAULT', 'Error creating default project list.');
define('UN_ERROR_IN_WISHLIST', 'Error determining if product in project list.');
define('UN_ERROR_CREATE_WISHLIST', 'Error creating project list.');
define('UN_ERROR_ADD_WISHLIST', 'Error adding project list item.');
define('UN_ERROR_EDIT_WISHLIST', 'Error editing wishlist item.');
define('UN_ERROR_ADD_PRODUCT_WISHLIST', 'Error adding product to project list.');
define('UN_ERROR_DELETE_DEFAULT_WISHLIST', 'Error deleting default project list.');
define('UN_ERROR_DELETE_WISHLIST', 'Error deleting project list.');
define('UN_ERROR_DELETE_PRODUCT_WISHLIST', 'Error deleting product from projectlist.');