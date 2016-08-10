<?php

class AHT_Backupcms_Model_Backupcms extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('backupcms/backupcms');
    }
}