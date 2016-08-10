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
 * Abstact multiple mode delivery method
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
abstract class Innoexts_Warehouse_Model_Warehouse_Mode_Multiple_Delivery_Method_Abstract extends Varien_Object 
{
    /**
     * Quote
     * 
     * @var Innoexts_Warehouse_Model_Sales_Quote
     */
    protected $_quote;
    /**
     * Stock items
     * 
     * @var array of Innoexts_Warehouse_Model_Cataloginventory_Stock_Item
     */
    protected $_stockItems;
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
     * Get warehouse config
     * 
     * @return Innoexts_Warehouse_Model_Config
     */
    protected function getWarehouseConfig()
    {
        return $this->getWarehouseHelper()->getConfig();
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
        $this->_quote = $quote;
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
     * Get stock item stock identifier combination value
     * 
     * @param array $stockItems
     * @param array $combinations
     * @return mixed
     */
    protected function getStockItemStockIdCombinationValue($stockItems, $combination)
    {
        $value = null;
        if ($_quote = $this->getQuote()) {
            $quote = clone $_quote;
            $quote->applyInStockStockItemStockIdCombination($stockItems, $combination);
            $quote->applyStockAddresses();
            foreach ($quote->getAllShippingAddresses() as $shippingAddress) {
                $this->getWarehouseHelper()->copyCustomerShippingAddressIfEmpty($shippingAddress);
                $shippingAddress->collectTotals();
                $shippingAddress->setCollectShippingRates(true);
                $shippingAddress->collectShippingRates();
            }
            $shippingRateCombinations = $quote->getShippingRateCombinations();
            if (count($shippingRateCombinations)) {
                foreach ($shippingRateCombinations as $shippingRateCombination) {
                    $shippingAddresses = $quote->getAllShippingAddresses();
                    $enabled = true;
                    if (count($shippingAddresses) == count($shippingRateCombination)) {
                        foreach ($shippingAddresses as $shippingAddress) {
                            $stockId = intval($shippingAddress->getStockId());
                            if (isset($shippingRateCombination[$stockId])) {
                                $shippingRateCode = $shippingRateCombination[$stockId];
                                $shippingAddress->setShippingMethod($shippingRateCode);
                            } else {
                                $enabled = false;
                                break;
                            }
                        }
                    }
                    if ($enabled) {
                        $quote->setTotalsCollectedFlag(false);
                        $quote->collectTotals();
                        $combinationValue = $this->getQuoteValue($quote);
                        if (is_numeric($combinationValue) && ((is_null($value)) || ($combinationValue < $value))) {
                            $value = $combinationValue; 
                        }
                    }
                }
            } else {
                $quote->setTotalsCollectedFlag(false);
                $quote->collectTotals();
                $combinationValue = $this->getQuoteValue($quote);
                if (is_numeric($combinationValue) && ((is_null($value)) || ($combinationValue < $value))) {
                    $value = $combinationValue; 
                }
            }
            unset($quote);
        }
        return $value;
    }
    /**
     * Get quote value
     * 
     * @param Innoexts_Warehouse_Model_Sales_Quote $quote
     * @return float
     */
    abstract protected function getQuoteValue($quote);
    /**
     * Get min value stock item stock identifier combination
     * 
     * @param array $stockItems
     * @param array $combinations
     * @return array
     */
    protected function getMinValueStockItemStockIdCombination($stockItems, $combinations)
    {
        $combination = null;
        if (count($combinations)) {
            $minValue = null; 
            $index = null;
            foreach ($combinations as $combinationIndex => $combination) {
                $value = $this->getStockItemStockIdCombinationValue($stockItems, $combination);
                if (is_numeric($value) && ((is_null($minValue)) || ($value < $minValue))) {
                    $minValue = $value;
                    $index = $combinationIndex;
                }
            }
            if (isset($combinations[$index])) {
                $combination = $combinations[$index];
            } else {
                $combination = $combinations[0];
            }
        }
        return $combination;
    }
    /**
     * Apply stock items
     * 
     * @param array $stockItems
     * @return Innoexts_Warehouse_Model_Warehouse_Mode_Single_Delivery_Method_Abstract
     */
    protected function _applyStockItems($stockItems)
    {
        if ($quote = $this->getQuote()) {
            if ($quote->isInStockStockItems($stockItems)) {
                $combinations = $quote->getInStockStockItemStockIdCombinations($stockItems);
                if (count($combinations)) {
                    $combination = $this->getMinValueStockItemStockIdCombination($stockItems, $combinations);
                    if (!is_null($combination)) {
                        $quote->applyInStockStockItemStockIdCombination($stockItems, $combination);
                    }
                }
            }
        }
        return $this;
    }
    /**
     * Apply stock items
     * 
     * @return bool
     */
    public function applyStockItems()
    {
        $applied = false;
        if ($quote = $this->getQuote()) {
            $config = $this->getWarehouseConfig();
            $stockItems = $quote->getInStockStockItems();
            if ($quote->isInStockStockItems($stockItems)) {
                if ($config->isForceCartNoBackordersEnabled()) {
                    $stockItemsWithoutBackorders = $quote->getInStockStockItemsWithoutBackorders($stockItems, false);
                    if ($quote->isInStockStockItems($stockItemsWithoutBackorders)) {
                        $this->_applyStockItems($stockItemsWithoutBackorders);
                        $applied = true;
                    }
                }
                if ($config->isForceCartItemNoBackordersEnabled() && !$applied) {
                    $stockItemsWithoutBackorders = $quote->getInStockStockItemsWithoutBackorders($stockItems, true);
                    if ($quote->isInStockStockItems($stockItemsWithoutBackorders)) {
                        $this->_applyStockItems($stockItemsWithoutBackorders);
                        $applied = true;
                    }
                }
                if (!$applied) {
                    $this->_applyStockItems($stockItems);
                    $applied = true;
                }
            }
        }
        return $applied;
    }
}