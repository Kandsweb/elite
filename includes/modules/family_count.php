<?php
  $base_code = substr($products_model,0,8);
  $family_array = array();
  $rs = $db->Execute("SELECT products_model FROM " . TABLE_PRODUCTS . " WHERE products_model LIKE '$base_code%' AND products_status = 1 ORDER BY products_model" );
  while(!$rs->EOF){
    $family_array[] = $rs->fields['products_model'];
    $rs->MoveNext();
  }
  $max_family_count = sizeof($family_array);
  $current_family_pos = array_search($products_model, $family_array);
  $current_family_pos++;
?>
