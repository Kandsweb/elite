<?php

$filter_box_content .= '<div class="filterContainer" id="filterBox">
<div class="filterHead">Narrow your search</div>';

  //$filter_box_content.=var_dump($_SESSION['filter_array']);

    $filter_box_content .= zen_draw_form('products_filter', zen_href_link(FILENAME_PRODUCT_LISTING, zen_get_all_get_params(array('action'))), 'get');
    $filter_array = get_filter_array($_SESSION['filter_array'][1]);
    $filter_box_content .= 'By Type ' . zen_draw_pull_down_menu('filter1', $filter_array, ($_SESSION['filter_array'][2]), 'onchange="FilterSendValue(this.options[this.selectedIndex].value, 1, \''. zen_get_all_get_params() .'\')"');

    unset($filter_array);
    $filter_array[] = array('id' => -1, 'text'=> 'All');

    if(isset($_SESSION['filter_array'][2]))$filter_array = get_filter_array($_SESSION['filter_array'][2]);

    $filter_box_content .= ' - By Style ' . zen_draw_pull_down_menu('filterB', $filter_array, $_SESSION['filter_array'][3], 'id="filter2" onchange="FilterSendValue(this.options[this.selectedIndex].value, 2)"');


    if(isset($_SESSION['filter_array'][3]))$filter_array = get_filter_array($_SESSION['filter_array'][3]);
    $filter_box_content .= '..... By Size ' .zen_draw_pull_down_menu('filterC', $filter_array, $_SESSION['filter_array'][4], 'id="filter3" onchange="FilterSendValue(this.options[this.selectedIndex].value, 3)"');


    if(isset($_SESSION['filter_array'][4]))$filter_array = get_filter_array($_SESSION['filter_array'][4]);
    $filter_box_content .= '..... By Finish ' .zen_draw_pull_down_menu('filterC', $filter_array, '0', 'id="filter3" onchange="FilterSendValue(this.options[this.selectedIndex].value, 4)"');

    //$filter_box_content .= '<input type="submit" name="submit" value="Filter"</>';
    $filter_box_content .= '</form>';

    $filter_box_content .= '</div>';

?>