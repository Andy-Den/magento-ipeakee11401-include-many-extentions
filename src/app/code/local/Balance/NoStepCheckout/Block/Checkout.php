<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Balance_NoStepCheckout_Block_Checkout extends Balance_NoStepCheckout_Block_Abstract {

    public function getSubmitUrl() {
        $routeParams = array(
            Mage_Core_Model_Url::FORM_KEY => Mage::getSingleton('core/session')->getFormKey()
        );
        return Mage::helper('nostepcheckout')->formatSecureUrl(Mage::getUrl('nostepcheckout/checkout/checkout', $routeParams));
    }

}