<?php

class Balanceinternet_Shoppingdotcomfeed_Block_Adminhtml_Shoppingdotcomfeed extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'adminhtml_shoppingdotcomfeed';
        $this->_blockGroup = 'shoppingdotcomfeed';
        $this->_headerText = Mage::helper('shoppingdotcomfeed')->__('Shopping.com  Feed Manager');
        $this->_addButtonLabel = Mage::helper('shoppingdotcomfeed')->__('Add Feed');
        parent::__construct();
    }

}