<?php

class Balance_NoStepCheckout_CartController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $customerLoggedIn = Mage::helper('customer')->isLoggedIn();
        $helper = Mage::helper('nostepcheckout');
        $params = $this->getRequest()->getParams();

        $response = array();
        $formData = $params;

        if (!$customerLoggedIn) {
            $response['redirect_url'] = Mage::getUrl('customer/account');
        } else {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            if (!$helper->validateCustomerAddress($customer)) {
                $response['error'] = 'You must have default billing/shipping address';
            } else {
                try {
                    $quoteConvertor = Mage::getModel('nostepcheckout/quote_convertor');
                    $creatorQuote = $quoteConvertor->getQuote();

                    $formBlock = $this->getLayout()->createBlock('nostepcheckout/cart')->setTemplate('nostepcheckout/cart.phtml');
                    $formBlock->setFormData($formData);
                    $formBlock->setBillingAddress($quoteConvertor->getBillingAddress());
                    $formBlock->setShippingAddress($quoteConvertor->getShippingAddress());
                    $formBlock->setCreatorQuote($creatorQuote);
                    $formContent = $formBlock->toHtml();
                    $response['content'] = $formContent;
                } catch (Exception $e) {
                    Mage::log($e->getMessage(), 1, 'nostepcheckout.log');
                }
            }
        }
        $response = json_encode($response);
        $this->getResponse()->setBody($response);
    }

    public function checkoutAction() {
        $customerLoggedIn = Mage::helper('customer')->isLoggedIn();
        if (!$customerLoggedIn) {
            $response['redirect_url'] = Mage::getUrl('customer/account');
        } else {
            try {
//                if (!$this->_validateFormKey()) {
//                    throw new Exception('Invalid Form key');
//                }


//            $formData['patient_reference'] = $params['patient_reference'];
//                $customer = Mage::getSingleton('customer/session')->getCustomer();
                $quoteConvertor = Mage::getModel('nostepcheckout/quote_convertor');
                $order = $quoteConvertor->convert();
                Mage::dispatchEvent('nostepcheckout_save_order_after', array('order' => $order, 'quote' => $quoteConvertor->getQuote()));
            } catch (Exception $e) {

                Mage::log($e->getMessage(), 1, 'nostepcheckout.log');
            }
            if ($order && $order->getId() > 0) {
                $block = $this->getLayout()->createBlock('nostepcheckout/checkout_success')->setTemplate('nostepcheckout/cart/success.phtml');
            } else {
                $block = $this->getLayout()->createBlock('nostepcheckout/checkout_failure')->setTemplate('nostepcheckout/cart/failure.phtml');
            }

            $response['content'] = $block->toHtml();
        }
        $this->getResponse()->setBody(json_encode($response));
    }

    protected function _validateFormKey() {
        if (!($formKey = $this->getRequest()->getParam('form_key', null))
                || $formKey != Mage::getSingleton('core/session')->getFormKey()) {
            return false;
        }
        return true;
    }

}
