<?php
/**
 * split_page_results Class.
 *
 * @package classes
 * @copyright Copyright 2003-2009 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: split_page_results.php 13998 2009-07-28 16:06:06Z drbyte $
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
/**
 * Split Page Result Class
 *
 * An sql paging class, that allows for sql reslt to be shown over a number of pages using  simple navigation system
 * Overhaul scheduled for subsequent release
 *
 * @package classes
 */
class splitPageResults extends base {
  var $sql_query, $number_of_rows, $current_page_number, $number_of_pages, $number_of_rows_per_page, $page_name;

  /* class constructor */
  function splitPageResults($query, $max_rows, $count_key = '*', $page_holder = 'page', $debug = false) {
    global $db, $count_max_sql;
    $max_rows = ($max_rows == '' || $max_rows == 0) ? 20 : $max_rows;
    $this->sql_query = $query;
    $this->page_name = $page_holder;

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //Because of the product proirty field pulling in additional records the std method of counting the number of products returns the wrong number of items
    //echo __FILE__. __LINE__.'<br />';
    //$my_count = $db->Execute($count_max_sql);
    //$my_count = $my_count->RecordCount();
    //echo __FILE__. __LINE__.'<br />';
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ($debug) {
      echo 'original_query=' . $query . '<br /><br />';
    }
    if (isset($_GET[$page_holder])) {
      $page = $_GET[$page_holder];
    } elseif (isset($_POST[$page_holder])) {
      $page = $_POST[$page_holder];
    } else {
      $page = '';
    }

    if (empty($page) || !is_numeric($page)) $page = 1;
    $this->current_page_number = $page;

    $this->number_of_rows_per_page = $max_rows;

///This section may not be needed if my count system works
    $pos_to = strlen($this->sql_query);

    $query_lower = strtolower($this->sql_query);
    $pos_from = strpos($query_lower, ' from', 0);

    $pos_group_by = strpos($query_lower, ' group by', $pos_from);
    if (($pos_group_by < $pos_to) && ($pos_group_by != false)) $pos_to = $pos_group_by;

    $pos_having = strpos($query_lower, ' having', $pos_from);
    if (($pos_having < $pos_to) && ($pos_having != false)) $pos_to = $pos_having;

    $pos_order_by = strpos($query_lower, ' order by', $pos_from);
    if (($pos_order_by < $pos_to) && ($pos_order_by != false)) $pos_to = $pos_order_by;

    if (strpos($query_lower, 'distinct') || strpos($query_lower, 'group by')) {
      $count_string = 'distinct ' . zen_db_input($count_key);
    } else {
      $count_string = zen_db_input($count_key);
    }
    //$count_query = "select count(" . $count_string . ") as total, substr(products.products_model,1,8) as p_model, " .
    $count_query = "select count(" . $count_string . ") as total " .
    substr($this->sql_query, $pos_from, ($pos_to - $pos_from));
    if ($debug) {
      echo 'count_query=' . $count_query . '<br /><br />';
    }
    $count = $db->Execute($count_query);

    $this->number_of_rows = $count->fields['total'];

    ///////////(my count system part 2)///////////////////////////////////
    if($my_count>$this->number_of_rows)$this->number_of_rows = $my_count;
    //////////////////////////////////////////////////////////////////////

    $this->number_of_pages = ceil($this->number_of_rows / $this->number_of_rows_per_page);

    if ($this->current_page_number > $this->number_of_pages) {
      $this->current_page_number = $this->number_of_pages;
    }

    $offset = ($this->number_of_rows_per_page * ($this->current_page_number - 1));

    // fix offset error on some versions
    if ($offset <= 0) { $offset = 0; }

    $this->sql_query .= " limit " . ($offset > 0 ? $offset . ", " : '') . $this->number_of_rows_per_page;
  }

  /* class functions */

