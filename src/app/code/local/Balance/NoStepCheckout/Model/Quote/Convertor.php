<?php

class Balance_NoStepCheckout_Model_Quote_Convertor extends Mage_Core_Model_Abstract {

    private $_quote;
    private $_shippingAddress;
    private $_billingAddress;
    private $_shippingMethodCode;
    private $_limitCarrier;
    private $_paymentMethodCode;
    
    private $_orderStatus;

    public function _construct() {
        parent::_construct();
        $this->_quote = Mage::getSingleton('checkout/session')->getQuote();
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $this->_billingAddress = $this->_getAddress($customer->getDefaultBillingAddress(), 'billing');
        $this->_shippingAddress = $this->_getAddress($customer->getDefaultShippingAddress(), 'shipping');
        /* $this->_shippingMethodCode = Mage::helper('nostepcheckout')->getConfigData('order_from_cart/shipping_method');
        $this->_paymentMethodCode = Mage::helper('nostepcheckout')->getConfigData('order_from_cart/payment_method');
        $this->_orderStatus = Mage::helper('nostepcheckout')->getConfigData('order_from_cart/order_status'); */
        /* BEGIN REF: GOD-1823*/
        $this->_shippingMethodCode = Mage::getStoreConfig('balance_nostepcheckout/order_from_product/shipping_method');
        $this->_paymentMethodCode = 'checkmo';
        $this->_orderStatus = Mage::getStoreConfig('balance_nostepcheckout/order_from_product/order_status');
        /* END REF: GOD-1823*/
        $this->_limitCarrier = $this->_setLimitCarrier();
    }

    private function _setLimitCarrier() {
        if (strstr($this->_shippingMethodCode, '_')) {
            $temp = explode('_', $this->_shippingMethodCode);
            $this->_limitCarrier = $temp[0];
        } else {
            $this->_limitCarrier = $this->_shippingMethodCode;
        }
        return $this->_limitCarrier;
    }

    public function convert() {
        if (!Mage::helper('customer')->isLoggedIn())
            return;
        try {
            $customer = Mage::getSingleton('customer/session')->getCustomer();

            $quote = Mage::getSingleton('checkout/session')->getQuote();
            $quote->assignCustomer($customer);
            $quote->setBillingAddress($this->getBillingAddress());
            $quote->setShippingAddress($this->getShippingAddress());

            $quote->getShippingAddress()->setCollectShippingRates(true)->collectShippingRates()
                    ->setShippingMethod($this->_shippingMethodCode)->setPaymentMethod($this->_paymentMethodCode);
            $quote->getPayment()->importData(array('method' => $this->_paymentMethodCode));
            $service = Mage::getModel('sales/service_quote', $quote);
            $service->submitAll();
            $order = $service->getOrder();
            if ($order) {
                try {
                    $order->setStatus($this->_orderStatus)->save();
                    $order->sendNewOrderEmail();
                } catch (Exception $e) {
                    Mage::log($e->getMessage(), 1, 'nostepcheckout.log');
                }
            }
            if ($order->getId()) {
                $quote->setIsActive(false)->save();
            }
            Mage::getSingleton('nostepcheckout/session')->setLastNscOrderId($order->getId());
            Mage::getSingleton('nostepcheckout/session')->setLastNscRealOrderId($order->getIncrementId());
            Mage::dispatchEvent('nostepcheckout_order_save_after', array('order' => $order));
            return $order;
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
            Mage::log($e->getMessage(), 1, 'nostepcheckout.log');
        }
        return;
    }

    public function getQuote() {
        if ($this->_quote) {
            $this->_quote->setShippingAddress($this->getShippingAddress())->getShippingAddress()->setLimitCarrier($this->_limitCarrier)->setShippingMethod($this->_shippingMethodCode)->setCollectShippingRates(true)->collectShippingRates();
            $this->_quote->collectTotals();
            return $this->_quote;
        }
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    protected function _getAddress($address, $type = 'billing') {
        $helper = Mage::helper('nostepcheckout');
        $prefix = $type . '_address_cart_form/';
        $fields = $helper->getAddressFields();
        if (!$helper->getConfigData($prefix . 'use_customer_default')) {
            $address = Mage::getModel('sales/quote_address');
            $address->setAddressType($type);
            foreach ($fields as $field) {
                $address->setData($field, $helper->getConfigData($prefix . $field));
            }
        }

        return $address;
    }

    public function getBillingAddress() {
        return $this->_billingAddress;
    }

    public function getShippingAddress() {
        return $this->_shippingAddress;
    }

}