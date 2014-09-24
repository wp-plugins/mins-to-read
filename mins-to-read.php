<?php
/*
Plugin Name: Mins To Read
Plugin URI: http://www.think201.com
Description: Mins To Read is a plugin which calculates the read time of a blog post based on words present in it.
Author: Think201, Vivek Pandey
Version: 1.0
Author URI: http://www.think201.com
License: GPL v1

Mins To Read Plugin
Copyright (C) 2014, Think201 - think201.com@gmail.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
/**
 * @package Main
 */

//start session
if (session_id() == '') {
	session_start();
}

if(version_compare(PHP_VERSION, '5.2', '<' )) 
{
	if (is_admin() && (!defined( 'DOING_AJAX' ) || !DOING_AJAX )) 
	{
		require_once(ABSPATH . 'wp-admin/includes/plugin.php');
		deactivate_plugins( __FILE__ );
		wp_die( sprintf( __( 'Mins To Read requires PHP 5.2 or higher, as does WordPress 3.2 and higher. The plugin has now disabled itself.', 'Mins To Read' ), '<a href="http://wordpress.org/">', '</a>' ));
	} 
	else 
	{
		return;
	}
}

register_activation_hook( __FILE__, array('MinsToRead', 'activation') );
register_deactivation_hook( __FILE__, array('MinsToRead', 'deactivation') );
register_uninstall_hook(    __FILE__, array('MinsToRead', 'uninstall') );

add_action( 'plugins_loaded', array('MinsToRead', 'init' ) );

class MinsToRead
{
	protected static $instance = null;

	public function __construct()
    {
    	$DefaultData = serialize(array('mtr_reading_speed' => '200'));

    	if ( !defined( 'MTR_PATH' ) )
		define( 'MTR_PATH', plugin_dir_path( __FILE__ ) );

		if ( ! defined( 'MTR_MINS_READ' ) )
		define( 'MTR_MINS_READ', 'minstoread' );

		if ( ! defined( 'MTR_DEFAULTCLASS' ) )
		define( 'MTR_DEFAULTCLASS', 'minstoread' );	

		if ( ! defined( 'MTR_DEFFLTPOSITION' ) )
		define( 'MTR_DEFFLTPOSITION', 'Top Right' );	

		if ( ! defined( 'MTR_MINS_READ_DEFLT_VALUE' ) )
		define( 'MTR_MINS_READ_DEFLT_VALUE', $DefaultData );	

		if ( !defined( 'MTR_BASENAME' ) )
		define( 'MTR_BASENAME', plugin_basename( __FILE__ ) );

		if ( !defined( 'MTR_VERSION' ) )
		define('MTR_VERSION', '1.0' );

		if ( !defined( 'MTRPLUGIN_DIR' ) )
		define('MTRPLUGIN_DIR', dirname(__FILE__) );
    }

    public static function get_instance() 
    {
	 	// create an object
	 	NULL === self::$instance and self::$instance = new self;

	 	return self::$instance;
	 }

    // Initiation Hook
    public function init()
    {
    	// Add action hook for adding admin sub menu under setting page
		add_action( 'admin_menu', array( MinsToRead::get_instance(), 'admin_menu' ) );

		// Call mtr style
		add_action( 'init', array( MinsToRead::get_instance(), 'style' ) );

		// Call mtr on post publish
		add_action( 'publish_post', array( MinsToRead::get_instance(), 'set_mtr' ) );

		add_filter('the_content', array(MinsToRead::get_instance(), 'content_filter'));
    }

    // Calling Function for Activation Hook
	public function activation()
	{
		// Calling Function to Setup Database Option Values and initial setup for Plugin
		if ( get_option( MTR_MINS_READ ) !== false ) 
		{
		    // The option already exists, so we just update it.
		    update_option( MTR_MINS_READ, MTR_MINS_READ_DEFLT_VALUE );
		} 
		else 
		{
		    // The option hasn't been added yet. We'll add it with $autoload set to 'no'.
		    $deprecated = null;
		    $autoload = 'no';
		    add_option( MTR_MINS_READ, MTR_MINS_READ_DEFLT_VALUE, $deprecated, $autoload );
		}
	}

	// Calling Function to Drop Database Tables
	public function deactivation()
	{
		// No Action
		return true;
	}

