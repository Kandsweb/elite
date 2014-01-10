<?php
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

class ProductData {
  //prams available - name                 = display name
  //                - prefix               = display before name
  //                - postfix              = display after the name
  //                - type      - logic    = when the field is a simple yes/no data
  var  $specs_map= array(
    'dimensions_height' => array('name' =>'Height', 'postfix' => 'mm'),
    'dimensions_width' => array('name' =>'Width',  'postfix' => 'mm'),
    'product_length' => array('name' =>'Length',  'postfix' => 'mm'),
    'dimensions_depth' => array('name' =>'Projection',  'postfix' => 'mm'),
    'product_cut_out' => array('name' =>'Cut Out',  'postfix' => 'mm'),
    'product_dia' => array('name' =>'Diameter',  'postfix' => 'mm'),
    'product_min_drop' => array('name' =>'Minimun Drop',  'postfix' => 'mm'),
    'product_max_drop' => array('name' =>'Maximun Drop',  'postfix' => 'mm'),
    'product_recess' => array('name' =>'Recess Depth',  'postfix' => 'mm'),
    'bulbs_included' => array('name' =>'Bulbs / Lamps Included',  'postfix' => '', 'type' => 'logic'),
    'ip_rating' => array('name' =>'IP Rating',  'prefix' => 'IP'),
    'product_voltage' => array('name' =>'Voltage',  'postfix' => 'volts'),
    'product_shade_inc' => array('name' =>'Shade(s) Included',  'postfix' => '', 'type' => 'logic'),
    'product_guarantee' => array('name' =>'Guarantee',  'postfix' => 'Yrs'),
    'product_transformer' => array('name' =>'Transformer Included',  'postfix' => '', 'type' => 'logic'),
    'product_driver' => array('name' =>'Driver Included',  'postfix' => '', 'type' => 'logic' ),
    'product_surface_temp' => array('name' =>'Surface Tempature',  'postfix' => '&deg c'),
    'product_cable' => array('name' =>'Cable Length',  'postfix' => 'm'),
    'product_safety_class' => array('name' =>'Safety Class',  'postfix' => ''),
    'product_application' => array('name' =>'Application',  'postfix' => ''),
    'product_weight_limit' => array('name' =>'Weight Limit',  'postfix' => 'kg'),
    'product_tilt' => array('name' =>'Tilt',  'postfix' => '&deg'),
    'product_weight' => array('name' =>'Weight',  'postfix' => 'kg')
    );

    var $reorder_map = array(
      'Length',
      'Width',
      'Height'
    );

    var $sort_array = array();

    var $specs_array = array();
    var $product_id;

  function get_product_specs($pID){
    global $db;
    $sql = 'SELECT * FROM ' . TABLE_PRODUCTS_EXTRA_FIELDS . ' WHERE products_id = ' . $pID;
    $result = $db->Execute($sql);

    if(!$result->EOF){
      foreach($result->fields as $key => $value){
        if($value != NULL && $value != ''){
          $this->specs_array[$key] = $value;
        }
      }
    }
    ///Add weight from products table
    $res = $db->Execute("SELECT products_weight FROM " . TABLE_PRODUCTS . " WHERE products_id =   $pID");
    if(!$res->EOF){
      if($res->fields['products_weight'] != NULL && $res->fields['products_weight'] != '' ){
        $this->specs_array['product_weight'] = $res->fields['products_weight'] ;
      }
    }

    return $this->_user_friendly_specs($this->specs_array);
  }


  function _user_friendly_specs($specs_array){
    $res = array();
    foreach($this->specs_array as $key => $value){
      if(isset($this->specs_map[$key])){
        if($this->specs_map[$key]['type'] == 'logic'){
          $res[$this->specs_map[$key]['name']] = $value==1?'Yes':'No';
        }elseif ($value != 0){
          if(isset($this->specs_map[$key]['prefix'])){
            $res[$this->specs_map[$key]['name']] .= $this->specs_map[$key]['prefix'] ;
          }
          $res[$this->specs_map[$key]['name']] .=$value ;
          if(!strpos($value, $this->specs_map[$key]['postfix'])>0){
            $res[$this->specs_map[$key]['name']] .= $this->specs_map[$key]['postfix'] ;
          }

        }
      }
    }
    return $res;
  }

  //Reorder layout of specs table for interiors items
  function reOrder($in){
    $new = array();
    foreach($this->reorder_map as $key){
      if(array_key_exists($key, $in)){
        $new[$key] = $in[$key];
        unset($in[$key]);
      }
    }
    if(sizeof($in)>0){
      $new = array_merge($new, $in);
    }
    return $new;
  }

} ////////////////////////Class End///////////////////////////////////////////////////////

function get_variant_string($pID){
  global $db, $box_title;
  $sql = 'SELECT product_variant FROM ' . TABLE_PRODUCTS_EXTRA_FIELDS . ' WHERE products_id = ' . $pID;
    $result = $db->Execute($sql);
    $box_title = 'Product Variations';
    if($_SESSION['department']==1) return $result->fields['product_variant'];
    if($_SESSION['department']==2){
      //Department is interiors, so the variant string can possaible be a list of colours
      if(strpos($result->fields['product_variant'],';')>0){
        //A ; has been found so the string is a list of colours
        $raw_array = explode(';',$result->fields['product_variant']);
        $colours = '';
        $count = 0;
        foreach($raw_array as $value){
          $res = $db->Execute("SELECT foptions_name FROM " . TABLE_FILTER_OPTIONS_VALUES . " WHERE foptions_group = 4 AND foptions_value = $value");
          $colours .= zen_image('includes/templates/KandS/images/bullet_colour.gif').' '. $res->fields['foptions_name'] . zen_draw_separator('pixel_trans.gif',20);
          $count++;
          if($count==3){
            $colours .= '<br />';
            $count = 0;
          }
        }
        $box_title = 'Colour Variations';
        return $colours;
      }
    }
}

function get_bulb_string($pID){
    global $db;
    $sql = "SELECT bulbs_qty, bulbs_type, bulbs_watts, bulbs_cap, bulbs_s1, bulbs_s2 FROM " . TABLE_PRODUCTS_EXTRA_FIELDS . " WHERE products_id  = $pID";
    $result = $db->Execute($sql);
    if(!$result->EOF){
      $string = '<br /><br />';
      /*if($result->fields['bulbs_qty'] > 0 && $result->fields['bulbs_qty'] != ''){
        $string .= $result->fields['bulbs_qty'] . ' x ';
        if($result->fields['bulbs_watts']>0 && $result->fields['bulbs_watts'] != ''){
            $string .= $result->fields['bulbs_watts'] . 'watt ' ;
        }
        if($result->fields['bulbs_type']!='0' && $result->fields['bulbs_type'] != ''){
            $string .= $result->fields['bulbs_type'];
        }
        if($result->fields['bulbs_cap'] != '' && $result->fields['bulbs_cap'] !='0'){
            $string .= ', ' .$result->fields['bulbs_cap'];
        }
      }else{ */
      if($result->fields['bulbs_s1'] != '' && $result->fields['bulbs_s1'] !='0'){
          $string .= $result->fields['bulbs_s1'];
      }
      //}
      if($result->fields['bulbs_s2']!=0&&$result->fields['bulbs_s2']!='')$string .= '<br /><i>Or </i>&nbsp;&nbsp;&nbsp;'.$result->fields['bulbs_s2'];
    }
    return $string;
  }

?>
