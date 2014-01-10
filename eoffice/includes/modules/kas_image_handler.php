<?php

    $file_name = $uploader->getFileName();
    $image = new SimpleImage();
    $image->load($target_path . $file_name);
    $image->resizeFill(600,350);
    $image->imagesharpen();
    $image->save($target_path.$file_name);











class SimpleImage {

   var $image;
   var $image_type;

   function load($filename) {

      $image_info = getimagesize($filename);
      $this->image_type = $image_info[2];
      if( $this->image_type == IMAGETYPE_JPEG ) {

         $this->image = imagecreatefromjpeg($filename);
      } elseif( $this->image_type == IMAGETYPE_GIF ) {

         $this->image = imagecreatefromgif($filename);
      } elseif( $this->image_type == IMAGETYPE_PNG ) {

         $this->image = imagecreatefrompng($filename);
      }
   }
   function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {

      if( $image_type == IMAGETYPE_JPEG ) {
         $res = imagejpeg($this->image,$filename,$compression);
      } elseif( $image_type == IMAGETYPE_GIF ) {

         imagegif($this->image,$filename);
      } elseif( $image_type == IMAGETYPE_PNG ) {

         imagepng($this->image,$filename);
      }
      if( $permissions != null) {

         chmod($filename,$permissions);
      }
   }
   function output($image_type=IMAGETYPE_JPEG) {
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image);
      } elseif( $image_type == IMAGETYPE_GIF ) {

         imagegif($this->image);
      } elseif( $image_type == IMAGETYPE_PNG ) {

         imagepng($this->image);
      }
   }
   function getWidth() {
      return imagesx($this->image);
   }

   function getHeight() {
      return imagesy($this->image);
   }

   function resizeToHeight($height) {
      $ratio = $height / $this->getHeight();
      $width = $this->getWidth() * $ratio;
      $this->resize($width,$height);
   }

   function resizeToWidth($width) {
      $ratio = $width / $this->getWidth();
      $height = $this->getheight() * $ratio;
      $this->resize($width,$height);
   }

   function resizeToFit($width, $height){
         $ratioW = $width / $this->getWidth();
         $ratioH = $height / $this->getHeight();
         if($ratioW<$ratioH){
             $width = $this->getWidth() * $ratioW;
             $height = $this->getHeight() * $ratioW;
         }else{
           $width = $this->getWidth() * $ratioH;
             $height = $this->getHeight() * $ratioH;
         }
         $this->resizeFill($width, $height);
   }

   function resizeToMax($max) {
            $height = $this->getHeight();
            $width = $this->getWidth();
            if($height >= $width){
                $ratio = $max / $this->getHeight();
            }else{
                $ratio = $max / $this->getWidth();
            }
      $new_width = $this->getWidth()*$ratio;
      $new_height = $this->getHeight() * $ratio;
      $this->resize($new_width,$new_height);
   }


   function scale($scale) {
      $width = $this->getWidth() * $scale/100;
      $height = $this->getheight() * $scale/100;
      $this->resize($width,$height);
   }

   function resize($width,$height) {
      $new_image = imagecreatetruecolor($width, $height);
      imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
      $this->image = $new_image;
   }

  function resizeFill($box_width,$box_height) {
        $canvas = imagecreatetruecolor($box_width,$box_height);
        $bkgnd_color = imagecolorallocate($canvas, 238,238,238);
        imagefilledrectangle($canvas,0,0,$box_width,$box_height,$bkgnd_color);

        $height=$box_height;
        $width=$box_width;

        //Calculate size of image
        $ratioW = $width / $this->getWidth();
        $ratioH = $height / $this->getHeight();
        if($ratioW<$ratioH){
             $width = $this->getWidth() * $ratioW;
             $height = $this->getHeight() * $ratioW;
        }else{
           $width = $this->getWidth() * $ratioH;
             $height = $this->getHeight() * $ratioH;
        }
    //Calculate pos of image on background
    $h_pos = intval(($box_height - $height)/2);
    $w_pos = intval(($box_width - $width)/2);

    //Copy to canvas and resize
    imagecopyresampled($canvas,$this->image,$w_pos,$h_pos,0,0,$width,$height, $this->getWidth(), $this->getHeight());

        $this->image = $canvas;
   }

   function imagesharpen() {
        $matrix = array(
            array(-1, -1, -1),
            array(-1, 16, -1),
            array(-1, -1, -1),
        );
        $divisor = array_sum(array_map('array_sum', $matrix));
        $offset = 0;
        $res=imageconvolution($this->image, $matrix, $divisor, $offset);
        //$this->image = $image;
    }

   public function __destruct(){
        imagedestroy($this->image);
     }
}
?>
