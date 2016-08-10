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
 * Lowstock product report grid
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Adminhtml_Report_Product_Lowstock_Grid extends Mage_Adminhtml_Block_Report_Product_Lowstock_Grid
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
     * Prepare columns
     * 
     * @return Innoexts_Warehouse_Block_Adminhtml_Report_Product_Lowstock_Grid
     */
    protected function _prepareColumns()
    {
        $helper = $this->getWarehouseHelper();
        $prevColumn = 'sku';
        $stockIds = $helper->getCatalogInventoryHelper()->getStockIds();
        foreach ($stockIds as $stockId) {
            $fieldName = 'qty_'.$stockId;
            $this->addColumnAfter($fieldName, array(
                'header'    => sprintf($helper->__('%s Stock Qty'), $helper->getWarehouseTitleByStockId($stockId)), 
                'align'     => 'right', 
                'sortable'  => false,
                'filter'    => 'adminhtml/widget_grid_column_filter_range',
                'index'     => $fieldName,
                'type'      => 'number', 
            ), $prevColumn);
            $prevColumn = $fieldName;
        }
        parent::_prepareColumns();
        if (isset($this->_columns['qty'])) unset($this->_columns['qty']);
        return $this;
    }
    /**
     * Prepare collection
     * 
     * @return 
     */
    protected function _prepareCollection()
    {
        $helper = $this->getWarehouseHelper();
        if ($this->getRequest()->getParam('website')) {
            $storeIds = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } else if ($this->getRequest()->getParam('group')) {
            $storeIds = Mage::app()->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } else if ($this->getRequest()->getParam('store')) {
            $storeId = (int)$this->getRequest()->getParam('store');
        } else {
            $storeId = '';
        }
        $collection = Mage::getResourceModel('reports/product_lowstock_collection')
            ->addAttributeToSelect('*')
            ->setStoreId($storeId)
            ->filterByIsQtyProductTypes();
        if ($storeId) {
            $collection->addStoreFilter($storeId);
        }
        $stockIds = $helper->getCatalogInventoryHelper()->getStockIds();
        foreach ($stockIds as $stockId) {
            $fieldName = 'qty_'.$stockId;
            $collection->joinStockItem($stockId, array($fieldName => 'qty'));
        }
        $collection->useManageStockFilter_($stockIds, $storeId);
        $collection->useNotifyStockQtyFilter_($stockIds, $storeId);
        $collection->setOrder('sku', 'asc');
        $this->setCollection($collection);
        
        $this->_preparePage();
        $columnId = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
        $dir      = $this->getParam($this->getVarNameDir(), $this->_defaultDir);
        $filter   = $this->getParam($this->getVarNameFilter(), null);
        if (is_null($filter)) $filter = $this->_defaultFilter;
        if (is_string($filter)) {
            $data = $this->helper('adminhtml')->prepareFilterString($filter);
            $this->_setFilterValues($data);
        } else if ($filter && is_array($filter)) $this->_setFilterValues($filter);
        else if(0 !== sizeof($this->_defaultFilter)) $this->_setFilterValues($this->_defaultFilter);
        if (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex()) {
            $dir = (strtolower($dir)=='desc') ? 'desc' : 'asc';
            $this->_columns[$columnId]->setDir($dir);
            $this->_setCollectionOrder($this->_columns[$columnId]);
        }
        if (!$this->_isExport) {
            $this->getCollection()->load();
            $this->_afterLoadCollection();
        }
        return $this;
    }
}