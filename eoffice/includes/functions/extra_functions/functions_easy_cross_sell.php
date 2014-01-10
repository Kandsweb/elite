<?php
function pId_to_mId($pid){
    $pid = zen_db_prepare_input($pid);
    global $db;

    $result = $db->Execute("select p.products_model from " . TABLE_PRODUCTS . " p " .
                   "where p.products_id = '$pid' LIMIT 1");


    if ($result->RecordCount() > 0) {
      return $result->fields['products_model'];
    }
    return NULL;
 }
?>