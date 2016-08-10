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
 * Refresh category flat index by changelog action
 *
 * @category   Innoexts
 * @package    Innoexts_WarehouseEnterprise
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_WarehouseEnterprise_Model_Enterprise_Catalog_Index_Action_Product_Price_Refresh_Changelog 
    extends Enterprise_Catalog_Model_Index_Action_Product_Price_Refresh_Changelog 
{
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
     * Get price indexer helper
     * 
     * @return Innoexts_Warehouse_Helper_Catalog_Product_Price_Indexer
     */
    protected function getProductPriceIndexerHelper()
    {
        return $this->getWarehouseHelper()
            ->getProductPriceIndexerHelper();
    }
    /**
     * Get version helper
     * 
     * @return Innoexts_Core_Helper_Version
     */
    protected function getVersionHelper()
    {
        return $this->getProductPriceIndexerHelper()
            ->getVersionHelper();
    }
    /**
     * Retrieve catalog_product attribute instance by attribute code
     *
     * @param string $attributeCode
     * 
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    protected function _getAttribute($attributeCode)
    {
        return Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
    }
    /**
     * Add attribute join condition to select and return Zend_Db_Expr
     * attribute value definition
     * If $condition is not empty apply limitation for select
     *
     * @param Varien_Db_Select $select
     * @param string $attrCode              the attribute code
     * @param string|Zend_Db_Expr $entity   the entity field or expression for condition
     * @param string|Zend_Db_Expr $store    the store field or expression for condition
     * @param Zend_Db_Expr $condition       the limitation condition
     * @param bool $required                if required or has condition used INNER join, else - LEFT
     * 
     * @return Zend_Db_Expr                 the attribute value expression
     */
    protected function _addAttributeToSelect($select, $attrCode, $entity, $store, $condition = null, $required = false)
    {
        $attribute      = $this->_getAttribute($attrCode);
        $attributeId    = $attribute->getAttributeId();
        $attributeTable = $attribute->getBackend()->getTable();
        $adapter        = $this->_connection;
        $joinType       = !is_null($condition) || $required ? 'join' : 'joinLeft';
        if ($attribute->isScopeGlobal()) {
            $alias = 'ta_' . $attrCode;
            $select->$joinType(
                array($alias => $attributeTable),
                "{$alias}.entity_id = {$entity} AND {$alias}.attribute_id = {$attributeId}"
                    . " AND {$alias}.store_id = 0",
                array()
            );
            $expression = new Zend_Db_Expr("{$alias}.value");
        } else {
            $dAlias = 'tad_' . $attrCode;
            $sAlias = 'tas_' . $attrCode;
            $select->$joinType(
                array($dAlias => $attributeTable),
                "{$dAlias}.entity_id = {$entity} AND {$dAlias}.attribute_id = {$attributeId}"
                    . " AND {$dAlias}.store_id = 0",
                array()
            );
            $select->joinLeft(
                array($sAlias => $attributeTable),
                "{$sAlias}.entity_id = {$entity} AND {$sAlias}.attribute_id = {$attributeId}"
                    . " AND {$sAlias}.store_id = {$store}",
                array()
            );
            if ($this->getVersionHelper()->isGe1600()) {
                $expression = $adapter->getCheckSql($adapter->getIfNullSql("{$sAlias}.value_id", -1) . ' > 0',
                    "{$sAlias}.value", "{$dAlias}.value");
            } else {
                $expression = new Zend_Db_Expr("IF({$sAlias}.value_id > 0, {$sAlias}.value, {$dAlias}.value)");
            }    
        }
        if (!is_null($condition)) {
            $select->where("{$expression}{$condition}");
        }
        return $expression;
    }
    /**
     * Prepare tier price index table
     *
     * @param int|array $entityIds
     * 
     * @return self
     */
    protected function _prepareTierPriceIndex($entityIds = null)
    {
        $helper             = $this->getWarehouseHelper();
        $indexerHelper      = $this->getProductPriceIndexerHelper();
        $isMultipleMode     = $helper->isMultipleMode();
        
        $adapter            = $this->_connection;
        $table              = $this->_getTable('catalog/product_index_tier_price');
        $this->_emptyTable($table);
        
        $price              = new Zend_Db_Expr("IF (tp.website_id=0, ROUND(tp.value * cwd.rate, 4), tp.value)");
        if ($isMultipleMode) {
            $group = array('tp.entity_id', 'cg.customer_group_id', 'cw.website_id');
        } else {
            $group = array('tp.entity_id', 'cg.customer_group_id', 'cw.website_id', 'cis.stock_id');
        }
        
        $columns = array(
            'entity_id'         => new Zend_Db_Expr('tp.entity_id'), 
            'customer_group_id' => new Zend_Db_Expr('cg.customer_group_id'), 
            'website_id'        => new Zend_Db_Expr('cw.website_id'), 
            'stock_id'          => new Zend_Db_Expr(
                new Zend_Db_Expr((($isMultipleMode) ? $helper->getDefaultStockId() : 'cis.stock_id'))
            ), 
            'min_price'         => new Zend_Db_Expr("MIN({$price})"), 
        );
        if ($isMultipleMode) {
            $group = array('tp.entity_id', 'cg.customer_group_id', 'cw.website_id');
        } else {
            $group = array('tp.entity_id', 'cg.customer_group_id', 'cw.website_id', 'cis.stock_id');
        }
        $select = $adapter->select()
            ->from(array('tp' => $this->_getTable(array('catalog/product', 'tier_price'))), array())
            ->join(
                array('cg' => $this->_getTable('customer/customer_group')), 
                'tp.all_groups = 1 OR (tp.all_groups = 0 AND (tp.customer_group_id = cg.customer_group_id))', 
                array()
            )
            ->join(
                array('cw' => $this->_getTable('core/website')),
                'tp.website_id = 0 OR tp.website_id = cw.website_id', array()
            )
            ->join(
                array('cwd' => $this->_getTable('catalog/product_index_website')),
                'cw.website_id = cwd.website_id', array()
            )
            ->join(
                array('cis' => $this->_getTable('cataloginventory/stock')), 
                '(tp.stock_id IS NULL) OR (tp.stock_id = cis.stock_id)', 
                array()
            )
            ->where('(cw.website_id != 0)')
            ->columns($columns)
            ->group($group);
        if (!empty($entityIds)) {
            $select->where('tp.entity_id IN(?)', $entityIds);
        }
        $query = $select->insertFromSelect($table);
        $adapter->query($query);
        return $this;
    }
    /**
     * Prepare batch price index table
     * 
     * @param int|array $entityIds
     * @param string $attributeCode
     * @param string $table
     * @param string $indexTable
     * 
     * @return self
     */
    protected function __prepareBatchPriceIndex($entityIds = null, $attributeCode, $table, $indexTable)
    {
        $helper             = $this->getWarehouseHelper();
        $priceHelper        = $helper->getProductPriceHelper();
        $indexerHelper      = $this->getProductPriceIndexerHelper();
        $isMultipleMode     = $helper->isMultipleMode();
        $adapter            = $this->_connection;
        
        $select             = $adapter->select()
            ->from(
                array('e' => $this->_getTable('catalog/product')), 
                array()
            )
            ->join(
                array('cw' => $this->_getTable('core/website')), 
                '', 
                array()
            )
            ->join(
                array('cwd' => $this->_getTable('catalog/product_index_website')), 
                '(cw.website_id = cwd.website_id)', 
                array()
            )
            ->join(
                array('csg' => $this->_getTable('core/store_group')), 
                'csg.website_id = cw.website_id AND cw.default_group_id = csg.group_id', 
                array()
            )
            ->join(
                array('cs' => $this->_getTable('core/store')),
                'csg.default_store_id = cs.store_id AND cs.store_id != 0', 
                array()
            )
            ->join(
                array('pw' => $this->_getTable('catalog/product_website')),
                '(pw.product_id = e.entity_id) AND (pw.website_id = cw.website_id)', 
                array()
            )
            ->join(
                array('cis' => $this->_getTable('cataloginventory/stock')), 
                '', 
                array()
            );
        $price              = $this->_addAttributeToSelect($select, $attributeCode, 'e.entity_id', 'cs.store_id');
        $select->joinLeft(array('cbgp' => $table), 
                implode(' AND ', array(
                    '(cbgp.product_id = e.entity_id)', 
                    '(cbgp.stock_id = cis.stock_id)', 
                    '(cbgp.website_id = 0)', 
                )), array());
        if ($priceHelper->isWebsiteScope()) {
            $select->joinLeft(
                array('cbp' => $table), 
                implode(' AND ', array(
                    '(cbp.product_id = e.entity_id)', 
                    '(cbp.stock_id = cis.stock_id)', 
                    '(cbp.website_id = cw.website_id)', 
                )), array()
            );
        }
        
        if ($priceHelper->isWebsiteScope()) {
            $price = new Zend_Db_Expr("IF (cbp.price IS NOT NULL, cbp.price, IF (cbgp.price IS NOT NULL, ROUND(cbgp.price * cwd.rate, 4), {$price}))");
        } else {
            $price = new Zend_Db_Expr("IF (cbgp.price IS NOT NULL, ROUND(cbgp.price * cwd.rate, 4), {$price})");
        }
        
        $columns    = array(
            'entity_id'         => new Zend_Db_Expr('e.entity_id'), 
            'stock_id'          => new Zend_Db_Expr(
                (($isMultipleMode) ? $helper->getDefaultStockId() : 'cis.stock_id')
             ), 
            'website_id'        => new Zend_Db_Expr('cw.website_id'), 
            'price'             => new Zend_Db_Expr(
                (($isMultipleMode) ? "MAX({$price})" : $price)
            ), 
            'min_price'         => new Zend_Db_Expr(
                (($isMultipleMode) ? "MIN({$price})" : "NULL")
            ), 
            'max_price'         => new Zend_Db_Expr(
                (($isMultipleMode) ? "MAX({$price})" : "NULL")
            )
        );
        if ($isMultipleMode) {
            $group              = array('e.entity_id', 'cw.website_id');
        } else {
            $group              = array('e.entity_id', 'cis.stock_id', 'cw.website_id');
        }
        $where                  = '(cw.website_id <> 0)';
        
        $select->where($where)
            ->columns($columns)
            ->group($group);
        if (!empty($entityIds)) {
            $select->where('e.entity_id IN(?)', $entityIds);
        }
        $adapter->delete($indexTable);
        $query = $select->insertFromSelect($indexTable);
        
        $adapter->query($query);
        return $this;
    }
    /**
     * Prepare batch price index table
     *
     * @param int|array $entityIds
     * 
     * @return self
     */
    protected function _prepareBatchPriceIndex($entityIds = null)
    {
        return $this->__prepareBatchPriceIndex(
            $entityIds, 
            'price', 
            $this->_getTable('catalog/product_batch_price'), 
            $this->getProductPriceIndexerHelper()
                ->getBatchPriceIndexTable()
        );
    }
    /**
     * Prepare batch special price index table
     *
     * @param int|array $entityIds the entity ids limitation
     * 
     * @return self
     */
    protected function _prepareBatchSpecialPriceIndex($entityIds = null)
    {
        return $this->__prepareBatchPriceIndex(
            $entityIds, 
            'special_price', 
            $this->_getTable('catalog/product_batch_special_price'), 
            $this->getProductPriceIndexerHelper()
                ->getBatchSpecialPriceIndexTable()
        );
    }
    /**
     * Reindex all
     * 
     * @return self
     */
    protected function _reindexAll()
    {
        $this->_useIdxTable(true);
        $this->_emptyTable($this->_getIdxTable());
        $this->_prepareWebsiteDateTable();
        
        $this->_prepareBatchPriceIndex();
        $this->_prepareBatchSpecialPriceIndex();
        
        $this->_prepareTierPriceIndex();
        $this->_prepareGroupPriceIndex();

        $indexers = $this->_getTypeIndexers();
        foreach ($indexers as $indexer) {
            $indexer->reindexAll();
        }
        $this->_syncData();

        return $this;
    }
    /**
     * Refresh entities index
     * 
     * @param array $changedIds
     * 
     * @return array affected ids
     */
    protected function _reindex($changedIds = array())
    {
        $this->_emptyTable($this->_getIdxTable());
        $this->_prepareWebsiteDateTable();
        $select = $this->_connection->select()
            ->from($this->_getTable('catalog/product'), array('entity_id', 'type_id'))
            ->where('entity_id IN(?)', $changedIds);
        $pairs  = $this->_connection->fetchPairs($select);
        $byType = array();
        foreach ($pairs as $productId => $productType) {
            $byType[$productType][$productId] = $productId;
        }

        $compositeIds    = array();
        $notCompositeIds = array();

        foreach ($byType as $productType => $entityIds) {
            $indexer = $this->_getIndexer($productType);
            if ($indexer->getIsComposite()) {
                $compositeIds += $entityIds;
            } else {
                $notCompositeIds += $entityIds;
            }
        }

        if (!empty($notCompositeIds)) {
            $select = $this->_connection->select()
                ->from(
                    array('l' => $this->_getTable('catalog/product_relation')),
                    'parent_id'
                )
                ->join(
                    array('e' => $this->_getTable('catalog/product')),
                    'e.entity_id = l.parent_id',
                    array('type_id')
                )
                ->where('l.child_id IN(?)', $notCompositeIds);
            $pairs  = $this->_connection->fetchPairs($select);
            foreach ($pairs as $productId => $productType) {
                if (!in_array($productId, $changedIds)) {
                    $changedIds[] = $productId;
                    $byType[$productType][$productId] = $productId;
                    $compositeIds[$productId] = $productId;
                }
            }
        }

        if (!empty($compositeIds)) {
            $this->_copyRelationIndexData($compositeIds, $notCompositeIds);
        }
        
        $this->_prepareBatchPriceIndex();
        $this->_prepareBatchSpecialPriceIndex();
        
        $this->_prepareTierPriceIndex($compositeIds + $notCompositeIds);
        $this->_prepareGroupPriceIndex($compositeIds + $notCompositeIds);

        $indexers = $this->_getTypeIndexers();
        foreach ($indexers as $indexer) {
            if (!empty($byType[$indexer->getTypeId()])) {
                $indexer->reindexEntity($byType[$indexer->getTypeId()]);
            }
        }

        $this->_syncData($changedIds);
        return $compositeIds + $notCompositeIds;
    }
}