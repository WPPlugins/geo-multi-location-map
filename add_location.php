<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
function gmlm_form_page_handler()
{
	global $wpdb;
	$table = $wpdb->prefix."posts";
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
<script src="<?php echo "http://maps.googleapis.com/maps/api/js?key=$api&sensor=false"; ?>"></script><?php
	$uploads1 = wp_upload_dir();
	$upload_path1 = $uploads1['baseurl']; 
	wp_enqueue_script('js1');
	global $wpdb; 
	$wpdb->show_errors();
    $table_name = $wpdb->prefix . 'locations';
    $message = '';
    $notice = '';
    // this is default $item which will be used for new records
    $default = array(
		'id' => 0,
        'title' => '',
        'address' => '',
		'description' => '',
		'longitude' => '',
        'latitude' => '',
		'status' => '',
		'image' => '',
		'marker_img' => '',
		);
	if ( isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        
        $item = shortcode_atts($default, $_REQUEST);
       	$item_valid = gmlm_data_validate($item);
		if ($item_valid === true) {
		
			if ($item['id'] == 0) {
				if(!empty($_FILES['image']['name'])) {
				$supported_types = array('application/jpg','application/png','application/jpeg');
					 $arr_file_type = wp_check_filetype(basename($_FILES['image']['name']));
        			 $uploaded_type = $arr_file_type['type'];
					 $upload = wp_upload_bits($_FILES['image']['name'], null, file_get_contents($_FILES['image']['tmp_name']),null);
					 if(isset($upload['error']) && $upload['error'] != 0 ) {
               				$notice = __('There was an error uploading your file. The error is: ' . $upload['error'], 'gmlm_data');
           			 }else {
					 			$item['image']= $upload['url'];
			    				} 
				} if(!empty($_FILES['marker_img']['name'])) {
				// Marker image uploded //
				$supported_types = array('application/jpg','application/png','application/jpeg');
					
						
					 $arr_marker_type = wp_check_filetype(basename($_FILES['marker_img']['name']));
        			 $upl_type = $arr_marker_type['type'];
					 $up_marker = wp_upload_bits($_FILES['marker_img']['name'], null, file_get_contents($_FILES['marker_img']['tmp_name']),null);					  
						if(isset($up_marker['error']) && $up_marker['error'] != 0 or isset($up_marker['error']) && $up_marker['error'] != 0  ) {
               				
				 			$notice = __('There was an error uploading your file. The error is: ' . $up_marker['error'], 'gmlm_data');
           			 	} else {
					 	
							$item['marker_img']= $up_marker['url'];
			    			
                		} 
				}
				$datum = $wpdb->get_results("SELECT * FROM $table_name WHERE address= '".$_REQUEST['address']."'");
				if($wpdb->num_rows > 0) {
								$notice = __('You can not add Dublicate Location');
				}else {
							$result = $wpdb->insert($table_name, $item); 
							$item['id'] = $wpdb->insert_id;
					}
			
               if ($result) {
                    $message = __('Item was successfully saved', 'gmlm_data');
                } 
            } else {
			
			if(!empty($_FILES['image']['name'])) {
					 $arr_file_type = wp_check_filetype(basename($_FILES['image']['name']));
        			 $uploaded_type = $arr_file_type['type'];
					 $upload = wp_upload_bits($_FILES['image']['name'], null, file_get_contents($_FILES['image']['tmp_name']),null);
					if(isset($upload['error']) && $upload['error'] != 0) {
               				$notice = __('There was an error uploading your file. The error is: ' . $upload['error'], 'gmlm_data');
           			 } else {
					 		$item['image']= $upload['url'];
			    			} 
			}else
			{
				 $item['image']= trim(esc_url($_REQUEST['oldimage']));
				 
			}
			if(!empty($_FILES['marker_img']['name'])) {
					 $arr_file_type = wp_check_filetype(basename($_FILES['marker_img']['name']));
        			 $uploaded_type = $arr_file_type['type'];
					 $upload = wp_upload_bits($_FILES['marker_img']['name'], null, file_get_contents($_FILES['marker_img']['tmp_name']),null);
					if(isset($upload['error']) && $upload['error'] != 0) {
               				$notice = __('There was an error uploading your file. The error is: ' . $upload['error'], 'gmlm_data');
           			 } else {
					 		$item['marker_img']= $upload['url'];
			    			
						} 
			}else
			{
				 $item['marker_img']= trim(esc_url($_REQUEST['oldmarker']));
				
			}
			$result = $wpdb->update($table_name, $item, array('id' => $item['id']));
			if ($result === FALSE) {
					$notice = __('There was an error while updating item', 'gmlm_data');
                } else {
                      $message = __('Item was successfully updated', 'gmlm_data');
                }
            } 
        } else {
            $notice = $item_valid;
        }
    }
    else {
        $item = $default;
        if (isset($_REQUEST['id'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", intval($_REQUEST['id'])), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('Item not found', 'gmlm_data');
            }
        }
    }
    // here we adding our custom meta box
    add_meta_box('location_form_meta_box', 'Locations data', 'gmlm_data_form_meta_box_handler', 'location', 'normal', 'default');
    ?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Location', 'gmlm_data')?> <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=locations');?>"><?php _e('back to list', 'gmlm_data')?></a>
    </h2>

    <?php if (!empty($notice)): ?>
    <div id="notice" class="error"><p><?php echo $notice ?></p></div>
    <?php endif;?>
    <?php if (!empty($message)): ?>
    <div id="message" class="updated"><p><?php echo $message ?></p></div>
    <?php endif;?>

    <form id="form" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
        <?php /* NOTICE: here we storing id to determine will be item added or updated */ ?>
        <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>
		 <input type="hidden" name="oldimage" value="<?php echo $item['image'] ?>"/>
		 <input type="hidden" name="oldmarker" value="<?php echo $item['marker_img'] ?>"/>
		
        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content" style="width:50%;height:600px">
                    <?php /* And here we call our custom meta box */ ?>
                    <?php do_meta_boxes('location', 'normal', $item); ?>
                    <input type="submit" value="<?php _e('Save', 'gmlm_data')?>" id="submit" class="button-primary" name="submit">
					<a href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=locations');?>"><input type="button" value="<?php _e('Cancel', 'gmlm_data')?>" id="cancel" class="button-primary" name="Cancel"></a>
    
				</div>
				 <div id="post-body-content" style="width:50%;height:530px;background-color:#CCCCCC" >
                  <div id="dvMap" style="width:100%; height: 100%;"></div>
				</div>
            </div>
        </div>
    </form>
</div>
<?php
}
/**
 *  Form for adding andor editing row
 * ============================================================================
 *
 * In this part you are going to add admin page for adding andor editing items
 * You cant put all form into this function, but in this example form will
 * be placed into meta box, and if you want you can split your form into
 * as many meta boxes as you want
 **/

