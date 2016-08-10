<?php

class Balanceinternet_Shoppingdotcomfeed_Model_Mysql4_Feedproducts_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('shoppingdotcomfeed/feedproducts');
    }
}