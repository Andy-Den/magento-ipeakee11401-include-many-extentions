<?php

class Balance_NoStepCheckout_Block_Cart_Form extends Mage_Checkout_Block_Cart_Abstract {
    protected function _toHtml() {
        $helper = Mage::helper('nostepcheckout');
        if($helper->isEnabled()) {
            return parent::_toHtml();
        }
        return '';
               
    }
    public function getSubmitUrl($additional = array()) {        
        $routeParams = array(           
//            Mage_Core_Model_Url::FORM_KEY => Mage::getSingleton('core/session')->getFormKey()
        );   
        return Mage::helper('nostepcheckout')->formatSecureUrl(Mage::getUrl('nostepcheckout/cart/index', $routeParams));
    }
            
}