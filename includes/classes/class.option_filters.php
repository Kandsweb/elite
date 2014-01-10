<?php
  if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

define('STYLE', 'Style');
define('COLOUR', 'Colour');
define('FINISH', 'Finish');
define('MATERIAL', 'Material');

class OptionFilter {
  var $options_filter_array = NULL;//array of all available options
  var $last_path = NULL;  //Last path used to build active options
  var $options_active = array(); //Array of options which are active for current path
  var $active_filters = array();  //Array holds the $_GET parms for 's' 'c' 'm' 'f'   The option values user selected and display is to be filtered on
                                  //If there are values in this it means user has filtered their search

  function init(){
    if($this->options_filter_array === NULL){
      $this->fill_options_array();
    }
    //echo var_dump($this->options_filter_array);
    //die('Option filter class INIT - EOF');
  }
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////
  //Returns a html string with the checkboxes for the given option. $for = 'Style', 'Finish', 'Colour' or 'Material'
  //if valid only is true it will only return options valid. If $show_single is true it will show options that have only 1 selection
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////
  function get_options($for_option, $valid_only = true, $show_single=false){
    $output = '';
    $count = 0;
    $jscode='';
    if($for_option == STYLE)  $jscode = 'onChange="switchOption(\'Style\', this);"';

    if((sizeof($this->options_active[$for_option])>1) || (sizeof($this->options_active[$for_option])==1 && $show_single)){
      foreach($this->options_active[$for_option] as  $value){
          if(isset($this->active_filters[$for_option]) && in_array($value, $this->active_filters[$for_option])){
            $checked = 'CHECKED';
          }else{
            $checked = '';
          }
          $output .= '<input type="checkbox" name="option_' . $for_option . '" id="' . $for_option .'" " value="' . $value . '" '. $checked . $jscode .' >' . array_search($value,$this->options_filter_array[$for_option]) . '<br />';
      }
    }

    return $output;
  }
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////
  //fills the options array with all possiable values from db table. This is only called on first run and stored in session
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////
  function fill_options_array(){
  global $db;
  $sql = "SELECT * FROM " . TABLE_FILTER_OPTIONS_NAMES . " ORDER BY foptions_sort";
  $res = $db->Execute($sql);
  while(!$res->EOF){
    $res1 = $db->Execute("SELECT * FROM " . TABLE_FILTER_OPTIONS_VALUES . " WHERE foptions_group=" .$res->fields['foptions_id'] . " ORDER BY foptions_name");
    while(!$res1->EOF){
      $this->options_filter_array[$res->fields['foptions_name']][$res1->fields['foptions_name']] = $res1->fields['foptions_value'];
      $res1->MoveNext();
    }
    $res->MoveNext();
  }
}

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////
  //// Fills class var option_active with all options that are valid for the given path
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  function fill_available_options($cPath){
    global $db;
    //if($cPath == $this->last_path && $this->options_active != NULL)return;
    //Store path
    $p_array = array();
    $this->last_path = $cPath;
    unset($this->options_active);
    $path_array = explode('_',$cPath);
    $path = $path_array[sizeof($path_array)-1];
    if(sizeof($path_array) == 2){
      //If only 2 levels deep the get sub cats for each of this cat's sub cat's
      if($_SESSION['category_tree']->category_tree[$path]['sub_cats']!=NULL){
        $path_array = $_SESSION['category_tree']->category_tree[$path]['sub_cats'];
        foreach($path_array as $value){
            if(is_array($_SESSION['category_tree']->category_tree[$value]['sub_cats'])) $p_array = array_merge($p_array, $_SESSION['category_tree']->category_tree[$value]['sub_cats']);
        }
      }
    }elseif(sizeof($path_array)==3){
      $p_array = $_SESSION['category_tree']->category_tree[$path]['sub_cats'];
    }
    if($p_array == NULL){
      $in_cats = $path;
    }else{
      $in_cats = implode(',',$p_array);
    }
    foreach($this->options_filter_array as $key => $value){
       $sql ="SELECT DISTINCT
          product_extra_fields.product_". strtolower($key) . ",
          products_to_categories.products_id,
          products_to_categories.categories_id,
          products.products_model
        FROM
          products_to_categories
          INNER JOIN product_extra_fields
            ON products_to_categories.products_id = product_extra_fields.products_id
          INNER JOIN products
            ON products_to_categories.products_id = products.products_id
        WHERE
          products_to_categories.categories_id IN($in_cats) AND
          products.products_status = 1
        GROUP BY
          product_extra_fields.product_". strtolower($key) ;
//echo $sql;
      $res = $db->Execute($sql);
      while(!$res->EOF){
        $o_value = $res->fields['product_'. strtolower($key)];
        if($o_value > 0){
          if(array_search($o_value, $this->options_filter_array[$key])!='')
            $this->options_active[$key][]=$o_value;
        }
        $res->MoveNext();
      }
    }
  }

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////Gets the $_GET parms for s,c,m,f and stores into class var $active_filters
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////
  function get_filter_prams(){
    unset($this->active_filters);
    if(isset($_GET['s'])){
      $this->active_filters['Style'] = explode(',', $_GET['s']);
    }
    if(isset($_GET['f'])){
      $this->active_filters['Finish'] = explode(',',$_GET['f']);
    }
    if(isset($_GET['c'])){
      $this->active_filters['Colour'] = explode(',',$_GET['c']);
    }
    if(isset($_GET['m'])){
      $this->active_filters['Material'] = explode(',',$_GET['m']);
    }
    //echo var_dump($this->options_active);
  }


