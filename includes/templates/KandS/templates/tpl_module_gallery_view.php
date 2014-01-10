  <?php
  if($listing_split==""){
    if($products_all_split != NULL){
      $listing_split = $products_all_split;
    }elseif($products_new_split != NULL){
      $listing_split = $products_new_split;
    }
  }
  //echo __FILE__ . __LINE__;
  ?>
  <div id="linksBox">
  <div id="productsListingTopNumber" class="navSplitPagesResult back"><?php echo $listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></div>
  <?php
  echo '<div id="maxInfo">';
if($max_product_count > 0 && $max_product_count>$listing_split->number_of_rows){ ?>
   Expanding to
  <?php echo $max_product_count;
  echo '&nbsp;&nbsp;<span class="expandHelp" title="More Info|' . 'There are ' . $listing_split->number_of_rows . ' unique items displayed from this category for your viewing. Many of the items come in different colours, sizes, finishes etc which can only be seen when you view a particular item.<br /> As a result this category has a total of '. $max_product_count . ' different items">' . zen_image_button('help_m.gif','') . '</span>';
  ?>
<?php }
echo '</div>';
?>

</div>
<?php
/////////Elite code
echo '<div id="galleryBoxMid">';
?>

<?php
// require($template->get_template_dir('/tpl_modules_listing_display_order.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_listing_display_order.php');
if($_GET['main_page']!='advanced_search_result' && $_GET['main_page']!='promotions' && $_GET['main_page']!='events'){
?>
<div id="sorter">
<?php
//bypass the sort by if on advanced page results
  echo zen_draw_form('sorter_form', zen_href_link($_GET['main_page'],zen_get_all_get_params('view')), 'get');
  echo zen_draw_hidden_field('main_page', $_GET['main_page']);
  if(isset($_GET['cPath']))echo zen_draw_hidden_field('cPath', $_GET['cPath']);
  if(isset($_GET['view']))echo zen_draw_hidden_field('view', $_GET['view']);
  //These are in for the advanced search page results BUT are NOT use at present
  //if(isset($_GET['keyword']))echo zen_draw_hidden_field('keyword', $_GET['keyword']);
  //if(isset($_GET['search_in_description']))echo zen_draw_hidden_field('search_in_description', $_GET['search_in_description']);
  //if(isset($_GET['categories_id']))echo zen_draw_hidden_field('categories_id', $_GET['categories_id']);
  //if(isset($_GET['dfrom']))echo zen_draw_hidden_field('dfrom', $_GET['dfrom']);
  //if(isset($_GET['dto']))echo zen_draw_hidden_field('dto', $_GET['dto']);
  echo zen_hide_session_id();
  $disp_order=0;
  $disp_order_default = 0;
  if(isset($_GET['disp_order']))$disp_order = $_GET['disp_order'];
?>
 <label for="disp-order-sorter"><?php echo TEXT_INFO_SORT_BY; ?></label>
  <select name="disp_order" onchange="this.form.submit();" id="disp-order-sorter">
<?php if ($disp_order != $disp_order_default) { ?>
    <option value="<?php echo $disp_order_default; ?>" <?php echo ($disp_order == $disp_order_default ? 'selected="selected"' : ''); ?>><?php echo PULL_DOWN_ALL_RESET; ?></option>
<?php  }// reset to store default
    ////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// IMPORTANT Any changes here must be paired up with index_filters/default_filter.php Line 183 approx  ?>
    <option value="0" <?php echo ($disp_order == '0' ? 'selected="selected"' : ''); ?>>Popularity</option>
    <option value="6" <?php echo ($disp_order == '6' ? 'selected="selected"' : ''); ?>>Latest new Items</option>
    <option value="1" <?php echo ($disp_order == '1' ? 'selected="selected"' : ''); ?>><?php echo TEXT_INFO_SORT_BY_PRODUCTS_NAME; ?></option>
    <option value="2" <?php echo ($disp_order == '2' ? 'selected="selected"' : ''); ?>><?php echo TEXT_INFO_SORT_BY_PRODUCTS_NAME_DESC; ?></option>
    <!--<option value="3" <?php //echo ($disp_order == '3' ? 'selected="selected"' : ''); ?>><?php //echo TEXT_INFO_SORT_BY_PRODUCTS_PRICE; ?></option>
    <option value="4" <?php //echo ($disp_order == '4' ? 'selected="selected"' : ''); ?>><?php //echo TEXT_INFO_SORT_BY_PRODUCTS_PRICE_DESC; ?></option>-->
    <option value="5" <?php echo ($disp_order == '5' ? 'selected="selected"' : ''); ?>><?php echo TEXT_INFO_SORT_BY_PRODUCTS_MODEL; ?></option>
    <option value="7" <?php echo ($disp_order == '7' ? 'selected="selected"' : ''); ?>><?php echo TEXT_INFO_SORT_BY_PRODUCTS_DATE; ?></option>
    <option value="8" <?php echo ($disp_order == '8' ? 'selected="selected"' : ''); ?>>Random</option>
    </select></form></div>
    <?php
    }else{
      echo zen_draw_separator('pixel_trans.gif','200','25');
    }//eof bypass for advanced search results
