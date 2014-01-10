<?php
  require('includes/application_top.php');

switch($_GET['action']){
    case 'scan':
        $files = glob("../images/products/*.{jpg,gif,png,tiff}", GLOB_BRACE);
        $oprhans = array();
        foreach($files as $img_name ){
            if(is_orphan_image($img_name)){
                $orphans[] = $img_name;
            }
        }
        break;

    case 'purge':
        foreach($_POST[ftr] as $file){
            @unlink($file);
        }
        zen_redirect(zen_href_link('orphan_image','action=sus'));
        break;
}

function is_orphan_image($imgName){
    global $db;
    $n = preg_replace('/..\/images\//','',$imgName);
    //look for additional image marker
    $pos=(strrpos($n,'_'));
    if($pos!==false){
        $ext = pathinfo($n,PATHINFO_EXTENSION);
        $fn = pathinfo($n,PATHINFO_FILENAME);
        $p = pathinfo($imgName,PATHINFO_DIRNAME);
        $pos=(strrpos($fn,'_'));
        $fn= substr($fn,0,$pos);
        $n = $p.'/'.$fn.'.'.$ext;
    }
    $sql = "SELECT products_image FROM products WHERE products_image = '".$n."'";
    $res = $db->Execute($sql);
    if($res->EOF){
        return ture;
    }else{
        return false;
    }
}
?>

<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
<script language="javascript" src="includes/menu.js"></script>
<script language="javascript" src="includes/general.js"></script>
<script language="javascript" src="includes/javascript/jquery-1.9.0.min.js"></script>
<script type="text/javascript">
  <!--
  function init()
  {
    cssjsmenu('navbar');
    if (document.getElementById)
    {
      var kill = document.getElementById('hoverJS');
      kill.disabled = true;
    }
  }

  function dim(){
      $("#fuzz").css("height", $(document).height());
      $("#fuzz").fadeIn();
  }
    // -->
</script>
</head>
<body onload="init()">

<div id="fuzz">
  <div class="msgbox">
    <h4>Sending request<br />Please wait....</h4>
  <img src="images/loading-gif-animation.gif" width="80" height="80" /><br />
  This process will take some time to complete<br>
  Please wait.
  </div>
</div>
<!--End the overlay and message box-->


<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->


  <table style="border:none;width:100%; border-spacing:0;cell-padding:0;">
    <tr style="padding-top:10px;">
      <td class="pageHeading">Orphan Image Handler</td>
      <td class="pageHeading" colspan="2" style="text-align:right;vertical-align:top;padding-top:10px;">&nbsp;

      </td>
    </tr>
  </table>


<span Style="colour:red;font-weight: bold;">
The scan may take a considerable time to complete.<br>
Do Not run a scan during server busy periods.</span>
<br><br>
<?php
    echo '<a href="'.zen_href_link('orphan_image','action=scan').'" onclick="dim();">'.zen_image_button('button_preview.gif').'</a>';

    if($_GET['action'] == 'scan'){
        if(sizeof($orphans)>0){
            echo '<br><br>'.zen_draw_form('purge','orphan_image','action=purge');
            echo '<a href="'.zen_href_link('orphan_image','action=clean').'" onclick="dim();">'.zen_image_submit('button_remove.gif').'</a><br><br>';
            echo sizeof($orphans).' orphan images found<br><br>';
            $x=1;
            foreach($orphans as $orphan){
                echo zen_draw_checkbox_field('ftr[]',$orphan,true);
                echo $orphan.'<br/>';
                $x++;
            }
            echo '</form>';
        }else{
            echo '<br><b>
            There are no orphaned images</b>';
        }
    }

    if($_GET['action'] == 'sus'){
        echo '<br><br>Purge Complete';
    }
?>











<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>