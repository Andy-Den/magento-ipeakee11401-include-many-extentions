<?php

class Balanceinternet_Shoppingdotcomfeed_Block_Adminhtml_Catalog extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'adminhtml_catalog';
        $this->_blockGroup = 'shoppingdotcomfeed';
        $this->_headerText = Mage::helper('shoppingdotcomfeed')->__("Step 2 of 3 - Select Products from the grid, Select 'Add Products' from the Action drop down and 'submit'.");
        parent::__construct();
        $this->_removeButton('add');
    }

}