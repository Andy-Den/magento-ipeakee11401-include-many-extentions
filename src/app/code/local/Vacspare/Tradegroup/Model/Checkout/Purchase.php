<?php

class Vacspare_Tradegroup_Model_Checkout_Purchase extends Mage_Core_Model_Abstract
{
    
    protected function _getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }
    
    
    
    /*
     * save Payment Method
     * 
     */
    public function saveBilling()
    {
        $billing_data  = array();
        $customerAddressId = Mage::getSingleton('customer/session')->getCustomer()->getDefaultBilling();
        if ($customerAddressId){
            if(!empty($customerAddressId)){
               
                $billingAddress = Mage::getModel('customer/address')->load($customerAddressId);
                if(is_object($billingAddress) && $billingAddress->getCustomerId() ==  Mage::helper('customer')->getCustomer()->getId()){
                    $billing_data = array_merge($billing_data, $billingAddress->getData());
                    $this->_getOnepage()->saveBilling($billing_data, $customerAddressId);
                }
            }
        } 
    }
    
    
    /*
     * @save shipping address 
     * 
     */
    public function saveShipping(){
        $shipping_data  = array();
        $shippingAddressId = Mage::getSingleton('customer/session')->getCustomer()->getDefaultShipping();
        if(!empty($shippingAddressId)){
            $shippingAddress = Mage::getModel('customer/address')->load($shippingAddressId);
            if(is_object($shippingAddress) && $shippingAddress->getCustomerId() ==  Mage::helper('customer')->getCustomer()->getId()){
                $shipping_data = array_merge($shipping_data, $shippingAddress->getData());
                $this->_getOnepage()->saveBilling($shipping_data, $shippingAddressId);
            }
        }
    }
    
    
    /*
     * @Set Default shipping method
     * @param: config: tradegroups/general/defaul_shipping_method
     */
    public function saveDefaultShippingMethod(){
        $shipping_method = Mage::getStoreConfig('tradegroups/general/defaul_shipping_method');
        $this->_getOnepage()->saveShippingMethod($shipping_method);
    }
    
    
    /*
     * @Set Default Payment Method
     * @param: config: tradegroups/general/defaul_payment_method
     */
    public function saveDefaultPaymentMethod(){
        $payment_method = Mage::getStoreConfig('tradegroups/general/defaul_payment_method');
        $payment = array('method' => $payment_method);
        $this->_getOnepage()->savePayment($payment);
    }
    
    
    
    /*
     * @save order with step by step
     * 
     */
    public function saveInfo(){
        $this->saveBilling();
        $this->saveShipping();
        $this->saveDefaultShippingMethod();
        $this->saveDefaultPaymentMethod();
    }
    
}