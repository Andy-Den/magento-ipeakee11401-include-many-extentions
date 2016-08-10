<?php

/**
 * 
 */
class Balance_Warranty_RegistrationController extends Mage_Core_Controller_Front_Action {

    /**
     * Load the index blocks
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->loadLayoutUpdates();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Register Your Warranty'));
        $this->renderLayout();
    }

    /**
     * Save the product from the registration form against the customer
     */
    public function saveProductRegistrationAction()
    {
        if (!$this->_getSession()->isLoggedIn()) {
            Mage::getSingleton('core/session')->addError('You must be logged in to view warranty information, please login below');
            $this->_redirect('customer/account/login');
            return;
        }
        $request = $this->getRequest();
        $product = $request->getParam('product');
        $warranty = Mage::getModel('warranty/warranty');
        $warranty->setCustomerId($this->_getSession()->getCustomerId());
        $warranty->setMake($product['make']);
        $warranty->setModel($product['model']);
        $warranty->setTerm($product['warranty_term']);
        $warranty->setSerial($product['serial']);
        $warranty->setDateOfPurchase($request->getParam('year') . '-' . $request->getParam('month') . '-' . $request->getParam('day'));
        $warranty->setPrice($product['price']);
        $warranty->setStoreOfPurchase($product['store']);
        $warranty->setPurchaseReasonPrice($product['purchase_reason_price']);
        $warranty->setPurchaseReasonFeatures($product['purchase_reason_features']);
        $warranty->setPurchaseReasonBrand($product['purchase_reason_brand']);
        $warranty->setPurchaseReasonOther($product['purchase_reason_other']);
        $warranty->save();
    }

    /**
     * 
     */
    public function saveCustomerContactAction()
    {
        $customerData = $this->getRequest()->getParam('contact');
        $customer = $this->_getSession()->getCustomer();
        $email = $customer->getEmail();

        $customer->setGender($customerData['gender']);
        $customer->setData('children_under_18', $customerData['children']);
        $customer->setData('owns_pets', $customerData['pets']);
        $customer->setData('warranty_postcode', $customerData['postcode']);
        $customer->setData('warranty_telephone', $customerData['telephone']);
        $customer->save();

        if($customerData['newsletter'] == 1) {
            Mage::getModel('newsletter/subscriber')->subscribe($email);
        }
    }

    /**
     * Redirects the user to the my account page with a notification
     */
    public function successAction()
    {

        Mage::getSingleton('core/session')->addSuccess('Warranty successfully saved. <a href="' . Mage::getUrl('warranty/registration/') . '">Click here</a> to register another product');
        $this->_redirect('warranty/list/');
        return;
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