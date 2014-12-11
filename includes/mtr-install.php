<?php
	
class MTR_Install
{
	//Function to Setup DB Tables
	public static function activate()
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

	public static function deactivate()
	{
		return true;
	}

	public static function delete()
	{
		// Remove the data from Options table for MTR
		delete_option( 'minstoread' );		
	}
}

?>