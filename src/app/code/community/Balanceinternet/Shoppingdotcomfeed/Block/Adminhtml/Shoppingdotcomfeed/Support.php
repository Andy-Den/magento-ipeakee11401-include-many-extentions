<?php

class Balanceinternet_Shoppingdotcomfeed_Block_Adminhtml_Shoppingdotcomfeed_Support extends Mage_Adminhtml_Block_Widget_Form_Container {

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
            return Mage::helper('shoppingdotcomfeed')->__('Add Item');
        }
    }

    protected function _afterToHtml($html) {
        $filename = Mage::helper('shoppingdotcomfeed')->getFileNameForFeed($this->getRequest()->getParam('id_feed'));
        $html = null;
        $html = '<h2>Help / Support</h2>';
        $html .= '<h4 class="icon-head head-adminhtml-shoppingdotcomfeed">How to add a feed:</h4>';
        $html .= '<p>To create a feed to Shopping.com you first require an existing account to link to the country portal. Please select the relevant country from the following list to visit the shopping.com portal and create your account.  Once you have setup your account, please note your FTP details and enter them into the feed details below so that the products can be uploaded to shopping.com.</p>';
        return $html;
    }

}

