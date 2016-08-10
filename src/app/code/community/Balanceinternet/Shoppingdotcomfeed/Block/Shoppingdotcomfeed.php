<?php

class Balanceinternet_Shoppingdotcomfeed_Block_Shoppingdotcomfeed extends Mage_Core_Block_Template {

    public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    public function getShoppingdotcomfeed() {
        if (!$this->hasData('shoppingdotcomfeed')) {
            $this->setData('shoppingdotcomfeed', Mage::registry('shoppingdotcomfeed'));
        }
        return $this->getData('shoppingdotcomfeed');
    }

}

