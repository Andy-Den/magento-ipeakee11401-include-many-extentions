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
 * Invoice grid collection
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Mysql4_Sales_Order_Invoice_Grid_Collection extends Mage_Sales_Model_Mysql4_Order_Invoice_Grid_Collection
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
     * Adding stock titles to result collection
     * 
     * @return Innoexts_Warehouse_Model_Mysql4_Sales_Order_Invoice_Grid_Collection
     */
    public function addStockTitlesToResult()
    {
        $invoiceStocks = array();
        foreach ($this as $invoice) {
            $invoiceStocks[$invoice->getId()] = array();
        }
        if (!empty($invoiceStocks)) {
            $select = $this->getConnection()->select()
                ->from(array('invoice_stock' => $this->getTable('warehouse/invoice_grid_warehouse')))
                ->join(array('stock' => $this->getTable('cataloginventory/stock')), 'stock.stock_id = invoice_stock.stock_id', array('stock_name'))
                ->where($this->getConnection()->quoteInto('invoice_stock.entity_id IN (?)', array_keys($invoiceStocks)));
            $data = $this->getConnection()->fetchAll($select);
            foreach ($data as $row) {
                $invoiceStocks[$row['entity_id']][] = $row['stock_id'];
            }
        }
        foreach ($this as $invoice) {
            if (isset($invoiceStocks[$invoice->getId()])) {
                $invoice->setData('stocks', $invoiceStocks[$invoice->getId()]);
            }
        }
        return $this;
    }
    /**
     * Add stocks field
     * 
     * @return Innoexts_Warehouse_Model_Mysql4_Sales_Order_Grid_Collection
     */
    public function joinStocksField()
    {
        $this->addFilterToMap('stocks', 'invoice_stock.stock_id');
        $this->getSelect()->joinLeft(
            array('invoice_stock' => $this->getTable('warehouse/invoice_grid_warehouse')), 
            '(main_table.entity_id = invoice_stock.entity_id)', 
            array('stocks' => 'invoice_stock.stock_id')
        );
        return $this;
    }
}