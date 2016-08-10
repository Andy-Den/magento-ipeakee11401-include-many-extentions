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
 * Catalog observer
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Observer_Catalog
{
    /**
     * Get request
     * 
     * @return Mage_Core_Controller_Request_Http
     */
    protected function getRequest() 
    {
        return Mage::app()->getRequest();
    }
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
     * Get warehouse config
     * 
     * @return Innoexts_Warehouse_Model_Config
     */
    protected function getWarehouseConfig()
    {
        return $this->getWarehouseHelper()->getConfig();
    }
    /**
     * Add product multi-inventory tab
     * 
     * @param 	Varien_Event_Observer $observer
     * @return 	Innoexts_Warehouse_Model_Observer_Catalog
     */
    public function addProductMultipleInventoryTab(Varien_Event_Observer $observer)
    {
        $helper = $this->getWarehouseHelper();
        $config = $this->getWarehouseConfig();
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs) {
            $request = $this->getRequest();
            if (($request->getActionName() == 'edit') || ($request->getParam('type'))) {
                $tabsIds = $block->getTabsIds();
                $after = ((array_search('inventory', $tabsIds) !== false) && (array_search('inventory', $tabsIds) > 0)) ? 
                    $tabsIds[array_search('inventory', $tabsIds) - 1] : 'categories';
                $block->removeTab('inventory');
                $block->addTab('multipleinventory', array(
                    'after' => $after, 
                    'label' => $helper->__('Inventory'), 
                    'content' => $block->getLayout()->createBlock('warehouse/adminhtml_catalog_product_edit_tab_multipleinventory')->toHtml(), 
	            ));
	            if ($config->isShelfEnabled()) {
    	            $block->addTab('shelf', array(
                        'after' => $after, 
                        'label' => $helper->__('Shelf Information'), 
                        'content' => $block->getLayout()->createBlock('warehouse/adminhtml_catalog_product_edit_tab_shelf')->toHtml(), 
    	            ));
	            }
                if ($config->isPriorityMultipleModeDeliveryMethod()) {
                    $block->addTab('priority', array(
                        'after' => $after, 
                        'label' => $helper->__('Priority'), 
                        'content' => $block->getLayout()->createBlock('warehouse/adminhtml_catalog_product_edit_tab_priority')->toHtml(), 
    	            ));
                }
                if ($config->isShippingCarrierFilterEnabled()) {
                    $block->addTab('shipping', array(
                        'after' => $after, 
                        'label' => $helper->__('Shipping'), 
                        'content' => $block->getLayout()->createBlock('warehouse/adminhtml_catalog_product_edit_tab_shipping')->toHtml(), 
    	            ));
                }
            }
        }
        return $this;
    }
    /**
     * Save product shelf
     * 
     * @param 	Varien_Event_Observer $observer
     * @return 	Innoexts_Warehouse_Model_Observer_Catalog
     */
    public function saveProductShelf(Varien_Event_Observer $observer)
    {
        $config = $this->getWarehouseConfig();
        if (!$config->isShelfEnabled()) {
            return $this;
        }
        $product = $observer->getEvent()->getProduct();
        if ($product && ($product instanceof Mage_Catalog_Model_Product)) {
            $resource = $product->getResource();
            $productShelfTable = $resource->getTable('catalog/product_shelf');
            $adapter = $resource->getWriteConnection();
            $_shelfs = $product->getShelfs();
            if (count($_shelfs)) {
                $shelfs = $stockIds = $oldShelfs = $oldStockIds = array();
                foreach ($_shelfs as $shelf) {
                    if (isset($shelf['stock_id']) && $shelf['stock_id'] && isset($shelf['name']) && $shelf['name']) {
                        $productId = $product->getId();
                        $stockId = intval($shelf['stock_id']);
                        array_push($stockIds, $stockId);
                        $shelfs[$stockId] = array(
                            'product_id' => $productId, 'stock_id' => $stockId, 'name' => $shelf['name'],  
                        );
                    }
                }
                $select = $adapter->select()->from($productShelfTable)->where('product_id=?', $product->getId());
                $query = $adapter->query($select);
                while ($shelf = $query->fetch()) {
                    $stockId = intval($shelf['stock_id']);
                    array_push($oldStockIds, $stockId);
                    $oldShelfs[$stockId] = $shelf;
                }
                foreach ($oldStockIds as $oldStockId) {
                    if (!in_array($oldStockId, $stockIds)) {
                        $adapter->delete($productShelfTable, array(
                            $adapter->quoteInto('product_id = ?', $product->getId()), 
                            $adapter->quoteInto('stock_id = ?', $oldStockId)
                        ));
                    }
                }
                foreach ($stockIds as $stockId) {
                    if (!in_array($stockId, $oldStockIds)) {
                        $adapter->insert($productShelfTable, $shelfs[$stockId]);
                    } else {
                        $adapter->update($productShelfTable, $shelfs[$stockId], array(
                            $adapter->quoteInto('product_id = ?', $product->getId()), 
                            $adapter->quoteInto('stock_id = ?', $stockId), 
                        ));
                    }
                }
            }
        }
        return $this;
    }
    /**
     * Load product shelf
     * 
     * @param 	Varien_Event_Observer $observer
     * @return 	Innoexts_Warehouse_Model_Observer_Catalog
     */
    public function loadProductShelf(Varien_Event_Observer $observer)
    {
        $config = $this->getWarehouseConfig();
        if ($config->isShelfEnabled()) {
            $product = $observer->getEvent()->getProduct();
            if ($product instanceof Mage_Catalog_Model_Product) {
                $resource = $product->getResource();
                $productShelfTable = $resource->getTable('catalog/product_shelf');
                $adapter = $resource->getWriteConnection();
                $select = $adapter->select()->from($productShelfTable)->where('product_id = ?', $product->getId());
                $query = $adapter->query($select);
                $shelfs = array();
                while ($shelf = $query->fetch()) {
                    $stockId = intval($shelf['stock_id']);
                    $shelfs[$stockId] = $shelf; 
                }
                $product->setShelfs($shelfs);
            }
        }
        return $this;
    }
    /**
     * Remove product shelf
     * 
     * @param 	Varien_Event_Observer $observer
     * @return 	Innoexts_Warehouse_Model_Observer_Catalog
     */
    public function removeProductShelf(Varien_Event_Observer $observer)
    {
        $config = $this->getWarehouseConfig();
        if ($config->isShelfEnabled()) {
            $product = $observer->getEvent()->getProduct();
            if ($product instanceof Mage_Catalog_Model_Product) {
                $product->unsShelfs();
            }
        }
        return $this;
    }
    /**
     * Save product stock price
     * 
     * @param 	Varien_Event_Observer $observer
     * @return 	Innoexts_Warehouse_Model_Observer_Catalog
     */
    public function saveProductStockPrice(Varien_Event_Observer $observer)
    {
        $config = $this->getWarehouseConfig();
        if (!$config->isDiscountEnabled()) {
            return $this;
        }
        $product = $observer->getEvent()->getProduct();
        if ($product && ($product instanceof Mage_Catalog_Model_Product)) {
            $resource = $product->getResource();
            $productStockPriceTable = $resource->getTable('catalog/product_stock_price');
            $adapter = $resource->getWriteConnection();
            $_stockPrices = $product->getStockPrices();
            if (count($_stockPrices)) {
                $stockPrices = $stockIds = $oldStockPrices = $oldStockIds = array();
                foreach ($_stockPrices as $stockPrice) {
                    if (isset($stockPrice['stock_id']) && isset($stockPrice['price'])) {
                        $productId = $product->getId();
                        $stockId = intval($stockPrice['stock_id']);
                        $price = ($stockPrice['price'] && ($stockPrice['price'] > 0)) ? round(floatval($stockPrice['price']), 2) : 0;
                        $priceType = (isset($stockPrice['price_type']) && ($stockPrice['price_type'] == 'percent')) ? 'percent' : 'fixed';
                        array_push($stockIds, $stockId);
                        $stockPrices[$stockId] = array(
                            'product_id' => $productId, 'stock_id' => $stockId, 'price' => $price, 'price_type' => $priceType, 
                        );
                    }
                }
                $select = $adapter->select()->from($productStockPriceTable)->where('product_id=?', $product->getId());
                $query = $adapter->query($select);
                while ($stockPrice = $query->fetch()) {
                    $stockId = intval($stockPrice['stock_id']);
                    array_push($oldStockIds, $stockId);
                    $oldStockPrices[$stockId] = $stockPrice;
                }
                foreach ($oldStockIds as $oldStockId) {
                    if (!in_array($oldStockId, $stockIds)) {
                        $adapter->delete($productStockPriceTable, array(
                            $adapter->quoteInto('product_id = ?', $product->getId()), 
                            $adapter->quoteInto('stock_id = ?', $oldStockId)
                        ));
                    }
                }
                foreach ($stockIds as $stockId) {
                    if (!in_array($stockId, $oldStockIds)) {
                        $adapter->insert($productStockPriceTable, $stockPrices[$stockId]);
                    } else {
                        $adapter->update($productStockPriceTable, $stockPrices[$stockId], array(
                            $adapter->quoteInto('product_id = ?', $product->getId()), 
                            $adapter->quoteInto('stock_id = ?', $stockId), 
                        ));
                    }
                }
            }
        }
        return $this;
    }
    /**
     * Load product stock price
     * 
     * @param 	Varien_Event_Observer $observer
     * @return 	Innoexts_Warehouse_Model_Observer_Catalog
     */
    public function loadProductStockPrice(Varien_Event_Observer $observer)
    {
        $config = $this->getWarehouseConfig();
        if ($config->isDiscountEnabled()) {
            $product = $observer->getEvent()->getProduct();
            if ($product instanceof Mage_Catalog_Model_Product) {
                $resource = $product->getResource();
                $productStockPriceTable = $resource->getTable('catalog/product_stock_price');
                $adapter = $resource->getWriteConnection();
                $select = $adapter->select()->from($productStockPriceTable)->where('product_id = ?', $product->getId());
                $query = $adapter->query($select);
                $stockPrices = array();
                while ($stockPrice = $query->fetch()) {
                    $stockId = intval($stockPrice['stock_id']);
                    $stockPrices[$stockId] = $stockPrice; 
                }
                $product->setStockPrices($stockPrices);
            }
        }
        return $this;
    }
    /**
     * Load product collection stock price
     * 
     * @param 	Varien_Event_Observer $observer
     * @return 	Innoexts_Warehouse_Model_Observer_Catalog
     */
    public function loadProductCollectionStockPrice(Varien_Event_Observer $observer)
    {
        $config = $this->getWarehouseConfig();
        if ($config->isDiscountEnabled()) {
            $collection = $observer->getEvent()->getCollection();
            if ($collection) {
                $productIds = array();
                foreach ($collection as $product) {
                    array_push($productIds, $product->getId());
                }
                if (count($productIds)) {
                    $productStockPriceTable = $collection->getTable('catalog/product_stock_price');
                    $adapter = $collection->getConnection();
                    $select = $adapter->select()->from($productStockPriceTable)->where($adapter->quoteInto('product_id IN (?)', $productIds));
                    $query = $adapter->query($select);
                    $productStockPrices = array();
                    while ($stockPrice = $query->fetch()) {
                        $stockId = intval($stockPrice['stock_id']);
                        $productStockPrices[$stockPrice['product_id']][$stockId] = $stockPrice; 
                    }
                    foreach ($collection as $product) {
                        $productId = $product->getId();
                        $stockPrices = (isset($productStockPrices[$productId])) ? $productStockPrices[$productId] : array();
                        $product->setStockPrices($stockPrices);
                    }
                }
            }
        }
        return $this;
    }
    /**
     * Remove product stock price
     * 
     * @param 	Varien_Event_Observer $observer
     * @return 	Innoexts_Warehouse_Model_Observer_Catalog
     */
    public function removeProductStockPrice(Varien_Event_Observer $observer)
    {
        $config = $this->getWarehouseConfig();
        if ($config->isDiscountEnabled()) {
            $product = $observer->getEvent()->getProduct();
            if ($product instanceof Mage_Catalog_Model_Product) {
                $product->unsStockPrices();
            }
        }
        return $this;
    }
    /**
     * Get product final price
     * 
     * @param Varien_Event_Observer $observer
     * @return Innoexts_Warehouse_Model_Observer_Catalog
     */
    public function getProductFinalPrice($observer)
    {
        $config = $this->getWarehouseConfig();
        if ($config->isDiscountEnabled()) {
            $product = $observer->getEvent()->getProduct();
            if ($product instanceof Mage_Catalog_Model_Product) {
                $helper = $this->getWarehouseHelper();
                $stockItem = $product->getStockItem();
                if ($stockItem) {
                    $stockId = $stockItem->getStockId();
                } else {
                    $stockId = $helper->getDefaultStockId();
                }
                $finalPrice = $helper->getProductFinalPrice($product, $stockId);
                $product->setFinalPrice($finalPrice);
            }
        }
        return $this;
    }
    /**
     * Get product collection final price
     * 
     * @param Varien_Event_Observer $observer
     * @return Innoexts_Warehouse_Model_Observer
     */
    public function getProductCollectionFinalPrice($observer)
    {
        $helper = $this->getWarehouseHelper();
        $config = $this->getWarehouseConfig();
        if ($config->isDiscountEnabled()) {
            $collection = $observer->getEvent()->getCollection();
            foreach ($collection as $product) {
                $stockItem = $product->getStockItem();
                if ($stockItem) {
                    $stockId = $stockItem->getStockId();
                } else {
                    $stockId = $helper->getDefaultStockId();
                }
                $finalPrice = $helper->getProductFinalPrice($product, $stockId);
                $product->setFinalPrice($finalPrice);
            }
        }
        return $this;
    }
    /**
     * Prepare product price index table
     *
     * @param Varien_Event_Observer $observer
     * @return Innoexts_Warehouse_Model_Observer_Catalog
     */
    public function prepareProductPriceIndexTable(Varien_Event_Observer $observer)
    {
        $helper   = $this->getWarehouseHelper();
        $config   = $this->getWarehouseConfig();
        $stockId  = $observer->getEvent()->getStockId();
        if ($config->isDiscountEnabled() && $stockId) {
            $config                  = $this->getWarehouseConfig();
            $select                  = clone $observer->getEvent()->getSelect();
            $indexTable              = $observer->getEvent()->getIndexTable();
            $entityId                = $observer->getEvent()->getEntityId();
            $updateFields            = $observer->getEvent()->getUpdateFields();
            $resource                = Mage::getSingleton('core/resource');
            $adapter                 = $resource->getConnection('core_write');
            $productStockPriceTable  = $resource->getTableName('catalog/product_stock_price');
            if (empty($updateFields)) {
                return $this;
            }
            if (is_array($indexTable)) {
                foreach ($indexTable as $key => $value) {
                    if (is_string($key)) {
                        $indexAlias = $key;
                    } else {
                        $indexAlias = $value;
                    }
                    break;
                }
            } else {
                $indexAlias = $indexTable;
            }
            if ($config->isMultipleMode()) {
                foreach ($updateFields as $priceField) {
                    if ($priceField == 'price') {
                        continue;
                    }
                    $priceCond = $adapter->quoteIdentifier(array($indexAlias, $priceField));
                    $priceAlias = $priceField.'_psp';
                    if ($priceField == 'min_price') {
                        $function = 'MIN';
                    } else if ($priceField == 'max_price') {
                        $function = 'MAX';
                    }
                    $priceSelect = $adapter->select()
                        ->from(array($priceAlias => $productStockPriceTable), array())
                        ->where($priceAlias.'.product_id = '.$entityId)
                        ->columns(new Zend_Db_Expr(
                        $function."(IF(".
                            $priceAlias.".price_type = 'fixed', ".
                            "IF(".$priceAlias.".price < {$priceCond}, {$priceCond} - ".$priceAlias.".price, {$priceCond}), ".
                            "IF(".$priceAlias.".price < 100, ROUND({$priceCond} - (".$priceAlias.".price * ({$priceCond} / 100)), 4), {$priceCond})".
                        "))"
                    ));
                    $priceExpr = new Zend_Db_Expr("(".$priceSelect->assemble().")");
                    $select->columns(array($priceField => $priceExpr));
                }
            } else {
                $select->join(
                    array('psp' => $productStockPriceTable), 
                    '(psp.product_id = '.$entityId.') AND (psp.stock_id = '.$stockId.')', 
                    array()
                );
                foreach ($updateFields as $priceField) {
                    $priceCond = $adapter->quoteIdentifier(array($indexAlias, $priceField));
                    $priceExpr = new Zend_Db_Expr(
                        "IF(".
                            "psp.price_type = 'fixed', ".
                            "IF(psp.price < {$priceCond}, {$priceCond} - psp.price, {$priceCond}), ".
                            "IF(psp.price < 100, ROUND({$priceCond} - (psp.price * ({$priceCond} / 100)), 4), {$priceCond})".
                        ")"
                    );
                    $select->columns(array($priceField => $priceExpr));
                }
            }
            $query = $select->crossUpdateFromSelect($indexTable);
            $adapter->query($query);
        }
        return $this;
    }
    /**
     * Before product collection load
     * 
     * @param Varien_Event_Observer $observer
     * @return Innoexts_Warehouse_Model_Observer
     */
    public function beforeProductCollectionLoad(Varien_Event_Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();
        if ($collection) {
            $helper = $this->getWarehouseHelper();
            $select = $collection->getSelect();
            $connection = $collection->getConnection();
            if (!$collection->getFlag('stock_id')) {
                if ($helper->getConfig()->isMultipleMode()) {
                    $stockId = $connection->quote($helper->getDefaultStockId());
                } else {
                    $stockId = $connection->quote($this->getWarehouseConfig()->getStockId());
                }
            } else {
                $stockId = $collection->getFlag('stock_id');
            }
            $fromPart = $select->getPart(Zend_Db_Select::FROM);
            if (isset($fromPart['price_index'])) {
                $oldJoinCond = $fromPart['price_index']['joinCondition'];
                $joinCond = $oldJoinCond.' AND price_index.stock_id = '.$stockId;
                $fromPart['price_index']['joinCondition'] = $joinCond;
                $select->setPart(Zend_Db_Select::FROM, $fromPart);
            }
        }
        return $this;
    }
    /**
     * Save product stock priority
     * 
     * @param 	Varien_Event_Observer $observer
     * @return 	Innoexts_Warehouse_Model_Observer_Catalog
     */
    public function saveProductStockPriority(Varien_Event_Observer $observer)
    {
        $config = $this->getWarehouseConfig();
        if (!$config->isPriorityMultipleModeDeliveryMethod()) {
            return $this;
        }
        $product = $observer->getEvent()->getProduct();
        if ($product && ($product instanceof Mage_Catalog_Model_Product)) {
            $productId = $product->getId();
            $resource = $product->getResource();
            $productStockPriorityTable = $resource->getTable('catalog/product_stock_priority');
            $adapter = $resource->getWriteConnection();
            $_stockPriorities = $product->getStockPriorities();
            if (count($_stockPriorities)) {
                $stockPriorities = $stockIds = $oldStockPriorities = $oldStockIds = array();
                foreach ($_stockPriorities as $stockPriority) {
                    if (isset($stockPriority['stock_id']) && isset($stockPriority['priority'])) {
                        $stockId = (int) $stockPriority['stock_id'];
                        $priority = (int) $stockPriority['priority'];
                        array_push($stockIds, $stockId);
                        $stockPriorities[$stockId] = array(
                            'product_id' => $productId, 'stock_id' => $stockId, 'priority' => $priority, 
                        );
                    }
                }
                $select = $adapter->select()->from($productStockPriorityTable)->where('product_id=?', $productId);
                $query = $adapter->query($select);
                while ($stockPriority = $query->fetch()) {
                    $stockId = intval($stockPriority['stock_id']);
                    array_push($oldStockIds, $stockId);
                    $oldStockPriorities[$stockId] = $stockPriority;
                }
                foreach ($oldStockIds as $oldStockId) {
                    if (!in_array($oldStockId, $stockIds)) {
                        $adapter->delete($productStockPriorityTable, array(
                            $adapter->quoteInto('product_id = ?', $productId), 
                            $adapter->quoteInto('stock_id = ?', $oldStockId)
                        ));
                    }
                }
                foreach ($stockIds as $stockId) {
                    if (!in_array($stockId, $oldStockIds)) {
                        $adapter->insert($productStockPriorityTable, $stockPriorities[$stockId]);
                    } else {
                        $adapter->update($productStockPriorityTable, $stockPriorities[$stockId], array(
                            $adapter->quoteInto('product_id = ?', $productId), 
                            $adapter->quoteInto('stock_id = ?', $stockId), 
                        ));
                    }
                }
            }
        }
        return $this;
    }
    /**
     * Load product stock priority
     * 
     * @param 	Varien_Event_Observer $observer
     * @return 	Innoexts_Warehouse_Model_Observer_Catalog
     */
    public function loadProductStockPriority(Varien_Event_Observer $observer)
    {
        $helper = $this->getWarehouseHelper();
        $config = $this->getWarehouseConfig();
        if (!$config->isPriorityMultipleModeDeliveryMethod()) {
            return $this;
        }
        $product = $observer->getEvent()->getProduct();
        if ($product instanceof Mage_Catalog_Model_Product) {
            $resource = $product->getResource();
            $productStockPriorityTable = $resource->getTable('catalog/product_stock_priority');
            $adapter = $resource->getWriteConnection();
            $select = $adapter->select()->from($productStockPriorityTable)->where('product_id = ?', $product->getId());
            $query = $adapter->query($select);
            $stockPriorities = array();
            while ($stockPriority = $query->fetch()) {
                $stockId = (int) $stockPriority['stock_id'];
                $priority = (int) $stockPriority['priority'];
                $stockPriorities[$stockId] = $priority;
            }
            $product->setStockPriorities($stockPriorities);
        }
        return $this;
    }
    /**
     * Load product collection stock priority
     * 
     * @param 	Varien_Event_Observer $observer
     * @return 	Innoexts_Warehouse_Model_Observer_Catalog
     */
    public function loadProductCollectionStockPriority(Varien_Event_Observer $observer)
    {
        $helper = $this->getWarehouseHelper();
        $config = $this->getWarehouseConfig();
        if (!$config->isPriorityMultipleModeDeliveryMethod()) {
            return $this;
        }
        $collection = $observer->getEvent()->getCollection();
        if ($collection) {
            $productIds = array();
            foreach ($collection as $product) {
                array_push($productIds, $product->getId());
            }
            if (count($productIds)) {
                $productStockPriorityTable = $collection->getTable('catalog/product_stock_priority');
                $adapter = $collection->getConnection();
                $select = $adapter->select()->from($productStockPriorityTable)->where($adapter->quoteInto('product_id IN (?)', $productIds));
                $query = $adapter->query($select);
                $productStockPriorities = array();
                while ($stockPriority = $query->fetch()) {
                    $productId = (int) $stockPriority['product_id'];
                    $stockId = (int) $stockPriority['stock_id'];
                    $priority = (int) $stockPriority['priority'];
                    $productStockPriorities[$productId][$stockId] = $priority; 
                }
                foreach ($collection as $product) {
                    $productId = $product->getId();
                    $stockPriorities = array();
                    if (isset($productStockPriorities[$productId])) {
                        $stockPriorities = $productStockPriorities[$productId];
                    }
                    $product->setStockPriorities($stockPriorities);
                }
            }
        }
        return $this;
    }
    /**
     * Remove product stock priority
     * 
     * @param 	Varien_Event_Observer $observer
     * @return 	Innoexts_Warehouse_Model_Observer_Catalog
     */
    public function removeProductStockPriority(Varien_Event_Observer $observer)
    {
        $config = $this->getWarehouseConfig();
        if (!$config->isPriorityMultipleModeDeliveryMethod()) {
            return $this;
        }
        $product = $observer->getEvent()->getProduct();
        if ($product instanceof Mage_Catalog_Model_Product) {
            $product->unsStockPriorities();
        }
        return $this;
    }
    /**
     * Save product stock shipping carrier
     * 
     * @param 	Varien_Event_Observer $observer
     * @return 	Innoexts_Warehouse_Model_Observer_Catalog
     */
    public function saveProductStockShippingCarrier(Varien_Event_Observer $observer)
    {
        $config = $this->getWarehouseConfig();
        if (!$config->isShippingCarrierFilterEnabled()) {
            return $this;
        }
        $product = $observer->getEvent()->getProduct();
        if ($product && ($product instanceof Mage_Catalog_Model_Product)) {
            $productId = $product->getId();
            $resource = $product->getResource();
            $productStockShippingCarrierTable = $resource->getTable('catalog/product_stock_shipping_carrier');
            $adapter = $resource->getWriteConnection();
            $_stockShippingCarriers = $product->getStockShippingCarriers();
            if (count($_stockShippingCarriers)) {
                $stockShippingCarriers = $oldStockShippingCarriers = array();
                foreach ($_stockShippingCarriers as $stockShippingCarrier) {
                    if (
                        isset($stockShippingCarrier['stock_id']) && isset($stockShippingCarrier['shipping_carrier']) && 
                        is_array($stockShippingCarrier['shipping_carrier']) && count($stockShippingCarrier['shipping_carrier'])
                    ) {
                        $stockId = intval($stockShippingCarrier['stock_id']);
                        $shippingCarriers = $stockShippingCarrier['shipping_carrier'];
                        foreach ($shippingCarriers as $shippingCarrier) {
                            $stockShippingCarriers[$stockId][$shippingCarrier] = array(
                                'product_id'         => $productId, 
                                'stock_id'           => $stockId, 
                                'shipping_carrier'   => $shippingCarrier, 
                            );
                        }
                    }
                }
                $select = $adapter->select()->from($productStockShippingCarrierTable)->where('product_id=?', $productId);
                $query = $adapter->query($select);
                while ($stockShippingCarrier = $query->fetch()) {
                    $stockId = intval($stockShippingCarrier['stock_id']);
                    $shippingCarrier = $stockShippingCarrier['shipping_carrier'];
                    $oldStockShippingCarriers[$stockId][$shippingCarrier] = $stockShippingCarrier;
                }
                foreach ($oldStockShippingCarriers as $stockId => $_oldStockShippingCarriers) {
                    foreach ($_oldStockShippingCarriers as $shippingCarrier => $stockShippingCarrier) {
                        if (!(isset($stockShippingCarriers[$stockId]) && isset($stockShippingCarriers[$stockId][$shippingCarrier]))) {
                            $adapter->delete($productStockShippingCarrierTable, array(
                                $adapter->quoteInto('product_id = ?', $productId), 
                                $adapter->quoteInto('stock_id = ?', $stockId), 
                                $adapter->quoteInto('shipping_carrier = ?', $shippingCarrier)
                            ));
                        }
                    }
                }
                foreach ($stockShippingCarriers as $stockId => $_stockShippingCarriers) {
                    foreach ($_stockShippingCarriers as $shippingCarrier => $stockShippingCarrier) {
                        if (!(isset($oldStockShippingCarriers[$stockId]) && isset($oldStockShippingCarriers[$stockId][$shippingCarrier]))) {
                            $adapter->insert($productStockShippingCarrierTable, $stockShippingCarriers[$stockId][$shippingCarrier]);
                        }
                    }
                }
            }
        }
        return $this;
    }
    /**
     * Load product stock shipping carrier
     * 
     * @param 	Varien_Event_Observer $observer
     * @return 	Innoexts_Warehouse_Model_Observer_Catalog
     */
    public function loadProductStockShippingCarrier(Varien_Event_Observer $observer)
    {
        $helper = $this->getWarehouseHelper();
        $config = $this->getWarehouseConfig();
        if (!$config->isShippingCarrierFilterEnabled()) {
            return $this;
        }
        $product = $observer->getEvent()->getProduct();
        if ($product instanceof Mage_Catalog_Model_Product) {
            $resource = $product->getResource();
            $productStockShippingCarrierTable = $resource->getTable('catalog/product_stock_shipping_carrier');
            $adapter = $resource->getWriteConnection();
            $select = $adapter->select()->from($productStockShippingCarrierTable)->where('product_id = ?', $product->getId());
            $query = $adapter->query($select);
            $stockShippingCarriers = array();
            while ($stockShippingCarrier = $query->fetch()) {
                $stockId = (int) $stockShippingCarrier['stock_id'];
                $shippingCarrier = $stockShippingCarrier['shipping_carrier'];
                $stockShippingCarriers[$stockId][$shippingCarrier] = $shippingCarrier;
            }
            $product->setStockShippingCarriers($stockShippingCarriers);
        }
        return $this;
    }
    /**
     * Load product collection stock shipping_carrier
     * 
     * @param 	Varien_Event_Observer $observer
     * @return 	Innoexts_Warehouse_Model_Observer_Catalog
     */
    public function loadProductCollectionStockShippingCarrier(Varien_Event_Observer $observer)
    {
        $helper = $this->getWarehouseHelper();
        $config = $this->getWarehouseConfig();
        if (!$config->isShippingCarrierFilterEnabled()) {
            return $this;
        }
        $collection = $observer->getEvent()->getCollection();
        if ($collection) {
            $productIds = array();
            foreach ($collection as $product) {
                array_push($productIds, $product->getId());
            }
            if (count($productIds)) {
                $productStockShippingCarrierTable = $collection->getTable('catalog/product_stock_shipping_carrier');
                $adapter = $collection->getConnection();
                $select = $adapter->select()->from($productStockShippingCarrierTable)->where($adapter->quoteInto('product_id IN (?)', $productIds));
                $query = $adapter->query($select);
                $productStockShippingCarriers = array();
                while ($stockShippingCarrier = $query->fetch()) {
                    $stockId = (int) $stockShippingCarrier['stock_id'];
                    $productId = (int) $stockShippingCarrier['product_id'];
                    $shippingCarrier = $stockShippingCarrier['shipping_carrier'];
                    $productStockShippingCarriers[$productId][$stockId][$shippingCarrier] = $shippingCarrier;
                }
                foreach ($collection as $product) {
                    $productId = $product->getId();
                    $stockShippingCarriers = array();
                    if (isset($productStockShippingCarriers[$productId])) {
                        $stockShippingCarriers = $productStockShippingCarriers[$productId];
                    }
                    $product->setStockShippingCarriers($stockShippingCarriers);
                }
            }
        }
        return $this;
    }
    /**
     * Remove product stock shipping carrier
     * 
     * @param 	Varien_Event_Observer $observer
     * @return 	Innoexts_Warehouse_Model_Observer_Catalog
     */
    public function removeProductStockShippingCarrier(Varien_Event_Observer $observer)
    {
        $config = $this->getWarehouseConfig();
        if (!$config->isShippingCarrierFilterEnabled()) {
            return $this;
        }
        $product = $observer->getEvent()->getProduct();
        if ($product instanceof Mage_Catalog_Model_Product) {
            $product->unsStockShippingCarriers();
        }
        return $this;
    }
}