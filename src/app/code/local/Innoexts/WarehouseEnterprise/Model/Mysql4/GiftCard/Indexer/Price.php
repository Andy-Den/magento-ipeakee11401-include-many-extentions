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
 * Gift card products price indexer resource
 *
 * @category   Innoexts
 * @package    Innoexts_WarehouseEnterprise
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_WarehouseEnterprise_Model_Mysql4_GiftCard_Indexer_Price 
    extends Enterprise_GiftCard_Model_Mysql4_Indexer_Price 
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
        return $this->getWarehouseHelper()
            ->getVersionHelper();
    }
    /**
     * Prepare products default final price in temporary index table
     *
     * @param int|array $entityIds  the entity ids limitation
     * 
     * @return self
     */
    protected function _prepareFinalPriceData($entityIds = null)
    {
        $helper             = $this->getWarehouseHelper();
        $indexerHelper      = $this->getProductPriceIndexerHelper();
        $isMultipleMode     = $helper->isMultipleMode();
        $adapter            = $this->_getWriteAdapter();
        $this->_prepareDefaultFinalPriceTable();
        $select             = $indexerHelper->getFinalPriceSelect($adapter);
       
        $select->where('e.type_id=?', $this->getTypeId());
        
        $statusCond     = $adapter->quoteInto('=?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        $this->_addAttributeToSelect($select, 'status', 'e.entity_id', 'cs.store_id', $statusCond, true);
        
        $select->columns(array('tax_class_id' => new Zend_Db_Expr('0')));
        
        $indexerHelper->addStockJoin($select);
        
        $allowOpenAmount    = $this->_addAttributeToSelect($select, 'allow_open_amount', 'e.entity_id', 'cs.store_id');
        $openAmountMin      = $this->_addAttributeToSelect($select, 'open_amount_min', 'e.entity_id', 'cs.store_id');
        $attrAmounts        = $this->_getAttribute('giftcard_amounts');
        $select->joinLeft(
            array('gca' => $this->getTable('enterprise_giftcard/amount')), 
            implode(' AND ', array(
                '(gca.entity_id = e.entity_id)', 
                '(gca.attribute_id = '.$attrAmounts->getAttributeId().')', 
                '(gca.website_id = cw.website_id OR gca.website_id = 0)', 
            )), 
            array()
        );
        $amountsExpr    = new Zend_Db_Expr("MIN(IF(gca.value_id IS NULL, NULL, gca.value))");
        $openAmountExpr = new Zend_Db_Expr("MIN(IF(
            {$allowOpenAmount} = 1, 
            IF({$openAmountMin} > 0, {$openAmountMin}, 0), 
            NULL
        ))");
        $priceExpr      = new Zend_Db_Expr(
            "ROUND(IF(
                {$openAmountExpr} IS NULL, 
                IF({$amountsExpr} IS NULL, 0, {$amountsExpr}), 
                IF(
                    {$amountsExpr} IS NULL, 
                    {$openAmountExpr}, 
                    IF(
                        {$openAmountExpr} > {$amountsExpr}, 
                        {$amountsExpr}, 
                        {$openAmountExpr}
                    )
                )
            ), 4)"
        );
        $select->columns(array(
            'price'            => new Zend_Db_Expr('NULL'), 
            'final_price'      => $priceExpr, 
            'min_price'        => $priceExpr, 
            'max_price'        => new Zend_Db_Expr('NULL'), 
            'tier_price'       => new Zend_Db_Expr('NULL'), 
            'base_tier'        => new Zend_Db_Expr('NULL'), 
        ));
        if ($this->getVersionHelper()->isGe1700()) {
            $select->columns(array(
                'group_price'      => new Zend_Db_Expr('gp.price'), 
                'base_group_price' => new Zend_Db_Expr('gp.price'), 
            ));
        }
        $select->columns(array(
            'stock_id'      => new Zend_Db_Expr('cis.stock_id'), 
        ));
        
        $select->group(array(
            'e.entity_id', 
            'cg.customer_group_id', 
            'cw.website_id', 
            
            'cis.stock_id', 
            
        ));
        
        if (!is_null($entityIds)) {
            $select->where('e.entity_id IN(?)', $entityIds);
        }
        $eventData = array(
            'select'            => $select, 
            'entity_field'      => new Zend_Db_Expr('e.entity_id'), 
            'website_field'     => new Zend_Db_Expr('cw.website_id'), 
            'store_field'       => new Zend_Db_Expr('cs.store_id'), 
            'stock_field'       => new Zend_Db_Expr('cis.stock_id'), 
        );
        Mage::dispatchEvent('prepare_catalog_product_index_select', $eventData);
        $query = $select->insertFromSelect($this->_getDefaultFinalPriceTable());
        $adapter->query($query);
        return $this;
    }
    /**
     * Apply custom option minimal and maximal price to temporary final price index table
     *
     * @return self
     */
    protected function _applyCustomOption()
    {
        $indexerHelper      = $this->getProductPriceIndexerHelper();
        $adapter            = $this->_getWriteAdapter();
        $coaTable           = $this->_getCustomOptionAggregateTable();
        $copTable           = $this->_getCustomOptionPriceTable();
        $finalPriceTable    = $this->_getDefaultFinalPriceTable();
        $this->_prepareCustomOptionAggregateTable();
        $this->_prepareCustomOptionPriceTable();
        
        $select             = $indexerHelper->getOptionTypePriceSelect($adapter, $finalPriceTable);
        $query              = $select->insertFromSelect($coaTable);
        $adapter->query($query);
        
        $select             = $indexerHelper->getOptionPriceSelect($adapter, $finalPriceTable);
        $query              = $select->insertFromSelect($coaTable);
        $adapter->query($query);
        
        $select             = $indexerHelper->getAggregatedOptionPriceSelect($adapter, $coaTable);
        $query              = $select->insertFromSelect($copTable);
        $adapter->query($query);
        
        
        $table              = array('i' => $finalPriceTable);
        $select             = $indexerHelper->getOptionFinalPriceSelect($adapter, $copTable);
        $query              = $select->crossUpdateFromSelect($table);
        $adapter->query($query);
        
        if ($this->getVersionHelper()->isGe1620()) {
            $adapter->delete($coaTable);
            $adapter->delete($copTable);
        } else {
            if ($this->useIdxTable()) {
                $adapter->truncate($coaTable);
                $adapter->truncate($copTable);
            } else {
                $adapter->delete($coaTable);
                $adapter->delete($copTable);
            }
        }
        
        return $this;
    }
    /**
     * Mode final prices index to primary temporary index table
     *
     * @return self
     */
    protected function _movePriceDataToIndexTable()
    {
        $indexerHelper      = $this->getProductPriceIndexerHelper();
        $columns            = $indexerHelper->getPriceSelectColumns();
        $adapter            = $this->_getWriteAdapter();
        $table              = $this->_getDefaultFinalPriceTable();
        $select             = $adapter->select()->from($table, $columns);
        $query              = $select->insertFromSelect($this->getIdxTable());
        $adapter->query($query);
        
        if ($this->getVersionHelper()->isGe1620()) {
            $adapter->delete($table);
        } else {
            if ($this->useIdxTable()) {
                $adapter->truncate($table);
            } else {
                $adapter->delete($table);
            }
        }
        
        return $this;
    }
}