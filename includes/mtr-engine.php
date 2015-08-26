<?php

class MTREngine
{
    public static function calculate_mtr($post_id = null)
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

    public static function set_mtr($post_id = null)
    {
        // Check if post id
        if ( empty( $post_id ) ) return false;

        // Check if it's not a post
        if ( get_post_type( $post_id ) != 'post' ) return false;

        // post doesn't have mtr value
        $mtr_value = MTREngine::calculate_mtr($post_id);
        
        if ( ! update_post_meta ($post_id, '_mtr_post', $mtr_value) ) 
        {               
            add_post_meta($post_id, '_mtr_post', $mtr_value, true );    
        }       

        return $mtr_value;
    }

    public static function bulk_calculate_mtr()
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
            MTREngine::set_mtr($post->ID);
        }   

        wp_reset_postdata();
    }    

    public static function get_mtr($post_id = null)
    {
        // get post mtr meta value and return 
        $mtr = get_post_meta($post_id, '_mtr_post', true);

        if(empty($mtr))
        {
            $mtr = MTREngine::set_mtr($post_id);
        }

        return $mtr;
    }    
}