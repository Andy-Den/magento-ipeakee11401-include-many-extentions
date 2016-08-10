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
 * Abstact single mode delivery method
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
abstract class Innoexts_Warehouse_Model_Warehouse_Mode_Single_Delivery_Method_Abstract extends Varien_Object 
{
    /**
     * Get stock identifier
     * 
     * @var int
     */
    protected $_stockId;
    /**
     * Quote
     * 
     * @var Innoexts_Warehouse_Model_Sales_Quote
     */
    protected $_quote;
    /**
     * Get warehouse helper
     *
     * @return  Innoexts_Warehouse_Helper_Data
     */
    protected function getWarehouseHelper()
    {
        return Mage::helper('warehouse');
    }
    /**
     * Get catalog inventory helper
     * 
     * @return Innoexts_Warehouse_Helper_Cataloginventory
     */
    protected function getCatalogInventoryHelper()
    {
        return $this->getWarehouseHelper()->getCatalogInventoryHelper();
    }
    /**
     * Get quote
     * 
     * @return Innoexts_Warehouse_Model_Sales_Quote
     */
    public function getQuote()
    {
        return $this->_quote;
    }
    /**
     * Set quote
     * 
     * @param Innoexts_Warehouse_Model_Sales_Quote $quote
     * @return Innoexts_Warehouse_Model_Warehouse_Mode_Single_Delivery_Method_Abstract
     */
    public function setQuote($quote)
    {
        if (is_null($this->_quote) && is_null($quote)) {
            return $this;
        }
        $this->_quote = $quote;
        $this->_stockId = null;
        return $this;
    }
    /**
     * Check if delivery method is active
     * 
     * @return bool
     */
    public function isActive()
    {
        $flag = $this->getData('active');
        if (!empty($flag) && ($flag !== 'false')) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * Get title
     * 
     * @return string
     */
    public function getTitle()
    {
        return $this->getWarehouseHelper()->__($this->getData('title'));
    }
    /**
     * Get description
     * 
     * @return string
     */
    public function getDescription()
    {
        return $this->getWarehouseHelper()->__($this->getData('description'));
    }
    /**
     * Get default stock identifier
     * 
     * @return int
     */
    protected function getDefaultStockId()
    {
        return $this->getCatalogInventoryHelper()->getDefaultStockId();
    }
    /**
     * Get customer shipping address
     * 
     * @return Varien_Object
     */
    protected function getCustomerShippingAddress()
    {
        $address = null;
        $addressHelper = $this->getWarehouseHelper()->getAddressHelper();
        $quote = $this->getQuote();
        if ($quote) {
            $shippingAddress = $quote->getShippingAddress();
            if ($shippingAddress && !$addressHelper->isEmpty($shippingAddress)) {
                $address = $addressHelper->cast($shippingAddress);
            }
        }
        if (!$address || $addressHelper->isEmpty($address)) {
            $address = $addressHelper->cast($this->getWarehouseHelper()->getCustomerLocatorHelper()->getCustomerAddress());
        }
        return $address;
    }
    /**
     * Apply stock items
     * 
     * @return Innoexts_Warehouse_Model_Warehouse_Mode_Single_Delivery_Method_Abstract
     */
    public function applyStockItems()
    {
        if ($quote = $this->getQuote()) {
            $stockId = $this->getStockId();
            if ($stockId) {
                foreach ($quote->getAllItems() as $item) {
                    $item->setStockId($stockId);
                }
            }
        }
        return $this;
    }
    /**
     * Get warehouse 
     * 
     * @return Innoexts_Warehouse_Model_Warehouse
     */
    protected function getWarehouse()
    {
        return Mage::getModel('warehouse/warehouse');
    }
    /**
     * Get stock identifier
     * 
     * @return int
     */
    abstract protected function _getStockId();
    /**
     * Get stock identifier
     * 
     * @return int
     */
    public function getStockId()
    {
        if (is_null($this->_stockId)) {
            $quote = $this->getQuote();
            if ($quote) {
                $stockId = $quote->getStockId();
                if ($stockId) {
                    $this->_stockId = $stockId;
                }
            }
            if (is_null($this->_stockId)) {
                $stockId = $this->_getStockId();
                if ($stockId) {
                    $this->_stockId = $stockId;
                } else {
                    $this->_stockId = $this->getDefaultStockId();
                }
            }
        }
        return $this->_stockId;
    }
}