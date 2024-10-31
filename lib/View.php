<?php

namespace Webdesignby;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if( ! class_exists('Webdesignby\View')) {
    
    class View extends \Webdesignby\Base{
        
        private $dir = '';
        
        public $page_title = "Plugin Settings";
        public $menu_title = "plugin settings";
        public $capabilites = "manage_options";
        public $page_slug = "plugin-name";
        public $option_key = "option_key";
        public $plugin_key = "plugin_key";
        public $plugin_path = "plugin_path";
        public $parent = "";
        public $template = "default";
        public $plugins = array();
        public $data = array();
        public $model;
     
        public function setData($data = array()){
            $this->data = $data;
        }
        
        public function setModel($model){
            $this->model = $model;
        }
        
        public function getView( $view, $data = array() ){
            
            $this->data = array_merge( $data, $this->data);
            
            foreach($this->data as $name=>$value){
                ${$name} = $value;
            }
            $plugin_key = $this->plugin_key;
            $plugin_slug = $this->page_slug;
            $path = $this->plugin_path . 'view/' . $view . '.php';
            include($path);;
        }
        
        public function setDir($dir){
            $this->dir = $dir;
        }
        
    }
    
}