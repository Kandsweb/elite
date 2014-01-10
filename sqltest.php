
<?php
include('includes/application_top.php');
global $db;

$start_time = microtime(true);
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$sub_arr = 'I14 12921, I14 15507, I14 15505, I14 16768';//19,20,21,51,52,53';

$sql= "SELECT fc.caption
          FROM products p
          JOIN product_extra_fields pef
            ON pef.products_id = p.products_id
          JOIN family_captions fc
            ON fc.id = pef.family_caption
          WHERE p.products_model = 'M41 1001'";


/*

"(SELECT p.products_id, p.products_type, p.master_categories_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, pd.products_description, p.products_sort_order, p.product_is_call, p.product_is_always_free_shipping, p.products_qty_box_status, p.products_model, pd.products_viewed, pef.product_style,pef.product_priority FROM products_to_categories p2c INNER JOIN products_description pd ON pd.products_id = p2c.products_id INNER JOIN products p ON p.products_id = p2c.products_id INNER JOIN product_extra_fields pef ON pef.products_id = p2c.products_id WHERE p.products_status = 1 AND p2c.categories_id IN(40,77,78,79,80,81,82,46,74,75,76,71,72,73,84,88,89,90,85,91,92,93,94,86,131,132,192,87) GROUP BY LEFT(p.products_model,8))
UNION
(SELECT p.products_id, p.products_type, p.master_categories_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, pd.products_description, p.products_sort_order, p.product_is_call, p.product_is_always_free_shipping, p.products_qty_box_status, p.products_model, pd.products_viewed, pef.product_style, pef.product_priority FROM products_to_categories p2c INNER JOIN products_description pd ON pd.products_id = p2c.products_id INNER JOIN products p ON p.products_id = p2c.products_id INNER JOIN product_extra_fields pef ON pef.products_id = p2c.products_id WHERE p.products_status = 1 AND p2c.categories_id IN(40,77,78,79,80,81,82,46,74,75,76,71,72,73,84,88,89,90,85,91,92,93,94,86,131,132,192,87) AND pef.product_priority IS NOT NULL) ORDER BY product_priority desc, products_viewed desc, rand()
";


*/




$res = $db->Execute($sql);

$end_time = microtime(true);

$num_of_fields = sizeof($res->fields);
$num_of_rows = $res->RecordCount();
?>





<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>
<?php
if($res)echo "Statement PASSED <br />$sql<br />";
echo "Time to process ".  ($end_time- $start_time) . '<br />';
echo "Number of rows returned $num_of_rows<br />";
echo "Number of columns returned $num_of_fields<br />";

echo "<table border='1'><tr>";
  foreach($res->fields as $key => $value){
    echo "<td>" . $key . '</td>';
  }
echo "</tr>";

for ($r = 0; $r < $num_of_rows; $r++){
  echo "<tr>";
  foreach($res->fields as $key => $value){
    echo "<td>" . $value . '</td>';
  }
  echo '</tr>';
  $res->MoveNext();
}

/////////////////////////////////////////////////
/*$f_array = array();
for ($r = 0; $r < $num_of_rows; $r++){
  foreach($res->fields as $key => $value){
    $f_array[$r][] = $value;
  }
  $res->MoveNext();
}
$count=0;
for ($r = 0; $r < sizeof($f_array); $r++){
  if(!file_exists('images/'.$f_array[$r][3])){
     $count++;
     echo '<tr>';
     echo '<td>' . $f_array[$r][0] . '</td>';
     echo '<td>' . $f_array[$r][1] . ' ('.$r.')['. $count . ']</td>';
     echo '<td>' . $f_array[$r][2] . '</td>';
     echo '<td>' . $f_array[$r][3] . '</td>';
     echo '<td>' . $f_array[$r][4] . '</td>';
     echo '<td>' . $f_array[$r][5] . '</td>';
     echo '</tr>';
  }
}*/
///////////////////////////////////////////////////
echo "</table>";


?>
<body>
</body>
</html>