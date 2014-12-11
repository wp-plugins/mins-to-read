<?php

class MinsToRead
{
	protected static $instance = null;

	public function __construct()
    {
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
		// Call mtr on post publish
		add_action( 'publish_post', array( MinsToRead::get_instance(), 'set_mtr' ) );

		add_filter('the_content', array(MinsToRead::get_instance(), 'content_filter'));
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

		if(isset($data['mtr_custom_class']))
		{
			$mtr_custom_class = $data['mtr_custom_class'];
		}
		else
		{
			$mtr_custom_class = ' ';	
		}
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

?>