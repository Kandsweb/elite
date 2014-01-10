<?php
/**
 * Cookie Usage Page
 *
 * @package page
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: header_php.php 2968 2006-02-04 20:00:28Z wilt $
 */
require(DIR_WS_MODULES . zen_get_module_directory('require_languages.php'));
$breadcrumb->add(NAVBAR_TITLE);
$user_call = (isset($_GET['a'])?true:false);
$HTTP_USER_AGENT=$_SERVER['HTTP_USER_AGENT'];
$os=""; $osh="";
if (eregi ("(win|microsoft)", $HTTP_USER_AGENT)==true) $os="Windows";
if (eregi ("(unix|x11|lynx|konqueror|w3m)", $HTTP_USER_AGENT)==true) $os="Unix";
if (eregi ("linux", $HTTP_USER_AGENT)==true) $os="Linux";
if (eregi ("bsd", $HTTP_USER_AGENT)==true) $os="*BSD";
if (eregi ("sunos", $HTTP_USER_AGENT)==true) $os="SunOS";
if (eregi ("hp-ux", $HTTP_USER_AGENT)==true) $os="HP-UX";
if (eregi ("os/2", $HTTP_USER_AGENT)==true) $os="OS/2";
if (eregi ("qnx", $HTTP_USER_AGENT)==true) $os="QNX";
if (eregi ("mac", $HTTP_USER_AGENT)==true) $os="Macintosh";
if (eregi ("(powerpc|ppc)(mac)", $HTTP_USER_AGENT)==true) $os="Macintosh PowerPC";
if (eregi ("beos", $HTTP_USER_AGENT)==true) $os="Beos";
if (eregi ("solaris", $HTTP_USER_AGENT)==true) $os="Solaris";
if (eregi ("amigaos", $HTTP_USER_AGENT)==true) $os="AmigaOS";
if (eregi ("windows nt|winnt", $HTTP_USER_AGENT)==true) $os="Windows NT";
if (eregi ("windows nt 4", $HTTP_USER_AGENT)==true) $os="Windows NT 4";
if (eregi ("(windows nt 5|windows 2000)", $HTTP_USER_AGENT)==true) $os="Windows 2000";
if (eregi ("(windows nt 5.1|windows xp)", $HTTP_USER_AGENT)==true) $os="Windows XP";
if (eregi ("windows me", $HTTP_USER_AGENT)==true) $os="Windows ME";
if (eregi ("(windows 98|win98)", $HTTP_USER_AGENT)==true) $os="Windows 98";
if (eregi ("(windows 95|win95)", $HTTP_USER_AGENT)==true) $os="Windows 95";
if (eregi ("(windows 3.1|win3.1)", $HTTP_USER_AGENT)==true) $os="Windows 3.1";
if (eregi ("(windows 3.11|win3.11)", $HTTP_USER_AGENT)==true) $os="Windows 3.11";
if (eregi ("(mandrake|mdk)", $HTTP_USER_AGENT)==true) $os="Linux Mandrake";
if (eregi ("debian", $HTTP_USER_AGENT)==true) $os="Linux Debian";
if (eregi ("webtv", $HTTP_USER_AGENT)==true) $os="WebTV";

$browser=""; $browserh="";
if (eregi ("(netscape|mozilla)", $HTTP_USER_AGENT)==true) $browser="Netscape";
if (eregi ("mozilla/3", $HTTP_USER_AGENT)==true) $browser="Netscape 3";
if (eregi ("mozilla/4.5", $HTTP_USER_AGENT)==true) $browser="Netscape 4.5";
if (eregi ("mozilla/4.6", $HTTP_USER_AGENT)==true) $browser="Netscape 4.6";
if (eregi ("mozilla/4.7", $HTTP_USER_AGENT)==true) $browser="Netscape 4.7";
if (eregi ("(mozilla/5|gecko/)", $HTTP_USER_AGENT)==true) $browser="Mozilla";
if (eregi ("netscape/6", $HTTP_USER_AGENT)==true) $browser="Netscape 6";
if (eregi ("msie", $HTTP_USER_AGENT)==true) $browser="IE";
if (eregi ("msie 3", $HTTP_USER_AGENT)==true) $browser="IE 3";
if (eregi ("msie 3.0", $HTTP_USER_AGENT)==true) $browser="IE 3.0";
if (eregi ("msie 3.01", $HTTP_USER_AGENT)==true) $browser="IE 3.01";
if (eregi ("msie 4", $HTTP_USER_AGENT)==true) $browser="IE 4";
if (eregi ("msie 4.0", $HTTP_USER_AGENT)==true) $browser="IE 4.0";
if (eregi ("msie 4.01", $HTTP_USER_AGENT)==true) $browser="IE 4.01";
if (eregi ("msie 5", $HTTP_USER_AGENT)==true) $browser="IE 5";
if (eregi ("msie 5.0", $HTTP_USER_AGENT)==true) $browser="IE 5.0";
if (eregi ("msie 5.01", $HTTP_USER_AGENT)==true) $browser="IE 5.01";
if (eregi ("msie 5.1", $HTTP_USER_AGENT)==true) $browser="IE 5.1";
if (eregi ("msie 5.5", $HTTP_USER_AGENT)==true) $browser="IE 5.5";
if (eregi ("msie 6", $HTTP_USER_AGENT)==true) $browser="IE 6";
if (eregi ("msie 6.0", $HTTP_USER_AGENT)==true) $browser="IE 6.0";
if (eregi ("msie 6.0b", $HTTP_USER_AGENT)==true) $browser="IE 6.0b";
if (eregi ("opera", $HTTP_USER_AGENT)==true) $browser="Opera";
if (eregi ("opera.2", $HTTP_USER_AGENT)==true) $browser="Opera 2";
if (eregi ("opera.3", $HTTP_USER_AGENT)==true) $browser="Opera 3";
if (eregi ("opera.4", $HTTP_USER_AGENT)==true) $browser="Opera 4";
if (eregi ("opera.5", $HTTP_USER_AGENT)==true) $browser="Opera 5";
if (eregi ("opera.5.11", $HTTP_USER_AGENT)==true) $browser="Opera 5.11";
if (eregi ("opera.5.12", $HTTP_USER_AGENT)==true) $browser="Opera 5.12";
if (eregi ("opera.6", $HTTP_USER_AGENT)==true) $browser="Opera 6";
if (eregi ("opera.7.01", $HTTP_USER_AGENT)==true) $browser="Opera 7.01";
if (eregi ("opera.7.10", $HTTP_USER_AGENT)==true) $browser="Opera 7.10";
if (eregi ("opera.7.11", $HTTP_USER_AGENT)==true) $browser="Opera 7.11";
if (eregi ("lynx", $HTTP_USER_AGENT)==true) $browser="lynx";
if (eregi ("w3m", $HTTP_USER_AGENT)==true) $browser="w3m";
if (eregi ("konqueror", $HTTP_USER_AGENT)==true) $browser="Konqueror";
?>
