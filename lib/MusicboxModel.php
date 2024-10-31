<?php

namespace Webdesignby;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once('BaseModel.php');

class MusicboxModel extends \Webdesignby\BaseModel{
    
    public function __construct($db){
            $this->_db = $db;
    }
    
    /*
     *  Working with Music Boxes
     */
    
    public function insert($data = array() ){
     
        $sql_add = "INSERT INTO `" . $this->_db->prefix . "webdesignby_musicbox` "
                    . " (`name`, `description`) VALUES ('" . $data['name'] . "', '" . $data['description'] . "')";
        $this->_db->query($sql_add);
        return $this->_db->insert_id ;
        
    }
    
    public function delete($id){
        $sql = "DELETE FROM `" . $this->_db->prefix . "webdesignby_musicbox_musicbox_tracks_assoc` WHERE `musicbox_id` =  '" . (int) $id . "'";
        $this->_db->query($sql);
        $sql2 = "DELETE FROM `" . $this->_db->prefix . "webdesignby_musicbox` WHERE `id` =  '" . (int) $id . "'";
        $this->_db->query($sql2);
    }
    
    public function update($id, $data){
        $sql = "UPDATE `" . $this->_db->prefix . "webdesignby_musicbox` SET `name`='" . $data['name'] . "', "
                . " `description` = '" . $data['description'] . "' WHERE `id` = '" . (int) $id . "'";
        $this->_db->query($sql);
    }
    
    public function updateSort($id, $sort_order){
        if( empty($id) || empty($sort_order) )
            return false;
        $sql = "UPDATE `" . $this->_db->prefix . "webdesignby_musicbox` SET `sort_order`='" . (int) $sort_order. "' "
                . " WHERE `id` = '" . (int) $id . "'";
        // echo $sql;
        $this->_db->query($sql);
    }
    
    public function updateTrackSort($musicbox_id, $track_id, $sort_order){
        if( empty($musicbox_id) || empty($track_id) || empty($sort_order) )
            return false;
        
        $sql = "UPDATE `" . $this->_db->prefix . "webdesignby_musicbox_musicbox_tracks_assoc` SET `sort_order` = '" 
                . (int) $sort_order . "' WHERE `musicbox_id` = '" . (int) $musicbox_id . "' AND `track_id` = '" . (int) $track_id . "'";
        // echo $sql;
        $this->_db->query($sql);
    }
    
    /*
    public function findAll(){
        $sql = "SELECT `mb`.`id` as `musicbox_id`, `t`.`id` as `track_id` FROM `" . $this->_db->prefix . "webdesignby_musicbox` `mb` "
                . "LEFT JOIN `" . $this->_db->prefix . "webdesignby_musicbox_musicbox_tracks_assoc` `mbta` ON `mbta`.`musicbox_id` = `mb`.`id` "
                . "LEFT JOIN  `" . $this->_db->prefix . "webdesignby_musicbox_tracks` `t` ON `mbta`.`track_id` = `t`.`id`";
        $results = $this->_db->get_results($sql);
        return $results;
    }
     * 
     */
    public function findAll($order_by = "`sort_order` ASC, `id` DESC"){
        $sql = "SELECT `mb`.*  FROM `" . $this->_db->prefix . "webdesignby_musicbox` `mb` "
               . " ORDER BY " . $order_by;
        $results = $this->_db->get_results($sql);
        return $results;
    }
    
    public function find($id){
        if( empty($id) )
            return;
        
         $sql = "SELECT `mb`.*  FROM `" . $this->_db->prefix . "webdesignby_musicbox` `mb` WHERE id='" . (int) $id . "'";
    
        return $this->_db->get_row($sql);
    }
    
    public function getTracks($musicbox_id){
        if( empty($musicbox_id) )
            return;
        
        $sql = "SELECT `t`.* FROM `" . $this->_db->prefix . "webdesignby_musicbox_tracks` `t` "
                . "INNER JOIN `" . $this->_db->prefix . "webdesignby_musicbox_musicbox_tracks_assoc` `mbta` "
                . "ON `mbta`.`track_id` = `t`.`id` WHERE `mbta`.`musicbox_id` = '" . (int) $musicbox_id . "'" 
                . " ORDER BY `mbta`.`sort_order` ASC";
        $results = $this->_db->get_results($sql);
        return $results;
    }
    
    /*
     *  Working with Tracks
     */
    
