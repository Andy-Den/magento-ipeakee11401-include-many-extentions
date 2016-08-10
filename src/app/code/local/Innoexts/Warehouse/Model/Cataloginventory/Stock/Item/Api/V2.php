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
 * @copyright   Copyright (c) 2014 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

/**
 * Stock item api V2
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Cataloginventory_Stock_Item_Api_V2 
    extends Innoexts_Warehouse_Model_Cataloginventory_Stock_Item_Api 
{
    /**
     * Update stock
     * 
     * @param int $productId
     * @param int $stockId
     * @param mixed $data
     * 
     * @return bool
     */
    protected function _update($productId, $data, $stockId)
    {
        $product = Mage::getModel('catalog/product');
        $newId = $product->getIdBySku($productId);
        if ($newId) {
            $productId = $newId;
        }
        $storeId = $this->_getStoreId();
        $product->setStoreId($storeId)->load($productId);
        if (!$product->getId()) {
            $this->_fault('not_exists');
        }
        $stocksData = $product->getStocksData();
        $stockData = (isset($stocksData[$stockId])) ? $stocksData[$stockId] : null;
        if (!$stockData) {
            $stockData = array();
        }
        $stockData['stock_id'] = $stockId;
        if (isset($data->qty)) {
            $stockData['qty'] = $data->qty;
        }
        if (isset($data->is_in_stock)) {
            $stockData['is_in_stock'] = $data->is_in_stock;
        }
        if (isset($data->manage_stock)) {
            $stockData['manage_stock'] = $data->manage_stock;
        }
        if (isset($data->use_config_manage_stock)) {
            $stockData['use_config_manage_stock'] = $data->use_config_manage_stock;
        }
        
        if ($this->getVersionHelper()->isGe1700()) {
            if (isset($data->use_config_backorders)) {
                $stockData['use_config_backorders'] = $data->use_config_backorders;
            }
            if (isset($data->backorders)) {
                $stockData['backorders'] = $data->backorders;
            }
        }
        
        $stocksData[$stockId] = $stockData;
        $product->setStocksData($stocksData);
        try {
            $product->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_updated', $e->getMessage());
        }
        return true;
    }
}