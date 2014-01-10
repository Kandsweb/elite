<?php
define('EXTRA_FIELDS','
    pdex.dimensions_height,
    pdex.dimensions_width,
    pdex.dimensions_depth,
    pdex.bulbs_qty,
    pdex.bulbs_type,
    pdex.bulbs_watts,
    pdex.bulbs_cap,
    pdex.ip_rating,
    pdex.manufactures_code,
    pdex.product_colour,
    pdex.product_style,
    pdex.product_finish,
    pdex.product_material,
    pdex.bulbs_included,
    pdex.product_dia,
    pdex.product_min_drop,
    pdex.product_max_drop,
    pdex.product_length,
    pdex.product_safety_class,
    pdex.product_options,
    pdex.product_voltage,
    pdex.product_guarantee,
    pdex.product_shade_inc,
    pdex.product_transformer,
    pdex.product_driver,
    pdex.product_cut_out,
    pdex.product_recess,
    pdex.product_surface_temp,
    pdex.product_cable,
    pdex.product_carriage,
    pdex.product_statements,
    pdex.product_tilt,
    pdex.product_variant,
    pdex.family_caption,
    pdex.rrp,
    pdex.rate_1,
    pdex.rate_2,
    pdex.rate_3,
    pdex.bulbs_s1,
    pdex.bulbs_s2,
    pdex.show_price,
    pdex.web_price,
    pdex.now_price');


  /// Functions for working with the product viewed count

  //Get the viewed offset - this is the amount the product viewed count has been changed by user to alter its natural position
  function get_viewed_offset($pID){
    global $db;
    $v_res = $db->Execute("SELECT viewed_offset FROM products_viewed_adjustment WHERE products_id = $pID");
    return ($v_res->EOF?0:$v_res->fields['viewed_offset']);
  }

  //Get the actual viewed count
  function get_viewed_actual($pID){
    return get_viewed_count($pID) - get_viewed_offset($pID);
  }

  //Get the stored view count, this is the actual + viewed offset
  function get_viewed_count($pID){
    global $db;
    $v_res = $db->Execute("SELECT products_viewed FROM products_description WHERE products_id = $pID");
    return $v_res->fields['products_viewed'];
  }

  //Update the viewed count (actual + offset)
  function update_viewed_count($pID, $count){
    global $db;
    $v_res = $db->Execute("UPDATE products_description SET products_viewed = $count WHERE products_id = $pID LIMIT 1");
  }

  //Update the offset value
  function update_viewed_offset($pID, $offset){
    global $db;
    $v_res = $db->Execute("UPDATE products_viewed_adjustment SET viewed_offset = $offset WHERE products_id = $pID LIMIT 1");
  }

  //Is product item marked as a priorty view item - returns true/false
  function is_priority_item($pID){
    global $db;
    $v_res = $db->Execute("SELECT viewed_offset FROM products_viewed_adjustment WHERE products_id = $pID");
    return !$v_res->EOF;
  }


  //Other functions/////////////////////////////////////////////////////

  //This is a copy of the org zen function used on public side, it is here for me to use on the admin side
  // Return all subcategory IDs
  // TABLES: categories
  function kas_get_subcategories(&$subcategories_array, $parent_id = 0) {
    global $db;
    $subcategories_query = "select categories_id
                            from " . TABLE_CATEGORIES . "
                            where parent_id = '" . (int)$parent_id . "'";

    $subcategories = $db->Execute($subcategories_query);

    while (!$subcategories->EOF) {
      $subcategories_array[sizeof($subcategories_array)] = $subcategories->fields['categories_id'];
      if ($subcategories->fields['categories_id'] != $parent_id) {
        kas_get_subcategories($subcategories_array, $subcategories->fields['categories_id']);
      }
      $subcategories->MoveNext();
    }
  }

  //Returns the filter option name (ie Colour)
  function get_filter_option_name($foption){
    global $db;
    $sql = "SELECT foptions_name FROM " . TABLE_FILTER_OPTIONS_NAME . " WHERE foptions_id = " . $foption;
    $foptions = $db->Execute($sql);
    return $foptions->fields['foptions_name'];
  }
?>