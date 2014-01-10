<?php

session_start();

include_once 'config.inc.php';
include_once 'cookies.inc.php';
include_once 'functions.inc.php';
if (!function_exists('json_encode'))include 'functions_php5.php';

if (strtoupper($_SERVER['REQUEST_METHOD']) == 'GET')
  {
  	if (!function_exists('json_decode1'))include 'functions_php5.php';
    global $cookies, $geoip;

    $iseu = true;

    if ($geoip === true)
      {
        if (local_iseu() === false)
          {
            $iseu = false;
          }
      }

    $debug = (isset($_GET['debug']))?$_GET['debug']:0;
//die(var_dump($cookies));
    $cookie_list = Array();
    foreach ($cookies as $cookie)
      {
        $consent = null;
        $permanent = false;

        $name = $cookie['name'];
        $cookie_name = 'KAS_' . $name;
        if (isset($_COOKIE[$cookie_name]))
          {
          	//////////////////////////////////////////////
          	//Kands - To componsate for elite server runing php4
          	//$data = json_decode1($_COOKIE[$cookie_name],true);
          	$data = $_COOKIE[$cookie_name];
          	$data=(strstr($data, 'true')?true:false);
						///////////////////////////////////////////////
            $consent = $data;//['consent'];
            $_SESSION[$name] = $consent;
            $permanent = true;
           // die('isset cookie'.$_COOKIE[$cookie_name]);
          }
        else if (isset($_SESSION[$name]))
          {
            $consent = $_SESSION[$name];
            $permanent = false;
            die('isset session'.$_SESSION[$name]);
          }
        else
          {
            if ($iseu === false)
              {
                $consent = true;
                $permanent = false;
                update_settings($name, $consent, $permanent);
              }
          }
        $cookie['consent'] = $consent;
        $cookie['permanent'] = $permanent;
        $cookie_list[] = $cookie;
      }

    if ($debug)
      {
        $debug1 = Array('name' => 'debug1', 'title' => 'debug1', 'description' => 'debug1', 'consent' => false, 'permanent' => false);
        $debug2 = Array('name' => 'debug2', 'title' => 'debug2', 'description' => 'debug2', 'consent' => false, 'permanent' => true);
        $debug3 = Array('name' => 'debug3', 'title' => 'debug3', 'description' => 'debug3', 'consent' => true, 'permanent' => false);
        $debug4 = Array('name' => 'debug4', 'title' => 'debug4', 'description' => 'debug4', 'consent' => true, 'permanent' => true);

        $cookie_list[] = $debug1;
        $cookie_list[] = $debug2;
        $cookie_list[] = $debug3;
        $cookie_list[] = $debug4;
      }

    header('Content-type: text/json');
    header('Content-type: application/json');
   	echo json_encode($cookie_list);
  }
else if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
  {
  	//On live server - to debug use die to output the data required and view it in firefox concosle
    if (!isset($_POST['cookiedata'])){
        header("HTTP/1.1 400 No Cookie Data Found");
      }else{
        if (get_magic_quotes_gpc()){
          $c = stripslashes($_POST['cookiedata']);
        }else{
          $c = $_POST['cookiedata'];
        }
        $cookiedata = json_decode($c, true);
//die(var_dump($cookiedata));
        if (count($cookies) != count($cookiedata))
          {
            $expected = count($cookies);
            $actual = count($cookiedata);
            header("HTTP/1.1 400 Wrong Cookie Count, expected: $expected, actual: $actual");
          }
        else
          {
            foreach ($cookiedata as $cookie)
              {
          			$cook = objectToArray($cookie);
//die(var_dump($cook).'[ 1 ->'.$cook['name'].' 2 ->'.$cook['consent'].' 3 ->'.$cook['permanent'].' ]');
                //update_settings($cook['name'], $cook['consent'], $cook['permanent']);
                update_settings($cook);
              }
            header('HTTP/1.1 200 Setting Complete');
          }
      }
  }
?>
