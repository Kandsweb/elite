<?php
  define('TEXT_PRODUCTS_MANUFACTURER_CODE', 'Manufactures Stock Code:');
  define('TEXT_PRODUCTS_DIMENSIONS', 'Product<br />Dimensions');
  define('TEXT_PRODUCTS_DIMENSIONS_HEIGHT', 'Height:');
  define('TEXT_PRODUCTS_DIMENSIONS_WIDTH', 'Width:');
  define('TEXT_PRODUCTS_DIMENSIONS_DEPTH', 'Depth:');
  define('TEXT_PRODUCTS_DIMENSIONS_LENGTH', 'Length:');
  define('TEXT_PRODUCTS_BULBS', 'Lamps');
  define('TEXT_PRODUCTS_BULBS_QTY', 'Qty:');
  define('TEXT_PRODUCTS_BULBS_CAP', 'Cap:');
  define('TEXT_PRODUCTS_BULBS_WATTS', 'Wattage:');
  define('TEXT_PRODUCTS_BULBS_TYPE', 'Type:');
  define('TEXT_PRODUCTS_IP_RATING', 'IP Rating:');
  define('TEXT_PRODUCTS_CM', 'mm');

  function get_bulb_array(){
    return array(array('id'=>0, 'text'=>'N/A'),
                 array('id'=>1, 'text'=>'GLS'),
                 array('id'=>2, 'text'=>'Candle'),
                 array('id'=>3, 'text'=>'MR16'),
                 array('id'=>4, 'text'=>'Halagon'),
                );
  }

  function get_cap_array(){
    return array(array('id'=>0, 'text'=>'N/A'),
                 array('id'=>1, 'text'=>'BC'),
                 array('id'=>2, 'text'=>'SBC'),
                 array('id'=>3, 'text'=>'ES'),
                 array('id'=>4, 'text'=>'SES'),
                 array('id'=>10, 'text'=>'GU10'),
                 array('id'=>5, 'text'=>'G9'),
                 array('id'=>6, 'text'=>'G4'),
                 array('id'=>7, 'text'=>'L1 Tube'),
                 array('id'=>8, 'text'=>'L1 Spot'),
                 array('id'=>9, 'text'=>'L1 Candle'),
                );
  }

?>
