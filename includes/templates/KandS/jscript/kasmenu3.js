//Interiors
ddsmoothmenu.init({
  mainmenuid: "tabMenu2", //menu DIV id
  orientation: 'h', //Horizontal or vertical menu: Set to "h" or "v"
  classname: 'kasmenu3', //class added to menu's outer DIV
  customtheme: ["#BB9E8C", "#18374a"],
  contentsource: "markup" //"markup" or ["container_id", "path_to_menu_file"]
})
$(document).ready(function(){
$('#cat1').hover(
  function () {
    $(this).css({'cursor':'pointer','background-color':'#18374a'})
  },
  function () {
    $(this).css({'background-color':'#414141'})
  }
);

$('#cat2').css({'border-color':' #bb9e8c'});
$('#cat1').css({'border-color':'silver'});

});//end (document).ready