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
 * @package     Innoexts_WarehouseEnterprise
 * @copyright   Copyright (c) 2014 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

/**
 * Enterprise stock index observer
 *
 * @category   Innoexts
 * @package    Innoexts_WarehouseEnterprise
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_WarehouseEnterprise_Model_Enterprise_Cataloginventory_Index_Observer 
    extends Innoexts_Warehouse_Model_Cataloginventory_Observer 
{
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
     * Process catalog inventory item save event
     *
     * @param Varien_Event_Observer $observer
     */
    public function processStockItemSaveEvent(Varien_Event_Observer $observer)
    {
        $helper = Mage::helper('enterprise_cataloginventory/index');
        if ($helper->isLivePriceAndStockReindexEnabled()) {
            $productId  = $observer->getEvent()->getItem()->getProductId();
            $client     = $this->_getClient($helper->getIndexerConfigValue('cataloginventory_stock', 'index_table'));
            $arguments  = array('value' => $productId);
            $client->execute('enterprise_cataloginventory/index_action_refresh_row', $arguments);
        }
    }
    /**
     * Process shell reindex catalog product price refresh event
     *
     * @param Varien_Event_Observer $observer
     */
    public function processShellProductReindexEvent(Varien_Event_Observer $observer)
    {
        $client = $this->_getClient(
            Mage::helper('enterprise_index')->getIndexerConfigValue('cataloginventory_stock', 'index_table')
        );
        $client->execute('enterprise_cataloginventory/index_action_refresh');
    }
    /**
     * Get client
     *
     * @param string $metadataTableName
     * 
     * @return Enterprise_Mview_Model_Client
     */
    protected function _getClient($metadataTableName)
    {
        $client = Mage::getModel('enterprise_mview/client');
        $client->init($metadataTableName);
        return $client;
    }
    /**
     * Refresh stock index for specific stock items after succesful order placement
     *
     * @param Varien_Event_Observer $observer
     * 
     * @return self
     */
    public function reindexQuoteInventory($observer)
    {
        foreach ($this->_itemsForReindex as $item) {
            $item->save();
        }
        $this->_itemsForReindex = array();
        return $this;
    }
    /**
     * Execute inventory index operations
     *
     * @param Varien_Event_Observer $observer
     */
    public function processUpdateWebsiteForProduct(Varien_Event_Observer $observer)
    {
        $helper = Mage::helper('enterprise_cataloginventory/index');
        if (!$helper->isLivePriceAndStockReindexEnabled()) {
            return;
        }
        $client = $this->_getClient(
            Mage::helper('enterprise_index')->getIndexerConfigValue('cataloginventory_stock', 'index_table')
        );
        $client->execute('enterprise_cataloginventory/index_action_refresh_changelog');
    }
}