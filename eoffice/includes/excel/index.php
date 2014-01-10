<?php 
// Создался ли лист? 
if ( !file_exists('sheets/binary.xls') ) { 

    // Внедрение PEAR::Spreadsheet_Excel_Writer 
    require_once "Spreadsheet/Writer.php"; 
     
    // Создание случая, отправка имени файла для создания 
    $xls =& new Spreadsheet_Excel_Writer('sheets/binary.xls'); 
     
    //Добавление листа к файлу, возвращение объекта для добавления данныx 
    $sheet =& $xls->addWorksheet('Binary Count'); 
     
    // Пишем несколько цифр 
    for ( $i=0;$i<11;$i++ ) { 
    // Использование функции PHP decbin()для преобразования целого числа в //бинарные данные
      $sheet->write($i,0,decbin($i)); 
    } 
     $sheet->write(($i+1),3,'1111'); 
     $sheet->write(($i+2),4,'2222'); 
    // Конец листа, отправка обозревателю
    $xls->close(); 
}
?>
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
<script language="javascript" src="includes/menu.js"></script>
<script language="javascript" src="includes/general.js"></script>
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
  if (typeof _editor_url == "string") HTMLArea.replaceAll();
  }
  // -->
</script>
<?php if (HTML_EDITOR_PREFERENCE=="FCKEDITOR") require(DIR_WS_INCLUDES.'fckeditor.php'); ?>
<?php if (HTML_EDITOR_PREFERENCE=="HTMLAREA")  require(DIR_WS_INCLUDES.'htmlarea.php'); ?>
</head>