  // display split-page-number-links
  function display_links($max_page_links, $parameters = '') {
    global $request_type;

    $display_links_string = '';

    $class = '';

    if (zen_not_null($parameters) && (substr($parameters, -1) != '&')) $parameters .= '&';

    // previous button - not displayed on first page
    if ($this->current_page_number > 1) $display_links_string .= '<a class="prevPg" href="' . zen_href_link($_GET['main_page'], $parameters . $this->page_name . '=' . ($this->current_page_number - 1), $request_type) . '" title=" ' . PREVNEXT_TITLE_PREVIOUS_PAGE . ' ">'.zen_image('includes/templates/KandS/images/arrowhead-left.gif') .PREVNEXT_BUTTON_PREV . '</a>&nbsp;&nbsp;';

    // check if number_of_pages > $max_page_links
    $cur_window_num = intval($this->current_page_number / $max_page_links);
    if ($this->current_page_number % $max_page_links) $cur_window_num++;

    $max_window_num = intval($this->number_of_pages / $max_page_links);
    if ($this->number_of_pages % $max_page_links) $max_window_num++;

    // previous window of pages
    if ($cur_window_num > 1) $display_links_string .= '<a class="prevPg" href="' . zen_href_link($_GET['main_page'], $parameters . $this->page_name . '=' . (($cur_window_num - 1) * $max_page_links), $request_type) . '" title=" ' . sprintf(PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE, $max_page_links) . ' ">'. zen_image('includes/templates/KandS/images/dbl_left.gif',sprintf(PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE, $max_page_links),'15px') .'</a>';

    // page nn button
    for ($jump_to_page = 1 + (($cur_window_num - 1) * $max_page_links); ($jump_to_page <= ($cur_window_num * $max_page_links)) && ($jump_to_page <= $this->number_of_pages); $jump_to_page++) {
      if ($jump_to_page == $this->current_page_number) {
        //Current page
        $display_links_string .= '&nbsp;<strong class="currentPage">' . $jump_to_page . '</strong>&nbsp;';
      } else {
        //Other page numbers
        $display_links_string .= '&nbsp;<a class="nonCurrent" href="' . zen_href_link($_GET['main_page'], $parameters . $this->page_name . '=' . $jump_to_page, $request_type) . '" title=" ' . sprintf(PREVNEXT_TITLE_PAGE_NO, $jump_to_page) . ' ">' . $jump_to_page . '</a>&nbsp;';
      }
    }

    // next window of pages
    if ($cur_window_num < $max_window_num) $display_links_string .= '<a class="nextPg" href="' . zen_href_link($_GET['main_page'], $parameters . $this->page_name . '=' . (($cur_window_num) * $max_page_links + 1), $request_type) . '" title=" ' . sprintf(PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE, $max_page_links) . ' ">'. zen_image('includes/templates/KandS/images/dbl_right.gif',sprintf(PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE, $max_page_links),'15px') .'</a>&nbsp;';

    // next button
    if (($this->current_page_number < $this->number_of_pages) && ($this->number_of_pages != 1)) $display_links_string .= '&nbsp;<a class="nextPg" href="' . zen_href_link($_GET['main_page'], $parameters . 'page=' . ($this->current_page_number + 1), $request_type) . '" title=" ' . PREVNEXT_TITLE_NEXT_PAGE . ' ">' . PREVNEXT_BUTTON_NEXT . zen_image('includes/templates/KandS/images/arrowhead-right.gif') . '</a>&nbsp;';

    if ($display_links_string == '&nbsp;<strong class="current">1</strong>&nbsp;') {
      return '&nbsp;';
    } else {
      return $display_links_string;
    }
  }

  // display number of total products found
  function display_count($text_output) {
    $to_num = ($this->number_of_rows_per_page * $this->current_page_number);
    if ($to_num > $this->number_of_rows) $to_num = $this->number_of_rows;

    $from_num = ($this->number_of_rows_per_page * ($this->current_page_number - 1));

    if ($to_num == 0) {
      $from_num = 0;
    } else {
      $from_num++;
    }

    if ($to_num <= 1) {
      // don't show count when 1
      return '';
    } else {
      return sprintf($text_output, $from_num, $to_num, $this->number_of_rows);
    }
  }
}
?>