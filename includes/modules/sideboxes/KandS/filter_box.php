<?php


  require($template->get_template_dir('tpl_filter_box.php',DIR_WS_TEMPLATE, $current_page_base,'sideboxes'). '/tpl_filter_box.php');

  if($display_box){

    $title = "xxxx";
    $left_corner = false;
    $right_corner = false;
    $right_arrow = false;
    $title_link = false;

    require($template->get_template_dir($column_box_default, DIR_WS_TEMPLATE, $current_page_base,'common') . '/' . $column_box_default);

  }
?>