  ///////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////Returns the number of filters active, 0=None
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////
  function is_filter_on(){
    return sizeof($this->active_filters);
  }

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////Returns the number of options active for the given filter (Style, Colour, Finish, Materials), 0=None
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////
  function count_options_on($filter){
    return sizeof($this->active_filters[$filter]);
  }

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////
  //returns array with all values active for the given $filter or if 'All' is used then all filters
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////
  function get_filter_options_on($filter='All'){
    if($filter=='All')return $this->active_filters;
    return $this->active_filters[$filter];
  }

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////
  //clears all filteres
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////
  function clear_filters(){
    unset($this->active_filters);
  }


  ///////////////////////////////////////////////////////////////////////////////////////////////////////////
  //returns string to show what filteres are in place for a listing
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////
  function build_string(){
    global $template, $current_page;
    $output = '';
    $max_af = sizeof($this->active_filters);
    $c_count = 0;
    if($max_af > 0){
      $output = '<div id="infoFiltered1"><b>Filtered Results</b> You have filtered the results by</div><div id="infoFiltered2">';
      foreach($this->active_filters as $filter => $value){
        switch($filter){
          case STYLE:
          $c_count++;
          $output .=   array_search($value[0], $this->options_filter_array[$filter]) ;
          break;
          case COLOUR:
          case FINISH:
          case MATERIAL:
            $c_count++;
            if(sizeof($this->active_filters[$filter])<2){
              $output .=   array_search($value[0], $this->options_filter_array[$filter]) ;
            }else{
              if($filter == FINISH){
                $output .=  sizeof($this->active_filters[$filter]) . ' ' . $filter . 'es';
              }else{
                $output .=  sizeof($this->active_filters[$filter]) . ' ' . $filter . 's';
              }
            }
            $colour_warning='';
            //echo $filter;
            //if($filter == 'COLOUR'){
            //  $colour_warning = '<br /><b>The images showen may not match your colour selection, however all items displayed are available in the colours you have picked</b>';
            //}
          break;
        }
        $output .= ' '.zen_image($template->get_template_dir('Delete.png',DIR_WS_TEMPLATE, $current_page_base,'images'). '/Delete.png','Clear filter','','','class="qrf" onClick="removeQfilter(\''.$filter.'\');"');
        $output .= zen_draw_separator('pixel_trans.gif',20,1);
      }
      if($colour_warning !='')$output.=$colour_warning;
      $output .= '</div>';
      ?>
  <script type="text/javascript">
  <!--
  function removeQfilter(qfRemove){
    document.body.style.cursor="wait";
    switch(qfRemove){
      case "<?php echo STYLE; ?>":
        window.location ='<?php echo zen_html_entity_decode(zen_href_link($current_page, zen_get_all_get_params(array('s')))); ?>';
        break;
      case "<?php echo COLOUR; ?>":
        window.location ='<?php echo zen_html_entity_decode(zen_href_link($current_page, zen_get_all_get_params(array('c')))); ?>';
        break;
      case "<?php echo FINISH; ?>":
        window.location ='<?php echo zen_html_entity_decode(zen_href_link($current_page, zen_get_all_get_params(array('f')))); ?>';
        break;
      case "<?php echo MATERIAL; ?>":
        window.location ='<?php echo zen_html_entity_decode(zen_href_link($current_page, zen_get_all_get_params(array('m')))); ?>';
        break;
    }
  }
  -->
</script>
<?php
      return $output . ' ';
    }
  }
}
?>
