<?php
function local_iseu()
{
	if (!function_exists('json_decode'))include_once 'functions_php5.php';
  if (!isset($_SERVER['REMOTE_ADDR']))
    {
      return true;
    }
  $data = array('client_ip' => $_SERVER['REMOTE_ADDR']);

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_URL, 'http://api.wolf-software.com/geoip/iseu.php');
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  $result = curl_exec($ch);

  $results = json_decode($result, true);

  if ($results['iseu'] == false)
    {
      return false;
    }
  return true;
}

function always_include_plugin($flag = false)
{
  global $cookies;

  create_fallback();

  $count = 0;
  if ($flag == false)
    {
      foreach ($cookies as $cookie)
        {
          if (have_consent($cookie['name']) === false)
            {
              $count++;
            }
        }
    }
  if (($flag == true) || ($count > 0))
    {
      echo "<script src=\"/assets/js/plugin.js\" type=\"text/javascript\"></script>\n";
    }
  else
    {
      echo "<script src=\"/assets/js/plugin-nocheck.js\" type=\"text/javascript\"></script>\n";
    }
}

function have_consent($name, $type = true)
{
  if ((isset($_SESSION[$name])) && ($_SESSION[$name] == $type))
    {
      return true;
    }
  return false;
}

function have_cookie($name)
{
  $cookie_name = 'KAS_' . $name;

  if (isset($_COOKIE[$cookie_name]))
    {
      return true;
    }
  $perm_name = $name . '_perm';
  if (isset($_SESSION[$perm_name]))
    {
      return true;
    }
  return false;
}

function create_fallback()
{
  global $cookies;
	if (!function_exists('json_decode'))include_once 'functions_php5.php';

  foreach ($cookies as $cookie)
    {
      $name = $cookie['name'];
      $cookie_name = 'KAS_' . $name;
      $perm_name = $name . '_perm';
      if (isset($_COOKIE[$cookie_name]))
        {
        	$data = json_decode($_COOKIE[$cookie_name], true);
          $consent = $data['consent'];
          $_SESSION[$name] = $consent;
          $_SESSION[$perm_name] = true;
        }
    }
}

function update_settings($data){
	$name = $data['name'];
	$consent = $data['consent'];
	$permanent = $data['permanent'];
	//echo 'Name='.$data['name'].'|Consent='.$data['consent'].'|Perm='.$data['permanent'];
	if (!function_exists('json_encode'))include 'functions_php5.php';
  $perm_name = $name . '_perm';
  $expire = time() + (60 * 60 * 24 * 366);
  $domain = $_SERVER['HTTP_HOST'];

  $_SESSION[$name] = $consent;

  $cookie_name = 'KAS_' . $name;
  if ($permanent){
      $data = Array('consent' => $consent);
      $string = json_encode($data);
      setcookie($cookie_name, $string, $expire, '/', $domain);
      //echo  'Name='.$cookie_name.'|Consent='.$string.'|';
      $_SESSION[$perm_name] = true;
    }else{
	    if($consent){
				setcookie($name,$consent, 0, '/', $domain);
	    }else{
				if(isset($_COOKIE[$name]))setcookie($name, "", time() - 3600, '/', $domain);
			  if($name=='kands'){if(isset($_COOKIE['zenid']))setcookie('zenid', "", time() - 36000, '/elite_kands');}
			  if($name=='google'){
					setcookie('google', "", time() - 36000, '/');
					setcookie('__utma', "", time() - 36000, '/');
					setcookie('__utmb', "", time() - 36000, '/');
					setcookie('__utmc', "", time() - 36000, '/');
					setcookie('__utmz', "", time() - 36000, '/');
			  }
	    }
      if (isset($_COOKIE[$cookie_name]))
        {
          setcookie($cookie_name, "", time() - 3600, '/', $domain);
          unset($_COOKIE[$cookie_name]);
          unset($_SESSION[$perm_name]);
        }

    }
}

function update_settings1($name, $consent, $permanent)
{
	//die($name.' '.$consent.' '.$permanent);
	if (!function_exists('json_encode'))include 'functions_php5.php';
  $perm_name = $name . '_perm';
  $expire = time() + (60 * 60 * 24 * 366);
  $domain = $_SERVER['HTTP_HOST'];

  $_SESSION[$name] = $consent;

  $cookie_name = 'KAS_' . $name;
  if ($permanent){
      $data = Array('consent' => $consent);
      $string = json_encode($data);
      setcookie($cookie_name, $string, $expire, '/', $domain);
      $_SESSION[$perm_name] = true;
    }else{
	    if($consent){
				setcookie($name,$consent, 0, '/', $domain);
	    }else{
				if(isset($_COOKIE[$name]))setcookie($name, "", time() - 3600, '/', $domain);
			  if($name=='kands'){if(isset($_COOKIE['zenid']))setcookie('zenid', "", time() - 36000, '/elite_kands');}
			  if($name=='google'){
					setcookie('google', "", time() - 36000, '/');
					setcookie('__utma', "", time() - 36000, '/');
					setcookie('__utmb', "", time() - 36000, '/');
					setcookie('__utmc', "", time() - 36000, '/');
					setcookie('__utmz', "", time() - 36000, '/');
			  }
	    }
      if (isset($_COOKIE[$cookie_name]))
        {
          setcookie($cookie_name, "", time() - 3600, '/', $domain);
          unset($_COOKIE[$cookie_name]);
          unset($_SESSION[$perm_name]);
        }

    }
}

function objectToArray($d) {
		if (is_object($d)) {
			// Gets the properties of the given object
			// with get_object_vars function
			$d = get_object_vars($d);
		}

		if (is_array($d)) {
			/*
			* Return array converted to object
			* Using __FUNCTION__ (Magic constant)
			* for recursive call
			*/
			return array_map(__FUNCTION__, $d);
		}
		else {
			// Return array
			return $d;
		}
	}
?>
