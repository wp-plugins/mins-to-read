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
		add_action( 'publish_post', array( 'MTREngine', 'set_mtr' ) );

		add_filter('the_content', array(MinsToRead::get_instance(), 'content_filter'));

		add_shortcode( "mtr_print", array(MinsToRead::get_instance(), "mtr_print"));
    }

    public function mtr_print()
    {
    	$Id = get_the_ID();

    	if(!empty($Id) AND get_post_type( $Id ) === 'post')
    	{
    		MTRView::print_mtr($Id);
    	}
    }

	public function content_filter($content)
	{
		if(is_single())
		{
			// check for position and set the position
			global $post;

			MTRView::show_mtr($post->ID);
		}		

		return $content;
	}


}

?>