<?php

class Balanceinternet_Shoppingdotcomfeed_Block_Adminhtml_Preselect extends Mage_Adminhtml_Block_Template {

    public function __construct() {
        parent::__construct();
        $this->productids = Mage::getModel('shoppingdotcomfeed/feedproducts')->getResource()->getProductIdsForFeed($this->getRequest()->getParam('id_feed'));
    }

    protected function _prepareLayout() {
        parent::_prepareLayout();
    }

}