<?php

class Balance_Sitemap_Helper_Data extends Mage_Sitemap_Helper_Data
{
    public function clearOldSitemaps($installer)
    {        
      try
      {         
        $connection = $installer->getConnection();  
        $table = $installer->getTable('sitemap/sitemap');             
        $query = 'SELECT sitemap_filename,sitemap_path FROM ' . $table;      
        $rows = $connection->fetchAll($query);
        foreach($rows as $row){
         $this->_deleteOldSitemapFile($row['sitemap_filename'], $row['sitemap_path']);
        }
        if (!empty($rows)){         
         if(!$connection->delete($table)){
             Mage::throwException('Cannot clear sitemap information!');
         }
        } 
        
       }catch (Exception $ex) 
       {
         Mage::log($ex);
         throw $ex;
        }
                
    } 
    
    protected function _deleteOldSitemapFile($filename,$sitemapPath)
    {
        //check the old extension path works
        $path = str_replace('//', '/', Mage::getBaseDir() .$sitemapPath).$filename;
        if (file_exists($path)){
            if (!unlink($path)){
                Mage::throwException('Cannot delete file :'.$path);
            }
        }
        
        //check for new extension paths
        $path = $sitemapPath.$filename;
        if (file_exists($path)){
            if (!unlink($path)){
                Mage::throwException('Cannot delete file :'.$path);
            }
        }
    }
    
}