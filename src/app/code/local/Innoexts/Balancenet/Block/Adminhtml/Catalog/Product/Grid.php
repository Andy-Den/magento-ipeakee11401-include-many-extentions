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
 * @package     Innoexts_Balancenet
 * @copyright   Copyright (c) 2011 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

/**
 * Product grid
 * 
 * @category   Innoexts
 * @package    Innoexts_Balancenet
 * @author     Innoexts Team <developers@innoexts.com>
 */
// class Innoexts_Balancenet_Block_Adminhtml_Catalog_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
// Was conflicting, rewritten thus
// 1. class Innoexts_Balancenet_Block_Adminhtml_Catalog_Product_Grid extends 
// 2. Innoexts_Warehouse_Block_Adminhtml_Catalog_Product_Grid extends
// 3. Balance_Storelocator_Block_Catalog_Product_Rewrite_Grid

class Innoexts_Balancenet_Block_Adminhtml_Catalog_Product_Grid extends Innoexts_Warehouse_Block_Adminhtml_Catalog_Product_Grid
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
     * Prepare grid collection object
     *
     * @return Innoexts_Warehouse_Block_Adminhtml_Catalog_Product_Grid
     */
    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');

        if ($store->getId()) {
            //$collection->setStoreId($store->getId());
            $collection->addStoreFilter($store);
            $collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
        }
        else {
            $collection->addAttributeToSelect('price');
            $collection->addAttributeToSelect('status');
            $collection->addAttributeToSelect('visibility');
        }
		
		$Attribute = new Mage_Eav_Model_Entity_Setup('core_setup');		
		$IDS = $Attribute->getAllAttributeSetIds();
		foreach($IDS as $Id)
		{
			$attribute_set = Mage::getModel('eav/entity_attribute_set')->load($Id);
			if($attribute_set)
			{
				$attribute_set_Name = $attribute_set->getAttributeSetName(); 		
				if($attribute_set_Name == 'Stockist')
				{
					$AttributeSetId = $Id;
				}
			}
		}
		
		
		$collection->addFieldToFilter(array(
			array('attribute'=>'attribute_set_id','neq'=>$AttributeSetId),
		));
		
        $this->setCollection($collection);
///////////////Parent(Mage_Adminhtml_Block_Widget_Grid) _prepareCollection Function Code ////////////////////////////////////
	   
	    if ($this->getCollection()) {

            $this->_preparePage();

            $columnId = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
            $dir      = $this->getParam($this->getVarNameDir(), $this->_defaultDir);
            $filter   = $this->getParam($this->getVarNameFilter(), null);

            if (is_null($filter)) {
                $filter = $this->_defaultFilter;
            }

            if (is_string($filter)) {
                $data = array();
                $filter = base64_decode($filter);
                parse_str(urldecode($filter), $data);
                $this->_setFilterValues($data);
            } else if ($filter && is_array($filter)) {
                $this->_setFilterValues($filter);
            } else if(0 !== sizeof($this->_defaultFilter)) {
                $this->_setFilterValues($this->_defaultFilter);
            }

            if (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex()) {
                $dir = (strtolower($dir)=='desc') ? 'desc' : 'asc';
                $this->_columns[$columnId]->setDir($dir);
                $column = $this->_columns[$columnId]->getFilterIndex() ?
                    $this->_columns[$columnId]->getFilterIndex() : $this->_columns[$columnId]->getIndex();
                $this->getCollection()->setOrder($column , $dir);
            }

            $this->getCollection()->load();
            $this->_afterLoadCollection();
        }
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $this->getCollection()->addWebsiteNamesToResult();
        $collection = $this->getCollection();
        $this->addQtysToCollection($collection);
        $this->addCostsToCollection($collection);
        return $this;
    }
}
