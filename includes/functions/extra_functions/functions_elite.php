<?php
//This creates an email to us when someone signs up
function create_account_email($data){

}



function get_price($for, $pID, $account_status){
    global $db, $currencies, $products_rate_1,$products_rate_2,$products_rate_3,$products_web_price,$products_price,$products_rrp;
    $rs=$db->execute("SELECT show_price FROM product_extra_fields WHERE products_id = $pID");
    if($rs->EOF)return '';
    $p=strpos($rs->fields['show_price'],$for);
    if($p===FALSE)return '<div class="cui">Contact us for pricing and availability information.</div>';
    $output='';
    switch(substr($rs->fields['show_price'],$p,1)){
        case 'A': //A
            if($account_status==0){
                if($products_rrp>0){
                    $output = '<div class="rrp">Recomended Retail Price '.$currencies->format($products_rrp).'</div>';
                    $output .= '<div class="yp">Web Price '.$currencies->format($products_web_price).'</div>';
                    $output .= '<div class="cui">Contact us for availability information.</div>';
                }else{
                    if($products_web_price>0){
                        $output = '<div class="rp">Retail Price '.$currencies->format($products_web_price).'</div>';
                        $output .= '<div class="cui">Contact us for availability information.</div>';
                    }else{
                        $output = '<div class="cui">Contact us for pricing and availability information.</div>';
                    }
                }
            }else{
                if($products_rrp>0){
                    $output = '<div class="rrp">Recomended Retail Price '.$currencies->format($products_rrp).'</div>';
                    //$output .= '<div class="yp">Your Price '.$currencies->format($products_web_price).'</div>';
                    $output .= '<div class="cui">Your account is pending approval please <br />contact us for your price and availability information.</div>';
                }else{
                    if($products_web_price>0){
                        $output = '<div class="rp">Retail Price '.$currencies->format($products_web_price).'</div>';
                        $output .= '<div class="cui">Your account is pending approval please <br />contact us for your price and availability information.</div>';
                    }else{
                        $output = '<div class="cui">Contact us for pricing and availability information.</div>';
                    }
                }
            }
            break;
            case 'B':
            case 'C':
            case 'D':
                if($account_status==0){
                    if($products_rrp>0){
                        $output = '<div class="rrp">Recomended Retail Price '.$currencies->format($products_rrp).'</div>';
                        $output .= '<div class="cui">Your account is pending approval please <br />contact us for your price and availability information.</div>';
                    }else{
                        if($products_web_price>0){
                            $output = '<div class="rp">Retail Price '.$currencies->format($products_web_price).'</div>';
                            $output .= '<div class="cui">Your account is pending approval please <br />contact us for your price and availability information.</div>';
                        }else{
                            $output = '<div class="cui">Your account is pending approval please <br />contact us for your price and availability information.</div>';
                        }
                    }
                }else{
                    switch(substr($rs->fields['show_price'],$p,1)){
                        case 'C':
                            $trade_rate = $products_rate_2;
                            break;
                        case 'B':
                            $trade_rate = $products_rate_1;
                            break;
                        case 'D':
                            $trade_rate = $products_rate_3;
                    }
                    if($products_rrp>0){
                        $output = '<div class="rrp">Recomended Retail Price '.$currencies->format($products_rrp).'</div>';
                        if($trade_rate>0){
                            $output .= '<div class="yp">Your Price '.$currencies->format($trade_rate).'</div>';
                            $output .= '<div class="cua">Contact us for availability information.</div>';
                        }else{
                            $output .= '<div class="cui">contact us for your price and availability information.</div>';
                        }
                    }else{
                        $output = '<div class="rp">Retail Price '.$currencies->format($products_web_price).'</div>';
                        if($trade_rate>0){
                            $output .= '<div class="rp">Your Price '.$currencies->format($trade_rate).'</div>';
                            $output .= '<div class="cui">Contact us for availability information.</div>';
                        }else{
                            $output .= '<div class="cui">contact us for your price and availability information.</div>';
                        }
                    }
                }
                break;
    }
    return $output;
}

function vat_split($gross){
    $values['net'] = $gross/1.2;
    $values['vat'] = $gross - $values['net'];
    $values['total'] = $gross;
    return $values;
}

//Return trades array in format for using in a dropdown list
function trades_pulldown_array(){
    global $trade_types_array;
    $ta = array();
    $ta[]=array('id'=>'0', 'text'=>'Please Select');
    foreach($trade_types_array as $idx => $trade){
        $ta[]=array('id'=>$trade,'text'=>$trade);
    }
    return $ta;
}

