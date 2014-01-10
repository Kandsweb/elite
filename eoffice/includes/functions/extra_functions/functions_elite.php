<?php

  function get_filter_option_array($foption_group, &$foptions_array){
    global $db;
    $sql = "SELECT * FROM " . TABLE_FILTER_OPTIONS . " WHERE foptions_group = " . $foption_group . " ORDER BY foptions_sort";
    $foptions = $db->Execute($sql);

    while(!$foptions->EOF){
      $foptions_array[$foption_group][$foptions->fields['foptions_index']]= array('name' => $foptions->fields['foptions_name'], 'value' => $foptions->fields['foptions_value']) ;
      $foptions->MoveNext();
    }
  }


function zen_get_manufacturers($manufacturers_array = '', $have_products = false) {
  global $db;
  if (!is_array($manufacturers_array)) $manufacturers_array = array();

  if ($have_products == true) {
    $manufacturers_query = "select distinct m.manufacturers_id, m.manufacturers_name
                            from " . TABLE_MANUFACTURERS . " m
                            left join " . TABLE_PRODUCTS . " p on m.manufacturers_id = p.manufacturers_id
                            where p.manufacturers_id = m.manufacturers_id
                            and (p.products_status = 1
                            and p.products_quantity > 0)
                            order by m.manufacturers_name";
  } else {
    $manufacturers_query = "select manufacturers_id, manufacturers_name
                            from " . TABLE_MANUFACTURERS . " order by manufacturers_name";
  }

  $manufacturers = $db->Execute($manufacturers_query);

  while (!$manufacturers->EOF) {
    $manufacturers_array[] = array('id' => $manufacturers->fields['manufacturers_id'], 'text' => $manufacturers->fields['manufacturers_name']);
    $manufacturers->MoveNext();
  }

  return $manufacturers_array;
}

function convert($str,$ky='145785421'){
  if($ky=='')return $str;
  $ky=str_replace(chr(32),'',$ky);
  if(strlen($ky)<8)exit('key error');
  $kl=strlen($ky)<32?strlen($ky):32;
  $k=array();for($i=0;$i<$kl;$i++){
  $k[$i]=ord($ky{$i})&0x1F;}
  $j=0;for($i=0;$i<strlen($str);$i++){
  $e=ord($str{$i});
  $str{$i}=$e&0xE0?chr($e^$k[$j]):chr($e);
  $j++;$j=$j==$kl?0:$j;}
  return $str;
}

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

function get_image_xref($orginal){
  global $db;
  $sql = "SELECT xref FROM image_xref WHERE orginal = '$orginal'";
  $result = $db->Execute($sql);
  if(!$result->EOF){
    return $result->fields['xref'];
  }
  return $orginal;
}

?>
