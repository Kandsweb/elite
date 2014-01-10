<?php
  //This generates the family tooltip info for the product listing pages
  //Inputs - the family model
  //         $hd (this is the hight of the image and is required to calculate the size of the spacer to keep the tooptip aligned to the top of the table cell)
  //         $cell_text is the orginal text/image that is to go in the cell, hence the tooptip text will be added before this and a spacer after it
  //Returns - An html formated string ready for outputing into the table cell ie the complete output
  function family_info_tooltip($modelID, $hd, $cell_text){
    //We need a spacer so the icon stays at the top of the tbl cell
    if($_SESSION['current_view'] == GALLERY_VIEW_MEDIUM){
      $spacer_height = ((132-$hd)/2)+5;
    }else{
      $spacer_height = ((90-$hd)/2);
    }
    $add_text='';

    $family_info = get_family_caption($modelID);
    if($family_info == NULL){
      //Build the normal faimly info text
      $family_info = family_quick_count($modelID);
      if($family_info > 1){
        if($family_info == 2){
          $family_info = 'There is ' . ($family_info-1) . ' other item in this range';
        }elseif($family_info > 2){
          $family_info = 'There are ' . ($family_info-1) . ' other items in this range';
        }
      }else $family_info = NULL;
    }
    if($family_info != NULL){
      //Build the additional output
      $add_text ='<div class="listingMoreInfo" title="Family Info|' . $family_info . '">' . '<img src="includes/templates/KandS/images/additional_info.jpg" alt=""/>' . '</div>' .
      '<div>' . zen_draw_separator('pixel_trans.gif',5, intval($spacer_height)) . '</div>';
    }
    $final_text =  $add_text . $cell_text;

    if($family_info != NULL)$final_text .= '<div>' . zen_draw_separator('pixel_trans.gif',5, intval($spacer_height)) . '</div>';

    return $final_text;
  }
?>
