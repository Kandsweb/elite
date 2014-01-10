<?php
  if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
$autoLoadConfig[0][] = array('autoType'=>'class', 'loadFile'=> 'product_data.php');

//Filter options class, used in side box
$autoLoadConfig[0][] = array('autoType'=>'class', 'loadFile'=> 'class.option_filters.php');
$autoLoadConfig[193][] = array('autoType'=>'classInstantiate',
                'className'=>'OptionFilter',
                'objectName'=>'OptionFilter',
                'checkInstantiated'=>true,
                'classSession'=>true);
$autoLoadConfig[194][] = array('autoType'=>'objectMethod',
                'objectName'=>'OptionFilter',
                'methodName' => 'init');
?>
