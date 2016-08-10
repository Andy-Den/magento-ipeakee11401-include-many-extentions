<?php

class Balance_NoStepCheckout_Block_Abstract extends Mage_Checkout_Block_Onepage_Abstract {

    protected function _toHtml() {
        $helper = Mage::helper('nostepcheckout');
        if ($helper->isEnabled()) {
            return parent::_toHtml();
        }
        return '';
    }
}