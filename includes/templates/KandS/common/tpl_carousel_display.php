<?php
////////////////////////////////////////////////////////////////////////////////////
//
//    Produces a carousel from the images in $list_box_contents
//
//    Set the id and the class in the calling page
//
////////////////////////////////////////////////////////////////////////////////////
?>
<?php
  if ($title) {
  ?>
<?php echo $title; ?>
<?php
 }
 ?>
<ul id="<?php echo $carousel_id; ?>" class="<?php echo $carousel_class; ?>">
<?php
$items_count=0;
if (is_array($list_box_contents) > 0 ) {
 for($row=0;$row<sizeof($list_box_contents);$row++) {
   for($col=0;$col<sizeof($list_box_contents[$row]);$col++){
     $items_count++;
 ?>
    <li> <?php echo $list_box_contents[$row][$col]['text']; ?></li>
 <?php
   }
 }
 echo '</ul>';
}
 ?>

<script type="text/javascript">
jQuery(document).ready(function() {
    jQuery('#<?php echo $carousel_id;?>').jcarousel({
        auto: 0,
        animation: 1000,
        scroll: 3,
        <?php if($items_count >4){
         // echo "wrap: 'circular'";
        }
        ?>
    });
});
</script>