echo '</div>';//</div><br class="clearBoth" />'
//////// EOF Elite code
?>

<div id="galleryBox">
<?php
echo zen_draw_form('galleryViewSelect', zen_href_link($current_page, zen_get_all_get_params(array('action'))) );

echo TEXT_GALLERY_SELECT_VIEW ;
if($_SESSION['current_view']!=GALLERY_VIEW_LIST){
  echo '<a href="' . zen_href_link($current_page, zen_get_all_get_params(array('action', 'view')) . 'view=1') . '">' . zen_image_button($image_button_list, GALLERY_BUTTON_LIST_ALT) . '</a>';
}else{
  echo zen_image_button($image_button_list, GALLERY_BUTTON_LIST_ALT);
}
if($_SESSION['current_view']!=GALLERY_VIEW_MEDIUM){
  echo '<a href="' . zen_href_link($current_page, zen_get_all_get_params(array('action', 'view')) . 'view=2') . '">' . zen_image_button($image_button_medium, GALLERY_BUTTON_MEDIUM_ALT) . '</a>';
}else{
  echo zen_image_button($image_button_medium, GALLERY_BUTTON_MEDIUM_ALT);
}
if($_SESSION['current_view']!=GALLERY_VIEW_SMALL){
  echo '<a href="' . zen_href_link($current_page, zen_get_all_get_params(array('action', 'view')) . 'view=3') . '">' . zen_image_button($image_button_small, GALLERY_BUTTON_SMALL_ALT) . '</a>';
}else{
  echo zen_image_button($image_button_small, GALLERY_BUTTON_SMALL_ALT);
}
echo '<br />';

$ii=0;
if($_SESSION['current_view']==GALLERY_VIEW_LIST){
  for($i=10, $n=$listing_split->number_of_rows; $i<$n; $i+=10){
    $view_array[] = array('id' => $i, 'text' => $i,);
    if($i>=50)break;
  }
}else{
  for($i=GALLERY_MAX_ROWS * $cols_in_view, $n= $listing_split->number_of_rows+(GALLERY_MAX_ROWS * $cols_in_view); $i<$n; $i+=(GALLERY_MAX_ROWS * $cols_in_view)){
    $view_array[] = array('id' => $i, 'text' => $i);
    $ii++;
    if($ii>5)break;
  }
}

if(sizeof($view_array) > 1){
  echo TEXT_GALLERY_NUMBER_PER_PG . zen_draw_pull_down_menu('per_page',$view_array, $_SESSION['per_page'], 'id="per_page" onchange="changeGalleryView()"');
}
echo '</form>';
//echo __FILE__ . __LINE__;
?>
</div>
<?php $page_links = TEXT_RESULT_PAGE . ' ' . $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, zen_get_all_get_params(array('page', 'info', 'x', 'y', 'main_page')));
if($page_links != '' && $page_links != ' &nbsp;'){
  ?>
  <div id="productsListingListingTopLinks" class="navSplitPagesLinks forward"><?php echo $page_links ?></div>
  <?php
}
////// Elite code
//output the info that you have filters applied
//from includeds/classes/class.option_filters.php
echo $_SESSION['OptionFilter']->build_string();
///// EOF Elite code ?>
<br class="clearBoth" />

<script type="text/javascript">
  <!--
  function changeGalleryView(){
    var pString = '<?php echo zen_html_entity_decode(zen_href_link($current_page, zen_get_all_get_params(array('pp')))); ?>';
      if(document.galleryViewSelect.onsubmit && !document.galleryViewSelect.onsubmit()){
        return;
      }
    //var pp = $("#per_page").val();
    document.body.style.cursor="wait";
    //window.location = pString + "&pp=" + pp;
    window.location = '<?php echo zen_html_entity_decode(zen_href_link($current_page, zen_get_all_get_params(array('pp')))); ?>' + "&pp=" + $("#per_page").val();
  }
$(document).ready(function(){
  document.body.style.cursor="auto";
  $('.expandHelp').cluetip({hoverClass: 'highlight', cursor:'help', splitTitle: '|', dropShadow: true, cluetipClass: 'default'});

    $('.qrf').hover(function(){
    $(this).css('cursor','pointer');
    }, function(){
      $(this).css('cursor', 'auto');
    });
});

  -->
</script>


