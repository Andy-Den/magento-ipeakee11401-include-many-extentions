<?php
class Balance_Sitemap_Model_Robots extends Mage_Core_Model_Abstract{
    
    const CACHE_TAG     = 'balance_sitemap_robots';
    protected $_cacheTag= 'balance_sitemap_robots';
    
    protected function _construct()
    {
        $this->_init('balance_sitemap/robots');
    }
    
    public function loadByStoreId($storeId){       
        $this->load($storeId, 'store_id');
        return $this;
    }
}