<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $wpdb;
$table = $wpdb->prefix."posts";
$table_name = $wpdb->prefix."locations";
$posttitle = 'Geo Multi Location Map';
$postid = $wpdb->get_var( "SELECT ID FROM $table WHERE post_title = '" . $posttitle . "'" );

$size = get_post_meta($postid , 'map_size', true);
$radius = get_post_meta($postid , 'map_redius', true);
$api = get_post_meta($postid , 'map_apikey', true);
if($api == "")
$api = "AIzaSyBMAt42KN__1_aVtPo9lx10tBMo2CMOyxE";
if($size == "")
$size = GMLM_ZOOM_SIZE;	
if($radius == "")
$radius = GMLM_RADIUS; ?>
<script type="text/javascript">
    var map_size = <?php echo json_encode($size); ?>;
	var map_radius = <?php echo json_encode($radius); ?>;
	var map_api = <?php echo json_encode($api); ?>;
</script>
<?php
wp_enqueue_script('js2');
global $wpdb;
$result = $wpdb->get_results("SELECT * FROM  $table_name where status='activate'");
?>
<script type="text/javascript">
 	var markers =<?php echo json_encode($result); ?>;
 </script>
<?php $uploads = wp_upload_dir(); 
	  $imgdefurl=plugins_url('image/globe-location.png' , __FILE__ ); ?>
<div class="omex_contain">
	<div class="div_left">
		<div class="omex_list_sidebar">
		<ul>
			<li></li>
			<?php global $wpdb;
			$result = $wpdb->get_results( "SELECT * FROM $table_name where status='activate'");
			  $cn=0;
			  foreach($result as $row)
 			  { $cn++; ?>
			<script> var imgpath = '<?php echo $urlpath;?>'; 
					var imgascpath = '<?php echo $imgdefurl;?>'; 
			</script>
			<li>
				<div class="omex_box_list"  id="<?php echo $row->id ; ?>">
				<div id="list_contain">
				<div id="omex_list_head">
				<div class="imgContainer">
					<?php
					if($row->image != ''){
							echo '<img src="'.$row->image .'" style="height:100px; width:100%" asc="'.$row->image .'" href="#">';
					}else {
							echo '<img src="' . $imgdefurl . '" style="height:100px; width:100%" > '; } ?>
				</div>
				<div class="textContainer">
						<span><a href="#" ><?php echo $cn; ?> .&nbsp; <?php echo $row->title."<br>";?></a> </span>
						<span><address><?php  echo $row->address."<br>";?></address> </span>
				</div>
				</div>
				</div>
				<div class="omex_desc"   id="<?php echo $row->id ; ?>desc" style="width:100%;height:auto;"><p><?php echo $row->description."<br>";?></p></div>
			</li>
			<?php } ?>
		</ul>
	</div>
</div>
<div class="div_right">
	<div class="map_area"> 
		<div id="dvMap" style="height: 500px; width:auto">
	</div>
</div>
</div>
</div>
