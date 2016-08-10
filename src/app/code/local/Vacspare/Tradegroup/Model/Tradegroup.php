<?php

class Vacspare_Tradegroup_Model_Tradegroup extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('tradegroup/tradegroup');
    }
}