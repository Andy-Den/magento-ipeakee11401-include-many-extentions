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
 * Full page cache processor
 * 
 * @category   Innoexts
 * @package    Innoexts_WarehouseEnterprise
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_WarehouseEnterprise_Model_Enterprise_Pagecache_Processor 
    extends Enterprise_PageCache_Model_Processor 
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
     * Populate request ids
     * 
     * @return self
     */
    protected function _createRequestIds()
    {
        parent::_createRequestIds();
        $uri = $this->_requestId;
        if (isset($_COOKIE[Innoexts_WarehouseEnterprise_Model_Enterprise_Pagecache_Cookie::COOKIE_CUSTOMER_ADDRESS])) {
            $uri .= '_'.$_COOKIE[Innoexts_WarehouseEnterprise_Model_Enterprise_Pagecache_Cookie::COOKIE_CUSTOMER_ADDRESS];
        }
        if (isset($_COOKIE[Innoexts_WarehouseEnterprise_Model_Enterprise_Pagecache_Cookie::COOKIE_CUSTOMER_STOCK_ID])) {
            $uri .= '_'.$_COOKIE[Innoexts_WarehouseEnterprise_Model_Enterprise_Pagecache_Cookie::COOKIE_CUSTOMER_STOCK_ID];
        }
        if (isset($_COOKIE[Innoexts_WarehouseEnterprise_Model_Enterprise_Pagecache_Cookie::COOKIE_CUSTOMER_PRODUCT_STOCK_IDS])) {
            $uri .= '_'.$_COOKIE[Innoexts_WarehouseEnterprise_Model_Enterprise_Pagecache_Cookie::COOKIE_CUSTOMER_PRODUCT_STOCK_IDS];
        }
        $this->_requestId       = $uri;
        $this->_requestCacheId  = $this->prepareCacheId($this->_requestId);
        return $this;
    }
}