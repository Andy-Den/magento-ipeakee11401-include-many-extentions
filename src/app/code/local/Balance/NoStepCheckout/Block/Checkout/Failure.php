<?php

class Balance_NoStepCheckout_Block_Checkout_Failure extends Mage_Checkout_Block_Onepage_Failure {
    public function getRealOrderId()
    {
        return Mage::getSingleton('nostepcheckout/session')->getLastNscRealOrderId();
    }

    /**
     *  Payment custom error message
     *
     *  @return	  string
     */
    public function getErrorMessage ()
    {
        $error = Mage::getSingleton('nostepcheckout/session')->getErrorMessage();
        // Mage::getSingleton('checkout/session')->unsErrorMessage();
        return $error;
    }

   
}