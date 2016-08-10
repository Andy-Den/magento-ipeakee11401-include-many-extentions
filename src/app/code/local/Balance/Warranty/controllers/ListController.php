<?php
/**
 * The controller that shows the list of warranties on the account page
 */
class Balance_Warranty_ListController extends Mage_Core_Controller_Front_Action
{
    /**
     * Load the index blocks
     */
    public function indexAction()
    {
        if (!$this->_getSession()->isLoggedIn()) {
            Mage::getSingleton('core/session')->addError('You must be logged in to view warranty information, please login below');
            $this->_redirect('customer/account/login');
            return;
        }
        $this->loadLayout();
        $this->loadLayoutUpdates();
        $this->renderLayout();
    }
    
    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
}