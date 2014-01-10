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
?>

<?php
  $noscr = "";
  $categories = array();
  $expanded = array();

  $content = '<div id="' . str_replace('_', '-', $box_id . 'Content') . '" class="sideBoxContent">' . "\n";
  $content .= '<link rel="stylesheet" type="text/css" href="' . $template->get_template_dir('tree.css', DIR_WS_TEMPLATE, $current_page_base,'css') . '/tree.css">' . "\n"; 
  $content .= '<script type="text/javascript" src="' . $template->get_template_dir('YAHOO.js', DIR_WS_TEMPLATE, $current_page_base,'jscript'). '/YAHOO.js"></script>' . "\n"
           .  '<script type="text/javascript" src="' . $template->get_template_dir('treeview.js', DIR_WS_TEMPLATE, $current_page_base,'jscript'). '/treeview.js"></script>' . "\n"
	   .  '<script type="text/javascript" src="' . $template->get_template_dir('loadNodes.js', DIR_WS_TEMPLATE, $current_page_base,'jscript'). '/loadNodes.js"></script>' . "\n";
  $content .= '<script type="text/javascript">' . "\n"
    . 'var tree;' . "\n"
    . 'var nodes = new Array();' . "\n"
    . 'var nodeIndex =0;' . "\n"
    . 'var root;' . "\n"
    . 'var tmpNode;' . "\n"
    . "\n"
    . 'function treeInit() {' . "\n"
    . '  tree = new YAHOO.widget.TreeView("yahootreemenuContent");' . "\n"
    . '  root = tree.getRoot();' . "\n"
    . '  loadTree();' . "\n"
    . '  tree.draw();' . "\n"
    . '}'
    . "\n\n"
    . 'function loadTree() {' . "\n";
    $content .= loadTopLevel();
    $content .= '}'
    . "\n\n"
    . 'function  loadDataForNode(node, onCompleteCallback) {' . "\n"
    . '  var request = "load_branch.php?cPath=" + node.data.id;' . "\n"
    . '  makeRequest(request, node, onCompleteCallback);' . "\n"
    . '}'
    . "\n\n"
    . "treeInit();\n";
  

  $content .= $noscr;

  if (SHOW_CATEGORIES_BOX_SPECIALS == 'true' or
      SHOW_CATEGORIES_BOX_PRODUCTS_NEW == 'true' or
      SHOW_CATEGORIES_BOX_FEATURED_PRODUCTS == 'true' or
      SHOW_CATEGORIES_BOX_PRODUCTS_ALL == 'true') {
    if (SHOW_CATEGORIES_SEPARATOR_LINK == '1') {
      $content .= '<hr id="catBoxDivider" />' . "\n";
    }
    if (SHOW_CATEGORIES_BOX_SPECIALS == 'true') {
      $show_this = $db->Execute("select s.products_id
                                 from " . TABLE_SPECIALS . " s
                                 where s.status= '1' limit 1");
      if ($show_this->RecordCount() > 0) {
        $content .= '<a class="category-links" href="' . zen_href_link(FILENAME_SPECIALS) . '">' . CATEGORIES_BOX_HEADING_SPECIALS . '</a><br />' . "\n";
      }
    }
    if (SHOW_CATEGORIES_BOX_PRODUCTS_NEW == 'true') {
      $display_limit = zen_get_new_date_range();

      $show_this = $db->Execute("select p.products_id from " . TABLE_PRODUCTS . " p where p.products_status = 1 " . $display_limit . " limit 1");
      if ($show_this->RecordCount() > 0) {
        $content .= '<a class="category-links" href="' . zen_href_link(FILENAME_PRODUCTS_NEW) . '">' . CATEGORIES_BOX_HEADING_WHATS_NEW . '</a><br />' . "\n";
      }
    }
    if (SHOW_CATEGORIES_BOX_FEATURED_PRODUCTS == 'true') {
      $show_this = $db->Execute("select products_id from " . TABLE_FEATURED . " where status= '1' limit 1");
      if ($show_this->RecordCount() > 0) {
        $content .= '<a class="category-links" href="' . zen_href_link(FILENAME_FEATURED_PRODUCTS) . '">' . CATEGORIES_BOX_HEADING_FEATURED_PRODUCTS . '</a>' . '<br />';
      }
    }
    if (SHOW_CATEGORIES_BOX_PRODUCTS_ALL == 'true') {
      $content .= '<a class="category-links" href="' . zen_href_link(FILENAME_PRODUCTS_ALL) . '">' . CATEGORIES_BOX_HEADING_PRODUCTS_ALL . '</a>';
    }
  }
  $content .= '</div>' . "\n";    

  ///////////////////////////////////////////////////////  

  function loadTopLevel() {
    global $db, $current_category_id, $noscr, $categories;
    global $expanded;

    $ret = "";

    // Get the categories data and put it into the tree
    $groups_cat = $db->Execute("select c.parent_id, c.categories_id, cd.categories_name
                               from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd
			       where c.categories_id = cd.categories_id
			       and cd.language_id='" . (int)$_SESSION['languages_id'] . "' 
			       and c.categories_status='1'
			       order by parent_id, sort_order, cd.categories_name
			       ");
			       
    $categories[0] = array('categories_id' => 0,
                           'parent_id' => -1,
                           'categories_name' => 'root',
                           'child_count' => 0,
			   'child_list' => array(),
                           'count' => 0,
                           'count_with_sub' => 0,
                           'path' => 0);
 			       
    while (!$groups_cat->EOF) {
      $categories[$groups_cat->fields['categories_id']] = array('categories_id' => $groups_cat->fields['categories_id'],
                                                                'parent_id' => $groups_cat->fields['parent_id'],
                                                                'categories_name' => $groups_cat->fields['categories_name'],
                                                                'child_count' => 0,
								'child_list' => array(),
                                                                'count' => 0,
                                                                'count_with_sub' => 0,
                                                                'path' => $groups_cat->fields['categories_id']);

      $groups_cat->MoveNext();
    }
    
    $types = $db->Execute("select category_id from " . TABLE_PRODUCT_TYPES_TO_CATEGORY . " where product_type_id='3'");
			  
    while (!$types->EOF) {
      if (array_key_exists($types->fields['category_id'], $categories)) {
        unset($categories[$types->fields['category_id']]);
      }
      $types->MoveNext();
    }
    
    //Count child for each category
    foreach($categories as $cat) {
      if (array_key_exists($cat['parent_id'], $categories)) {
        $categories[$cat['parent_id']]['child_count']++;
	array_push($categories[$cat['parent_id']]['child_list'], $cat['categories_id']);
      }
    }

    // Populate the product counts array
    $productsCount = $db->Execute("select count(p2c.products_id) as count, p2c.categories_id 
                                   from " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . 
				   TABLE_CATEGORIES . " c, " .
				   TABLE_PRODUCTS . " p  
				   where c.categories_id=p2c.categories_id
				   and c.categories_status='1'
				   and p.products_id=p2c.products_id
				   and p.products_status='1'
				   group by categories_id");
			
    while(!$productsCount->EOF) {
      if (array_key_exists($productsCount->fields['categories_id'], $categories)) {
        $categories[$productsCount->fields['categories_id']]['count'] = $productsCount->fields['count'];
        $categories[$productsCount->fields['categories_id']]['count_with_sub'] = $productsCount->fields['count'];
      }
      $productsCount->MoveNext();
    }
    
    foreach($categories as $cat) {
      
      $parentId = $cat['parent_id'];
      
      // walkup to root and update counters and cPath. don`t count for root 
      while($parentId > 0 and array_key_exists($parentId, $categories)) {
        $categories[$parentId]['count_with_sub'] += $cat['count'];
        $categories[$cat['categories_id']]['path'] = $parentId . '_' . $categories[$cat['categories_id']]['path'];
	$parentId = $categories[$parentId]['parent_id'];
      }
    }

    if (zen_not_null($current_category_id)) {
      $temp_id = $current_category_id;
      do {
	$expanded[] = $temp_id;
	$temp_id = $categories[$temp_id]['parent_id'];
      } while ($temp_id != 0);
    }

    $expanded[] = 0;
    $expanded =	array_reverse($expanded);

    
    $iter_num = count($expanded) < 2 ? 1 : count($expanded) - 1;
    
    if (YAHOOTREEMENU_SHOW_CATEGORY_LINK) {
      $iter_num++;
    }

    $partialPath = '';

    for ($i = 0; $i < $iter_num; $i++) {  
      
      if ($i > 0) $partialPath .= $expanded[$i] . '_';

      foreach ($categories[$expanded[$i]]['child_list'] as $cat) {
        $ret .= renderNode($categories, $cat, $expanded[$i], $categories[$cat]['child_count'], ($categories[$cat]['categories_id'] == $expanded[$i+1]) and ($i < $iter_num - 1), $cat == $expanded[$i + 1], YAHOOTREEMENU_WITHOUT_AJAX);
      }
    }

    $noscr .= "\n</script>\n";
    $noscr .= "\n<noscript>\n";

    foreach ($categories[0]['child_list'] as $cat) {
      $noscr .= '<a class="category-top" href="' . zen_href_link(FILENAME_DEFAULT, 'cPath=' . $categories[$cat]['path']) . '">';


      if ($categories[$cat]['categories_id'] == $current_category_id) {
        $noscr .= '<span class="category-subs-parent">' . $categories[$cat]['categories_name'] . '</span>';
      } else {
        $noscr .= $categories[$cat]['categories_name'];
      }
      
      if ($categories[$cat]['child_count'] > 0) {
        $noscr .= CATEGORIES_SEPARATOR;
      }
      $noscr .= "</a>";
        
      if (SHOW_COUNTS == 'true') {
        if ((CATEGORIES_COUNT_ZERO == '1' and $categories[$cat]['count_with_sub'] == 0) or $categories[$cat]['count_with_sub'] >= 1) {
          $noscr .= CATEGORIES_COUNT_PREFIX . $categories[$cat]['count_with_sub'] . CATEGORIES_COUNT_SUFFIX;
        }
      }
        
      $noscr .= "<br />";
      if (count($expanded) > 1) {
        if ($categories[$cat]['categories_id'] == $expanded[1]) {
          loadBranch($categories[$cat]['categories_id'], 1, &$ret);
        }
      }
    }

    $noscr .= "\n</noscript>\n";
    return $ret;
  }
  
  function loadBranch($parent_id, $depth = 0, &$str) {
    global $categories, $noscr, $expanded;

    foreach ($categories[$parent_id]['child_list'] as $cat) {
      
      $noscr .= '<a class="category-subs" href="' . zen_href_link(FILENAME_DEFAULT, 'cPath=' . $categories[$cat]['path']) . '">';

      if (count($expanded) > $depth + 1) {
        if ($categories[$cat]['categories_id'] == $expanded[$depth + 1]) {
          $noscr .= '<span class="category-subs-parent">';
        }
      }
      
      for ($i = 0; $i < $depth; $i++) {
        $noscr .= CATEGORIES_SUBCATEGORIES_INDENT;
      }
      
      $noscr .= CATEGORIES_SEPARATOR_SUBS . $categories[$cat]['categories_name'];

      if ($categories[$cat]['child_count'] > 0) {
        $noscr .= CATEGORIES_SEPARATOR;
      }
      
      $noscr .= '</a>';

      if (count($expanded) > $depth + 1) {
        if ($categories[$cat]['categories_id'] == $expanded[$depth + 1]) {
          $noscr .= '</span>';
        }
      }

      if (SHOW_COUNTS == 'true') {
        if ((CATEGORIES_COUNT_ZERO == '1' and $categories[$cat]['count_with_sub'] == 0) or $categories[$cat]['count_with_sub'] >= 1) {
          $noscr .= CATEGORIES_COUNT_PREFIX . $categories[$cat]['count_with_sub'] . CATEGORIES_COUNT_SUFFIX;
        }
      }

      $noscr .= '<br />';

      if (count($expanded) > $depth + 1) {
        if ($categories[$cat]['categories_id'] == $expanded[$depth + 1]) {
          loadBranch($categories[$cat]['categories_id'], $depth + 1, &$str);
        }
      }
    }
  }
  
  function renderNode($categories, $cat_id, $parent_id, $has_child, $expand, $current, $walk_in_depth) {
    $ret = '';
    $catProductsCount = $categories[$cat_id]['count_with_sub'];
    if ($catProductsCount > 0 or !YAHOOTREEMENU_SKIP_EMPTY) {
      if (SHOW_COUNTS == 'true') {
        if ((CATEGORIES_COUNT_ZER0 == '1' && $catProductsCount == 0) or $catProductsCount >= 1) {
          $catProductsCountForTitle = "&nbsp;" . CATEGORIES_COUNT_PREFIX . $catProductsCount . CATEGORIES_COUNT_SUFFIX;
        } else {
          $catProductsCountForTitle = '';
        }          	
      }
      
      if (($categories[$cat_id]['count'] > 0) || YAHOOTREEMENU_SHOW_CATEGORY_LINK) {
        $link = '", href:"' . zen_href_link(FILENAME_DEFAULT, 'cPath=' . $categories[$cat_id]['path']) . '"';
      } else {
        $link = '"';
      }
          
      if ($parent_id == 0) {
        $ret .=  '  nodes['. $categories[$cat_id]['categories_id'] .'] = new YAHOO.widget.MenuNode({label: "' . htmlentities($categories[$cat_id]['categories_name']) . $catProductsCountForTitle . $link . '}, root, false);' . "\n"
             .   '  nodes['. $categories[$cat_id]['categories_id'] .'].data = new nodeData('. $categories[$cat_id]['categories_id'] .', ' . $categories[$cat_id]['categories_id'] . ');' . "\n";
      } else {
        $ret .=  '  nodes['. $categories[$cat_id]['categories_id'] .'] = new YAHOO.widget.MenuNode({label: "' . htmlentities($categories[$cat_id]['categories_name']) . $catProductsCountForTitle . $link . '}, nodes[' . (int)$parent_id . '], false);' . "\n"
             .   '  nodes['. $categories[$cat_id]['categories_id'] .'].data = new nodeData('. $categories[$cat_id]['categories_id'] .', "' . $partialPath . $categories[$cat_id]['categories_id'] . '");' . "\n";
      }

      if (YAHOOTREEMENU_MULTI_EXPAND) {
        $ret .= '  nodes['. $categories[$cat_id]['categories_id'] .'].multiExpand = true;' . "\n";
      }
      
      if ($has_child) {
        if ($expand) {
	  $ret .= '  nodes['. $categories[$cat_id]['categories_id'] .'].expand();' . "\n"; 
	} else {
	  if (!$walk_in_depth) {
            $ret .= '  nodes['. $categories[$cat_id]['categories_id'] .'].setDynamicLoad(loadDataForNode);' . "\n";
	  }
	}
      }
      
      if ($current) {
        $ret .= '  nodes['. $categories[$cat_id]['categories_id'] .'].labelStyle = "current";' . "\n";
      }
      
      if ($walk_in_depth) {
        foreach ($categories[$cat_id]['child_list'] as $cat) {
	  $ret .= renderNode($categories, $cat, $cat_id, $categories[$cat]['child_count'], false, false, true);
	}
      }
    }
    
    return $ret;
  }
?>
