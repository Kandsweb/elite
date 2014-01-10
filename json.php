<?php
	session_start();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Expires" content="Fri, Jan 01 1900 00:00:00 GMT">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Lang" content="en">
<meta name="author" content="">
<meta http-equiv="Reply-to" content="@.com">
<meta name="generator" content="PhpED 5.6">
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="creation-date" content="11/11/2008">
<meta name="revisit-after" content="15 days">
<title>Untitled</title>
<link rel="stylesheet" type="text/css" href="my.css">
<link rel="stylesheet" type="text/css" href="includes/templates/KandS/css/stylesheet_jpecr-default.css" />
<script type="text/javascript" src="includes/templates/KandS/jscript/jscript__jquery.1.7.2min.js"></script>
<script type="text/javascript" src="includes/templates/KandS/jscript/jscript_jpecr-2.0.0.js"></script>
</head>
<body>
<?php
error_reporting(E_ALL);


if (!function_exists('json_encode'))include 'includes/cookie_law/php/functions_php5.php';


echo '<script type="text/javascript">
		$(document).ready( function () {
			$.ws.jpecr({
				displayButtonSelector: \'.jpecrDisplayButton\',
				growlerType: \'bar\',
				popupType: \'modal\',
				debug: false,
			});
		});
	</script>';




//include 'functions_php5.php';

//die ('Iam Dead');
?>
<br>
1<br>
2<br>
3<br>
<a href="#" class="jpecrDisplayButton">Cookie Settings.....</a>
<br><br>
<?php echo var_dump($_SESSION);?>
</body>
</html>
