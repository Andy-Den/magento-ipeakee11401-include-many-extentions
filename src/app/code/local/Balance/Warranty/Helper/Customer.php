<?php
/**
 * The helper for the customer registration functions
 */
class Balance_Warranty_Helper_Customer extends Mage_Core_Helper_Data
{
    /**
     * Retrieve customer register form post url
     *
     * @return string
     */
    public function getRegisterPostUrl()
    {
        return $this->_getUrl('warranty/registration_customer_account/createPost');
    }
    
    public function getLoginPostUrl()
    {
        return $this->_getUrl('warranty/registration_customer_account/loginPost');
    }
    
}