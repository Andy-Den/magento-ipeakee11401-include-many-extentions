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
 * Full page cache observer
 * 
 * @category   Innoexts
 * @package    Innoexts_WarehouseEnterprise
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_WarehouseEnterprise_Model_Enterprise_Pagecache_Observer 
    extends Enterprise_PageCache_Model_Observer 
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
     * Set cookie for customer
     *
     * @param Varien_Event_Observer $observer
     * 
     * @return self
     */
    public function applyCustomerAddress(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $this->_getCookie()->updateCustomerCookies();
        return $this;
    }
}