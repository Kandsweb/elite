<script type="text/javascript" src="includes/templates/KandS/jscript/jscript__jquery.1.7.2min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $('#tables').change(function(){

    })
    $('#insert_k1').click(function(){
        var txt = $.trim($(this).text());
        var box = $("#query");
        var txt = $('#keyword1').val();
        box.val(box.val() + txt + ' ');
    })
    $('#insert_k2').click(function(){
        var txt = $.trim($(this).text());
        var box = $("#query");
        var txt = $('#tables').val();
        box.val(box.val() + txt + ' ');
    })
    $('#insert_k3').click(function(){
        var txt = $.trim($(this).text());
        var box = $("#query");
        var txt = $('#keyword2').val();
        box.val(box.val() + txt + ' ');
    })
    $('#clear').click(function(){
       $("#query").val('');
        var txt = $('#keyword2').val();
        box.val(box.val() + txt);
    })
});//EO doc ready


</script>
<?php

 include('includes/configure.php');
$mysqlhost = DB_SERVER;
$mysqlusr = DB_SERVER_USERNAME;
$mysqlpass = DB_SERVER_PASSWORD;
mysql_connect($mysqlhost,$mysqlusr,$mysqlpass);

if(!isset($_POST['db'])){
    $_POST['db']=DB_DATABASE;
}

$tables_dropdown = '<select name="tables" id="tables">';
mysql_select_db($_POST['db']);
$result = mysql_query("SHOW TABLE STATUS");
while($array = mysql_fetch_array($result)){
    $tables_dropdown .= ' <option value="'.$array[Name].'">'.$array[Name].'</option>';
}
$tables_dropdown .= '</select>';



$statement1 = array(
    0=>array('id'=>'SELECT', 'text'=>'SELECT','fields'=>3),
    1=>array('id'=>'UPDATE', 'text'=>'UPDATE','fields'=>0),
    2=>array('id'=>'ALTER TABLE', 'text'=>'ALTER TABLE','fields'=>0),
    3=>array('id'=>'CREATE TABLE', 'text'=>'CREATE TABLE','fields'=>0),
    4=>array('id'=>'CREATE TABLE IF NOT EXISTS', 'text'=>'CREATE TABLE IF NOT EXISTS','fields'=>0),
    5=>array('id'=>'CREATE TEMPORARY TABLE', 'text'=>'CREATE TEMPORARY TABLE','fields'=>0),
    6=>array('id'=>'CREATE TEMPORARY TABLE IF NOT EXISTS', 'text'=>'CREATE TEMPORARY TABLE IF NOT EXISTS','fields'=>0),
    7=>array('id'=>'TRUNCATE TABLE', 'text'=>'TRUNCATE TABLE','fields'=>0),
    8=>array('id'=>'DROP TABLE', 'text'=>'DROP TABLE','fields'=>0),
    9=>array('id'=>'DESCRIBE', 'text'=>'DESCRIBE','fields'=>1),
    10=>array('id'=>'SHOW TABLES', 'text'=>'SHOW TABLES','fields'=>0)
    );

$statement2 = array(
    0=>array('id'=>'FROM', 'text'=>'FROM','fields'=>3),
    1=>array('id'=>'WHERE', 'text'=>'WHERE','fields'=>0)
    );
?>

<html>
<head><title>KandS Web MySQL Command Line</title>
</head>
<body>

<?php
if (isset($_POST['submitquery'])) {
     if (get_magic_quotes_gpc()) $_POST['query'] = stripslashes($_POST['query']);
     echo('<p><b>Query:</b><br />'.nl2br($_POST['query']).'</p>');

     mysql_select_db($_POST['db']);
     $result = mysql_query($_POST['query']);

     if ($result) {
        if (@mysql_num_rows($result)) { ?>
            <p><b>Result Set:</b></p>
              <table border="1">
              <thead>
              <tr>
              <?php
              for ($i=0;$i<mysql_num_fields($result);$i++) {
                echo('<th>'.mysql_field_name($result,$i).'</th>');
              }
              ?>
              </tr>
              </thead>
                    <tbody>

      <?php
      while ($row = mysql_fetch_row($result)) {
        echo('<tr>');
        for ($i=0;$i<mysql_num_fields($result);$i++) {
          echo('<td>'.$row[$i].'</td>');
        }
        echo('</tr>');
      }
      ?>
      </tbody>
      </table>
              <?php
        }else {
            echo('<p><b>Query OK:</b> '.mysql_affected_rows().' rows affected.</p>');
        }
     }else {
         echo('<p><b>Query Failed</b> '.mysql_error().'</p>');
     }
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// ?>
<form action="<?php $_SERVER['PHP_SELF']?>" method="POST">
<p>Target Database:
<select name="db">
<?php
$dbs = mysql_list_dbs();
for ($i=0;$i<mysql_num_rows($dbs);$i++) {
  $dbname = mysql_db_name($dbs,$i);
  if ($dbname == $_POST['db'])
    echo("<option selected>$dbname</option>");
  else
    echo("<option>$dbname</option>");
}
?>
</select>
</p>


<p>SQL Query:<br />
<textarea onFocus="this.select()" cols="60" rows="5" name="query" id="query">
<?php echo htmlspecialchars($_POST['query'])?>
</textarea>
</p>

<p><input type="submit" name="submitquery" value="Submit Query (Alt-Q)"

          accesskey="Q" />
<input type="button" name="clear" id="clear" value="Clear"><br>
</p>

</form>

<?php
echo draw_dropdown("keyword1", $statement1);
?>
<input type="button" name="insert_k1" id="insert_k1" value="Insert"><br>
<?php
echo $tables_dropdown;
?>
<input type="button" name="insert_k2" id="insert_k2" value="Insert"><br>
<?php
echo draw_dropdown("keyword2", $statement2);
?>
<input type="button" name="insert_k3" id="insert_k3" value="Insert"><br>
</body>
</html>


<?php ///////////////////////////////////////////////////////////////////////////////////////////
function draw_dropdown($id, $values){
$field = '<select id="' . $id . '"';
$parameters=null; $default=null;
    if (($parameters!=NULL)) $field .= ' ' . $parameters;

    $field .= '>' . "\n";

    //if (empty($default) && isset($GLOBALS[$name]) && is_string($GLOBALS[$name]) ) $default = stripslashes($GLOBALS[$name]);

    for ($i=0, $n=sizeof($values); $i<$n; $i++) {
      $field .= '  <option value="' . $values[$i]['id'] . '"';
      if ($default == $values[$i]['id']) {
        $field .= ' selected="selected"';
      }

      $field .= '>' . $values[$i]['text'] . '</option>' . "\n";
    }
    $field .= '</select>' . "\n";

    //if ($required == true) $field .= TEXT_FIELD_REQUIRED;

    return $field;
}

?>