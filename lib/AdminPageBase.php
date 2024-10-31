<?php

namespace Webdesignby;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if( ! class_exists('Webdesignby\AdminPageBase')) {
    
    abstract class AdminPageBase extends \Webdesignby\View{

        abstract function add();
        
        function __construct($config = array()) {
           $this->setConfig($config);
           $this->setDir('admin');
        }
        
        /**
         * Populate template data
         */
        public function addData($data = array()){
            if(!empty($data) && is_array($data)){
                foreach($data as $name=>$value){
                    $this->data[$name] = $value;
                }
            }
        }
        
        public function getUrl($queryvars = ""){
            return "admin.php?page=" . $this->page_slug . $queryvars;
        }
        
    }
    
}