    public function addTrack($data=array()){
        
        if( empty($data) )
            return false;
        
        if( empty($data['trackId']) )
            return false;
        
        if( empty($data['musicbox_id']) )
            return false;
        
        // does this track already exist?
        $trackId = $data['trackId'];
        $musicbox_id = $data['musicbox_id'];
        $sql_select_track = "SELECT * FROM `" . $this->_db->prefix . "webdesignby_musicbox_tracks` `t` WHERE `t`.`trackId` = '" . (int) $trackId . "'";
        $rowTrack = $this->_db->get_row($sql_select_track);
        if( ! empty($rowTrack) ){
            $track_id = $rowTrack->id;
            $this->updateTrack($track_id, $data);
        }else{
            $sql_insert_track = "INSERT INTO `" . $this->_db->prefix . "webdesignby_musicbox_tracks` 
                        (`artistId`, `artistName`, `trackId`, `trackName`, `collectionId`, `collectionName`, 
                            `collectionCensoredName`, `trackCensoredName`, `artistViewUrl`, `collectionViewUrl`, 
                            `trackViewUrl`, `previewUrl`, `artworkUrl60`, `artworkUrl100`) 
                        VALUES ('" . $data['artistId'] . "', '" . $data['artistName'] . "', '" . $data['trackId'] . "', '" . $data['trackName'] . "', '" 
                                    . $data['collectionId'] . "', '" . $data['collectionName'] . "', '" . $data['collectionCensoredName'] . "', '" . $data['trackCensoredName'] . "', '" 
                                    . $data['artistViewUrl'] . "', '" . $data['collectionViewUrl'] . "', '" . $data['trackViewUrl'] . "', '" 
                                    . $data['previewUrl'] . "', '" . $data['artworkUrl60'] . "', '" . $data['artworkUrl100'] . "')";
            $this->_db->query($sql_insert_track);
            $track_id = $this->_db->insert_id ;
        }
        
        $sql_duplicate_check = "SELECT * FROM `" . $this->_db->prefix . "webdesignby_musicbox_musicbox_tracks_assoc` WHERE `musicbox_id`='" . (int) $musicbox_id . "' AND `track_id` = '" . (int) $track_id . "'";
        $rowAssoc = $this->_db->get_row($sql_duplicate_check);
        if( empty($rowAssoc) ){
            $sql_musicbox_max_sort = "SELECT MAX(`sort_order`) AS `max_sort` FROM `" . $this->_db->prefix . "webdesignby_musicbox_musicbox_tracks_assoc` WHERE `musicbox_id` = " . (int) $musicbox_id;
            $max_sort = $this->_db->get_row($sql_musicbox_max_sort);
            $next_sort = $max_sort->max_sort + 1;
            // add webdesignby_musicbox_musicbox_tracks_assoc
            $sql_musicbox_track_assoc = "INSERT INTO `" . $this->_db->prefix . "webdesignby_musicbox_musicbox_tracks_assoc` "
                                            . "(`musicbox_id`, `track_id`, `sort_order`) VALUES('" . (int) $musicbox_id . "', '" . (int) $track_id . "', '" . (int) $next_sort . "')";
            $this->_db->query($sql_musicbox_track_assoc);
            return true;
        }else{
            $this->addError(array("error"=>"This track already exists in this musicbox!"));
            return false;
        }
    }
    
    public function updateTrack($track_id=null, $data=array()){
        
        if( empty($data) || empty($track_id) )
            return false;
        
        $sql = "UPDATE `" . $this->_db->prefix . "webdesignby_musicbox_tracks` SET "
                . " `artistId` = '" . $data['artistId'] . "', "
                . " `trackName` = '" . $data['trackName'] . "', "
                . " `collectionId` = '" . $data['collectionId'] . "', "
                . " `collectionName` = '" . $data['collectionName'] . "', "
                . " `collectionCensoredName` = '" . $data['collectionCensoredName'] . "', "
                . " `trackCensoredName` = '" . $data['trackCensoredName'] . "', "
                . " `artistViewUrl` = '" . $data['artistViewUrl'] . "', "
                . " `collectionViewUrl` = '" . $data['collectionViewUrl'] . "', "
                . " `trackViewUrl` = '" . $data['trackViewUrl'] . "', "
                . " `previewUrl` = '" . $data['previewUrl'] . "', "
                . " `artworkUrl60` = '" . $data['artworkUrl60'] . "', "
                . " `artworkUrl100` = '" . $data['artworkUrl100'] . "' "
                . " WHERE id='" . (int) $track_id . "'";
        $this->_db->query($sql);
    }
    
    public function deleteTrack($id){
        if( empty($id) )
            return;
         $sql = "DELETE FROM `" . $this->_db->prefix . "webdesignby_musicbox_tracks` WHERE `id` = '" . (int) $id . "';";
         $this->_db->query($sql);
         
    }
    
    /*
     *  Install / Uninstall
     */
    
    public function install(){
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        
        $table_name =  $this->_db->prefix . "webdesignby_musicbox";
        if($this->_db->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $sql_table1 = "CREATE TABLE `" . $this->_db->prefix . "webdesignby_musicbox` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `name` varchar(255) NOT NULL,
                    `description` text NOT NULL,
                    `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                     `sort_order` int(11) DEFAULT NULL,
                    PRIMARY KEY (`id`)
                  ) ENGINE=InnoDB " . $this->getCharsetCollate() . ";";
            // $this->_db->query($sql_table1); 

            \dbDelta( $sql_table1 );
        }

        $table_name =  $this->_db->prefix . "webdesignby_musicbox_tracks";
        if($this->_db->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $sql_table2 = "CREATE TABLE `" . $this->_db->prefix . "webdesignby_musicbox_tracks` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `musicbox_id` int(10) unsigned NOT NULL,
                    `artistId` int(10) unsigned NOT NULL,
                    `collectionId` int(10) unsigned NOT NULL,
                    `trackId` int(10) unsigned NOT NULL,
                    `artistName` varchar(255) CHARACTER SET latin1 NOT NULL,
                    `collectionName` varchar(255) CHARACTER SET latin1 NOT NULL,
                    `trackName` varchar(255) CHARACTER SET latin1 NOT NULL,
                    `collectionCensoredName` varchar(255) CHARACTER SET latin1 NOT NULL,
                    `trackCensoredName` varchar(255) CHARACTER SET latin1 NOT NULL,
                    `artistViewUrl` text CHARACTER SET latin1 NOT NULL,
                    `collectionViewUrl` text CHARACTER SET latin1 NOT NULL,
                    `trackViewUrl` text CHARACTER SET latin1 NOT NULL,
                    `previewUrl` text CHARACTER SET latin1 NOT NULL,
                    `artworkUrl60` text CHARACTER SET latin1 NOT NULL,
                    `artworkUrl100` text CHARACTER SET latin1 NOT NULL,
                    PRIMARY KEY (`id`,`musicbox_id`)
                  ) ENGINE=InnoDB " . $this->getCharsetCollate() . ";";
            // $this->_db->query($sql_table2);
            \dbDelta( $sql_table2 );
        }
        
        $table_name =  $this->_db->prefix . "webdesignby_musicbox_musicbox_tracks_assoc";
        if($this->_db->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $sql_table3 = "CREATE TABLE `" . $this->_db->prefix . "webdesignby_musicbox_musicbox_tracks_assoc` (
                    `musicbox_id` int(11) NOT NULL,
                    `track_id` int(11) NOT NULL,
                    `sort_order` int(11) DEFAULT NULL,
                    PRIMARY KEY (`musicbox_id`,`track_id`)
                  ) ENGINE=InnoDB " . $this->getCharsetCollate() . ";";

            // $this->_db->query($sql_table3);
            \dbDelta( $sql_table3 );
        }
    }
    
    public static function uninstall(){
        global $wpdb;
        $table1_name = $wpdb->prefix . "webdesignby_musicbox_tracks";
        if( self::tableExists($table1_name) ){
            $sql_table1 = "DROP TABLE `" . $table1_name . "`;";
            $wpdb->query($sql_table1); 
        }
        
        $table2_name = $wpdb->prefix  . "webdesignby_musicbox";
        if( self::tableExists($table2_name) ){
            $sql_table2 = "DROP TABLE `" . $table2_name . "`;";
            $wpdb->query($sql_table2); 
        }
        
        $table3_name = $wpdb->prefix . "webdesignby_musicbox_musicbox_tracks_assoc";
        if( self::tableExists($table3_name) ){
            $sql_table3 = "DROP TABLE `" . $table3_name . "`;";
            $wpdb->query($sql_table3); 
        }
        
    }

    
}