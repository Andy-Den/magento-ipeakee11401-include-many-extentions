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
 * Assigned stores single mode delivery method
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Warehouse_Mode_Single_Delivery_Method_Assignedstore 
    extends Innoexts_Warehouse_Model_Warehouse_Mode_Single_Delivery_Method_Abstract 
{
    /**
     * Get store
     * 
     * @return Mage_Core_Model_Store
     */
    protected function getStore()
    {
        $store = null;
        if ($quote = $this->getQuote()) {
            $store = $quote->getStore();
        }
        if (!$store) {
            $store = Mage::app()->getStore();
        }
        return $store;
    }
    /**
     * Get stock identifier
     * 
     * @return int
     */
    protected function _getStockId()
    {
        $stockId = null;
        $store = $this->getStore();
        if ($store) {
            $storeId = $store->getId();
            $warehouse = $this->getWarehouse();
            $resource = $warehouse->getResource();
            $adapter = $resource->getReadConnection();
            $select  = $adapter->select()
                ->from(array('w' => $resource->getMainTable()))
                ->joinLeft(array('ws' => $resource->getTable('warehouse/warehouse_store')), 'w.warehouse_id = ws.warehouse_id')
                ->order(array('w.warehouse_id ASC'))
                ->limit(1);
            $select->where('ws.store_id = ?', $storeId);
            $data = $adapter->fetchRow($select);
            $warehouse->setData($data);
            if ($warehouse->getId()) {
                $stockId = $warehouse->getStockId();
            }
        }
        return $stockId;
    }
}