<?php
  $display_box = false;
  //Get department and it's subs'
  $paths =$_SESSION['category_tree']->exceptional_list;
  $pos = sizeof($paths)-1;
  $department_subs= NULL;
  while($department_subs==NULL && $pos > 0){
     $department = $paths[$pos];
     $department_subs = $_SESSION['category_tree']->category_tree[$department]['sub_cats'];
     $pos--;
  }
  $previous_department = $paths[$pos];
  if(sizeof($department_subs) >1 ){
    $display_box = true;

    $content = '<div id="filterBox" class="filterBox">';
    $content .='<div id="filterBoxTop" class="filterBoxTop">';
    $content .= "You are viewing all lighting in<br />";
    $content .='<ul>';

    //Top level
    $content .=' <li><a href="' . zen_href_link('index',  'cPath='.$_SESSION['category_tree']->category_tree[$paths[1]]['cPath']) . '"' . $sub_rel . '>' . $_SESSION['category_tree']->category_tree[$paths[1]]['name'];
    $content .= '</a></li>';
    if(sizeof($paths)==3){
      $content .= '<li>' . $_SESSION['category_tree']->category_tree[$paths[2]]['name'] . '</li>';
    }elseif(sizeof($paths)==4){
      $content .=' <li><a href="' . zen_href_link('index',  'cPath='.$_SESSION['category_tree']->category_tree[$paths[2]]['cPath']) . '"' . $sub_rel . '>' . $_SESSION['category_tree']->category_tree[$paths[2]]['name'];
      $content .= '</a></li>';
      $content .= '<li>' . $_SESSION['category_tree']->category_tree[$paths[3]]['name'] . '</li>';
    }
    $content .= '</ul>';

    $content .= '</div>';//eof filterBoxTop

    //Start of Sub Levels
    $output .='<div class="filterBoxSub" id="filterBoxSubs">';
    $output .= 'Narrow your search to view only from...';
    $output .='<ul>';

    for($i=0; $i<sizeof($department_subs); $i++){
      if($department_subs[$i] != $paths[sizeof($paths)-1]){
        $sub_rel = '';
        $output .='<li><a href="' . zen_href_link('index',  'cPath='.$_SESSION['category_tree']->category_tree[$department_subs[$i]]['cPath']) . '"' . $sub_rel . '>';
        $output .= zen_get_categories_name($department_subs[$i]);
        $output .= '</a></li>';
      }
    }

    $output .= '</ul>';
    $output .= '</div></div>';

    $content .= $output;

    $content .= '</div>';
  }
?>
