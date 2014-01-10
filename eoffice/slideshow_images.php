<?php
  require('includes/application_top.php');
  define('MAX_SLIDESHOW_IMAGES', 12);    //This value must be a multiple of 6
  define('IMAGE_PATH','../images/slideshow/');

    if($_SERVER['HTTP_HOST']=='localhost'){
        $target_path = $_SERVER['DOCUMENT_ROOT'].'Elite_KandS/images/slideshow/';    //FOR LOCALHOST ONLY
    }else{
        $target_path = $_SERVER['DOCUMENT_ROOT'].'/images/slideshow/';    //FOR SERVER ONLY
    }

    if($_GET['action']=='images_update'){
          $imgs = array();
          foreach(array_keys($_POST)as $key){
              if(preg_match('/img\d{1,2}/',$key)){
                  $imgs[]=$key;
              }
          }
          if(sizeof($imgs)){
              foreach($imgs as $key){
                  unlink($_POST[$key]);
              }
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
<script src="includes/javascript/jquery-1.9.0.min.js"></script>
<script language="javascript" src="includes/javascript/jquery-ui-1.8.16.custom.min.js"></script>
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

      // -->
</script>
</head>
<body onload="init()">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<h1> Slideshow Images</h1>
        <?php
            foreach(glob(IMAGE_PATH.'{*.jpeg,*.jpg,*png,*gif}',GLOB_BRACE)as $file){
                $images_array[]=$file;
            }
            if(sizeof($images_array)>0){
                echo zen_draw_form('slideshow_images', 'slideshow_images.php', 'action=images_update', 'post', 'enctype="multipart/form-data"');
                sort($images_array);
                $cnt=0;
                $idx=1;
                foreach($images_array as $image){
                    echo '<div class="pv_img_'.$cnt.'">' . zen_image($image.'?id='.uniqid(),'',150).'<br><div>'.
                        zen_draw_checkbox_field('img'.$idx,$image).' Delete</div></div>';
                    $cnt++;
                    $idx++;
                    if($cnt>3){
                        echo '<br />'.zen_draw_separator('pixel_trans.gif','100%','20px');
                        $cnt=0;
                    }
                }
                echo '<br />'.zen_draw_separator('pixel_trans.gif','100%','20px');
                echo zen_image_submit('button_delete.gif','Delete Selected Images');
                echo '</form>';
            }
        ?>
<?php
include(DIR_FS_ADMIN.DIR_WS_MODULES.'kas_multi_uploader.php');

?>