<?php
/* ------------------------------------------------
 * YahooTreeMenuWithAJAX for Zen-Cart v0.9
 *
 * Copyright (c) 2006 Andrew Yermakov <andrew@cti.org.ua>
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR AND CONTRIBUTORS ``AS IS'' AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED.  IN NO EVENT SHALL THE AUTHOR OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS
 * OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
 * OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF
 * SUCH DAMAGE.
  ------------------------------------------------
*/

require('includes/application_top.php');

Header("Content-Type: application/xml; charset=" . CHARSET); 
Header("Content-disposition: inline; filename=branch.xml"); 

echo '<?xml version="1.0" encoding="' . CHARSET . '"?>';
?>
<?php
    $categoriesProductsCount = array();
    $categoriesProductsCountWithSub = array();
    $categories = array();


    // Getting the categories datas and putting it into the tree
    $groups_cat = $db->Execute("select c.parent_id, c.categories_id, cd.categories_name
                               from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd
			       where c.categories_id = cd.categories_id
			       and cd.language_id='" . (int)$_SESSION['languages_id'] . "'
			       and c.categories_status='1'
			       order by sort_order, cd.categories_name
			       ");
			       
    while (!$groups_cat->EOF) {
      $categories[$groups_cat->fields['categories_id']]['categories_id'] = $groups_cat->fields['categories_id'];
      $categories[$groups_cat->fields['categories_id']]['parent_id'] = $groups_cat->fields['parent_id'];
      $categories[$groups_cat->fields['categories_id']]['categories_name'] = $groups_cat->fields['categories_name'];
      $categories[$groups_cat->fields['categories_id']]['child_count'] = 0;
      $groups_cat->MoveNext();
    }
    
    foreach ($categories as $cat) {
        $categories[$cat['parent_id']]['child_count']++;
    }

    // Populating of the product counts array
    $productsCount = $db->Execute("select count(p2c.products_id) as count, p2c.categories_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c, " . TABLE_PRODUCTS . " p  where c.categories_id=p2c.categories_id and c.categories_status='1' and p.products_id=p2c.products_id and p.products_status='1' group by categories_id");

    while(!$productsCount->EOF) {
      if (zen_get_product_types_to_category($productsCount->fields['categories_id']) != '3') {
        $categoriesProductsCount[$productsCount->fields['categories_id']]['count'] = $productsCount->fields['count'];
        $categoriesProductsCount[$productsCount->fields['categories_id']]['categories_id'] = $productsCount->fields['categories_id'];
      }
      $productsCount->MoveNext();
    }

    foreach($categoriesProductsCount as $count) {
      if(array_key_exists($count['categories_id'], $categoriesProductsCountWithSub)) {
        $categoriesProductsCountWithSub[$count['categories_id']] += $count['count'];
      } else {
        $categoriesProductsCountWithSub[$count['categories_id']] = $count['count'];
      }
      
      $parentId = $categories[$count['categories_id']]['parent_id'];
      
      // walkup to root and update counters
      while($parentId > 0) {
        if(array_key_exists($parentId, $categoriesProductsCountWithSub)) {
          $categoriesProductsCountWithSub[$parentId] += $count['count'];
        } else {
          $categoriesProductsCountWithSub[$parentId] = $count['count'];
        }
	$parentId = $categories[$parentId]['parent_id'];
      }
    }
?>
<root>
<?php
    foreach($categories as $cat) {
      if ($cat['parent_id'] == $_GET['cPath'] ) {
        $productsCount = $categoriesProductsCountWithSub[$cat['categories_id']];
        if ($productsCount > 0 or !YAHOOTREEMENU_SKIP_EMPTY) {
          if (SHOW_COUNTS == 'true') {
            if ((CATEGORIES_COUNT_ZER0 == '1' && $productsCount == 0) or $productsCount >= 1) {
	      $productsCountForTitle = " (". $productsCount .")";
            } else {
              $productsCountForTitle = '';
            }
          }
	  
	  if (zen_not_null($categoriesProductsCount[$cat['categories_id']]['count'])) {
	    $cnt = $categoriesProductsCount[$cat['categories_id']]['count'];
	  } else {
	    $cnt = 0;
	  }
	  
	  echo "<category>\n";
	  echo "<name>\n";
	  echo htmlentities($cat['categories_name']) . $productsCountForTitle ."\n";
	  echo "</name>\n";
	  echo "<id>\n";
	  echo $cat['categories_id'] . "\n";
	  echo "</id>\n";
	  echo "<productscount>\n";
	  echo $cnt . "\n";
	  echo "</productscount>\n";
	  echo "<childcount>\n";
	  echo $categories[$cat['categories_id']]['child_count'] . "\n";
	  echo "</childcount>\n";
	  echo "<multiexpand>\n";
	  if (YAHOOTREEMENU_MULTI_EXPAND) {
	    echo "1\n";
	  } else {
	    echo "0\n";
	  }
	  echo "</multiexpand>\n";
	  echo "</category>\n";
        }
      }
    }
?>
</root>
