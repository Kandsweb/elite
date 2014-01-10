<?php
global $department;
//$department = (isset($_SESSION['department']) ? $_SESSION['department'] : 1);
$department = substr($cPath,0,1);
if($department=='')$department=1;
$_SESSION['department'] = $department;
if($department == 1){
  //Load the relevent js file for the menu. For some reason this can not be combined into on file, if you do the secon one does not work
  //These init the ddsmoothmenu ?>
  <script type="text/javascript" src="includes/templates/KandS/jscript/kasmenu2.js"></script>
<?php }else{ ?>
  <script type="text/javascript" src="includes/templates/KandS/jscript/kasmenu3.js"></script>
<?php } ?>
<div class="kasboxTop">
  <div id="cat1<?php //echo ($department == 1 ?1:2); ?>"<?php if($department==2){ ?> onclick="location.href='<?php echo zen_href_link('index','cPath=1') ?>'" <?php } ?>>
      <a href="<?php echo zen_href_link('index','cPath=1') ?>"><?php echo zen_get_categories_name(1); ?></a>

  </div>
  <div id="cat2<?php //echo ($department == 1 ?2:1); ?>" <?php if($department==1){ ?> onclick="location.href='<?php echo zen_href_link('index','cPath=2') ?>'" <?php } ?> >
      <a href="<?php echo zen_href_link('index','cPath=2') ?>"><?php echo zen_get_categories_name(2); ?></a>
  </div><br style="clear: both" />
</div>
<div class="kasboxBot<?php    //DO NOT CLOSE this as it closes 2 lines below
  //set kasboxBot class according to department
 echo ($department==1?2:3) ?>">
<div class="kasmenu2" id="tabMenu2">
  <ul>
    <?php
      $department_subs = $_SESSION['category_tree']->category_tree[$department]['sub_cats'];
      for($i=0; $i<sizeof($department_subs); $i++){
    ?>
    <li><a href="<?php
        echo zen_href_link('index',  'cPath='.$_SESSION['category_tree']->category_tree[$department_subs[$i]]['cPath']) . ' "' . $sub_rel . '>'.  zen_output_string_protected($_SESSION['category_tree']->category_tree[$department_subs[$i]]['name']); ?></a>
<?php
 $content = '';
  if('has_sub' == $_SESSION['category_tree']->category_tree[$department_subs[$i]]['sub']){
   $content .=  '<ul>';
   $department_subs_subs = $_SESSION['category_tree']->category_tree[$department_subs[$i]]['sub_cats'];
   for($ii=0;$ii<sizeof($department_subs_subs); $ii++){
     $content .= '<li><a href="' . zen_href_link('index',  'cPath='.$_SESSION['category_tree']->category_tree[$department_subs_subs[$ii]]['cPath']) . ' ">'.  zen_output_string_protected($_SESSION['category_tree']->category_tree[$department_subs_subs[$ii]]['name']) . '</a></li>';
   }
   $content .= '</ul>';
  }
  echo $content;

?>
    </li>

    <?php
    }
    ?>
  </ul>
<br style="clear: both" />
</div><br style="clear: both" />
</div>
