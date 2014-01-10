<?php
/**
 * Side Box Template
 * includes/templates/templates_default/sideboxes/tpl_recent.php
 *
 */
  $carousel_id='recent_carousel_sidebox';
  $carousel_class='jcarousel-skin-tango';

  $content = "";
  if(!$recent_products->EOF){
    $content .= '<ul id="'. $carousel_id  . '" class="'. $carousel_class . '">';
    while(!$recent_products->EOF){

      $recent_products->fields['products_image']=get_image_xref($recent_products->fields['products_image']);

    //$content .= '<div id="' . str_replace('_', '-', $box_id . 'Content') . '" class="sideBoxContent centeredContent">';
    //$content .= '<li><a href="' . zen_href_link(zen_get_info_page($recent_products->fields["products_id"]), 'products_id=' . $recent_products->fields["products_id"]) . '">' .  zen_image(DIR_WS_IMAGES . $recent_products->fields['products_image'], '','', 120) . '</a><br /><div class="familyViewItem">'. $recent_products->fields['products_name']. '</div> ' . '<br /></li>' ;
    if (!isset($productsInCategory[$recent_products->fields['products_id']])) $productsInCategory[$recent_products->fields['products_id']] = zen_get_generated_category_path_rev($recent_products->fields['master_categories_id']);



    $content .='<li>'.(($recent_products->fields['products_image'] == '' and PRODUCTS_IMAGE_NO_IMAGE_STATUS == 0) ? '' : '<a href="' . zen_href_link(zen_get_info_page($recent_products->fields['products_id']), 'cPath=' . $productsInCategory[$recent_products->fields['products_id']] . '&products_id=' . $recent_products->fields['products_id']) . '">' . zen_image(DIR_WS_IMAGES . $recent_products->fields['products_image'], $recent_products->fields['products_name'], IMAGE_FEATURED_PRODUCTS_LISTING_WIDTH, IMAGE_FEATURED_PRODUCTS_LISTING_HEIGHT) . '</a><br />') . '<a href="' . zen_href_link(zen_get_info_page($recent_products->fields['products_id']), 'cPath=' . $productsInCategory[$recent_products->fields['products_id']] . '&products_id=' . $recent_products->fields['products_id']) . '"><span class="familyViewItem">' . $recent_products->fields['products_name'] . '</span></a><br />' . $products_price . '</li>';

    $recent_products->MoveNext();
   }
   $content .= '</ul>';

   $content .= '<script type="text/javascript">
jQuery(document).ready(function() {
    jQuery(\'#' . $carousel_id . '\').jcarousel({
        auto: 0,
        animation: 1000,
        vertical: true,
        scroll: 1

    });
});
</script>';
  }
 ?>