<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
add_meta_box('gmlm_single_view_meta_box', 'Use Shortcode to view map on your site', 'gmlm_locations_view_meta_box_handler', 'gmlm_locations_view', 'normal', 'default'); ?>
<div class="wrap">
	<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Geo Locations', 'gmlm_data')?></h2><br />
	<div class="metabox-holder" id="poststuff">
            <div id="post-body">
				<div id="post-body-content">
                   <div id="view_meta1">
				   <?php do_meta_boxes('gmlm_locations_view', 'normal', $item); ?>
					</div>
				</div>
			</div>
	</div>
</div>
<?php
function gmlm_locations_view_meta_box_handler()
{
	echo "[geo_map]";
}
?>