<?php

class Balance_Warranty_Model_Resource_Warranty extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Internal constructor
     */
    protected function _construct() {
        $this->_init('warranty/warranty', 'id');
    }
}