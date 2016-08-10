<?php
class Balance_Sitemap_Model_Resource_Robots_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract{
    
    public function _construct()
    {
        $this->_init('balance_sitemap/robots');
    }
}