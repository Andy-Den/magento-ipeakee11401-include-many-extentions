<?php
/**
 * Warranty registration login / registration
 *
 * @category   Balance
 * @package    Balance_Warranty
 * @author     Carey Sizer <carey@balanceinternet.com.au>
 */
class Balance_Warranty_Block_Registration_Login extends Balance_Warranty_Block_Registration_Abstract
{
    protected function _construct()
    {
        if (!$this->isCustomerLoggedIn()) {
            $this->getRegistration()->setStepData('login', array('label'=>Mage::helper('warranty')->__('Login or Register'), 'allow'=>true));
        }
        parent::_construct();
    }

    public function getMessages()
    {
        return Mage::getSingleton('customer/session')->getMessages(true);
    }

    public function getPostAction()
    {
        return Mage::helper('warranty/customer')->getLoginPostUrl();
    }

    public function getMethod()
    {
        return $this->getQuote()->getMethod();
    }

    public function getMethodData()
    {
        return $this->getCheckout()->getMethodData();
    }

    public function getSuccessUrl()
    {
        return $this->getUrl('*/*');
    }

    public function getErrorUrl()
    {
        return $this->getUrl('*/*');
    }

    /**
     * Retrieve username for form field
     *
     * @return string
     */
    public function getUsername()
    {
        return Mage::getSingleton('customer/session')->getUsername(true);
    }
    
    /**
     * Should show login
     * @return boolean
     */
    public function isShow()
    {
        return true;
    }
}
