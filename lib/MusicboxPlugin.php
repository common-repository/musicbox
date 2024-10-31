<?php

namespace Webdesignby;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once( 'Plugin.php' );

class MusicboxPlugin extends \Webdesignby\Plugin{
    
    protected   $_config;
    private     $_redirect;
    private     $_flash_message;
    const       itunes_affiliate_id = "1001l8uc";

    public function __construct($config){
        $this->_config = $config;
        
        // ajax handler function...
        \add_action( 'wp_ajax_itunes_search', array($this, 'itunes_search') );
        \add_action( 'wp_ajax_musicbox_addtrack', array($this, 'musicbox_addtrack') );
        \add_action( 'wp_ajax_musicbox_sort', array($this, 'musicbox_sort') );
        \add_action( 'wp_ajax_musicbox_tracks_sort', array($this, 'musicbox_tracks_sort') );
        \add_action( 'wp_ajax_refresh_musicbox', array($this, 'refresh_musicbox') );
        
        parent::__construct($config);
        
    }
    
    public function init(){
        return true;
    }
    
    public function enqueue_soundmanager(){
        \wp_enqueue_script( 'soundmanager2', $this->base_url . 'vendor/soundmanager/script/soundmanager2-jsmin.js', array(), "2.97a.20150601");
    }

    private function enqueue_jquery(){
        \wp_enqueue_script('jquery');
    }
    
    private function enqueue_jqueryui(){
        \wp_enqueue_script('jquery-ui-core');
        \wp_enqueue_script('jquery-ui-sortable');
        \wp_enqueue_script('jquery-ui-accordion');
    }
    
    public function admin_enqueue_scripts(){
        $screen = \get_current_screen();
        $musicbox_admin_screen_id = "toplevel_page_webdesignby-musicbox";
        if( $screen->id == $musicbox_admin_screen_id){
            $this->enqueue_jquery();
            $this->enqueue_jqueryui();
        }
        $this->enqueue_admin_styles();
    }
    
    public function enqueue_admin_styles(){
        
        // admin styles
        if( is_admin() ){
            wp_enqueue_style("musicbox-admin",  $this->base_url . 'styles/admin.css', array(), '1.0', 'screen');
            wp_enqueue_style("jquery-ui",  $this->base_url . 'js/jquery-ui-1.11.4.custom/jquery-ui.min.css', array(), '1.11.4', 'screen');
        }
        
    }
    
    /* ---------------------------------------------
     * 
     *  Ajax functions - add actions up above!!!
     */
    public function itunes_search(){
        
        $itunes = new iTunesInfo();
        $params = $_GET['params'];
        $result = $itunes->search($params);
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: application/json');
        echo $result;
        wp_die(); // this is required to terminate immediately and return a proper response
        
    }
    
    public function itunes_lookup($id){
        $itunes = new iTunesInfo();
        $result = $itunes->lookup($id);
        return $result;
    }
    
    public function musicbox_addtrack(){
        
        // track data
        $track_info = $_POST['track_info'];
        $musicbox_id = $_POST['musicbox_id'];
        
        $track_id = $track_info['track_id'];
        $track_name = $track_info['track_name'];
        $artist_id = $track_info['artist_id'];
        $artist_name = $track_info['artist_name'];
        $collection_id = $track_info['collection_id'];
        $collection_name = $track_info['collection_name'];
        $collection_censored_name = $track_info['collection_censored_name'];
        $track_censored_name = $track_info['track_censored_name'];
        $artist_view_url = $track_info['artist_view_url'];
        $collection_view_url = $track_info['collection_view_url'];
        $track_view_url = $track_info['track_view_url'];
        $preview_url = $track_info['preview_url'];
        $artworkUrl100 = $track_info['artworkUrl100'];
        $artworkUrl60 = $track_info['artworkUrl60'];
        
        $track_data = array(
            'musicbox_id' => $musicbox_id,
            'trackId' => $track_id,
            'trackName' => $track_name,
            'artistId' => $artist_id,
            'artistName' => $artist_name,
            'collectionId' => $collection_id,
            'collectionName' => $collection_name,
            'collectionCensoredName' => $collection_censored_name,
            'trackCensoredName' => $track_censored_name,
            'artistViewUrl' => $artist_view_url,
            'collectionViewUrl' => $collection_view_url,
            'trackViewUrl' => $track_view_url,
            'previewUrl' => $preview_url,
            'artworkUrl60' => $artworkUrl60,
            'artworkUrl100' => $artworkUrl100,
        );
        
        $add_result = $this->model->addTrack($track_data);
        if( ! $add_result ){
            $result['error'] = true;
            $message = "";
            foreach($this->model->getErrors() as $error ){
                foreach($error as $name=>$value){
                    $message .= $value. "\n";
                }
            }
            $result["message"] = $message;
        }else{
            $result['message'] = "track " . $track_id . " added.";
        }
        $result['artist_id'] = $artist_id;
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: application/json');
        echo json_encode($result);
        wp_die(); // this is required to terminate immediately and return a proper response
        
    }
    
