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
 * Priority multiple mode delivery method
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Warehouse_Mode_Multiple_Delivery_Method_Priority 
    extends Innoexts_Warehouse_Model_Warehouse_Mode_Multiple_Delivery_Method_Abstract 
{
    /**
     * Get quote value
     * 
     * @param Innoexts_Warehouse_Model_Sales_Quote $quote
     * @return float
     */
    protected function getQuoteValue($quote)
    {
        $value = 0;
        return $value;
    }
    /**
     * Apply stock items
     * 
     * @param array $stockItems
     * @return Innoexts_Warehouse_Model_Warehouse_Mode_Multiple_Delivery_Method_Priority
     */
    protected function _applyStockItems($stockItems)
    {
        $helper = $this->getWarehouseHelper();
        if ($quote = $this->getQuote()) {
            if ($quote->isInStockStockItems($stockItems)) {
                if (is_null($stockItems)) {
                    $stockItems = $quote->getInStockStockItems();
                }
                if (count($stockItems)) {
                    $combination = array();
                    foreach ($stockItems as $productId => $productStockItems) {
                        $stockId = null;
                        $minPriority = null;
                        if (count($productStockItems)) {
                            foreach ($productStockItems as $stockItem) {
                                $priority = null;
                                $product = $stockItem->getProduct();
                                $_stockId = $stockItem->getStockId();
                                if ($product) {
                                    $priority = $helper->getProductStockPriority($product, $_stockId);
                                } else {
                                    $warehouse = $stockItem->getWarehouse();
                                    if ($warehouse) {
                                        $priority = (int) $warehouse->getPriority();
                                    }
                                }
                                if (!is_null($priority) && (is_null($minPriority) || ($priority < $minPriority))) {
                                    $minPriority = $priority;
                                    $stockId = $_stockId;
                                }
                            }
                        }
                        if (is_null($stockId)) {
                            $combination = null;
                            break;
                        }
                        $combination[$productId] = $stockId;
                    }
                    if (!is_null($combination)) {
                        $quote->applyInStockStockItemStockIdCombination($stockItems, $combination);
                    }
                }
            }
        }
        return $this;
    }
}