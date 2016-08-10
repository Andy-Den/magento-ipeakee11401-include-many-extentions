<?php

class AHT_Backupcms_Model_Mysql4_Backupcms extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the backupcms_id refers to the key field in your database table.
        $this->_init('backupcms/backupcms', 'backupcms_id');
    }
}