//Return array of product id's where their model has the same base code'
function get_family_items($pID){
  global $db;
  $family_array = array();
  $res = $db->Execute("SELECT products_model FROM " . TABLE_PRODUCTS . " WHERE products_id = '" . $pID . "'");
  if(!$res->EOF){
    $base_model = substr($res->fields['products_model'],0,8);
    $res = $db->Execute("SELECT products_id FROM " . TABLE_PRODUCTS . " WHERE products_model LIKE '" . $base_model . "%'");
    while(!$res->EOF){
      $family_array[] = $res->fields['product_id'];
    }
  }
}

function get_slideshow_array(){
  $image_array = array();
  $base_name = DIR_WS_IMAGES . "slideshow/*.{jpg,gif,png}";
  foreach(glob($base_name, GLOB_BRACE) as $file){
    $image_array[] = $file;
  }
  return $image_array;
}

function put_slideshow(){
  $images = get_slideshow_array();
  if(sizeof($images)>0){
    ?>
    <div class="slideshow">
    <?php
    //var_dump($images);
    echo '<div>';
    echo zen_image($images[0],'','',300, '');
    echo '</div>';

    for($pos=1; $pos < sizeof($images); $pos++){
      echo zen_image($images[$pos],'','',300);
    }
  }
  ?>
  </div>
  <script type="text/javascript">
  $(document).ready(function() {
      $('.slideshow').cycle({
     fx: 'zoom',
     delay: -2000,
     speed: 4000
    });
  });
  </script>
  <?php
}

//Return model ID from given product ID
function mid_to_pid($mID){
  $mID = zen_db_prepare_input($mID);
  global $db;
  $sql = "select products_id, products_model from " . TABLE_PRODUCTS . " where products_model = '$mID' LIMIT 1";
  $result = $db->Execute($sql);

  if ($result->RecordCount() > 0) {
    return $result->fields['products_id'];
  }
  return NULL;
}

function family_quick_count($mID){
  global $db;
  $base_code = substr($mID,0,8);
  $sql = "select COUNT(products_model)as total from " . TABLE_PRODUCTS . " where products_model LIKE '$base_code%' AND products_status = 1 LIMIT 1";
  $result = $db->Execute($sql);
  if(!$result->EOF)  return $result->fields['total'];
  return 0;
}

//Checks if a 'family caption' is to be used instead of normal family count for the family info tooltip
//Returns the string which is to be used in the family info tooltip
//input - model ID
function get_family_caption($mID){
  $mID = zen_db_prepare_input($mID);
  global $db;
  $sql = "SELECT fc.caption
          FROM products p
          JOIN product_extra_fields pef
            ON pef.products_id = p.products_id
          JOIN family_captions fc
            ON fc.id = pef.family_caption
          WHERE p.products_model = '$mID'";
  $result = $db->Execute($sql);

  if ($result->RecordCount() > 0) {
    return $result->fields['caption'];
  }
  return NULL;
}

//Determins if the item is on promition and if so returns a fromated string
//Inputs: $pid -> product id
//        $page -> Use the variable $current_page when calling this function - Purpose - to have different formating for the product info page and the product listing page
function product_promotion($pid, $page){
  global $db, $currencies;
  $out = '';
  $sql = "SELECT now_price FROM " . TABLE_PRODUCTS_EXTRA_FIELDS . " WHERE products_id = $pid";
  $result = $db->Execute($sql);
  if(!$result->EOF){
    if($result->fields['now_price']!=NULL && $result->fields['now_price']!='' && $result->fields['now_price']!=0){
      $was_price = format_now_price($result->fields['now_price'],'price');

      if($page == 'product_info'){
        //Product info page
      $out = "<div id='promotion_box'><br />".PROMOTION_PREFIX . "<br /><div id='promotion_name'>" . PROMOTION_NAME . "</div>";
      if($was_price != ''){
        $out .= "<div id='promotion_was'>Was " . $currencies->display_price(zen_get_products_base_price($pid),0) . " </div>";
        $out .= "<div id='promotion_now'>Now " . $currencies->display_price(format_now_price($result->fields['now_price'],'price'),0)."</div>";
      }
      $out .= "<div id='now_price_text'>". format_now_price($result->fields['now_price'],'text') . "</div> <div>".PROMOTION_POSTFIX."</div></div>";
      }elseif($page=='promotions' || $page=='advanced_search_result' || $page=='products_all' || $page=='products_new'){
        //Promotions listing, Advanced Search Results, Products All and Products New pages
        if($was_price!=""){
          $out = "<div id='promotion_was_l'>Was " . $currencies->display_price(zen_get_products_base_price($pid),0) . " </div>";
          $out .="<div id='promotion_now_l'>Now " .$currencies->display_price(format_now_price($result->fields['now_price'],'price'),0)."</div><br />";
        }
        $out .= "<div id='promotion_now_l'>". format_now_price($result->fields['now_price'],'text') . "</div>";

      }elseif($page=='index'){
        //Product listing page
        if($was_price!=""){
          $out = "<div id='promotion_was_l'>Was " . $currencies->display_price(zen_get_products_base_price($pid),0) . " </div><div id='promotion_now_l'>Now " .$currencies->display_price(format_now_price($result->fields['now_price'],'price'),0)."</div><br />";
        }
        $out .= "<div id='now_price_text'>". format_now_price($result->fields['now_price'],'text') . "</div>";

      }else{
        $out = "Error - no format for call with page $page";
      }
    }
  }
  return $out;
}

