<?php
/**
 * Warranty resource
 */
class Balance_Warranty_Model_Resource_Warranty_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Construct and initialize the model
     */
    protected function _construct()
    {
        $this->_init('warranty/warranty');
    }
}