    public function musicbox_sort(){
        // var_dump($_POST['order']);
        $order = \parse_str($_POST['order'], $array);
        $i = 0;
        foreach($array['musicbox'] as $id){
            $i++;
            $this->model->updateSort($id, $i);
        }
    }
    
    public function musicbox_tracks_sort(){

        if( empty($_POST['musicbox_id']) )
            return false;
        
        if( empty($_POST['order']))
            return false;
        
        $musicbox_id = (int) $_POST['musicbox_id'];
        \parse_str($_POST['order'], $array);
        $i = 0;
        foreach($array['track'] as $id){
            $i++;
            $this->model->updateTrackSort($musicbox_id, $id, $i);
        }

    }
    
    public function refresh_musicbox(){
        $musicbox_id = $_POST['musicbox_id'];
        $musicbox = $this->getMusicbox($musicbox_id);
        $data['musicboxes'][] = $musicbox;
        $lib_path = plugin_dir_path( __FILE__ );
        $view_path = realpath($lib_path . "../view");
        $this->page_slug = $this->plugin_slug = $plugin_slug = "webdesignby-musicbox";
        include( $view_path . "/admin/musicbox-content.php" );
        exit();
    }
    
    // END AJAX
    
    /*
     *  Admin Functionality
     */
    
    private function getItunesAffiliateId(){
        return self::itunes_affiliate_id;
    }
    
    public function createAdminMenu($config = array(), $parent = ""){
        // $this->admin_page = new \Webdesignby\AdminPage($config);
        $admin_page = new \Webdesignby\AdminPage($config);
        // DON'T PROCESS IF WE AREN'T USING THE PLUGIN!!
        if( $this->isPluginPage() ){
            $admin_page = $this->processAdminPage( $admin_page );
        }
        $this->admin_pages[] = $admin_page;
        \add_action('admin_menu', array($this, 'adminPage'));
    }
    
    /*
     * Hook Into Wordpress Here...
     */
    
    public function adminPage($config = array()){
        
        // DON'T PROCESS IF WE AREN'T USING THE PLUGIN!!
        if( $this->isPluginPage() ){
            if( isset($_POST['action']) ){
                \check_admin_referer("musicbox_" . $_POST['action']);
            }elseif( isset($_GET['action'] ) ){
                \check_admin_referer("musicbox_" . $_GET['action']);
            }

            $this->doRedirect();
        }
        parent::adminPage($config = array());
    }
    
    /* ----------------------------------
     * MUSICBOX ADMIN
     *
     * Admin Pages Action Controller...
     * 
     */
    private function processAdminPage( \Webdesignby\AdminPage $admin_page ){
     
        $action = $this->getAdminAction();
        $data = array();
        
        $data['message'] = $this->getFlashMessage();
        
        
        $data['musicbox_filter'] = "";
        if( isset($_GET['webdesignby-musicbox-filter'] ) ){
            $data['musicbox_filter'] = $_SESSION['webdesignby-musicbox-filter'] = $_GET['webdesignby-musicbox-filter'];
        }elseif( isset( $_SESSION['webdesignby-musicbox-filter'] ) ){
            $data['musicbox_filter'] = $_SESSION['webdesignby-musicbox-filter'];
        }
        
        // echo get_current_screen();
        
        // START Music Box Controller
        if( ! empty($action) && ($admin_page->page_slug == "webdesignby-musicbox-edit")){
            switch($action){
                case "insert":
                    $data = array(
                            'name' => strip_tags($_POST['name']),
                            'description' => strip_tags($_POST['description'])
                        );
                    $this->model->insert($data);
                    $this->setFlashMessage("added.");
                    $this->_redirect = \admin_url('admin.php?page=webdesignby-musicbox');
                    break;
                
                case "edit":
                    $row = $this->model->find($_GET['id']);
                    $musicbox = array();
                    if( ! empty($row) ){
                        $musicbox['id'] = $row->id;
                        $musicbox['added'] = $row->added;
                        $musicbox['name'] = $row->name;
                        $musicbox['description'] = $row->description;
                    }
                    $data['musicbox'] = $musicbox;
                    
                    break;
                    
               case "update":
                   $update = array(
                       "name" => $_POST['name'],
                       "description" => $_POST['description'],
                   );
                   $id = $_POST['id'];
                   $this->model->update($id, $update);
                   $this->setFlashMessage("updated.");
                   $this->_redirect = \admin_url('admin.php?page=webdesignby-musicbox');
                   break;
            }
        }else{

            if( ($action  == "add-track") && ( $admin_page->page_slug == $this->plugin_slug) ){
                $trackId = $_GET['id'];
                $track_info = $this->itunes_lookup($trackId);
                $data['trackinfo'] = $track_info;
            }elseif( ($action == "delete") && ( $admin_page->page_slug == $this->plugin_slug) ){
                $this->model->delete($_GET['id']);

            }elseif( ($action == "delete-track") && ( $admin_page->page_slug == $this->plugin_slug) ){
                $this->model->deleteTrack($_GET['id']);
            }
            
             $data['musicboxes'] = $this->getMusicboxes($data['musicbox_filter']);
             $data['musicboxes_list'] = $this->getMusicboxes();
             if( count($data['musicboxes_list']) == 1 ){
                 $data['musicbox_filter'] = $data['musicboxes_list'][0]['musicbox']->id;
             }
        }
        
        
        $admin_page->addData($data); 

        return $admin_page;
    }
    
