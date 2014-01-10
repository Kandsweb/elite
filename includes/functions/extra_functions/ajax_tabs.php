<?php
function process_tab_return_data(){
  global $department_subs;
    if (isset($_POST['tab'])){
      $department = $_POST['tab'];
  }else{
      $value = "";
  }

  require('includes/templates/KandS/ajax'. '/' . 'tpl_ajax_tabs.php');

  //echo json_encode("returnValue"=>"CCCCCCCCCCCCCCCC");
  //echo json_encode(array("returnValue"=> $_SESSION['category_tree']->category_tree[1]['sub_cats'] ));
  echo json_encode(array("tabs"=>$output, 'subs'=>$output_subs));

}

