<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class webdesignby_musicbox_widget extends WP_Widget {
    
    const widget_name = "Musicbox";
    
    private $widget_args = array();
    private $text_domain = "webdesignby";
    
    public function __construct(){
        $this->set_description( __('Display Musicbox.', $this->text_domain) );
        parent::__construct(false, __(self::widget_name, $this->text_domain), $this->widget_args );
    }
    
    private function set_description($description = ""){
        if( ! empty($description) ){
            $this->widget_args['description'] = $description;
        }
    }
    
    private function get_instance_number(){
        return $this->number;
    }
    
    // widget form creation
    function form($instance) {	
        
        $id = $this->get_instance_number();
     
        $title = "Musicbox";
        $musicbox_id = 0;
        $autoplay = $description = 0;
        $tracks_perpage = 10;
        if( $instance ){
            if( isset($instance['title']) )
                $title = $instance['title'];
            if( isset($instance['musicbox_id']) )
                $musicbox_id = $instance['musicbox_id'];
            if( isset($instance['autoplay']) )
                $autoplay = $instance['autoplay'];
            if( isset($instance['tracks_perpage']) )
                $tracks_perpage = $instance['tracks_perpage'];
            if( isset($instance['description']) )
                $description = $instance['description'];
        }
        global $wpdb;
        $model = new \Webdesignby\MusicboxModel($wpdb);
        $musicboxes = $model->findAll();
        ?>
         <div id="<?php echo $this->text_domain . "_musicbox_" . $id; ?>">
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', $this->text_domain); ?>:</label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
                <br /><span class="tip"><?php _e('Only used here in admin widget area.', $this->text_domain); ?></span>
            </p>
            <p>
                 <label for="<?php echo $this->get_field_id('musicbox_id'); ?>"><?php echo __('Musicbox', $this->text_domain); ?>:</label>
                 <?php if(!empty($musicboxes)): ?>
                 <select id="<?php echo $this->get_field_id('musicbox_id'); ?>" name="<?php echo $this->get_field_name('musicbox_id');?>">
                     <?php foreach($musicboxes as $musicbox): ?>
                     <option value="<?php echo $musicbox->id; ?>" <?php if( $musicbox_id == $musicbox->id){?>selected="selected"<?php } ?>><?php echo $musicbox->name; ?></option>
                     <?php endforeach; ?>
                 </select>
                 <?php else: ?>
                 <?php _e("No music boxes to add!"); ?>
                 <?php endif; ?>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('autoplay'); ?>"><?php _e('Autoplay', $this->text_domain); ?></label>: 
                <input type="checkbox" value="1" name="<?php echo $this->get_field_name('autoplay'); ?>" id="<?php echo $this->get_field_id('autoplay'); ?>" <?php if( $autoplay ):?>checked="checked"<?php endif; ?> />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Display Musicbox description', $this->text_domain); ?></label>: 
                <input type="checkbox" value="1" name="<?php echo $this->get_field_name('description'); ?>" id="<?php echo $this->get_field_id('description'); ?>" <?php if( $description ):?>checked="checked"<?php endif; ?> />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('tracks_perpage'); ?>"><?php _e('Tracks per page', $this->text_domain);?></label>: 
                <input type="text" value="<?php echo $tracks_perpage; ?>" id="<?php echo $this->get_field_id('tracks_perpage'); ?>" name="<?php echo $this->get_field_name('tracks_perpage'); ?>" />
            </p>
         </div>
        <?php
        
    }
    
    // widget update
    function update($new_instance, $old_instance) {

        $instance = $old_instance;
        // Fields
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['musicbox_id'] = $new_instance['musicbox_id'];
        $instance['autoplay'] = $instance['description'] = 0;
        $instance['tracks_perpage'] = $new_instance['tracks_perpage'];
        if( isset($new_instance['autoplay']) )
            $instance['autoplay'] = $new_instance['autoplay'];
        if( isset($new_instance['description']) )
            $instance['description'] = $new_instance['description'];
        
        return $instance;

    }
    
    // widget display
    function widget($args, $instance) {
        extract( $args );
        
        // these are the widget options
        $title = apply_filters('widget_title', $instance['title']);
        $musicbox_id = $instance['musicbox_id'];
        $autoplay = $instance['autoplay'];
        $tracks_perpage = $instance['tracks_perpage'];
        $description = $instance['description'];
        global $webdesignby_musicbox;
        $musicbox = $webdesignby_musicbox->getMusicbox($musicbox_id);
        $plugin_dir = plugin_dir_path( __FILE__ );
        \wp_enqueue_style('webdesignby-musicbox-widget', plugin_dir_url( __FILE__ ) . 'styles/musicbox2.css');
        \wp_enqueue_style('soundmanager2-360-player', plugin_dir_url( __FILE__ ) . 'vendor/soundmanager/demo/360-player/360player.css');
        \wp_enqueue_script('webdesignby-musicbox-widget', plugin_dir_url( __FILE__ ) . "/js/musicbox2.js");
        \wp_enqueue_script('soundmanager2', plugin_dir_url( __FILE__ ) . "/vendor/soundmanager/script/soundmanager2-jsmin.js");
        \wp_enqueue_script('berniecode-animator', plugin_dir_url( __FILE__ ) . "/vendor/soundmanager/demo/360-player/script/berniecode-animator.js");
        \wp_enqueue_script('soundmanager2-360-player', plugin_dir_url( __FILE__ ) . "/vendor/soundmanager/demo/360-player/script/360player.js");
        $plugin_dir_url = plugin_dir_url( __FILE__ );
        echo $before_widget;
        include( $plugin_dir . "/view/musicbox2.php");
        echo $after_widget;
        
    }
    
}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("webdesignby_musicbox_widget");'));


// widget shortcode

function webdesignby_musicbox_widget_shortcode( $atts ) {

    // Configure defaults and extract the attributes into variables
    $atts = shortcode_atts( 
            array( 
                    'title'  => '',
                    'musicbox_id' => '',
                    'autoplay' => 0,
                    'tracks_perpage' => 10,
                    'description' => '',
            ), 
            $atts 
    );

    $args = array();

    ob_start();
    the_widget( 'webdesignby_musicbox_widget', $atts, $args ); 
    $output = ob_get_clean();

    return $output;
}


add_shortcode( 'musicbox', 'webdesignby_musicbox_widget_shortcode' );