    private function getMusicboxes( $filter_id = null ){
        
        $musicboxes = array();
        
        if( ! empty($filter_id) ){
            $musicbox = $this->getMusicbox($filter_id);
            if( ! empty($musicbox) ){
                $musicboxes[] = $musicbox;
                return $musicboxes;
            }
        }
        
        $musicboxes_r = $this->model->findAll();
        
        foreach($musicboxes_r as $musicbox_o){
            $musicbox = array();
            $tracks = $this->model->getTracks($musicbox_o->id);
            $musicbox['musicbox'] = $musicbox_o;
            $musicbox['tracks'] = $tracks;
            $musicbox['itunes_affiliate_id'] = $this->getItunesAffiliateId();
            $musicboxes[] = $musicbox;
        }  
        return $musicboxes;
    }
    
    public function getMusicbox($id){
        if( empty($id) )
            return false;
        
        $musicbox_o = $this->model->find($id);
        
        if( empty($musicbox_o) )
            return false;

        $musicbox = array();
        
        $tracks = $this->model->getTracks($musicbox_o->id);
        $musicbox['musicbox'] = $musicbox_o;
        $musicbox['tracks'] = $tracks;
        $musicbox['itunes_affiliate_id'] = $this->getItunesAffiliateId();

        return $musicbox;
    }
    
    private function getAdminAction(){
        $actions = array('edit', 'update', 'insert', 'delete', 'add-track', 'delete-track', 'refresh_musicbox');
        if( isset($_POST['action']) ){
            if(in_array($_POST['action'], $actions)){
                return $_POST['action'];
            }
        }elseif( isset($_GET['action']) ){
            if(in_array($_GET['action'], $actions)){
                return $_GET['action'];
            }
        }
    }
    
    private function doRedirect(){
        if( ! empty($this->_redirect) ){
            if( !empty($this->_flash_message) ){
                $_SESSION[ $this->plugin_key . 'flash_message'] = __($this->_flash_message, $this->plugin_slug);
            }
            \wp_redirect($this->_redirect);
             exit();
        }
    }
    
    private function setFlashMessage($message){
        if( ! isset($_SESSION) )
            session_start();
        
        $_SESSION[ $this->plugin_key . 'flash_message'] = $message;
    }
   
    private function getFlashMessage(){
        if( ! isset($_SESSION) )
            session_start();
        if( isset($_SESSION[ $this->plugin_key . 'flash_message']) ){
            $message =  $_SESSION[ $this->plugin_key . 'flash_message'];
             unset($_SESSION[ $this->plugin_key . 'flash_message']);
            return $message;
        }
       
    }
    
    private function isPluginPage(){
         if( isset($_GET['page']) && (stristr($_GET['page'], $this->plugin_slug))){
             return true;
         }
         return false;
    }
    
     public function admin_head(){
         ?>
   <style>
    #adminmenu .toplevel_page_<?php echo $this->plugin_slug; ?> div.wp-menu-image:before{
        content: "\f127";
    }
    </style>
    <?php
        }
        
    public function uninstall(){
        if(session_id() == '' || !isset($_SESSION)) {
            // session isn't started
            session_start();
        }
        if( isset( $_SESSION['webdesignby-musicbox-filter'] ) ){
            $_SESSION['webdesignby-musicbox-filter'] = "";
            unset($_SESSION['webdesignby-musicbox-filter']);
        }
       
        \Webdesignby\MusicboxModel::uninstall();
    }
    
}

