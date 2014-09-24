<?php
      // Check the user permission
      if ( !current_user_can( 'manage_options' ) )  
      {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
      }
      
      //Check if form is submitting
      if(isset($_POST['submitted'])) 
      {
            if($_POST['submitted'] === 'true')
            {
                  $mtr_reading_speed = trim($_POST['mtr_reading_speed']);                  
                  $mtr_custom_class = trim($_POST['mtr_custom_class']);
                  
                  $mtr_data = array(
                        'mtr_reading_speed'     => $mtr_reading_speed,                        
                        'mtr_custom_class'      => $mtr_custom_class
                        );

                  $serializedata = serialize($mtr_data);

                  if ( get_option( MTR_MINS_READ ) !== false ) 
                  {
                      // The option already exists, so we just update it.
                      update_option( MTR_MINS_READ, $serializedata );
                  } 
                  else 
                  {
                      // The option hasn't been added yet. We'll add it with auto-load set to 'no'.
                      $deprecated = null;
                      $autoload = 'no';
                      add_option( MTR_MINS_READ, $serializedata, $deprecated, $autoload );
                  }
            }
            else
            {
                  // Call the function to trigger calculate mins to read for all posts
                  $mtrObject = new MinsToRead();
                  $mtrObject->bulk_calculate_mtr();
            }
      }

      // get the data from db
      $data = unserialize(get_option(MTR_MINS_READ)); 
?>
<div id="minstoreadsettings" class="wrap">
	<h1><?php echo _( 'Mins To Read' ); ?></h1>
	<div class="mtr_general_settings">
		<h3>Settings</h3>
		<form name="mtr_settings_form" id="mtr_settings_form" action="<?php the_permalink(); ?>" method="post">	        
        <table>
        	<tr>
        		<td><strong><?php echo _( 'Reading Speed of Words(per minute):'); ?></strong></td>
        		<td>
        			<input type="text" id="mtr_reading_speed" value="<?php if(isset($data['mtr_reading_speed']))echo $data['mtr_reading_speed']; ?>" name="mtr_reading_speed" placeholder="Reading Speed">
        		</td>
        	</tr>            	
        	<tr>
        		<td><strong><?php echo _( 'Custom Class:' ); ?></strong></td>
        		<td>
        			<input type="text" id="mtr_custom_class" value="<?php if(isset($data['mtr_custom_class'])) echo $data['mtr_custom_class']; ?>" name="mtr_custom_class" placeholder="Enter your custom class">
        		</td>	
        	</tr>
        </table>	   
        <input type="hidden" name="submitted" id="submitted" value="true" /> 
        <br>
        <button class="button button-primary" type="submit"><?php echo _( 'Save Settings' ); ?></button>
    </form>
        
		<div class="calculatemistoreadforposts">
			<form name="calculatemistoreadforposts" id="calculatemistoreadforposts" action="<?php the_permalink(); ?>" method="post">
				<span class="minstoreadforposts">Get mins to read for all posts</span>
				<input type="hidden" name="submitted" id="submitted" value="save" /> 
				<button type="sunmit" id="minstoreadforallposts"><?php echo _( 'Run' ); ?></button>
			</form>
		</div>  
		<p class="createdby">Created By<a href="http://think201.com" target="_blank">Think201</a></p>
	</div>
</div>