<?php
class MTRAdmin
{
	protected static $instance = null;

    public static function get_instance() 
    {
	 	// create an object
	 	NULL === self::$instance and self::$instance = new self;

	 	return self::$instance;
	 }

    public function init()
    {
        $this->fileInlcudes(); 	

        add_action('admin_menu', array($this, 'menuItems')); 

        add_action( 'init', array($this, 'userFiles')); 
    }

    public function menuItems()
    {
        $PageA = add_options_page( 'Mins To Read', 'Mins To Read', 'manage_options', 'mins-to-read', array($this, 'admin_dashboard') );

        add_action('admin_print_scripts-' . $PageA, array($this, 'adminScriptStyles'));
    }

    public function fileInlcudes()
    {
        require_once MTRPLUGIN_DIR .'/includes/mtr-engine.php';
        require_once MTRPLUGIN_DIR .'/includes/mtr-view.php';
    }    

    // Minutes To Read Dashboard Function
    public function admin_dashboard() 
    {
        require_once(MTRPLUGIN_DIR.'/includes/admin-dashboard.php');    
    }

    public function adminScriptStyles()
    {
        if(is_admin()) 
        {        
            wp_enqueue_style( 'think201-wp', plugins_url( 'mins-to-read/css/think201-wp.css' ), '', MTR_VERSION, 'all' );
            wp_enqueue_style( 'mtr', plugins_url( 'mins-to-read/css/mtr.css' ), '', MTR_VERSION, 'all' );
        }
    }

    public function userFiles()
    {
        if (!is_admin()) 
        {
            wp_enqueue_style( 'mtr-user-css', plugins_url( 'mins-to-read/css/mtr.css' ), '', MTR_VERSION, 'all' );
        }
    }     
}
?>