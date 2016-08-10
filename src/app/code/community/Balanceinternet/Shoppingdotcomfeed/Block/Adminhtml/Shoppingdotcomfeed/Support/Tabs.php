<?php

class Balanceinternet_Shoppingdotcomfeed_Block_Adminhtml_Shoppingdotcomfeed_Support_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('shoppingdotcomfeed_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('shoppingdotcomfeed')->__('Help / Support'));
    }

    protected function _beforeToHtml() {
        $this->addTab('form_section', array(
            'label' => Mage::helper('shoppingdotcomfeed')->__('Help Information'),
            'title' => Mage::helper('shoppingdotcomfeed')->__('Help Information'),
            'content' => $this->getLayout()->createBlock('shoppingdotcomfeed/adminhtml_shoppingdotcomfeed_support_tab_form')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }

}