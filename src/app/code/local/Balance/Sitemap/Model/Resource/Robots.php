<?php
class Balance_Sitemap_Model_Resource_Robots extends Mage_Core_Model_Resource_Db_Abstract{
    protected function _construct()
    {
        $this->_init('balance_sitemap/robots', 'robots_id');
    }
    
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {        
        if (!$this->getIsRobotsUniqueToStore($object)) {
            Mage::throwException(Mage::helper('balance_sitemap')->__('A Robots.txt already exists for the selected store.'));
        }

        if (! $object->getId()) {
            $object->setCreationTime(Mage::getSingleton('core/date')->gmtDate());
        }
        $object->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());
        return $this;
    }
    
    /**
     * Check for unique  to selected store(s).
     *
     * 
     */
    public function getIsRobotsUniqueToStore(Mage_Core_Model_Abstract $object)
    {        
        $storeId = $object->getData('store_id');
        $select = $this->_getReadAdapter()->select()
            ->from(array('r' => $this->getMainTable()))
            ->where('r.store_id = ?', $storeId)
            ->where('r.is_active = ?', 1);

        if ($object->getId()) {
            $select->where('r.robots_id <> ?', $object->getId());
        }

        if ($this->_getReadAdapter()->fetchRow($select)) {
            return false;
        }

        return true;
    }

}
