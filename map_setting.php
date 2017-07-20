<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
add_meta_box('map_location_setting', 'Map Location Setting', 'map_location_setting', 'locations_setting', 'normal', 'default');
$message = '';

if ( isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__)))
{	
global $wpdb;
$table = $wpdb->prefix."posts";
$posttitle = 'Geo Multi Location Map';
$postid = $wpdb->get_var( "SELECT ID FROM $table WHERE post_title = '" . $posttitle . "'" );
	
	update_post_meta( $postid , 'map_size', intval($_POST['map_size']));
	update_post_meta( $postid , 'map_redius', intval($_POST['map_redius']));
	update_post_meta( $postid , 'map_apikey', esc_attr($_POST['map_apikey']) );
	
	$message = __('Map Setting sucessfully saved', 'gmlm_data');
	 if (!empty($message)): ?>
    <div id="message" class="updated"><p><?php echo $message ?></p></div>
 <?php endif;
}?>
<form id="form" method="POST" enctype="multipart/form-data">
	<div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content" style="width:100%;height:500px">
                    <?php /* And here we call our custom meta box */ ?>
                    <?php do_meta_boxes('locations_setting', 'normal', $item); ?>
                    <input type="submit" value="<?php _e('save changes', 'gmlm_data')?>" id="submit" class="button-primary" name="submit">
			 	</div>
				
			</div>
     </div>
<?php
function map_location_setting() {
	global $wpdb;
	$table = $wpdb->prefix."posts";
	$posttitle = 'Geo Multi Location Map';
	$postid = $wpdb->get_var( "SELECT ID FROM $table WHERE post_title = '" . $posttitle . "'" );

	$size = get_post_meta($postid , 'map_size', true);
	$radius = get_post_meta($postid , 'map_redius', true);
	$api = get_post_meta($postid , 'map_apikey', true);?>
	<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
    	<tbody>
    		<tr class="form-field">
			<input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
        		<th valign="top" scope="row"><label for="map_size"><?php _e('Map Zoom Size', 'gmlm_data')?></label></th>
        		<td>
					<input id="map_size" name="map_size" type="text" style="width: 95%" value="<?php echo esc_attr($size) ;?>"
                   size="6" class="code" placeholder="<?php _e('12', 'gmlm_data')?>" >
				    <i style="color:#CCCCCC">Optional, If not provided, standard size(12)will be used.</i>
				</td>
			</tr>
   			<tr class="form-field">
        		<th valign="top" scope="row"><label for="radius"><?php _e('Location Radius', 'gmlm_data')?></label></th>
        		<td>
    				<input id="radius" name="map_redius" type="text" style="width: 95%" value="<?php echo esc_attr($radius); ?>" 
					size="6" class="code" placeholder="<?php _e('5', 'gmlm_data')?>" >
				   <i style="color:#CCCCCC">Optional, default is 0 km.</i>
        		</td>
    		</tr>
			<tr class="form-field">
        		<th valign="top" scope="row"><label for="api_key"><?php _e('GeoCoding Google API Key', 'gmlm_data')?></label></th>
        		<td>
            		<input id="api_key" name="map_apikey" type="text" style="width: 95%" value="<?php echo esc_attr($api); ?>"
                   size="200" class="code" placeholder="<?php _e('Your Map API Key', 'gmlm_data')?>" >
				   <i style="color:#CCCCCC">Optional, But you should use a geo coding api key to track each geo coding request. It can be useful if 		                    you want to extends geo coding request limits.</i>
        		</td>
    		</tr>
		</tbody>
</table>
<?php } ?>