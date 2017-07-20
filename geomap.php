<?php
defined( 'ABSPATH' ) or die( 'Direct access to file is not allowed' );
/*
Plugin Name: Geo multi location map
Plugin URI: https://wordpress.org/plugins/geo-multi-location-map/
Description:This is multi Location map added to your site and lets your visitors to start finding you quickly!.
Version: 1.1.2
Author: Omex Infotech
Author URI: http://www.omexinfotech.com/
*/
?><?php 
global $post_id;
if ( !defined('ABSPATH') )
    define('ABSPATH', dirname(__FILE__) . '/');

if (!defined('GMLM_ZOOM_SIZE'))
    define('GMLM_ZOOM_SIZE', 10);

if (!defined('GMLM_RADIUS'))
    define('GMLM_RADIUS', 0);

if (!defined('GMLM_PLUGIN_NAME'))
    define('GMLM_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));

if (!defined('GMLM_PLUGIN_DIR'))
    define('GMLM_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . GMLM_PLUGIN_NAME);

if (!defined('GMLM_PLUGIN_URL'))
    define('GMLM_PLUGIN_URL', WP_PLUGIN_URL . '/' . GMLM_PLUGIN_NAME);

if (!defined('GMLM_VERSION_KEY'))
    define('GMLM_VERSION_KEY', gmlm_version);

if (!defined('GMLM_VERSION_NUM'))
    define('GMLM_VERSION_NUM', '1.1.2');
	
register_activation_hook(__FILE__, 'gmlm_install');
register_deactivation_hook( __FILE__,'gmlm_pluginUninstall');
function gmlm_map_view()
{
	include("map_viewshortcode.php");
}
function gmlm_map_setting()
{
	include("map_setting.php");
}
function gmlm_shortcode() {
	ob_start();
	wp_enqueue_style('style2');?>
	<script src="<?php echo "http://maps.googleapis.com/maps/api/js?key=$api&sensor=false"; ?>"></script>
	<?php include("geomap_view.php");
	return ob_get_clean();
}
include("add_location.php");


/**
intialize file source
*/
 
define('CONCATENATE_SCRIPTS', false);
function gmlm_header_jscss_intialize()
{ 
	load_plugin_textdomain('gmlm_data', false, GMLM_PLUGIN_NAME);
	wp_register_style('style1', plugins_url('css/location.css',__FILE__ ));
	wp_register_style('style2', plugins_url('css/geomap.css',__FILE__ ));
	wp_register_script( 'js1', plugins_url('js/location.js',__FILE__ ));
	wp_register_script( 'js2', plugins_url('js/mapview.js',__FILE__ ));
	wp_register_script( 'js3', plugins_url('js/confrm.js',__FILE__ ));
	add_shortcode( 'geo_map', 'gmlm_shortcode' );	
	}
add_action('init', 'gmlm_header_jscss_intialize');

function gmlm_admin_menu()
{
		add_menu_page(__('Geo Locations', 'gmlm_data'), __('Geo Locations', 'gmlm_data'), 'activate_plugins', 'locations',         		'gmlm_page_handler','dashicons-location-alt',3);
    	add_submenu_page('locations', __('Geo Locations', 'gmlm_data'), __('Geo Locations', 'gmlm_data'), 'activate_plugins', 'locations', 'gmlm_page_handler');
    	add_submenu_page('locations', __('Add new', 'gmlm_data'), __('Add new', 'gmlm_data'), 'activate_plugins', 'locations_form', 'gmlm_form_page_handler');
		add_submenu_page('locations', __('Map ShortCode', 'gmlm_data'), __('Map ShortCode', 'gmlm_data'), 'activate_plugins', 'locations_view', 'gmlm_map_view');
		add_submenu_page('locations', __('Map Setting', 'm.aplocator_data'), __('Map Setting', 'gmlm_data'), 'activate_plugins', 'locations_setting', 'gmlm_map_setting');
}
add_action('admin_menu', 'gmlm_admin_menu');
function gmlm_pluginUninstall() {
		global $wpdb;
        $table = $wpdb->prefix."locations";
		$wpdb->query("DROP TABLE IF EXISTS $table");
		$posttitle = 'Geo Multi Location Map';
		$postid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_title = '" . $posttitle . "'" );
		delete_post_meta($postid , 'map_size');
		delete_post_meta($postid , 'map_redius');
		delete_post_meta($postid , 'map_apikey');
		$delete = $wpdb->get_var( "DELETE FROM $wpdb->posts WHERE ID = '" . $postid . "'" );
} 
function gmlm_install()
{
	$my_post = array(
    'post_title'    => 'Geo Multi Location Map',
    'post_content'  => 'Geo Map Post',
    'post_status'   => 'private',
    'post_author'   => 1,
    'post_category' => array( 8,39 )
	);
	//file_put_contents(__DIR__.'/my_loggg.txt', ob_get_contents());
	global $wpdb;
	$post_id = wp_insert_post( $my_post, $wp_error );
    $table_name = $wpdb->prefix . 'locations'; 
	$charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE " . $table_name . " (
      id int(11) NOT NULL AUTO_INCREMENT,
      title VARCHAR(100) NOT NULL,
	  address text NOT NULL,
	  description text NOT NULL,
	  latitude VARCHAR(100) NOT NULL,
      longitude VARCHAR(100) NOT NULL,
	  status VARCHAR(100) NOT NULL,
	  image VARCHAR(100) NOT NULL,
	  marker_img VARCHAR(100) NOT NULL,
      PRIMARY KEY  (id)
    )$charset_collate;";
	 $wpdb->show_errors();
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    if (get_option(LOCATER_VERSION_KEY) != LOCATER_VERSION_NUM) {
        update_option(LOCATER_VERSION_KEY, LOCATER_VERSION_NUM);
    }
    $wpdb->show_errors();
}
/*function cltd_example_update_db_check()
{
    global $geo_multi_gmlm_db_version;
    if (get_site_option('geo_multi_gmlm_db_version') != $geo_multi_gmlm_db_version) {
        gmlm_install();
    }
}*/
//add_action('plugins_loaded', 'cltd_example_update_db_check');
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
class Gmlm_List_Data_Table extends WP_List_Table
{
   function __construct()
    {
        global $status, $page;
        parent::__construct(array(
            'singular' => 'location',
            'plural' => 'locations',
        ));
    }
    
    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }
   
	function get_views() { 
	global $wpdb;
    $table_name = $wpdb->prefix . 'locations'; // do not forget about tables prefix
	$activate_count = intval($wpdb->get_var("SELECT COUNT(id) FROM $table_name where status='activate'"));
	$deactivate_count = intval($wpdb->get_var("SELECT COUNT(id) FROM $table_name where status='deactivate'"));
	$all = intval($wpdb->get_var("SELECT COUNT(id) FROM $table_name "));
    $status_links = array(
		'all' => sprintf('<a href="?page=%s&view=all">%s</a>', esc_attr($_REQUEST['page']), __('All('.$all.')', 'gmlm_data')),
		'activate' => sprintf('<a href="?page=%s&view=activate">%s</a>', esc_attr($_REQUEST['page']), __('Activate('.$activate_count.')', 'gmlm_data')),
		'deactivate' => sprintf('<a href="?page=%s&view=deactivate">%s</a>', esc_attr($_REQUEST['page']), __('Deactivate('.$deactivate_count.')',			'gmlm_data')),
        );
    return $status_links;
	}
	function column_title($item)
	{
		wp_enqueue_script('js3');
		$actionStatus="";
		$actionName="";
		if($item['status']=='activate')
		{
			$actionStatus="deactivate";
			$actionName="Deactivate";
			
		}else {
			$actionStatus="activate";
			$actionName="Activate";
		}
		
        $actions = array(
            'edit' => sprintf('<a href="?page=locations_form&id=%s">%s</a>', $item['id'], __('Edit', 'gmlm_data')),
			'delete' => sprintf("<a href='javascript:Confirm(".$item['id'].")'>%s</a>", __('Delete', 'gmlm_data')),
			$actionStatus => sprintf('<a href="?page=%s&action='.$actionStatus.'&id=%s">%s</a>',esc_attr( $_REQUEST['page']), $item['id'], __($actionName, 'gmlm_data')), );
       
        return sprintf('%s %s',
            $item['title'],
            $this->row_actions($actions)
        );
	 }
   
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id']
        );
    }
	function column_img1($item)
    {
	if($item['marker_img']!='')
	{
        return sprintf(
            '<img src="'.$item['marker_img'].'" style="height:50px;width:50px" name="marker_img" />'.'<a style="float:right;padding-right:160px" href="javascript:Confirmimg('.$item['id'].')" name="image">Remove Marker</a>'
        );
	}else
	{
	return sprintf(
            'Default Marker Image'
        );
	}
    }
    
    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
            'title' => __('Title', 'gmlm_data'),
            'address' => __('Address', 'gmlm_data'),
			'img1' => __('Marker', 'gmlm_data')
        );
        return $columns;
    }
   	function get_sortable_columns()
    {
        $sortable_columns = array(
            'title' => array('title', true),
            'address' => array('address', true)
        );
        return $sortable_columns;
    }
	function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete',
			'activate' => 'Activate',
			'deactivate' => 'Deactivate'
        );
        return $actions;
    }
	
	function process_bulk_action()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'locations'; // do not forget about tables prefix
		
        if ('delete' === $this->current_action()){
		//$id1 = intval( $_REQUEST['id'] );
			$ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);
            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE id IN('intval',$ids)");
            }
		}
		 if ('deleteimg' === $this->current_action()){
		//$id1 = intval( $_REQUEST['id'] );
			$ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);
            if (!empty($ids)) {
                $wpdb->query("UPDATE $table_name SET marker_img='' WHERE id IN('intval',$ids)");
            }
		}
		if ('activate' === $this->current_action()){
		//$id1 = intval( $_REQUEST['id'] );
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);
            if (!empty($ids)) {
            	$wpdb->query("UPDATE $table_name SET status='activate' WHERE id IN('intval',$ids)");
            }
        }
		if ('deactivate' === $this->current_action()){
		//$id1 = intval( $_REQUEST['id'] );
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);
            if (!empty($ids)) {
            	$wpdb->query("UPDATE $table_name SET status='deactivate' WHERE id IN('intval',$ids)");
            }
        }
    }
    function prepare_items()
	{
        global $wpdb;
        $table_name = $wpdb->prefix . 'locations'; // do not forget about tables prefix
        $columns = $this->get_columns();
		
		$per_page = 10; // constant, how much records will be shown per page
		$hidden = array();
		$this->get_views();
        $sortable = $this->get_sortable_columns();
        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);
        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();
        // will be used in pagination settings
		if(($_REQUEST['view']=="activate") or ($_REQUEST['view']=="deactivate"))
		{
			$total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name where status='".intval($_REQUEST['view'])."'");
		}
		else{
        	$total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");
		}
        // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? max(0,$_REQUEST['paged'] - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'title';
		$order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? esc_attr($_REQUEST['order']) : 'asc';
		
		// [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
		if((esc_attr($_REQUEST['view'])=='activate') or (esc_attr($_REQUEST['view'])=='deactivate'))
		{
		 	$this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name where status='".esc_attr($_REQUEST['view'])."'  ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);
		}
		else
		{
		 	$this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);
		}
	 // [REQUIRED] configure pagination
        $this-> set_pagination_args(array( 
			'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }
}
function gmlm_page_handler()
{
	//include("map_tableview.php");
    	global $wpdb;
    	$table = new Gmlm_List_Data_Table();
    	$table->prepare_items();
		$message = '';
		if ('delete' === $table->current_action()) {
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items deleted: %d', 'gmlm_data'), count($_REQUEST['id'])) . '</p></div>'; 
 } ?>
<div class="wrap">
	<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Geo Locations', 'gmlm_data')?> <a class="add-new-h2"
   href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=locations_form');?>"><?php _e('Add new Location', 'gmlm_data')?></a></h2><?php echo '<br>'.$message; ?>
   	<form id="locations-table" method="GET">
        <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']) ?>"/><?php $views = $table->views(); $table->display(); ?>
    </form>
</div>
<?php 
} 
?>
