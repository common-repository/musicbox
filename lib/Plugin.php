<?php

namespace Webdesignby;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if( ! class_exists('Webdesignby\Plugin')) {
    
    class Plugin extends \Webdesignby\Base{

        public $plugin_key = "";
        public $plugin_slug = "";
        public $plugin_path = "";
        public $base_url = "";
        public $opts = array();
        protected $actions = array();
        protected $options_page;
        protected $admin_page;
        protected $admin_pages = array();
        protected $model;

        public function __construct( $config = null ) {

            $this->setConfig($config);

            if( isset($config['actions'])){
                $this->setActions($config['actions']);
            }elseif( ! empty($this->actions)){
                $this->setActions();
            }
            
            if( isset($config['image_sizes'])){
                $this->setImageSizes($config['image_sizes']);
            }
            
            $this->getOptions();
            
        }
        
        // WordPress plguin requires a valid init function callback
        public function init(){
            return true;
        }

        public function setActions($actions = array()){
            
            if( ! empty($actions) ){
                $this->actions = $actions;
            }

            foreach($this->actions as $action){
                \add_action( $action, array( $this, $action) );
            }
        }

        public function addAction($action){
            $this->actions[] = $action;
        }

        public function removeAction($action){
            $temp = array();
            foreach($this->actions as $a){
                if($action !== $a){
                    $temp[] = $action;
                }
            }
            \remove_action($action);

            $this->actions = $temp;
        }
        
        public function setImageSizes($image_sizes = array()){
            foreach($image_sizes as $image_size){
                $this->addImageSize($image_size);
            }
        }
        
        /**
         * Administration Options
         */

        public function createOptionsPage($config = array()){
            $this->options_page= new \Webdesignby\OptionsPage($config);
            \add_action('admin_menu', array($this, 'optionsPage'));
            // \do_action('admin_menu', $config);
        }
        
        public function optionsPage($config = array()){
            $this->options_page->add();
        }
        /**
         *  Administration Pages
         */
        
        public function createAdminMenu($config = array(), $parent = ""){
            $this->admin_pages[] = new \Webdesignby\AdminPage($config);
            \add_action('admin_menu', array($this, 'adminPage'));
        }

        public function adminPage($config = array()){
            foreach($this->admin_pages as $admin_page){
                $admin_page->add();
            }
        }
        
        public function addImageSize( $image_size = array() ){
            if( ! empty($image_size['name']))
                $name = $image_size['name'];
            else
                $name = $this->plugin_key;
            
            if( ! empty($image_size['width']))
                $width = $image_size['width'];
            else
                $width = 120;
            
            if( ! empty($image_size['height']))
                $height = $image_size['height']; 
            else
                $height = 120;
            
            if( ! empty($image_size['crop']))
                $crop = $image_size['crop'];
            else
                $crop = false;
            
            \add_image_size($name, $width, $height, $crop);
        }

        public function install(){
            if( is_a($this->model, 'Webdesignby\BaseModel')){
                $this->model->install();
            }
        }

        public function getOptions(){
            $this->opts = get_option($this->plugin_key);
            return $this->opts;
        }

    }

}