//This function splits the promotion now price into the two parts, price or text
//$part is either 'price' or 'text' and this determines the part to be returned
function format_now_price($now_price_text, $part='price'){
  $now_price_text = str_replace('£','',$now_price_text);
  if(strpos($now_price_text, '*')===FALSE){
    if($part=='price'){
      return $now_price_text;
    }else{
      return '';
    }
  }
  if($part=='price'){
    return substr($now_price_text,0,strpos($now_price_text,'*'));
  }else{
    return substr($now_price_text,strpos($now_price_text,'*')+1);
  }
}

//Counts number if items that are on promotion
function count_promotion_items(){
  global $db;
  $sql="SELECT * FROM " . TABLE_PRODUCTS_EXTRA_FIELDS . " WHERE now_price > 0";
  $result = $db->Execute($sql);
  $num_rows = $result->RecordCount();
  return $num_rows;
}

//Returns the image xref for the given orginal
function get_image_xref($orginal){
  global $db;
  $sql = "SELECT xref FROM image_xref WHERE orginal = '$orginal'";
  $result = $db->Execute($sql);
  if(!$result->EOF){
    return $result->fields['xref'];
  }
  return $orginal;
}

///This is not used ????? I think/////////////////////////////////////////////////////
function convert($str,$ky='145785421'){                                             //
  if($ky=='')return $str;                                                           //
  //$ky=str_replace(chr(32),'',$ky);//removes spaces
  //if(strlen($ky)<8)exit('key error');//Ensure key is greater than 8 digits
  $kl=strlen($ky)<32?strlen($ky):32;                                                //
  $k=array();for($i=0;$i<$kl;$i++){
  $k[$i]=ord($ky{$i})&0x1F;}
  $j=0;for($i=0;$i<strlen($str);$i++){                                               //
  $e=ord($str{$i});                                                                  //
  $str{$i}=$e&0xE0?chr($e^$k[$j]):chr($e);                                           //
  $j++;$j=$j==$kl?0:$j;}                                                             //
  return $str;                                                                       //
}                                                                                    //
///////////////////////////////////////////////////////////////////////////////////////

function encode($string,$key='145785421') {
    $key = sha1($key);
    $strLen = strlen($string);
    $keyLen = strlen($key);
    for ($i = 0; $i < $strLen; $i++) {
        $ordStr = ord(substr($string,$i,1));
        if ($j == $keyLen) { $j = 0; }
        $ordKey = ord(substr($key,$j,1));
        $j++;
        $hash .= strrev(base_convert(dechex($ordStr + $ordKey),16,36));
    }
    return $hash;
}

function decode($string,$key='145785421') {
    $key = sha1($key);
    $strLen = strlen($string);
    $keyLen = strlen($key);
    for ($i = 0; $i < $strLen; $i+=2) {
        $ordStr = hexdec(base_convert(strrev(substr($string,$i,2)),36,16));
        if ($j == $keyLen) { $j = 0; }
        $ordKey = ord(substr($key,$j,1));
        $j++;
        $hash .= chr($ordStr - $ordKey);
    }
    return $hash;
}

if(!function_exists(str_split)){
    function str_split ($string) {

    // don't proceed if the string is empty
    if (empty ($string) || strlen ($string) < 1) return false;

    // check to see if PHP 5+ really exists
    // if so, use it's str_split function. :)
    if (function_exists ('str_split')) {
        return str_split ($string);
    }

    // PHP 4 version
    // we'll store the result in this array
    $arr_string = array();
    // iterate over all the string's characters
    for ($i=0; $i<strlen($string); $i++) {
        // push current character onto our return array
        $arr_string[] = $string{$i};
    }
    // return finished array
    return $arr_string;
}
}
?>
