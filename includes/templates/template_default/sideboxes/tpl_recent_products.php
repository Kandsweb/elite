<?php
/**
 * Side Box Template
 * includes/templates/templates_default/sideboxes/tpl_recent.php
 *
 */
  //$content = "";
  $content = array();
  while(!$recent_products->EOF){

  //$content .= '<div id="' . str_replace('_', '-', $box_id . 'Content') . '" class="sideBoxContent centeredContent">';
  $content[] = '<a href="' . zen_href_link(zen_get_info_page($recent_products->fields["products_id"]), 'products_id=' . $recent_products->fields["products_id"]) . '">' .  zen_image(DIR_WS_IMAGES . $recent_products->fields['products_image'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a><br />' . $recent_products->fields['products_name'] . '<br />' ;
  //$content .= '</div>';
  $recent_products->MoveNext();
 }
 ?>