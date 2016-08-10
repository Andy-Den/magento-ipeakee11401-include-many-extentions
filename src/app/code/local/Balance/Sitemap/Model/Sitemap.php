<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Balance_Sitemap_Model_Sitemap extends Mage_Sitemap_Model_Sitemap {
   
    protected function _beforeSave(){
        
        return $this;
    }
    
     /**
     * Return real file path
     *
     * @return string
     */
    protected function getPath()
    {
        return  $this->getSitemapPath();
    }

    /**
     * Return full file name with path
     *
     * @return string
     */
    public function getPreparedFilename()
    {
        return $this->getPath() . $this->getSitemapFilename();
    }

    public function loadByStoreId($storeId){       
        $this->load($storeId, 'store_id');
        return $this;
    }
    
    public function getFileSize(){
       if (file_exists($this->getPreparedFilename()))
        return filesize($this->getPreparedFilename());
       return 0; 
    }        
}
