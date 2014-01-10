<?php
/**
 * Side Box Template
 *
 * @package templateSystem
 * @copyright Copyright 2009 KandS
 * @copyright Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.gnu.org/licenses/ GNU Public License V3.0
 * @version $Id: tpl_categories.php 001 2010-01-31 sm
 *
 * Referenced files:
 * includes/functions/extra_functions/categories_dressing_functions.php
 * includes/languages/english/extra_definitions/your_template/categories_dressing_defines.php - make user settings here
 *
 *. It's easier, more flexible now to tell the tree the "wrapper" you want to put the parent - children in, example:
 * $_SESSION['category_tree']->buildCategoryString('<ul class="{class}">{child}</ul>', '<li class="{class}"><a class="{class} category-top" href="{link}"><span>{name}</span></a><span>|</span>{child}</li>',
 *                         root_category_id, current_category_id, number_of_levels_to_expand, false, true);
 *
 * Can attach other links to the tree, example:
 * $_SESSION['category_tree']->attachToCategoryTree(array('name'=> HEADER_FILENAME_SOMETHING2, 'cPath' => zen_href_link(FILENAME_FILENAME_SOMETHING2, '', 'NONSSL', false), 'id' => 'sell', 'children' => array(
                                                    array('name'=>HEADER_FILENAME_SOMETHING1, 'id' => 'sales', 'cPath' => zen_href_link(FILENAME_FILENAME_SOMETHING1, '', 'NONSSL', false)),
                                                    array('name'=>HEADER_FILENAME_SOMETHING, 'id' => 'listings', 'cPath' => zen_href_link(FILENAME_SOMETHING, '', 'NONSSL', false))
                                                    )));
 */
  $currentLevel = 0;
  $rootLevel = 0;
  $expandTo = 2;
/*  $currentLevel = $_SESSION['category_tree']->getCurrentNavId();
  if ($currentLevel < 0 ){
    $currentLevel = 0;
  }
  $paths = explode('_', $_SESSION['category_tree']->retrievecPath($currentLevel));
  $sopaths = sizeof($paths);
  if ($paths){
    $rootLevel = $paths[0];
  }*/
  $content =$_SESSION['category_tree']->buildCategoryStringRoot($rootLevel , $currentLevel, $expandTo, true, true);
?>