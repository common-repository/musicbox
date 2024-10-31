<?php

namespace Webdesignby;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if( ! class_exists('Webdesignby\AdminPage')) {
    
    class AdminPage extends \Webdesignby\AdminPageBase{
        
        public $data = array();
        public $enqueue_scripts = array();
        public $enqueue_styles = array();
        
        /* 
         * add_menu_page
         * 
         * http://codex.wordpress.org/Function_Reference/add_menu_page
         * usage: add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
         * 
         * add_submenu_page
         * http://codex.wordpress.org/Function_Reference/add_submenu_page
         * usage: add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function ); 
         */
        public function add(){
            if( ! empty($this->parent)){
             \add_submenu_page($this->parent, $this->page_title, $this->menu_title, $this->capabilites, $this->page_slug, array( $this, 'admin_page' ) );
            }else{
                \add_menu_page($this->page_title, $this->menu_title, $this->capabilites, $this->page_slug, array($this, 'admin_page') );
            }
        }
        
        public function  admin_page () {
            $data = $this->data;
            $data['page_title'] = $this->page_title;
            $data['option_key'] = $this->option_key;
            $data['page_slug'] = $this->page_slug;
            if( ! empty($this->plugins) && is_array($this->plugins) ){
                foreach($this->plugins as $plugin){
                    $this->load($plugin);
                }
            }
            $this->enqueue_style();
            $this->getView($this->template, $data);
        }
        
        public function admin_subpage($options){
            
            $data = $this->data;
            if( ! empty($this->plugins) && is_array($this->plugins) ){
                foreach($this->plugins as $plugin){
                    $this->load($plugin);
                }
            }
            $this->enqueue_style();
            $this->getView($options['template'], $data);
        }
        
        /*
         * wp_enqueue_style( $handle, $src, $deps, $ver, $media );
         */
        public function enqueue_style(){
            if( ! empty($this->enqueue_styles)){
                foreach($this->enqueue_styles as $style){
                    \wp_enqueue_style( $style['handle'], $style['src'], $style['deps'], $style['ver']);
                }
            }
        }
        
        public function load($plugin){
            if($plugin == "media_uploader"){
                $this->mediaUploader();
            }
        }
        
        public function mediaUploader(){
            
            if(function_exists('wp_enqueue_media') && version_compare($this->getWpVersion(), '3.5', '>=')) {
                //call for new media manager
                wp_enqueue_media();
            }//old WP < 3.5
            else {
               wp_enqueue_script('media-upload');
               wp_enqueue_script('thickbox');
               wp_enqueue_style('thickbox');
            }
        }
        
    }
    
}