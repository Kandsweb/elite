<?php


  //Get Post Variables. The name is the same as
//what was in the object that was sent in the jQuery
if (isset($_POST['sendValue'])){
  header('Content-Type: text/xml');
  chdir('../');
  include('includes/application_top.php');
  process_filter_return_data();
}elseif(isset($_POST['tab'])){
  header('Content-Type: text/xml');
  chdir('../');
  include('includes/application_top.php');
  process_tab_return_data();
}

//Because we want to use json, we have to place things in an array and encode it for json.
//This will give us a nice javascript object on the front side.
?>
