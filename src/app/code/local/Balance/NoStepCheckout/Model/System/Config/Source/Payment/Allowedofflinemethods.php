<?php

class Balance_NoStepCheckout_Model_System_Config_Source_Payment_Allowedofflinemethods extends Mage_Adminhtml_Model_System_Config_Source_Payment_Allowedmethods {

    public function toOptionArray() {
        $methods = Mage::helper('payment')->getPaymentMethodList(true, true, true);        
        return $methods['offline']['value'];
    }

}