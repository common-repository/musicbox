<?php

namespace Webdesignby;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if( ! class_exists('Webdesignby\OptionsPage')) {
    
    class OptionsPage extends \Webdesignby\AdminPageBase{
        
        public $options_settings;
        
        function add(){

            \add_options_page( $this->page_title, $this->menu_title, $this->capabilites, $this->page_slug, array( $this, 'settings_page' ) );
        }

        function  settings_page () {
            $data = array();
            $data['page_title'] = $this->page_title;
            $data['option_key'] = $this->option_key;
            $data['page_slug'] = $this->page_slug;
            $data['options_settings'] = $this->options_settings;
            $this->getView('admin/settings', $data);
        }

    }

}