<?php

class Balance_NoStepCheckout_Model_Order_Creator extends Mage_Core_Model_Abstract {

    private $_storeId = '0';
    private $_currency = 'AUD';
    private $_customer = null;
    private $_quote = null;
    private $_products = array();
    private $_orderData = array();
    private $_paymentMethodCode = 'checkmo';
    private $_shippingMethodCode = 'flatrate_flatrate';
    private $_orderComment = '';
    
    private $_billingAddress;
    private $_shippingAddress;
    private $_limitCarrier;
    
    private $_sendConfirmation = '0';
    
    private $_orderStatus;
        
    public function _construct() {
        parent::_construct();
        $this->_storeId = Mage::app()->getStore()->getStoreId();
        $this->_currency = Mage::app()->getStore()->getCurrentCurrencyCode();
        $this->_shippingMethodCode = Mage::helper('nostepcheckout')->getConfigData('order_from_product/shipping_method');
        $this->_paymentMethodCode = Mage::helper('nostepcheckout')->getConfigData('order_from_product/payment_method');
        $this->_limitCarrier = $this->_setLimitCarrier();
        $this->_sendConfirmation = Mage::helper('nostepcheckout')->getConfigData('order_from_product/send_order_confirmation');
        $this->_orderStatus = Mage::helper('nostepcheckout')->getConfigData('order_from_product/order_status');
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

    public function setCustomer($customer = null) {
        if ($customer) {
            if (is_numeric($customer) && $customer > 0) {
                $customer = Mage::getModel('customer/customer')->load($customer);
            }
            $this->_customer = $customer;
        } else {
            $this->_customer = Mage::getSingleton('customer/session')->getCustomer();
        }

        return $this;
    }

    public function setOrderComment($comment = '') {
        $this->_orderComment = $comment;
        return $this;
    }

    public function addProduct($product, $requestInfo = null) {
        $productId = 0;
        if (is_numeric($product) && $product > 0) {
            $productId = $product;
        } else if ($product instanceof Mage_Catalog_Model_Product) {
            $productId = $product->getId();
        }
        if ($productId <= 0) {
            return $this;
        }

        $qty = 1;
        $superAttribute = array();
        $options = array();
        if ($requestInfo) {
            if (is_numeric($requestInfo) && $requestInfo > 0) {
                $qty = $requestInfo;
            } else if (is_array($requestInfo)) {
                if (isset($requestInfo['qty']) && $requestInfo['qty'] > 0) {
                    $qty = $requestInfo['qty'];
                }
                if(isset($requestInfo['super_attribute']) && count($requestInfo['super_attribute']) > 0) {
                    $superAttribute = $requestInfo['super_attribute'];
                }

                if(isset($requestInfo['serial_options']) && $requestInfo['serial_options'] != '') {
                    $options = unserialize($requestInfo['serial_options']);
                }
            }
        }

        $this->_products[$productId] = array('qty' => 1);
        if(count($superAttribute) > 0) {
            $this->_products[$productId]['super_attribute'] = $superAttribute;
        }

        if(count($options) > 0) {

            $this->_products[$productId]['options'] = $options;
        
        }
        
        return $this;
    }

    public function quickCreate($product, $customer) {
        $this->addProduct($product);
        $this->setCustomer($customer);
        return $this->create();
    }

    protected function prepareData() {
        $this->_orderData['session'] = array(
            'customer_id' => $this->_customer->getId(),
            'store_id' => $this->_storeId
        );

        $this->_orderData['payment'] = array(
            'method' => $this->_paymentMethodCode
        );

        $this->_orderData['add_products'] = $this->_products;


        $billingAddress = $this->_getAddress($this->_customer->getDefaultBillingAddress(), 'billing');
        $this->_billingAddress = $billingAddress;

        $shippingAddress = $this->_getAddress($this->_customer->getDefaultShippingAddress(), 'shipping');
        $this->_shippingAddress = $shippingAddress;

        $this->_orderData['order'] = array(
            'currency' => $this->_currency,
            'account' => array(
                'group_id' => $this->_customer->getGroupId(),
                'email' => $this->_customer->getEmail()
            ),
            'billing_address' => array(
                'customer_address_id' => $billingAddress->getId(),
                'prefix' => $billingAddress->getPrefix(),
                'firstname' => $billingAddress->getFirstname(),
                'middlename' => $billingAddress->getMiddlename(),
                'lastname' => $billingAddress->getLastname(),
                'suffix' => $billingAddress->getSuffix(),
                'company' => $billingAddress->getCompany(),
                'street' => $billingAddress->getStreetFull(),
                'city' => $billingAddress->getCity(),
                'country_id' => $billingAddress->getCountryId(),
                'region' => $billingAddress->getRegion(),
                'region_id' => $billingAddress->getRegionId(),
                'postcode' => $billingAddress->getPostcode(),
                'telephone' => $billingAddress->getTelephone(),
                'fax' => $billingAddress->getFax()
            ),
            'shipping_address' => array(
                'customer_address_id' => $shippingAddress->getId(),
                'prefix' => $shippingAddress->getPrefix(),
                'firstname' => $shippingAddress->getFirstname(),
                'middlename' => $shippingAddress->getMiddlename(),
                'lastname' => $shippingAddress->getLastname(),
                'suffix' => $shippingAddress->getSuffix(),
                'company' => $shippingAddress->getCompany(),
                'street' => $shippingAddress->getStreetFull(),
                'city' => $shippingAddress->getCity(),
                'country_id' => $shippingAddress->getCountryId(),
                'region' => $shippingAddress->getRegion(),
                'region_id' => $shippingAddress->getRegionId(),
                'postcode' => $shippingAddress->getPostcode(),
                'telephone' => $shippingAddress->getTelephone(),
                'fax' => $shippingAddress->getFax()
            ),
            'shipping_method' => $this->_shippingMethodCode,
            'comment' => array(
                'customer_note' => $this->_orderComment
            ),
            'send_confirmation' => $this->_sendConfirmation
        );

        return $this;
    }

    protected function _getAddress($address, $type = 'billing') {
        $helper = Mage::helper('nostepcheckout');
        $prefix = $type . '_address_product_form/';
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

    public function getShippingAddress() {
        return $this->_shippingAddress;
    }

    public function getBillingAddress() {
        return $this->_billingAddress;
    }

    public function create() {
        if (count($this->_orderData) <= 0) {
            $this->prepareData();
        }
        $orderData = $this->_orderData;

        $this->_initCreatorSession($orderData['session']);
        try {
            $this->_processQuote($orderData);
            if (!empty($orderData['payment'])) {
                $this->_getOrderCreatorModel()->setPaymentData($orderData['payment']);
                //$this->_getOrderCreatorModel()->getQuote()->getPayment()->addData($orderData['payment']);
                $this->getQuote()->getPayment()->addData($orderData['payment']);
            }

//                $item = $this->_getOrderCreateModel()->getQuote()->getItemByProduct($this->_product);
//                $item->addOption(new Varien_Object(
//                                array(
//                                    'product' => $this->_product,
//                                    'code' => 'option_ids',
//                                    'value' => '5' /* Option id goes here. If more options, then comma separate */
//                                )
//                ));
//                $item->addOption(new Varien_Object(
//                                array(
//                                    'product' => $this->_product,
//                                    'code' => 'option_5',
//                                    'value' => 'Some value here'
//                                )
//                ));
//            Mage::app()->getStore()->setConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_ENABLED, "0");
            $_order = $this->_getOrderCreatorModel()
                    ->importPostData($orderData['order'])
                    ->createOrder();
            $this->_getCreatorSession()->clear();
//                Mage::unregister('rule_data');
            $_order->setStatus($this->_orderStatus)->save();
            Mage::getSingleton('nostepcheckout/session')->setLastNscOrderId($_order->getId());
            Mage::getSingleton('nostepcheckout/session')->setLastNscRealOrderId($_order->getIncrementId());
            Mage::dispatchEvent('nostepcheckout_order_save_after', array('order' => $_order));
            return $_order;
        } catch (Exception $e) {
            Mage::getSingleton('nostepcheckout/session')->setErrorMessage($e->getMessage());
            Mage::log($e->getMessage(), 1, 'nostepcheckout.log');
        }
    }

    public function getQuote() {
        if ($this->_quote) {
            return $this->_quote;
        }
        if (count($this->_orderData) <= 0) {
            $this->prepareData();
        }
        $this->_processQuote($this->_orderData);
        return $this->_quote;
    }

    protected function _getOrderCreatorModel() {
        return Mage::getSingleton('adminhtml/sales_order_create');
    }

    protected function _getCreatorSession() {
        return Mage::getSingleton('adminhtml/session_quote');
    }

    protected function _initCreatorSession($data) {
        /* Get/identify customer */
        if (!empty($data['customer_id'])) {
            $this->_getCreatorSession()->setCustomerId((int) $data['customer_id']);
        }
        /* Get/identify store */
        if (!empty($data['store_id'])) {
            $this->_getCreatorSession()->setStoreId((int) $data['store_id']);
        }
        return $this;
    }

    protected function _processQuote($data = array()) {
        /* Saving order data */
        if (!empty($data['order'])) {
            $this->_getOrderCreatorModel()->importPostData($data['order']);
        }
        $this->_getOrderCreatorModel()->getBillingAddress();

        /* remove all items before adding new items in */
        if (!empty($data['add_products'])) {
            $this->_getOrderCreatorModel()->getQuote()->removeAllItems();
            $this->_getOrderCreatorModel()->addProducts($data['add_products']);
        }
        $this->_getOrderCreatorModel()->getQuote()->getShippingAddress()->setLimitCarrier($this->_limitCarrier);
        /* Collect shipping rates */

        $this->_getOrderCreatorModel()->collectShippingRates();

        /* Add payment data */
        if (!empty($data['payment'])) {
            $this->_getOrderCreatorModel()->getQuote()->getPayment()->addData($data['payment']);
        }
        $this->_getOrderCreatorModel()
                ->initRuleData()
                ->saveQuote();
        if (!empty($data['payment'])) {
            $this->_getOrderCreatorModel()->getQuote()->getPayment()->addData($data['payment']);
        }
        $this->_quote = $this->_getOrderCreatorModel()->getQuote();

        return $this;
    }

}