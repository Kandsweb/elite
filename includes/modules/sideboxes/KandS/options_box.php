<?php
    require($template->get_template_dir('tpl_filter_box.php',DIR_WS_TEMPLATE, $current_page_base,'sideboxes'). '/tpl_options_box.php');

  if($display_box){

    $title = "";
    $left_corner = false;
    $right_corner = false;
    $right_arrow = false;
    $title_link = false;

    require($template->get_template_dir($column_box_default, DIR_WS_TEMPLATE, $current_page_base,'common') . '/' . $column_box_default);
     ?>
   <script type="text/javascript">
    <!--
    function toggleOption(oID){
      var ckbx = $('input[name *= ' + oID + ']');
       for(var i in ckbx){
        ckbx[i].checked= !ckbx[i].checked;
      }
      //$('input[name *= ' + $oID + ']').attr('checked',true);
    }
    function clearOption(oID){
      var ckbx = $('input[name *= ' + oID + ']');
       for(var i in ckbx){
        ckbx[i].checked=false;
      }
      //$('input[name *= ' + $oID + ']').attr('checked',true);
    }

    function switchOption(oID, ckBox){
      var ocbv = ckBox.checked;
      clearOption(oID);
      ckBox.checked=ocbv
    }

    function processFilters(){
      var fString='';
      var tString = '';
      var pString = '<?php
      echo zen_html_entity_decode(zen_href_link($current_page, zen_get_all_get_params(array('s','f','m','c'))));
      ?>';
      $("input[name *= 'Style']:checkbox:checked").each(function(){
        tString = tString + $(this).val() + ",";
      });
      if(tString.length>0){
        fString = "&s=" + tString.substring(0, tString.length - 1);
      }
      //alert($("#optionsChecked :input:checked").size());
      tString = '';
      $("input[name *= 'Finish']:checkbox:checked").each(function(){
        tString = tString + $(this).val() + ",";
      });
      if(tString.length>0){
        fString = fString + "&f=" + tString.substring(0, tString.length - 1);
      }

      tString = '';
      $("input[name *= 'Colour']:checkbox:checked").each(function(){
        tString = tString + $(this).val() + ",";
      });
      if(tString.length>0){
        fString = fString + "&c=" + tString.substring(0, tString.length - 1);
      }

      tString = '';
      $("input[name *= 'Material']:checkbox:checked").each(function(){
        tString = tString + $(this).val() + ",";
      });
      if(tString.length>0){
        fString = fString + "&m=" + tString.substring(0, tString.length - 1);
      }


      pString = pString + fString;
     ///alert("Filter String " + pString);
     window.location = pString;

    }

    $('.optionBoxApply').hover(function(){
      $(this).css('font-weight','bold');
      $(this).css('cursor','pointer');
      $(this).css('color','#00ff00;');
    }, function(){
      $(this).css('cursor', 'auto');
      $(this).css('font-weight','normal');
      $(this).css('color','#ffff00;');
    });
    -->
    </script>
    <?php
  }
?>