function gmlm_data_form_meta_box_handler($item)
{
?>
<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
    <tbody>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="title"><?php _e('Title', 'gmlm_data')?></label>
        </th>
        <td>
		 <input id="status" name="status" type="hidden"  value="activate">
            <input id="title" name="title" type="text" style="width: 95%" value="<?php echo esc_attr($item['title'])?>"
                   size="50" class="code" placeholder="<?php _e('Location Title', 'gmlm_data')?>" required>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="address"><?php _e('Address', 'gmlm_data')?></label>
        </th>
        <td>
		<textarea id="address" name="address"  style="width: 95%" class="code" required ><?php echo esc_textarea($item['address'])?></textarea>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="decription"><?php _e('Description', 'gmlm_data')?></label>
        </th>
        <td>
     <textarea id="description" name="description" style="width: 95%" class="code" required><?php echo esc_textarea($item['description'])?></textarea>
        </td>
    </tr>
	<tr class="form-field">
        <th valign="top" scope="row">
            <label for="latitude"><?php _e('Latitude', 'gmlm_data')?></label>
        </th>
        <td>
            <input id="latitude" name="latitude" type="text" style="width: 95%" value="<?php echo esc_attr($item['latitude'])?>"
                   size="50" class="code" placeholder="<?php _e('Latitude', 'gmlm_data')?>" required>
        </td>
    </tr>
	<tr class="form-field">
        <th valign="top" scope="row">
            <label for="longitude"><?php _e('Longitude', 'gmlm_data')?></label>
        </th>
        <td>
            <input id="longitude" name="longitude" type="text" style="width: 95%" value="<?php echo esc_attr($item['longitude'])?>"
                   size="50" class="code" placeholder="<?php _e('Longitude', 'gmlm_data')?>" required>
        </td>
    </tr>
	<tr class="form-field">
        <th valign="top" scope="row">
            <label for="image"><?php _e('Location Image', 'gmlm_data')?></label>
        </th>
        <td>
            <input id="image" name="image" type="file" style="width: 95%" value="<?php echo esc_url($item['image'])?>"
                   size="50" class="code" placeholder="<?php _e('Location Image', 'gmlm_data')?>" >
				   <?php echo esc_url($item['image'])?>
        </td>
    </tr>
	<tr class="form-field">
        <th valign="top" scope="row">
            <label for="image"><?php _e('Map Marker', 'gmlm_data')?></label>
        </th>
        <td>
            <input id="marker_img" name="marker_img" type="file" style="width: 95%" value="<?php echo esc_url($item['marker_img'])?>"
                   size="50" class="code" placeholder="<?php _e('Marker Image', 'gmlm_data')?>" >
				   <?php echo esc_url($item['marker_img'])?>
				   
        </td>
    </tr>
    </tbody>
</table>

<?php
}
/**
 * Simple function that validates data and retrieve bool on success
 * and error message(s) on error
 *
 * @param $item
 * @return bool|string
 */
function gmlm_data_validate($item)
{
    $messages = array();
    if (empty($item['title'])) $messages[] = __('title is required', 'gmlm_data');
	if (empty($item['address'])) $messages[] = __('address is required', 'gmlm_data');
	if (empty($item['description'])) $messages[] = __('description is required', 'gmlm_data');
	if (empty($item['longitude'])) $messages[] = __('longitude is required', 'gmlm_data');
    if (empty($item['latitude'])) $messages[] = __('latitude is required', 'gmlm_data');
	//if(!empty($item['age']) && !absint(intval($item['age'])))  $messages[] = __('Age can not be less than zero');
    //if(!empty($item['age']) && !preg_match('/[0-9]+/', $item['age'])) $messages[] = __('Age must be number');
    //...
	if (empty($messages)) return true;
    return implode('<br />', $messages);
}
?>