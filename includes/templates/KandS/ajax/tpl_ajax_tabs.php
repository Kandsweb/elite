<?php
  //global $department_subs;

  $output = '<ul>';
  $output .='<li id="cat' . ($department == 1 ?1:2) . '"><a href="javascript:tabsSendValue(1)" >' . zen_get_categories_name(1) . '</a></li>';
  $output .='<li id="cat' . ($department == 1 ?2:1) . '"><a href="javascript:tabsSendValue(2)">' . zen_get_categories_name(2) . '</a></li>';
  $output .='</ul>';
  $output .='<div class="tabsSub" id="tabSubs">';
  $output .='<ul>';

  $department_subs = $_SESSION['category_tree']->category_tree[$department]['sub_cats'];
  for($i=0; $i<sizeof($department_subs); $i++){
    $sub_rel = '';
    if('has_sub' == $_SESSION['category_tree']->category_tree[$department_subs[$i]]['sub']){
      $sub_rel = 'rel=drop_sub_' . ($i+1);
    }
    $output .=' <li><a href="' . zen_href_link('index',  'cPath='.$_SESSION['category_tree']->category_tree[$department_subs[$i]]['cPath']) . '"' . $sub_rel . '>';
    $output .= zen_get_categories_name($department_subs[$i]);
    $output .= '</a></li>';
  }

  $output .= '</ul>';
  $output .= '</div></div>';

  for($i=0; $i<sizeof($department_subs); $i++){
    if('has_sub' == $_SESSION['category_tree']->category_tree[$department_subs[$i]]['sub']){
      $output_subs .=  '<div id="drop_sub_' . ($i+1) .'" class="dropSub1">';
      $department_subs_subs = $_SESSION['category_tree']->category_tree[$department_subs[$i]]['sub_cats'];
      for($ii=0;$ii<sizeof($department_subs_subs); $ii++){
        $output_subs .= '<a href="' . zen_href_link('index',  'cPath='.$_SESSION['category_tree']->category_tree[$department_subs_subs[$ii]]['cPath']) . ' ">'.  $_SESSION['category_tree']->category_tree[$department_subs_subs[$ii]]['name'] . '</a>';
       }
     $output_subs .= ' </div>';
    }
  }

?>
