<?php
/**
 * One page warranty registration common functionality block
 *
 * @category Balance
 * @package Balance_Warranty
 * @author  Carey Sizer <carey@balanceninternet.com.au>
 */
abstract class Balance_Warranty_Block_Registration_Abstract extends Mage_Core_Block_Template
{
    /**
     * The customer
     * @var Mage_Customer_Model_Customer 
     */
    protected $_customer;
    
    /**
     * The registration session
     * @var Balance_Warranty_Model_Registration_Session 
     */
    protected $_session;
    

    /**
     * Get logged in customer
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if (empty($this->_customer)) {
            $this->_customer = Mage::getSingleton('customer/session')->getCustomer();
        }
        return $this->_customer;
    }

    /**
     * Retrieve checkout session model
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getRegistration()
    {
        if (empty($this->_session)) {
            $this->_session = Mage::getSingleton('warranty/registration_session');
        }
        return $this->_session;
    }

    public function isCustomerLoggedIn()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }
    
    public function isShow()
    {
        return true;
    }
}