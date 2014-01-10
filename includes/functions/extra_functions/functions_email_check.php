<?php

// support windows platforms
  function getmxrrA($hostname, &$mxhosts, &$mxweight) {
    if (!empty ($hostname) ) {
      $output = "";
      @exec ("nslookup.exe -type=MX $hostname.", $output);
      $imx=0;

      foreach ($output as $line) {
        $parts = "";
        if (preg_match ("/^$hostname\tMX preference = ([0-9]+), mail exchanger = (.*)$/", $line, $parts) ) {
          $mxweight[$imx] = $parts[1];
          $mxhosts[$imx] = $parts[2];
          $imx++;
       }
      }
      return ($imx!=0);
    }
    return false;
  }



 /**
 * Validate Email Addresses Via SMTP
 * This queries the SMTP server to see if the email address is accepted.
 * @copyright http://creativecommons.org/licenses/by/2.0/ - Please keep this comment intact
 * @author gabe@fijiwebdesign.com
 * @contributers adnan@barakatdesigns.net
 * @version 0.1a
 */
class SMTP_validateEmail {

 /**
  * PHP Socket resource to remote MTA
  * @var resource $sock
  */
 var $sock;

 /**
  * Current User being validated
  */
 var $user;
 /**
  * Current domain where user is being validated
  */
 var $domain;
 /**
  * List of domains to validate users on
  */
 var $domains;
 /**
  * SMTP Port
  */
 var $port = 25;
 /**
  * Maximum Connection Time to an MTA
  */
 var $max_conn_time = 30;
 /**
  * Maximum time to read from socket
  */
 var $max_read_time = 5;

 /**
  * username of sender
  */
 var $from_user = 'user';
 /**
  * Host Name of sender
  */
 var $from_domain = 'elitelightingni.com';

 /**
  * Nameservers to use when make DNS query for MX entries
  * @var Array $nameservers
  */
 var $nameservers = array(
 '192.168.0.1'
);

  var $message = '';
  var $return_code = '';

 var $debug = false;

 /**
  * Initializes the Class
  * @return SMTP_validateEmail Instance
  * @param $email Array[optional] List of Emails to Validate
  * @param $sender String[optional] Email of validator
  */
 function SMTP_validateEmail($emails = false, $sender = false) {
  if ($emails) {
   $this->setEmails($emails);
  }
  if ($sender) {
   $this->setSenderEmail($sender);
  }
 }

 function _parseEmail($email) {
  $parts = explode('@', $email);
 $domain = array_pop($parts);
 $user= implode('@', $parts);
 return array($user, $domain);
 }

 /**
  * Set the Emails to validate
  * @param $emails Array List of Emails
  */
 function setEmails($emails) {
  //foreach($emails as $email) {
  list($user, $domain) = $this->_parseEmail($emails);
  if (!isset($this->domains[$domain])) {
    $this->domains[$domain] = array();
  }
  $this->domains[$domain][] = $user;
 //}
 }

 /**
  * Set the Email of the sender/validator
  * @param $email String
  */
 function setSenderEmail($email) {
 $parts = $this->_parseEmail($email);
 $this->from_user = $parts[0];
 $this->from_domain = $parts[1];
 }

