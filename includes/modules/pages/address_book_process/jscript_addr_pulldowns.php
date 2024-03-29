<?php
/**
 * jscript_addr_pulldowns
 *
 * handles pulldown menu dependencies for state/country selection
 *
 * @package page
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: jscript_addr_pulldowns.php 4830 2006-10-24 21:58:27Z drbyte $
 */
?>
<script language="javascript" type="text/javascript"><!--

function update_zone(theForm) {
  // if there is no zone_id field to update, or if it is hidden from display, then exit performing no updates
  if (!theForm || !theForm.elements["zone_id"]) return;
  if (theForm.zone_id.type == "hidden") return;

  // set initial values
  var SelectedCountry = theForm.zone_country_id.options[theForm.zone_country_id.selectedIndex].value;
  var SelectedZone = theForm.elements["zone_id"].value;

//BOE KandS
  var NoPostCodeZones = "<?php echo NO_POSTCODE_ZONES; ?> ";
  if(NoPostCodeZones.indexOf(SelectedCountry)>0){
    //hide postcode
    document.getElementById("postcode").value = "NOPOSTCODE";
    document.getElementById("postcode").className = 'hiddenField';
    document.getElementById("postcode").setAttribute('className', 'hiddenField');
    document.getElementById("pcText").className = 'hiddenField';
    document.getElementById("pcText").setAttribute('className', 'hiddenField');
    document.getElementById("pcAlert").className = 'hiddenField';
    document.getElementById("pcAlert").setAttribute('className', 'hiddenField');
    $("#pcDiv").hide();
  }else{
    if(document.getElementById("postcode").value == "NOPOSTCODE"){
        document.getElementById("postcode").value = "";
    }
    document.getElementById("postcode").className = 'inputLabel visibleField';
    document.getElementById("postcode").setAttribute('className', 'inputLabel visibleField');
    document.getElementById("pcText").className = 'accCreateLeft visibleField';
    document.getElementById("pcText").setAttribute('className', 'inputLabel visibleField');
    document.getElementById("pcAlert").className = 'alert visibleField';
    document.getElementById("pcAlert").setAttribute('className', 'alert visibleField');
    $("#pcDiv").show();
  }
 //EOE KandS


  // reset the array of pulldown options so it can be repopulated
  var NumState = theForm.zone_id.options.length;
  while(NumState > 0) {
    NumState = NumState - 1;
    theForm.zone_id.options[NumState] = null;
  }
  // build dynamic list of countries/zones for pulldown
<?php echo zen_js_zone_list('SelectedCountry', 'theForm', 'zone_id'); ?>

  // if we had a value before reset, set it again
  if (SelectedZone != "") theForm.elements["zone_id"].value = SelectedZone;

}

  function hideStateField(theForm) {
    theForm.state.disabled = true;
    theForm.state.className = 'hiddenField';
    theForm.state.setAttribute('className', 'hiddenField');
    //document.getElementById("stateLabel").className = 'hiddenField';
    //document.getElementById("stateLabel").setAttribute('className', 'hiddenField');
    document.getElementById("stText").className = 'hiddenField';
    document.getElementById("stText").setAttribute('className', 'hiddenField');
    document.getElementById("stBreak").className = 'hiddenField';
    document.getElementById("stBreak").setAttribute('className', 'hiddenField');
    document.getElementById("stDiv").style.display='none';
  }

  function showStateField(theForm) {
    theForm.state.disabled = false;
    theForm.state.className = 'inputLabel visibleField';
    theForm.state.setAttribute('className', 'visibleField');
    //document.getElementById("stateLabel").className = 'inputLabel visibleField';
    //document.getElementById("stateLabel").setAttribute('className', 'inputLabel visibleField');
    document.getElementById("stText").className = 'alert visibleField';
    document.getElementById("stText").setAttribute('className', 'alert visibleField');
    document.getElementById("stBreak").className = 'clearBoth visibleField';
    document.getElementById("stBreak").setAttribute('className', 'clearBoth visibleField');
    document.getElementById("stDiv").style.display='block';

  }
//--></script>