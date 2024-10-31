<?php

namespace Webdesignby;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if( ! class_exists('Webdesignby\Registry')){
    
    class Registry extends \Webdesignby\Base{
        
        public static $plugin_key;
        public static $plugin_slug;
        public static $plugin_path;
        public static $base_url;
        
        public function __construct($config){
            
             if( ! is_array($config) || empty($config))
                return false;

            foreach($config as $name=>$value){
                if( property_exists($this, $name))
                    self::${$name} = $value;
            }
            
        }

    }
    
}