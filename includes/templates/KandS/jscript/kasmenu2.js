ddsmoothmenu.init({
  mainmenuid: "tabMenu2", //menu DIV id
  orientation: 'h', //Horizontal or vertical menu: Set to "h" or "v"
  classname: 'kasmenu2', //class added to menu's outer DIV
  customtheme: ["#414141", "#18374a"],
  contentsource: "markup" //"markup" or ["container_id", "path_to_menu_file"]
})
$(document).ready(function(){
$('#cat2').hover(
  function () {
    $(this).css({'cursor':'pointer','background-color':'#18374a'})
  },
  function () {
    $(this).css({'background-color':'#bb9e8c'})
  }
);
$('#cat').css({'border-color':' #bb9e8c'});
$('#cat2').css({'border-color':'silver'});
});
