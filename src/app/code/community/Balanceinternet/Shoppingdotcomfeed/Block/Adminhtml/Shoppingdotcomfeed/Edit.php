<?php

class Balanceinternet_Shoppingdotcomfeed_Block_Adminhtml_Shoppingdotcomfeed_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();
        $badge_id = $this->getRequest()->getParam('id');

        $this->_objectId = 'id';
        $this->_blockGroup = 'shoppingdotcomfeed';
        $this->_controller = 'adminhtml_shoppingdotcomfeed';

        $this->_updateButton('save', 'label', Mage::helper('shoppingdotcomfeed')->__('Save Feed'));
        $this->_updateButton('delete', 'label', Mage::helper('shoppingdotcomfeed')->__('Delete Feed'));
        $this->_removeButton('reset');
    }

    public function getHeaderText() {
        if (Mage::registry('shoppingdotcomfeed_data') && Mage::registry('shoppingdotcomfeed_data')->getId()) {
            return Mage::helper('shoppingdotcomfeed')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('shoppingdotcomfeed_data')->getName()));
        } else {
            return Mage::helper('shoppingdotcomfeed')->__('Step 1 of 3 - Add Item');
        }
    }

}