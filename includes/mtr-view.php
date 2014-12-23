<?php

class MTRView
{
    public static function prepare_mtr($mtr_value)
    {
        $m = $mtr_value['m'];
        $s = $mtr_value['s'];

        $mtr_string = '';

        if(!empty($m))
        {
            $mtr_string = $mtr_string.' '.$m . ' min' . ($m == 1 ? '' : 's');
        }

        if(!empty($s))
        {
            $mtr_string = $mtr_string.' '.$s . ' sec' . ($s == 1 ? '' : 's');
        }

        return $mtr_string;
    }

    public static function print_mtr($post_id = null)
    {
        $mtr_string = MTRView::prepare_mtr(MTREngine::get_mtr($post_id));

        echo $mtr_string;
    }

    public static function show_mtr($post_id = null)
    {
        if(empty($post_id))
        {
            return false;
        }

        // get the meta post value
        $mtr_string = MTRView::prepare_mtr(MTREngine::get_mtr($post_id));

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
}
