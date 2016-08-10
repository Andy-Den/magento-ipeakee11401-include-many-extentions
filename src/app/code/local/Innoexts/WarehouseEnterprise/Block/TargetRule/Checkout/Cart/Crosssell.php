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
 * @copyright   Copyright (c) 2012 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

/**
 * TargetRule checkout cart cross-sell products
 * 
 * @category   Innoexts
 * @package    Innoexts_WarehouseEnterprise
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_WarehouseEnterprise_Block_TargetRule_Checkout_Cart_Crosssell extends Enterprise_TargetRule_Block_Checkout_Cart_Crosssell
{
    /**
     * Get warehouse enterprise helper
     * 
     * @return Innoexts_WarehouseEnterprise_Helper_Data
     */
    protected function getWarehouseEnterpriseHelper()
    {
        return Mage::helper('warehouseenterprise');
    }
    /**
     * Get warehouse helper
     * 
     * @return Innoexts_Warehouse_Helper_Data
     */
    public function getWarehouseHelper()
    {
        return $this->getWarehouseEnterpriseHelper()->getWarehouseHelper();
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
     * Get link collection for cross-sell
     *
     * @throws Mage_Core_Exception
     * @return Mage_Catalog_Model_Resource_Product_Collection | null
     */
    protected function _getTargetLinkCollection()
    {
        $collection = Mage::getModel('catalog/product_link')
            ->useCrossSellLinks()
            ->getProductCollection()
            ->setStoreId(Mage::app()->getStore()->getId())
            ->setGroupBy();
        $this->_addProductAttributesAndPrices($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInSiteFilterToCollection($collection);
        $stockStatus = Mage::getSingleton('cataloginventory/stock_status');
        $stockStatus->setStockId($this->getWarehouseConfig()->getStockId());
        $stockStatus->addIsInStockFilterToCollection($collection);
        return $collection;
    }
    /**
     * Retrieve product collection by product identifiers
     *
     * @param array $productIds
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    protected function _getProductCollectionByIds($productIds)
    {
        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->addFieldToFilter('entity_id', array('in' => $productIds));
        $this->_addProductAttributesAndPrices($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        $stockStatus = Mage::getSingleton('cataloginventory/stock_status');
        $stockStatus->setStockId($this->getWarehouseConfig()->getStockId());
        $stockStatus->addIsInStockFilterToCollection($collection);
        return $collection;
    }
}