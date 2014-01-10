<?php
$display_box=true;
 require($template->get_template_dir('tpl_adds1_sidebox.php',DIR_WS_TEMPLATE, $current_page_base,'sideboxes'). '/tpl_adds1_sidebox.php');
  if($display_box){
    $title = "";
    $left_corner = false;
    $right_corner = false;
    $right_arrow = false;
    $title_link = false;
    require($template->get_template_dir($column_box_default, DIR_WS_TEMPLATE, $current_page_base,'common') . '/' . $column_box_default);
  }

  $show_blank_sidebox = true;

?>
