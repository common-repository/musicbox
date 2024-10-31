<?php

namespace Webdesignby;

defined( 'ABSPATH' ) or die( 'Direct access not allowed' );

if( ! class_exists('Webdesignby\Base')) {
    
    class Base{

        public function setConfig($config = array()){
            
            if( ! is_array($config) || empty($config))
                return false;

            foreach($config as $name=>$value){
                if( property_exists($this, $name))
                    $this->{$name} = $value;
            }
        }
        
        public static function getWpVersion(){
            global $wp_version;
            return $wp_version;
        }

}

}
