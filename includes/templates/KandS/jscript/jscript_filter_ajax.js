
function FilterSendValue(sValue, fId, getPath){
  document.getElementById('opaque').style.display='block'
  toggle('Waitloading');
  //$("#productListing").fadeTo(1000,0.5);
  $("#productListing").slideUp();
  $.post("ajax/ajax_filters.php",{ sendValue: sValue, sendFilter: fId, sendPath: getPath },
  function(data){
    process(data, fId);
  }, "json");

  }

  //Pass an elements id and this will toggle its visability
  function toggle(id) {
    var state = document.getElementById(id).style.display;
      if (state == 'block') {
        document.getElementById(id).style.display = 'none';
      } else {
        document.getElementById(id).style.display = 'block';
      }
  }


  function process(data, fID){
     toggle('Waitloading');
     document.getElementById('opaque').style.display='none'
     $("#productListing").html(data.productsdisplay);
     //$("#productListing").html('nnnnnnnnnnnnn');
     $("#filterBox").html(data.filterbox);
     //$("#filterBox").html('xxxxxxxxxxxxxxxxxxx');
     $("#productListing").slideDown();
     //$("#productListing").fadeTo(10,1 );
  }
//////////////////////////////////////////////////////////////////////////////////////

function tabsSendValue(department){
  document.getElementById('opaque_all').style.display='block'
  //toggle('WaitloadingTabs');
  //$("#productListing").fadeTo(1000,0.5);
  //$("#productListing").slideUp();
  $.post("ajax/ajax_filters.php",{ tab: department },
  function(data){
    processTabs(data);
  }, "json");

  }

    function processTabs(data){
     //toggle('tabsLoading');
     document.getElementById('opaque_all').style.display='none'
     //$("#productListing").html(data.productsdisplay);
     $("#tabRoot").html(data.tabs);
     $("#sub_menus").html(data.subs);
    //$(document).ready(function(){
      //SYNTAX: tabdropdown.init("menu_id", [integer OR "auto"])
      tabdropdown.init("tabSubs");
    //}
  }
