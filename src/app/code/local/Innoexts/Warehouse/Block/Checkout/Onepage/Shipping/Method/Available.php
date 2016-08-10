<?php
/**
 * Innoexts
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the InnoExts Commercial License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://innoexts.com/commercial-license-agreement
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@innoexts.com so we can send you a copy immediately.
 *
 * @category    Innoexts
 * @package     Innoexts_Warehouse
 * @copyright   Copyright (c) 2011 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

/**
 * One page checkout shipping available method
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Checkout_Onepage_Shipping_Method_Available extends Mage_Checkout_Block_Onepage_Shipping_Method_Available

{
    /**
     * Address shipping methods
     *
     * @var array
     */
    protected $_addressShippingMethods;
    /**
     * Get warehouse helper
     *
     * @return Innoexts_Warehouse_Helper_Data
     */
    protected function getWarehouseHelper()
    {
        return Mage::helper('warehouse');
    }
    /**
     * Get sales quote
     *
     * @return Innoexts_Warehouse_Model_Sales_Quote
     */
    public function getQuote()
    {
        return parent::getQuote();
    }
    /**
     * Get warehouses
     *
     * @return array of Innoexts_Warehouse_Model_Warehouse
     */
    public function getWarehouses()
    {
        return $this->getQuote()->getWarehouses();
    }
    /**
     * Get addresses
     *
     * @return array of Innoexts_Warehouse_Model_Sales_Quote_Address
     */
    public function getAddresses()
    {
        return $this->getQuote()->getAllShippingAddresses();
    }
    /**
     * Get shipping rates
     *
     * @param int $stockId
     * @return array
     */
    public function _getShippingRates($stockId)
    {
        if (is_null($this->_rates)) {
            $this->getAddress()->collectShippingRates()->save();
            $rates = array();
            foreach ($this->getAddresses() as $address) {
                if ($address->getStockId()) {
                    $rates[$address->getStockId()] = $address->getGroupedAllShippingRates();
                }
            }
            $this->_rates = $rates;
        }
        if (isset($this->_rates[$stockId])) {
            return $this->_rates[$stockId];
        } else {
            return array();
        }
    }
    /**
     * Get address shipping method
     *
     * @param int $stockId
     * @return string
     */
    public function _getAddressShippingMethod($stockId)
    {
        if (is_null($this->_addressShippingMethods)) {
            $addressShippingMethods = array();
            foreach ($this->getAddresses() as $address) {
                if ($address->getStockId()) {
                    $addressShippingMethods[$address->getStockId()] = $address->getShippingMethod();
                }
            }
            $this->_addressShippingMethods = $addressShippingMethods;
        }
        if (isset($this->_addressShippingMethods[$stockId])) {
            return $this->_addressShippingMethods[$stockId];
        } else {
            return null;
        }
    }
    /**
     * Get shipping price
     *
     * @param float $price
     * @param bool $flag
     * @return float
     */
    public function _getShippingPrice($stockId, $price, $flag)
    {
        $quote = $this->getQuote();
        $address = $quote->getShippingAddressByStockId($stockId);
        if ($address) {
            return $quote->getStore()->convertPrice(Mage::helper('tax')->getShippingPrice($price, $flag, $address), true);
        } else {
            return null;
        }
    }
}