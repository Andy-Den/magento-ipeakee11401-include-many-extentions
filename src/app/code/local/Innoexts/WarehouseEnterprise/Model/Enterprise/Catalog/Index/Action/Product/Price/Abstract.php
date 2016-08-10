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
 * Full refresh price index
 * 
 * @category   Innoexts
 * @package    Innoexts_WarehouseEnterprise
 * @author     Innoexts Team <developers@innoexts.com>
 */
abstract class Innoexts_WarehouseEnterprise_Model_Enterprise_Catalog_Index_Action_Product_Price_Abstract 
    extends Enterprise_Catalog_Model_Index_Action_Product_Price_Abstract 
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
}