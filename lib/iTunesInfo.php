<?php

/*
 *  https://www.apple.com/itunes/affiliates/resources/documentation/itunes-store-web-service-search-api.html
 */
namespace Webdesignby;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class iTunesInfo{
 
    private $base_url = "https://itunes.apple.com/";
    private $request_url;
    private $response;
    
    public function search($params){
     
        $search_url = $this->getSearchUrl($params);
        $this->response = $this->getResponse($search_url);
        return $this->response;
    }
    
    public function lookup($id){
        $lookup_url = $this->getLookupUrl($id);
        $this->response = $this->getResponse($lookup_url);
        return $this->response;
    }
    
    private function getSearchUrl($params){
        $search_url = $this->request_url = $this->base_url . "search?term=" . urlencode($params);
        return $search_url;
    }
    
    private function getLookupUrl($id){
        $lookup_url = $this->request_url = $this->base_url . "lookup?id=" . (int) $id;
        return $lookup_url;
    }
    
    private function getResponse($url){
        return file_get_contents($url);
    }
    
}
