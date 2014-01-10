<?php
$display_box = false;
if ($current_page == 'index' && $cPath){

  $_SESSION['OptionFilter']->fill_available_options($cPath);

  $display_box = true;
  $option_count = 0;
  $button_count = 0;
  $content = '';

  $content .= '<div id="optionsBox" class="optionBox">';
  $content .='<div id="optionsBoxTop" class="optionBoxTop">';
  $content .= "Narrow your search";
  $content .= '&nbsp;&nbsp;<span class="filterHelp" title="More Info|' . 'You can narrow down the results displayed by ticking the appropriate filters corresponding to your requirements and then clicking the \'Apply Filters\' button">' . zen_image_button('help_m.gif','') . '</span>';
  $content .= '</div>';//eof optionsBoxTop

  //Style
  $temp = $_SESSION['OptionFilter']->get_options(STYLE, false);
  if(strlen($temp)){
    $content .= '<div class="optionBoxGroup" id="optionsChecked" ><h4>By Style</h4>';
    $content .=  zen_image($template->get_template_dir('toggle.jpg',DIR_WS_TEMPLATE, $current_page_base,'images'). '/toggle.jpg','Clear style filter','','','onClick="toggleOption(\'Style\')" ondblclick="clearOption(\'Style\')"');
    $content .= $temp;
    $content .= '</div>';
    $option_count++;
  } //eod optionBoxGroup style

  //Finish
 /* $temp = $_SESSION['OptionFilter']->get_options(FINISH, false);
  if(strlen($temp)){
    $content .= '<div class="optionBoxGroup"><h4>By Finish</h4>';
    $content .=  zen_image($template->get_template_dir('toggle.jpg',DIR_WS_TEMPLATE, $current_page_base,'images'). '/toggle.jpg','Clear finish filter','','','onClick="toggleOption(\'Finish\')" ondblclick="clearOption(\'Finish\')"');
    $content .= $temp;
    $content .= '</div>';
    $option_count++;
  }//eod optionBoxGroup finish
*/
  //apply
  if($option_count>1) {
    $content .=' <div class="optionBoxApply" onclick="processFilters()">Apply Filters</div>';
    $option_count = 0;
    $button_count++;
  }

  //colour
  $temp = $_SESSION['OptionFilter']->get_options('Colour', false);
  if(strlen($temp)){
    $content .= '<div class="optionBoxGroup"><h4>By Colour</h4>';
    $content .=  zen_image($template->get_template_dir('toggle.jpg',DIR_WS_TEMPLATE, $current_page_base,'images'). '/toggle.jpg','Clear colour filter','','','onClick="toggleOption(\'Colour\')" ondblclick="clearOption(\'Colour\')"');
    $content .= $_SESSION['OptionFilter']->get_options('Colour',true);
    $content .= '</div>';
    $option_count++;
  }//eod optionBoxGroup colour

  //Material
  $temp = $_SESSION['OptionFilter']->get_options('Material', false);
  if(strlen($temp)){
    $content .= '<div class="optionBoxGroup"><h4>By Material</h4>';
    $content .=  zen_image($template->get_template_dir('toggle.jpg',DIR_WS_TEMPLATE, $current_page_base,'images'). '/toggle.jpg','Clear material filter','','','onClick="toggleOption(\'Material\')" ondblclick="clearOption(\'Material\')"');
    $content .= $_SESSION['OptionFilter']->get_options('Material',true);
    $content .= '</div>';
    $option_count++;
  }//eod optionBoxGroup colour

  //apply
  if($option_count>1){
    $content .=' <div class="optionBoxApply" onclick="processFilters()">Apply Filters</div>';
    $button_count++;
  }

  if($button_count==0) $content .=' <div class="optionBoxApply" onclick="processFilters()">Apply Filters</div>';



  $content .= '</div>';

  //if there is no options don't show box
  if($option_count <1)$display_box=false;

?>
<script language="javascript" type="text/javascript"><!--
$(document).ready(function(){
  $('.filterHelp').cluetip({hoverClass: 'highlight', cursor:'help', splitTitle: '|', dropShadow: true, cluetipClass: 'default'});
});
//--></script>
<?php
}
?>