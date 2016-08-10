<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Balance_NoStepCheckout_CheckoutController extends Mage_Core_Controller_Front_Action {

    protected function _initProduct() {
        $productId = (int) $this->getRequest()->getParam('product');
        if ($productId) {
            $product = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($productId);
            if ($product->getId()) {
                return $product;
            }
        }
        return false;
    }

    public function indexAction() {
        $customerLoggedIn = Mage::helper('customer')->isLoggedIn();
        $helper = Mage::helper('nostepcheckout');
        $params = $this->getRequest()->getParams();
        
        $response = array();
        $formData = $params;
        if (!$this->_validateFormKey()) {
            echo 'Invalid Form Key';
            exit;
        }
        if (!$customerLoggedIn) {
            $response['redirect_url'] = Mage::getUrl('customer/account');
        } else {
            $product = $this->_initProduct();
            $formData['product'] = $product->getId();            

            $customer = Mage::getSingleton('customer/session')->getCustomer();
            if (!$helper->validateCustomerAddress($customer)) {
                $response['error'] = 'You must have default billing/shipping address';
            } else {
                try {
                    $orderCreator = Mage::getModel('nostepcheckout/order_creator');
                    $orderCreator->setCustomer($customer);
                    $requestInfo = array();
                    if(isset($params['super_attribute'])) {
                        $requestInfo['super_attribute'] = $params['super_attribute'];
                    }
                    if(isset($params['options'])) {
                        $requestInfo['serial_options'] = $params['options'];
                        $formData['serial_options'] = htmlentities(serialize($params['options']));
                    }
                    $orderCreator->addProduct($product, $requestInfo);
                    $creatorQuote = $orderCreator->getQuote();

                    $formBlock = $this->getLayout()->createBlock('nostepcheckout/checkout')->setTemplate('nostepcheckout/checkout.phtml');
                    $formBlock->setFormData($formData);
                    $formBlock->setBillingAddress($orderCreator->getBillingAddress());
                    $formBlock->setShippingAddress($orderCreator->getShippingAddress());
                    $formBlock->setProduct($product);
                    $formBlock->setCreatorQuote($creatorQuote);
                    $formContent = $formBlock->toHtml();
                    $response['content'] = $formContent;
                } catch (Exception $e) {
                    Mage::log($e->getMessage(), 1, 'nostepcheckout.log');
                }
                
            }
        }        
        $this->getResponse()->setBody(json_encode($response));
    }

    public function checkoutAction() {
        $customerLoggedIn = Mage::helper('customer')->isLoggedIn();
        if (!$customerLoggedIn) {
            $response['redirect_url'] = Mage::getUrl('customer/account');
        } else {
            try {
                if (!$this->_validateFormKey()) {
                    throw new Exception('Invalid Form key');                    
                }
                $product = $this->_initProduct();
                $params = $this->getRequest()->getParams();
//            $formData['patient_reference'] = $params['patient_reference'];

                $customer = Mage::getSingleton('customer/session')->getCustomer();

                $orderCreator = Mage::getModel('nostepcheckout/order_creator');
                $orderCreator->setCustomer($customer);
                $requestInfo = array();
                    if(isset($params['super_attribute'])) {
                        $requestInfo['super_attribute'] = $params['super_attribute'];
                    }
                    if(isset($params['serial_options'])) {
                        $requestInfo['serial_options'] = $params['serial_options'];
                    }
                    $orderCreator->addProduct($product, $requestInfo);
//                $orderCreator->addProduct($product);
                $order = $orderCreator->create();

                Mage::dispatchEvent('nostepcheckout_save_order_after', array('order' => $order, 'quote' => $orderCreator->getQuote()));
            } catch (Exception $e) {
                
                Mage::log($e->getMessage(), 1, 'nostepcheckout.log');                
            }
            if ($order && $order->getId() > 0) {
                $block = $this->getLayout()->createBlock('nostepcheckout/checkout_success')->setTemplate('nostepcheckout/checkout/success.phtml');
            } else {
                $block = $this->getLayout()->createBlock('nostepcheckout/checkout_failure')->setTemplate('nostepcheckout/checkout/failure.phtml');
            }

            $response['content'] = $block->toHtml();
            Mage::getSingleton('nostepcheckout/session')->clear();
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