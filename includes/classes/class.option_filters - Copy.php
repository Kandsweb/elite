<?php
  if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

class OptionFilter {
  var $options_available_array = NULL;//array of all available options
  var $last_path = NULL;

  function init(){
    if($this->$options_available_array === NULL){
      $this->fill_options_array();
      //die('Option filter class INIT - No array');
    }
    //echo var_dump($this->$options_available_array);
    //die('Option filter class INIT - EOF');
  }

  //Returns a html string with the checkboxes for the given option. $for = 'Style', 'Finish', 'Colour' or 'Material'
  function get_options($for_option){
    $output = '';
    foreach($this->$options_available_array[$for_option] as $key => $value){
      $output .= '<input type="checkbox" name="option_' . $for_option . '" id="' . $for_option .'" " value="' . $value . '" >' . $key . '<br />';
    }
    return $output;
  }

  //fills the options array with all possiable values from db table. This is only called on first run and stored in session
  function fill_options_array(){
    global $db;
    $sql = "SELECT * FROM " . TABLE_FILTER_OPTIONS_NAMES . " ORDER BY foptions_sort";
    $res = $db->Execute($sql);
    while(!$res->EOF){
      $res1 = $db->Execute("SELECT * FROM " . TABLE_FILTER_OPTIONS_VALUES . " WHERE foptions_group=" .$res->fields['foptions_id'] . " ORDER BY foptions_name");
      while(!$res1->EOF){
        $this->$options_available_array[$res->fields['foptions_name']][$res1->fields['foptions_name']] = $res1->fields['foptions_value'];
        $res1->MoveNext();
      }
      $res->MoveNext();
    }
  }

  function fill_available_options($cPath){
    if($cPath == $this->last_path)return;
    //$this->last_path = $cpath;
    $path_array = explode('_',$cPath);
    $path = $path_array[sizeof($path_array)];
    $path_array = $_SESSION['category_tree'];
    $sql = 'SELECT p.products_id, p.products_model FROM ' . TABLE_PRODUCTS . ' p ';
  }


}
?>
