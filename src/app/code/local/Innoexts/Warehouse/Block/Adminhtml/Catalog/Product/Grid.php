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
 * Product grid
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
// class Innoexts_Warehouse_Block_Adminhtml_Catalog_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
// Was conflicting, so instead we...
// See also Innoexts_Balancenet_Block_Adminhtml_Catalog_Product_Grid
class Innoexts_Warehouse_Block_Adminhtml_Catalog_Product_Grid extends Balance_Storelocator_Block_Catalog_Product_Rewrite_Grid
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
     * Get catalog inventory helper
     * 
     * @return Innoexts_Warehouse_Helper_Cataloginventory
     */
    protected function getCatalogInventoryHelper()
    {
        return $this->getWarehouseHelper()->getCatalogInventoryHelper();
    }
    /**
     * Add costs to collection
     * 
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection
     * @return Innoexts_Warehouse_Block_Adminhtml_Catalog_Product_Grid
     */
    protected function addCostsToCollection($collection)
    {
        $helper = $this->getWarehouseHelper();
        $stockIds = $this->getCatalogInventoryHelper()->getStockIds();
        foreach ($collection as $product) {
            $costs = array();
            foreach ($stockIds as $stockId) {
                $costs[$stockId] = $helper->getProductFinalPrice($product, $stockId);
            }
            $product->setCosts($costs);
        }
        return $this;
    }
    /**
     * Add qtys to collection
     * 
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection
     * @return Innoexts_Warehouse_Block_Adminhtml_Catalog_Product_Grid
     */
    protected function addQtysToCollection($collection)
    {
        $qtys = array();
        foreach ($collection as $product) {
            $qtys[$product->getId()] = array();
        }
        if (!empty($qtys)) {
            $connection = $collection->getConnection();
            $select = $connection->select()
                ->from(array('stock_item' => $collection->getTable('cataloginventory/stock_item')))
                ->where($connection->quoteInto('stock_item.product_id IN (?)', array_keys($qtys)));
            $data = $connection->fetchAll($select);
            foreach ($data as $row) {
                $qtys[$row['product_id']][$row['stock_id']] = floatval($row['qty']);
            }
        }
        foreach ($collection as $product) {
            if (isset($qtys[$product->getId()])) {
                $product->setData('qtys', $qtys[$product->getId()]);
            }
        }
        return $this;
    }
    /**
     * Prepare grid collection object
     *
     * @return Innoexts_Warehouse_Block_Adminhtml_Catalog_Product_Grid
     */
    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $collection = $this->getCollection();
        $this->addQtysToCollection($collection);
        $this->addCostsToCollection($collection);
        return $this;
    }
    /**
     * Prepare columns for layout
     * 
     * @return Innoexts_Warehouse_Block_Adminhtml_Catalog_Product_Grid
     */
    protected function _prepareColumns()
    {
        $helper = $this->getWarehouseHelper();
        $discountEnabled = $helper->getConfig()->isDiscountEnabled();
        $store = $this->_getStore();
        $this->addColumnAfter('qtys', array(
            'header'        => $helper->__('Qty'), 
            'sortable'      => false, 
            'index'         => 'qtys', 
            'width'         => '140px', 
            'align'         => 'left', 
            'renderer'	    => 'warehouse/adminhtml_catalog_product_grid_column_renderer_qtys', 
            'filter'	    => 'adminhtml/widget_grid_column_filter_range', 
        ), 'price');
        if ($discountEnabled) {
            $this->addColumnAfter('costs', array(
                'header'        => $helper->__('Costs'), 
                'currency_code' => $store->getBaseCurrency()->getCode(), 
                'index'         => 'costs', 
                'width'         => '140px', 
                'align'         => 'left', 
                'renderer'	    => 'warehouse/adminhtml_catalog_product_grid_column_renderer_costs', 
                'filter'        => false, 
                'sortable'      => false, 
            ), 'price');
        }
        parent::_prepareColumns();
        if (isset($this->_columns['qty'])) {
            unset($this->_columns['qty']);
        }
        return $this;
    }
    /**
     * Add column filter for collection
     * 
     * @return Innoexts_Warehouse_Block_Adminhtml_Catalog_Product_Grid
     */
    protected function _addColumnFilterToCollection($column)
    {
        $collection = $this->getCollection();
        if ($collection) {
            $connection = $collection->getConnection();
            if ($column->getId() == 'qtys') {
                $cond = $column->getFilter()->getCondition();
                $collection->getSelect()->joinInner(
                    array('stock_item' => $collection->getTable('cataloginventory/stock_item')), 
                    '(e.entity_id = stock_item.product_id)', array('qtys' => 'SUM(stock_item.qty)')
                );
                $collection->getSelect()->group(array('e.entity_id'));
                $condPieces = array();
                $qtysField = 'SUM(stock_item.qty)';
                if (isset($cond['from'])) array_push($condPieces, $qtysField.' >= '.$connection->quote($cond['from']));
                if (isset($cond['to'])) array_push($condPieces, $qtysField.' <= '.$connection->quote($cond['to']));
                if (count($condPieces)) $collection->getSelect()->having(implode(' AND ', $condPieces));
            }
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField(
                    'websites', 'catalog/product_website', 'website_id', 'product_id=entity_id', null, 'left'
                );
            }
            $field = ( $column->getFilterIndex() ) ? $column->getFilterIndex() : $column->getIndex();
            if ($column->getFilterConditionCallback()) {
                call_user_func($column->getFilterConditionCallback(), $this->getCollection(), $column);
            } elseif ($column->getId() != 'qtys') {
                $cond = $column->getFilter()->getCondition();
                if ($field && isset($cond)) $this->getCollection()->addFieldToFilter($field , $cond);
            }
        }
        return $this;
    }
}
