<?php
      $RetVal = false;

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
                  $mtr_style = trim($_POST['mtr_style']);
                  
                  $mtr_data = array(
                        'mtr_reading_speed'     => $mtr_reading_speed,                        
                        'mtr_custom_class'      => $mtr_custom_class,
                        'mtr_style'             => $mtr_style
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

                  $RetVal = true;
            }
            else
            {
                  // Call the function to trigger calculate mins to read for all posts
                  MTREngine::bulk_calculate_mtr();

                  $RetVal = true;
            }
      }

      // get the data from db
      $data = unserialize(get_option(MTR_MINS_READ)); 
?>

<div class='wrap t201plugin'>
 <h2><?php echo _( 'Mins To Read' ); ?></h2>

 <?php
 if($RetVal)
 {
 ?>
 <div id="message" class="updated below-h2">
    <p>Mins To Read Settings Updated</p>
  </div>
 <?php
  }
 ?>

  <div class="tbox">
    <div class="tbox-heading">
		<h3><?php _e('Configuration', 'mins-to-read'); ?></h3>
      <a href="http://labs.think201.com/plugin/mins-to-read" target="_blank" class="pull-right">Need help?</a>
    </div>
    <div class="tbox-body">
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
          <tr>
            <td><strong><?php echo _( 'Style Type:' ); ?></strong></td>
            <td>
                <select name="mtr_style">
                  <option value="mtr-top-right-corner" selected="">Top Right Corner</option>
                  <option value="mtr-center-right-corner" >Center Right Corner</option>
                  <option value="mtr-bottom-right-corner" >Bottom Right Corner</option>
                  <option value="code">Use through code (Custom Styling)</option>
                </select>              
            </td> 
          </tr>
        </table>	   
        <input type="hidden" name="submitted" id="submitted" value="true" /> 
        <br>
        <button class="button button-primary" type="submit"><?php echo _( 'Save Settings' ); ?></button>
    </form>
        <br><br>
			<form name="calculatemistoreadforposts" id="calculatemistoreadforposts" action="<?php the_permalink(); ?>" method="post">
				<p><strong>Get mins to read for all posts</strong> <button type="submit" id="minstoreadforallposts" class="button-primary">Calculate MTR for all Posts</button>  </p>
				<input type="hidden" name="submitted" id="submitted" value="save" /> 
			</form>

    </div>
    <div class="tbox-footer">
      Set the average reading speed of your blog readers in words per minute.
		</div>  
	</div>
  
  <div class="tbox">
    <div class="tbox-heading">
    <h3>Integration</h3>
      <a href="http://labs.think201.com/plugin/mins-to-read" target="_blank" class="pull-right">Need help?</a>
    </div>
    <div class="tbox-body">
    Shortcode: Use <strong>[mtr_print]</strong> inside the WordPress loop to get the Mins To Read printed.<br><br>
    Function:  Use <strong>if(function_exists('mtr_print')) mtr_print();</strong> anywhere in your theme files with passing the Post id to get the Mins To Read Printed.

    </div>
    <div class="tbox-footer">
      Integrating Mins To Read to your blog post.
    </div>  
  </div>


</div>