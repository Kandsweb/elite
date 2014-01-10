<?php

//
// To see the PHP example in action, please do the following steps.
//
// 1. Open test/js/uploader-demo-jquery.js file and change the request.endpoint
// parameter to point to this file.
//
//  ...
//  request: {
//    endpoint: "../server/php/example.php"
//  }
//  ...
//
// 2. As a next step, make uploads and chunks folders writable.
//
// 3. Open test/jquery.html to see if everything is working correctly,
// the uploaded files should be going into uploads folder.
//
// 4. If the upload failed for any reason, please open the JavaScript console,
// if this does not help please read the excellent documentation we have for you.
//
// https://github.com/valums/file-uploader/blob/master/readme.md
//

require_once '../includes/functions/functions_php5.php';    //Only required for Elite as they and using php 4 on server

// Include the uploader class
require_once 'qqFileUploader.php';

$uploader = new qqFileUploader();

// Specify the list of valid extensions, ex. array("jpeg", "xml", "bmp")
$uploader->allowedExtensions = array('jpeg', 'jpg', 'gif', 'png');

// Specify max file size in bytes.
$uploader->sizeLimit = 10 * 1024 * 1024;

// Specify the input name set in the javascript.
$uploader->inputName = 'qqfile';

// If you want to use resume feature for uploader, specify the folder to save parts.
$uploader->chunksFolder = 'chunks';

// Call handleUpload() with the name of the folder, relative to PHP's getcwd()
if($_SERVER['HTTP_HOST']=='localhost'){
  $target_path = $_SERVER['DOCUMENT_ROOT'].'Elite_KandS/images/slideshow/';    //FOR LOCALHOST ONLY
}else{
  $target_path = $_SERVER['DOCUMENT_ROOT'].'images/slideshow/';    //FOR SERVER ONLY
}

//KandS Work out the file name to use
//$name = get_image_file_name($_GET['reg']);

$org = $uploader->getName();
$ext = strtolower(pathinfo($org, PATHINFO_EXTENSION));

$result = $uploader->handleUpload($target_path, $org . '.' . $ext);


if($_SERVER['HTTP_HOST']=='localhost'){
      require_once($_SERVER['DOCUMENT_ROOT'].'/Elite_KandS/eoffice/ajax/'.'kas_image_handler.php');//FOR LOCALHOST ONLY
}else{
  require_once($_SERVER['DOCUMENT_ROOT'].'/eoffice/ajax/'.'kas_image_handler.php');//FOR SERVER
}

// To save the upload with a specified name, set the second parameter.
// $result = $uploader->handleUpload('uploads/', md5(mt_rand()).'_'.$uploader->getName());

// To return a name used for uploaded file you can use the following line.
$result['uploadName'] = $uploader->getUploadName();

header("Content-Type: text/plain");
echo json_encode($result);

function get_image_file_name($base){
    //replace spaces with hypnems
    $base = preg_replace("/ /",'-',$base);
    $last=1;
    $gotFiles=false;
    //Loop through all images with the given name
    foreach(glob('../../images/products/'.$base.'{*.jpg, *.gif, *.png}', GLOB_BRACE)as $file){
        $gotFiles=true;
        $f=pathinfo($file, PATHINFO_FILENAME);
        $p=strrchr($f,'_');
        if($p!==FALSE){
            $c=trim($p,'_');
            switch(true){
                case $c<$last:
                    return $base.'_'.$last;
                    break;
                case $c==$last:
                    $last++;
                    break;
            }
        }
    }
    if($gotFiles){
        //Only base found - no additional images
        return $base.'_'.$last;
    }
    return $base;
}