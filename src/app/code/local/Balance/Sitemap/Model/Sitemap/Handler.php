<?php
class Balance_Sitemap_Model_Sitemap_Handler {
    
    const SITEMAP_FOLDER             = '/sitemap/';
    
    const XML_FILE_PATH              = 'sitemap/generate/file_path';
    
    private $_helper;
    
    protected function _getHelper(){
        if (is_null($this->_helper)){
            $this->_helper = Mage::helper('balance_sitemap');            
        }
        return $this->_helper;
    }
    
    /**
     * 
     * @param string $dirName
     * @return int
     */
    public function isSitemapPathWritable($dirName,$undo = false)
    {                
        $errorCode = 1;
        if (!file_exists($dirName)){                    
            return 2;
        }    
        if(!is_dir($dirName)){
            return 3;
        }   
        
        $path = $dirName.self::SITEMAP_FOLDER;
        
        $pathExists = file_exists($path); 
        
        if ($pathExists || mkdir($path ,0664))
        {                  
            $testFile = $path.'/test.txt';      
            $f = null;
            if (($f = @fopen($testFile, 'w')))
            {
                $len = (int)@fwrite($f, 'test');
                if ( $len > 0) {
                  $errorCode = 0;
                }                 
            }
            if($f){
               @fclose($f);
               @unlink($testFile);
            }
            if (!$pathExists || $undo){
                @rmdir(path);
            }
        }
        return $errorCode;
    }    
    
    public function getFilePath(){
        $path = Mage::getStoreConfig(self::XML_FILE_PATH);
        if (!$path){
            $path = Mage::getBaseDir('media');
        }
        return strtolower($path);
    }
    
    public function getSitemapFilePath(){
       return $this->getFilePath().self::SITEMAP_FOLDER; 
    }
    
    public function getSitemapFilename($storeId){    
        return 'sitemap_store_id_'.$storeId.'.xml';
    }     


    public function getMessageForCode($errorCode){
        
        $message = $this->_getHelper()->__('Unknown Error');
        switch ($errorCode){
            case 0:
                $message = $this->_getHelper()->__('The specfied path exists and its writable.');
                break;
            case 1:
                $message = $this->_getHelper()->__('The directory specified does not have write permission!');    
                break;
            case 2:
                $message = $this->_getHelper()->__('The directory specified does not exist!');    
                break;                
            case 3:
                $message = $this->_getHelper()->__('The specified path is not a directory!');    
                break;
        }
        return $message;
    }
    
    public function initSitemaps()
    {                
        $this->_initSitmapDirectory();
        $this->_initSitemapModel();                
    }
   
    protected function _initSitmapDirectory()
    {        
        $path = $this->getFilePath();        
        if (!$path){
            Mage::throwException($this->__('Invalid File Path'));            
        }                
        $errorCode = $this->isSitemapPathWritable($path);        
        if ($errorCode > 0){
            Mage::throwException($this->getMessageForCode($errorCode));           
        }             
    }
    
    protected function _initSitemapModel(){
                
        $storesAvailable = array_keys(Mage::app()->getStores());        
        $storesInSitemap = array();
        $sitemapPath = $this->getSitemapFilePath();        
        $sitemaps = Mage::getModel('sitemap/sitemap')->getCollection();
        
        foreach ($sitemaps as $sitemap){
            if (strtolower($sitemap->getSitemapPath()) == $sitemapPath){                
                $storesInSitemap[] = $sitemap->getStoreId();
                //update file names
                $sitemap->setSitemapFilename($this->getSitemapFilename($sitemap->getStoreId()));
                $sitemap->setSitemapPath($sitemapPath);
                $sitemap->save();
            }
            else{
                // path is different
                $sitemap->delete();
            }   
        }
        // new stores
        $newSitemapStores  = array_diff($storesAvailable,$storesInSitemap);
        
        //insert new store site maps
        foreach ($newSitemapStores as $storeId){
            $sitemap = Mage::getModel('sitemap/sitemap');
            $sitemap->setSitemapFilename($this->getSitemapFilename($storeId));
            $sitemap->setSitemapPath($sitemapPath);
            $sitemap->setStoreId($storeId);
            $sitemap->save();
        }                     
    }
    
    public function getSitemap($storeId){
       // check valid store
       $validStore = false;
       if ($storeId && Mage::app()->getStore($storeId)->getId()){
           $validStore = true;
       }
       if (!$validStore){
           Mage::throwException($this->__('Invalid Store!'));
       }
     
       $sitemap = Mage::getModel('sitemap/sitemap')->loadByStoreId($storeId);
       $sitemapPath = $this->getSitemapFilePath();        
       //save site map if its not availale       
       if (!$sitemap->getId()){
            $sitemap->setSitemapFilename($this->getSitemapFilename($storeId));
            $sitemap->setSitemapPath($sitemapPath);
            $sitemap->setStoreId($storeId);
            $sitemap->save();
       } 
       
       return $sitemap;
    }


    public function outputXml($storeId)
    {       
       $sitemap = $this->getSitemap($storeId);     
       if(!file_exists($sitemap->getPreparedFilename())){
           $this->_initSitmapDirectory();
           $sitemap->generateXml();
       }
       $file =  fopen($sitemap->getPreparedFilename(),'r');
       if ($file){                  
            while(!feof($file)){
               print fread($file, 4096);                    
            }
            fclose($file);
            return;
       }
        Mage::throwException($this->_getHelper()->__('Sitemap file not found!'));
     }
     
     public function outputRobotsTxt($storeId)
    {       
       
       $robots = Mage::getModel('balance_sitemap/robots')->loadByStoreId($storeId);
       if (!$robots->getId() || !$robots->getIsActive()){                      
           $robots->loadByStoreId(0);//load default
        }
        
        if (($content = $robots->getContent()) && $robots->getIsActive() ){
             print $content;
             return;
         }
                            
        Mage::throwException($this->_getHelper()->__('Robots.txt file not found!'));
     }
}