	// Calling Function to Drop Database Tables
	public function uninstall()
	{	
		// Remove the data from Options table for MTR
		delete_option( 'minstoread' );
	}

    public function admin_menu() 
	{
		add_options_page( 'Mins To Read', 'Mins To Read', 'manage_options', 'mins-to-read', array('MinsToRead', 'admin_dashboard') );
	}

	// Minutes To Read Dashboard Function
	public function admin_dashboard() 
	{
		require_once(MTRPLUGIN_DIR.'/includes/admin-dashboard.php');	
	}

	public function style()
	{
		wp_enqueue_style( 'mtr-css', plugins_url( 'mins-to-read/css/mtr.css' ),	array( 'thickbox' ), MTR_VERSION, 'all' );
	}

	// DONE
	private function calculate_mtr($post_id = null)
	{
		$mtr_value = array();
		$mtr_value['m'] = 0;
		$mtr_value['s'] = 0;

		$post_object = get_post( $post_id );
		
		$content = $post_object->post_content;

		if(empty($content))
		{
			return $mtr_value;
		}

		$data = unserialize(get_option(MTR_MINS_READ));		

		// get the default option value
		$preferedReadSpeed = $data['mtr_reading_speed'];

		if($preferedReadSpeed < 50)
		{
			$preferedReadSpeed = 200;
		}

		// Get content of the post
		$content = str_replace(']]>', ']]&gt;', $content);	
		
		// Calculate the words from content
		$words = str_word_count(strip_tags($content));
		
		// Get the minutes
		$min = floor($words / $preferedReadSpeed);

		// get the seconds
		$sec = floor($words % $preferedReadSpeed / ($preferedReadSpeed / 60));	
	
		$mtr_value['m'] = $min;
		$mtr_value['s'] = $sec;

		return $mtr_value;
	}

	// DONE
	public function set_mtr($post_id = null)
	{
		// Check if post id
		if ( empty( $post_id ) ) return false;

		// Check if it's not a post
		if ( get_post_type( $post_id ) != 'post' ) return false;

		// post doesn't have mtr value
		$mtr_value = $this->calculate_mtr($post_id);
		
		if ( ! update_post_meta ($post_id, '_mtr_post', $mtr_value) ) 
		{ 				
			add_post_meta($post_id, '_mtr_post', $mtr_value, true );	
		}		

		return true;
	}

	public function get_mtr($post_id = null)
	{
		// get post mtr meta value and return 
		$mtr = get_post_meta($post_id, '_mtr_post', true);

		return $mtr;
	}

	private function prepare_mtr($mtr_value)
	{
		$m = $mtr_value['m'];
		$s = $mtr_value['s'];

		$mtr_string = $m . ' min' . ($m == 1 ? '' : 's') . ' ' . $s . ' sec' . ($s == 1 ? '' : 's');
		
		return $mtr_string;
	}

	public function show_mtr($post_id = null)
	{
		if(empty($post_id))
		{
			return false;
		}

		// get the meta post value
		$mtr_string = $this->prepare_mtr($this->get_mtr($post_id));

		// Get the user preference value from options.
		$data = unserialize(get_option(MTR_MINS_READ));	

		$mtr_custom_class = $data['mtr_custom_class'];
	?>
		<script type="text/javascript">
			jQuery('body').prepend('<span id="<?php echo MTR_DEFAULTCLASS; ?>" class="<?php echo $mtr_custom_class; ?>"> <?php echo $mtr_string; ?></span>');
		</script>
	<?php	
	}

	public function content_filter($content)
	{
		if(is_single())
		{
			// check for position and set the position
			global $post;

			$this->show_mtr($post->ID);
		}		

		return $content;
	}

	// DONE
	public function bulk_calculate_mtr()
	{
		// get post mtr meta value and return 
		$args = array(
				'post_type' => 'post',
				'posts_per_page' => -1,
				'post_status' => 'publish',
				'suppress_filters' => true
				);

		$posts = get_posts($args);

		foreach($posts as $post)
		{
			// call calulate mtr
			$this->set_mtr($post->ID);
		}	

		wp_reset_postdata();
	}
}

$initObj = MinsToRead::get_instance();
$initObj->init();

?>