 /**
 * Validate Email Addresses
 * @param String $emails Emails to validate (recipient emails)
 * @param String $sender Sender's Email
 * @return Array Associative List of Emails and their validation results
 */
 function validate($emails = false, $sender = false) {

  $results = array();

  if ($emails) {
   $this->setEmails($emails);
  }
  if ($sender) {
   $this->setSenderEmail($sender);
  }

  // query the MTAs on each Domain
  foreach($this->domains as $domain=>$users) {

  $mxs = array();

   // retrieve SMTP Server via MX query on domain
   list($hosts, $mxweights) = $this->queryMX($domain);

   if(count($hosts) == 0){
     //domain bad
     $this->message = sprintf(TEXT_EMAIL_ERROR_DOMAIN, $domain);
     return false;
   }
   // retrieve MX priorities
   for($n=0; $n < count($hosts); $n++){
    $mxs[$hosts[$n]] = $mxweights[$n];
   }
   asort($mxs);

   // last fallback is the original domain
   array_push($mxs, $this->domain);

   $this->debug(print_r($mxs, 1));

   $timeout = $this->max_conn_time/count($hosts);

   // try each host
   while(list($host) = each($mxs)) {
    // connect to SMTP server
    $this->debug("try $host:$this->port\n");
    if ($this->sock = @fsockopen($host, $this->port, $errno, $errstr, (float) $timeout)) {
     stream_set_timeout($this->sock, $this->max_read_time);
     break;
    }
   }

   // did we get a TCP socket
   if ($this->sock) {
    $reply = fread($this->sock, 2082);
    $this->debug("<<<\n$reply");

    preg_match('/^([0-9]{3}) /ims', $reply, $matches);
    $code = isset($matches[1]) ? $matches[1] : '';

    $results[$user.'@'.$domain] = false;

    // say helo
    $this->send("HELO ".$this->from_domain);
    // tell of sender
    $this->send("MAIL FROM: <".$this->from_user.'@'.$this->from_domain.">");

    // ask for each recepient on this domain
    foreach($users as $user) {

     // ask of recepient
     $reply = $this->send("RCPT TO: <".$user.'@'.$domain.">");

      // get code and msg from response
     preg_match('/^([0-9]{3}) /ims', $reply, $matches);
     $code = isset($matches[1]) ? $matches[1] : '';

     $results[$user.'@'.$domain] = $code;

    }
    $this->debug(print_r($results, 1));
    // quit
    $this->send("quit");
    // close socket
    fclose($this->sock);

   }
  }
  $this->return_code = $code;

  foreach($results as $email=>$code){
    if($email == $emails){
      switch($code){
        case 250:
          $this->message = 'Valid email';
          return true;
          break;
        case 512:
          $this->message = sprintf(TEXT_EMAIL_ERROR_512, $domain);
          return false;
          break;
        case 554:
          $this->message = sprintf(TEXT_EMAIL_ERROR_554, $domain);
          return false;
          break;
        case 515:
        case 550:
        case 553:
          $this->message = sprintf(TEXT_EMAIL_ERROR_550, $domain, $users[0]);
          return false;
          break;
        case 522:
        case 531:
        case 533:
        case 553:
          $this->message = sprintf(TEXT_EMAIL_ERROR_552, $domain);
          return false;
          break;
        case 541:
          $this->message = sprintf(TEXT_EMAIL_ERROR_541, $domain);
          return false;
          break;
        case 547:
          $this->message = sprintf(TEXT_EMAIL_ERROR_547, $domain);
          return false;
          break;
        default:
          $this->message = sprintf(TEXT_EMAIL_ERROR_UNKNOWEN, $code, $code);
          return false;
      }
    }
  }
 return $results;
 }


 function send($msg) {
  fwrite($this->sock, $msg."\r\n");

  $reply = fread($this->sock, 2082);

  $this->debug(">>>\n$msg\n");
  $this->debug("<<<\n$reply");

  return $reply;
 }

 /**
  * Query DNS server for MX entries
  * @return
  */
 function queryMX($domain) {
  $hosts = array();
 $mxweights = array();
  if (function_exists('getmxrr')) {
   getmxrr($domain, $hosts, $mxweights);
  } else {
   // windows
   getmxrrA($domain, $hosts, $mxweights);

  }
 return array($hosts, $mxweights);
 }

 /**
  * Simple function to replicate PHP 5 behaviour. http://php.net/microtime
  */
 function microtime_float() {
  list($usec, $sec) = explode(" ", microtime());
  return ((float)$usec + (float)$sec);
 }

 function debug($str) {
  if ($this->debug) {
   echo htmlentities($str).'<br/>';
  }
 }

}


////////////////////////////////////////////////////////////////////////////////////////////////////////
//This is an alternitave method

function validate_email($email){
   $mailparts=explode("@",$email);
   $hostname = $mailparts[1];

   // validate email address syntax
   $exp = "^[a-z\'0-9]+([._-][a-z\'0-9]+)*@([a-z0-9]+([._-][a-z0-9]+))+$";
   $b_valid_syntax=eregi($exp, $email);

   // get mx addresses by getmxrr
   if(function_exists('getmxrr')){
    $b_mx_avail=getmxrr( $hostname, $mx_records, $mx_weight );
   }else{
     $b_mx_avail=getmxrrA($hostname, $mx_records, $mx_weight);
   }
   $b_server_found=0;

   if($b_valid_syntax && $b_mx_avail){
     // copy mx records and weight into array $mxs
     $mxs=array();

     for($i=0;$i<count($mx_records);$i++){
       $mxs[$mx_weight[$i]]=$mx_records[$i];
     }

     // sort array mxs to get servers with highest prio
     ksort ($mxs, SORT_NUMERIC );
     reset ($mxs);

     while (list ($mx_weight, $mx_host) = each ($mxs) ) {
       if($b_server_found == 0){

         //try connection on port 25
         $fp = @fsockopen($mx_host,25, $errno, $errstr, 2);
         if($fp){
           $ms_resp="";
           // say HELO to mailserver
           $ms_resp.=send_command($fp, "HELO microsoft.com");

           // initialize sending mail
           $ms_resp.=send_command($fp, "MAIL FROM:<support@microsoft.com>");

           // try receipent address, will return 250 when ok..
           $rcpt_text=send_command($fp, "RCPT TO:<".$email.">");
           $ms_resp.=$rcpt_text;

           if(substr( $rcpt_text, 0, 3) == "250")
             $b_server_found=1;

           // quit mail server connection
           $ms_resp.=send_command($fp, "QUIT");

         fclose($fp);

         }

       }
    }
  }
  return $b_server_found;
}

?>
