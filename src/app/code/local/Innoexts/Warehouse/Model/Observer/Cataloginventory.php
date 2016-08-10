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
 * Catalog inventory observer
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Observer_Cataloginventory extends Mage_CatalogInventory_Model_Observer
{
    /**
     * Quote item quantities
     * 
     * @var array
     */
    protected $_qtys = array();
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
     * Get warehouse config
     * 
     * @return Innoexts_Warehouse_Model_Config
     */
    protected function getWarehouseConfig()
    {
        return $this->getWarehouseHelper()->getConfig();
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
     * Throw exception
     * 
     * @param string $message
     * @param string $helper
     */
    protected function throwException($message, $helper = 'cataloginventory')
    {
        Mage::throwException(Mage::helper($helper)->__($message));
    }
    /**
     * Get predefined stock identifier
     * 
     * @param Innoexts_Warehouse_Model_Sales_Quote $quote
     * @return int
     */
    protected function getStockId($quote = null)
    {
        return $this->getWarehouseConfig()->getStockId($quote);
    }
    /**
     * Add stock information to product
     * 
     * @param   Varien_Event_Observer $observer
     * @return  Innoexts_Warehouse_Model_Observer_Cataloginventory
     */
    public function addInventoryData($observer)
    {
        $product = $observer->getEvent()->getProduct();
        if ($product instanceof Mage_Catalog_Model_Product) {
            $stockId = $this->getStockId($product->getQuote());
            $stockItem = $this->getCatalogInventoryHelper()->getStockItemCached(intval($product->getId()), $stockId);
            if ($this->getWarehouseConfig()->isMultipleMode()) {
                $stockItem->assignAvailableProduct($product);
            } else {
                $stockItem->assignProduct($product);
            }
        }
        return $this;
    }
    /**
     * Remove stock information
     * 
     * @param   Varien_Event_Observer $observer
     * @return  Innoexts_Warehouse_Model_Observer_Cataloginventory
     */
    public function removeInventoryData($observer)
    {
        $product = $observer->getEvent()->getProduct();
        if (($product instanceof Mage_Catalog_Model_Product) && $product->getId()) {
            $this->getCatalogInventoryHelper()->unsetStockItemCached(intval($product->getId()));
        }
        return $this;
    }
    /**
     * Add stock status to collection
     * 
     * @param   Varien_Event_Observer $observer
     * @return  Innoexts_Warehouse_Model_Observer_Cataloginventory
     */
    public function addStockStatusToCollection($observer) {
        $productCollection = $observer->getEvent()->getCollection();
        if (!$productCollection->hasFlag('ignore_stock_items')) {
            $stockId = $this->getStockId();
            if ($productCollection->hasFlag('require_stock_items')) {
                $this->getCatalogInventoryHelper()->getStock($stockId)
                    ->addItemsToProducts($productCollection);
            } else {
                $this->getCatalogInventoryHelper()->getStockStatus($stockId)
                    ->addStockStatusToProducts($productCollection, null, $stockId);
            }
        }
        return $this;
    }
    /**
     * Add stock items to collection
     *
     * @param   Varien_Event_Observer $observer
     * @return  Innoexts_Warehouse_Model_Observer_Cataloginventory
     */
    public function addInventoryDataToCollection($observer)
    {
        $productCollection = $observer->getEvent()->getProductCollection();
        if (count($productCollection)) {
            $stockId = $this->getStockId();
            $this->getCatalogInventoryHelper()->getStock($this->getStockId())->addItemsToProducts($productCollection);
        }
        return $this;
    }
    /**
     * Add stock status limitation to catalog product select
     * 
     * @param Varien_Event_Observer $observer
     * @return Mage_CatalogInventory_Model_Observer
     */
    public function prepareCatalogProductIndexSelect(Varien_Event_Observer $observer)
    {
        $select   = $observer->getEvent()->getSelect();
        $entity   = $observer->getEvent()->getEntityField();
        $website  = $observer->getEvent()->getWebsiteField();
        $stock    = $observer->getEvent()->getStockField();
        $this->getCatalogInventoryHelper()->getStockStatusSingleton()
            ->prepareCatalogProductIndexSelect2($select, $entity, $website, $stock);
        return $this;
    }
    /**
     * Apply stock items for quote
     * 
     * @param Mage_Sales_Model_Quote $quote
     * @return Mage_CatalogInventory_Model_Observer
     */
    protected function applyQuoteStockItems($quote)
    {
        $quote->applyStocks();
        return $this;
    }
    /**
     * Whether quote item needs to be checked or not
     * 
     * @param $quoteItem Innoexts_Warehouse_Model_Sales_Quote_Item
     * @return bool
     */
    protected function isCheckQuoteItemQty($quoteItem)
    {
        if (!$quoteItem || !$quoteItem->getProductId() || !$quoteItem->getQuote() || $quoteItem->getQuote()->getIsSuperMode()) {
            return false;
        } else {
            return true;
        }
    }
    /**
     * Check product inventory data with qty options
     * 
     * @param  $quoteItem Innoexts_Warehouse_Model_Sales_Quote_Item
     * @return Innoexts_Warehouse_Model_Observer_Cataloginventory
     */
    protected function checkQuoteItemQtyWithOptions($quoteItem)
    {
        $quote = $quoteItem->getQuote();
        $stockItem = $quoteItem->getStockItem();
        $product = $quoteItem->getProduct();
        $options = $quoteItem->getQtyOptions();
        $qty = $quoteItem->getProduct()->getTypeInstance(true)->prepareQuoteItemQty($quoteItem->getQty(), $quoteItem->getProduct());
        $quoteItem->setData('qty', $qty);
        if ($stockItem) {
            $result = $stockItem->checkQtyIncrements($qty);
            if ($result->getHasError()) {
                $quoteItem->setHasError(true)->setMessage($result->getMessage());
                $quote->setHasError(true)->addMessage($result->getQuoteMessage(), $result->getQuoteMessageIndex());
            }
        }
        foreach ($options as $option) {
            if ($stockItem) {
                $option->setStockId($stockItem->getStockId());
            }
            $optionQty = $qty * $option->getValue();
            $increaseOptionQty = ($quoteItem->getQtyToAdd() ? $quoteItem->getQtyToAdd() : $qty) * $option->getValue();
            $option->unsetStockItem();
            $stockItem = $option->getStockItem();
            if (!$stockItem instanceof Mage_CatalogInventory_Model_Stock_Item) {
                $this->throwException('The stock item for Product in option is not valid.');
            }
            $stockItem->setOrderedItems(0);
            $stockItem->setIsChildItem(true);
            $stockItem->setSuppressCheckQtyIncrements(true);
            $qtyForCheck = $this->_getQuoteItemQtyForCheck($option->getProduct()->getId(), $quoteItem->getId(), $increaseOptionQty);
            $result = $stockItem->checkQuoteItemQty($optionQty, $qtyForCheck, $option->getValue());
            if (!is_null($result->getItemIsQtyDecimal())) {
                $option->setIsQtyDecimal($result->getItemIsQtyDecimal());
            }
            if ($result->getHasQtyOptionUpdate()) {
                $option->setHasQtyOptionUpdate(true);
                $quoteItem->updateQtyOption($option, $result->getOrigQty());
                $option->setValue($result->getOrigQty());
                $quoteItem->setData('qty', intval($qty));
            }
            if (!is_null($result->getMessage())) {
                $option->setMessage($result->getMessage());
            }
            if (!is_null($result->getItemBackorders())) {
                $option->setBackorders($result->getItemBackorders());
            }
            if ($result->getHasError()) {
                $option->setHasError(true);
                $quoteItem->setHasError(true)->setMessage($result->getQuoteMessage());
                $quote->setHasError(true)->addMessage($result->getQuoteMessage(), $result->getQuoteMessageIndex());
            }
            $stockItem->unsIsChildItem();
        }
        return $this;
    }
    /**
     * Check product inventory data without qty options
     * 
     * @param  $quoteItem Innoexts_Warehouse_Model_Sales_Quote_Item
     * @return Innoexts_Warehouse_Model_Observer_Cataloginventory
     */
    protected function checkQuoteItemQtyWithoutOptions($quoteItem)
    {
        $quote = $quoteItem->getQuote();
        $stockItem = $quoteItem->getStockItem();
        $product = $quoteItem->getProduct();
        $qty = $quoteItem->getQty();
        if (!$stockItem instanceof Mage_CatalogInventory_Model_Stock_Item) {
            $this->throwException('The stock item for Product is not valid.');
        }
        if ($quoteItem->getParentItem()) {
            $rowQty = $quoteItem->getParentItem()->getQty() * $qty;
            $qtyForCheck = $this->_getQuoteItemQtyForCheck($product->getId(), $quoteItem->getId(), 0);
        } else {
            $increaseQty = $quoteItem->getQtyToAdd() ? $quoteItem->getQtyToAdd() : $qty;
            $rowQty = $qty;
            $qtyForCheck = $this->_getQuoteItemQtyForCheck($product->getId(), $quoteItem->getId(), $increaseQty);
        }
        $productTypeCustomOption = $product->getCustomOption('product_type');
        if (!is_null($productTypeCustomOption)) {
            if ($productTypeCustomOption->getValue() == Mage_Catalog_Model_Product_Type_Grouped::TYPE_CODE) {
                $stockItem->setIsChildItem(true);
            }
        }
        $result = $stockItem->checkQuoteItemQty($rowQty, $qtyForCheck, $qty);
        if ($stockItem->hasIsChildItem()) {
            $stockItem->unsIsChildItem();
        }
        if (!is_null($result->getItemIsQtyDecimal())) {
            $quoteItem->setIsQtyDecimal($result->getItemIsQtyDecimal());
            if ($quoteItem->getParentItem()) {
                $quoteItem->getParentItem()->setIsQtyDecimal($result->getItemIsQtyDecimal());
            }
        }
        if ($result->getHasQtyOptionUpdate() && (!$quoteItem->getParentItem() || 
            $quoteItem->getParentItem()->getProduct()->getTypeInstance(true)
                ->getForceChildItemQtyChanges($quoteItem->getParentItem()->getProduct()))) {
            $quoteItem->setData('qty', $result->getOrigQty());
        }
        if (!is_null($result->getItemUseOldQty())) {
            $quoteItem->setUseOldQty($result->getItemUseOldQty());
        }
        if (!is_null($result->getMessage())) {
            $quoteItem->setMessage($result->getMessage());
            if ($quoteItem->getParentItem()) {
                $quoteItem->getParentItem()->setMessage($result->getMessage());
            }
        }
        if (!is_null($result->getItemBackorders())) {
            $quoteItem->setBackorders($result->getItemBackorders());
        }
        if ($result->getHasError()) {
            $quoteItem->setHasError(true);
            $quote->setHasError(true)->addMessage($result->getQuoteMessage(), $result->getQuoteMessageIndex());
        }
        return $this;
    }
    /**
     * Check product inventory data
     * 
     * @param  Varien_Event_Observer $observer
     * @return Mage_CatalogInventory_Model_Observer
     */
    public function checkQuoteItemQty($observer)
    {
        $quoteItem = $observer->getEvent()->getItem();
        $quote = $quoteItem->getQuote();
        if (!$this->isCheckQuoteItemQty($quoteItem)) return $this;
        $this->applyQuoteStockItems($quoteItem->getQuote());
        if ($quoteItem->getQtyOptions() && ($quoteItem->getQty() > 0)) {
            $this->checkQuoteItemQtyWithOptions($quoteItem);
        } else {
            $this->checkQuoteItemQtyWithoutOptions($quoteItem);
        }
        return $this;
    }
    /**
     * Saving product inventory data. Product qty calculated dynamically.
     * 
     * @param   Varien_Event_Observer $observer
     * @return  Innoexts_Warehouse_Model_Observer_Cataloginventory
     */
    public function saveInventoryData($observer)
    {
        $inventoryHelper = $this->getCatalogInventoryHelper();
        $product = $observer->getEvent()->getProduct();
        if (is_null($product->getStocksData())) {
            if ($product->getIsChangedWebsites() || $product->dataHasChangedFor('status')) {
                foreach ($inventoryHelper->getStockIds() as $stockId) {
                    $inventoryHelper->getStockStatusSingleton($stockId)->updateStatus($product->getId());
                }
            }
            return $this;
        }
        $data = $product->getStocksData();
        if (count($data)) {
            $keys = $inventoryHelper->getConfigItemOptions();
            foreach ($inventoryHelper->getStockIds() as $stockId) {
                $item = $inventoryHelper->getStockItem($stockId)->loadByProduct($product);
                foreach ($data as $dataItem) {
                    if (isset($dataItem['stock_id']) && ($stockId == (int) $dataItem['stock_id'])) {
                        foreach ($keys as $key) {
                            $useConfigKey = 'use_config_'.$key;
                            if (isset($dataItem[$useConfigKey]) && $dataItem[$useConfigKey]) $dataItem[$useConfigKey] = 1;
                            else $dataItem[$useConfigKey] = 0;
                        }
                        $item->addData($dataItem);
                    }
                }
                $item->setProduct($product);
                foreach ($keys as $key) {
                    if (is_null($item->getData($key))) {
                        $item->setData('use_config_'.$key, 1);
                    }
                }
                $originalQty = $item->getData('original_inventory_qty');
                if (strlen($originalQty) > 0) {
                    $item->setQtyCorrection($item->getQty() - $originalQty);
                }
                $item->save();
            }
        }
        return $this;
    }
    /**
     * Update items stock status and low stock date.
     *
     * @param Varien_Event_Observer $observer
     * @return  Innoexts_Warehouse_Model_Observer_Cataloginventory
     */
    public function updateItemsStockUponConfigChange($observer)
    {
        $inventoryHelper = $this->getCatalogInventoryHelper();
        foreach ($inventoryHelper->getStockIds() as $stockId) {
            $stockResourceSingleton = $inventoryHelper->getStockResourceSingleton($stockId);
            $stockResourceSingleton->updateSetOutOfStock();
            $stockResourceSingleton->updateSetInStock();
            $stockResourceSingleton->updateLowStockDate();
        }
        return $this;
    }
    /**
     * Cancel order item
     *
     * @param   Varien_Event_Observer $observer
     * @return  Innoexts_Warehouse_Model_Observer_Cataloginventory
     */
    public function cancelOrderItem($observer)
    {
        $item = $observer->getEvent()->getItem();
        $children = $item->getChildrenItems();
        $qty = $item->getQtyOrdered() - max($item->getQtyShipped(), $item->getQtyInvoiced()) - $item->getQtyCanceled();
        if ($item->getId() && ($productId = $item->getProductId()) && empty($children) && $qty) {
            $this->getCatalogInventoryHelper()->getStockSingleton($item->getStockId())->backItemQty($productId, $qty);
        }
        return $this;
    }
    /**
     * Return creditmemo items qty to stock
     *
     * @param Varien_Event_Observer $observer
     */
    public function refundOrderInventory($observer)
    {
        $inventoryHelper = $this->getCatalogInventoryHelper();
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $items = array();
        $isAutoReturnEnabled = Mage::helper('cataloginventory')->isAutoReturnEnabled();
        foreach ($creditmemo->getAllItems() as $item) {
            $return = false;
            if ($item->hasBackToStock()) {
                if ($item->getBackToStock() && $item->getQty()) {
                    $return = true;
                }
            } elseif ($isAutoReturnEnabled) {
                $return = true;
            }
            if ($return) {
                $orderItem = $item->getOrderItem();
                $stockId = ($orderItem) ? $orderItem->getStockId() : $inventoryHelper->getDefaultStockId();
                if (isset($items[$item->getProductId()])) {
                    $items[$item->getProductId()]['qty'] += $item->getQty();
                    $items[$item->getProductId()]['stock_id'] = $stockId;
                } else {
                    $items[$item->getProductId()] = array(
                        'qty' => $item->getQty(), 
                        'item'=> null, 
                        'stock_id' => $stockId, 
                    );
                }
            }
        }
        $inventoryHelper->getStockSingleton()->revertProductsSale($items);
        return $this;
    }
    /**
     * Adds stock item qty to $items (creates new entry or increments existing one)
     * $items is array with following structure:
     * array(
     *     $productId  => array(
     *         'qty'   => $qty, 
     *         'stock_id' => $stockId, 
     *         'item'  => $stockItems|null
     *     )
     * )
     *
     * @param Mage_Sales_Model_Quote_Item $quoteItem
     * @param array &$items
     */
    protected function _addItemToQtyArray($quoteItem, &$items)
    {
        $productId = $quoteItem->getProductId();
        if (!$productId) return;
        $stockItem = null;
        if ($quoteItem->getProduct()) {
            $stockItem = $quoteItem->getStockItem();
        }
        $stockId = ($stockItem) ? $stockItem->getStockId() : null;
        if (isset($items[$productId])) {
            $items[$productId]['qty']      += $quoteItem->getTotalQty();
            $items[$productId]['stock_id'] = $stockId;
        } else {
            $items[$productId] = array(
                'item'      => $stockItem, 
                'stock_id'  => $stockId, 
                'qty'       => $quoteItem->getTotalQty(), 
            );
        }
        return $this;
    }
    
    /**
     * Update Only product status observer
     *
     * @deprecated
     * @param Varien_Event_Observer $observer
     * @return Innoexts_Warehouse_Model_Observer_Cataloginventory
     */
    public function productStatusUpdate(Varien_Event_Observer $observer)
    {
        $inventoryHelper = $this->getCatalogInventoryHelper();
        $productId = $observer->getEvent()->getProductId();
        foreach ($inventoryHelper->getStockIds() as $stockId) {
            $inventoryHelper->getStockStatusSingleton($stockId)->updateStatus($productId);
        }
        return $this;
    }
    /**
     * Catalog Product website update
     *
     * @deprecated
     * @param Varien_Event_Observer $observer
     * @return Innoexts_Warehouse_Model_Observer_Cataloginventory
     */
    public function catalogProductWebsiteUpdate(Varien_Event_Observer $observer)
    {
        $inventoryHelper = $this->getCatalogInventoryHelper();
        $websiteIds     = $observer->getEvent()->getWebsiteIds();
        $productIds     = $observer->getEvent()->getProductIds();
        foreach ($websiteIds as $websiteId) {
            foreach ($productIds as $productId) {
                foreach ($inventoryHelper->getStockIds() as $stockId) {
                    $inventoryHelper->getStockStatusSingleton($stockId)->updateStatus($productId, null, $websiteId);
                }
            }
        }
        return $this;
    }
    /**
     * Add stock status to prepare index select
     * 
     * @deprecated
     * @param Varien_Event_Observer $observer
     * @return Innoexts_Warehouse_Model_Observer_Cataloginventory
     */
    public function addStockStatusToPrepareIndexSelect(Varien_Event_Observer $observer) {
        $inventoryHelper = $this->getCatalogInventoryHelper();
        $website        = $observer->getEvent()->getWebsite();
        $select         = $observer->getEvent()->getSelect();
        $inventoryHelper->getStockStatusSingleton($this->getStockId())->addStockStatusToSelect($select, $website);
        return $this;
    }
}