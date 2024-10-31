<?php

namespace Webdesignby;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if( ! class_exists('Webdesignby\BaseModel')) {
    
abstract class BaseModel{
 
    protected $_db;
    protected $_errors = array();
    
    abstract protected function install();
    
    function getCharsetCollate(){
            return $this->_db->get_charset_collate(); 
    }
    
    protected static function tableExists( $table_name ){
        global $wpdb;
        $sql = "SHOW TABLES LIKE '" . $table_name . "'";
        $result = $wpdb->query($sql);
        if( empty($result))
            return false;
        else
            return true;

    }
    
    protected function getTableName($name){
        return $this->_db->prefix . \Webdesignby\Registry::$plugin_key  . "_" . $name;
    }
    
    protected function addError($error = array()){
        $this->_errors[] = $error;
    }
    
    public function getErrors(){
        return $this->_errors;
    }

}

}