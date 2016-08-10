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
 * Full page cache cookie
 * 
 * @category   Innoexts
 * @package    Innoexts_WarehouseEnterprise
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_WarehouseEnterprise_Model_Enterprise_Pagecache_Cookie 
    extends Enterprise_PageCache_Model_Cookie 
{
    /**
     * Customer address cookie
     */
    const COOKIE_CUSTOMER_ADDRESS               = 'CUSTOMER_ADDRESS';
    /**
     * Customer address cookie
     */
    const COOKIE_CUSTOMER_STOCK_ID              = 'CUSTOMER_STOCK_ID';
    /**
     * Customer address cookie
     */
    const COOKIE_CUSTOMER_PRODUCT_STOCK_IDS     = 'CUSTOMER_PRODUCT_STOCK_IDS';
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
     * Keep customer cookies synchronized with customer session
     *
     * @return self
     */
    public function updateCustomerCookies()
    {
        $helper                         = $this->getWarehouseHelper();
        $customerHelper                 = $helper->getCoreHelper()
            ->getCustomerHelper();
        $addressHelper                  = $helper->getCoreHelper()
            ->getAddressHelper();
        $customerId                     = $customerHelper->getCustomerId();
        $customerGroupId                = $customerHelper->getCustomerGroupId();
        $customerAddress                = $helper->getCustomerLocatorHelper()
            ->getCustomerAddress();
        $customerAddressHash            = $addressHelper->getHash($customerAddress);
        $customerStockId                = $helper->getSession()
            ->getStockId();
        $customerProductStockIdsHash    = $helper->getSession()
            ->getProductStockIdsHash();
        
        if (!$customerId || is_null($customerGroupId)) {
            $customerCookies        = new Varien_Object();
            Mage::dispatchEvent('update_customer_cookies', array('customer_cookies' => $customerCookies));
            if (!$customerId) {
                $customerId             = $customerCookies->getCustomerId();
            }
            if (is_null($customerGroupId)) {
                $customerGroupId        = $customerCookies->getCustomerGroupId();
            }
        }
        if ($customerId && !is_null($customerGroupId)) {
            $this->setObscure(self::COOKIE_CUSTOMER, 'customer_' . $customerId);
            $this->setObscure(self::COOKIE_CUSTOMER_GROUP, 'customer_group_' . $customerGroupId);
            if ($customerHelper->isLoggedIn()) {
                $this->setObscure(self::COOKIE_CUSTOMER_LOGGED_IN, 'customer_logged_in_' . $customerHelper->isLoggedIn());
            } else {
                $this->delete(self::COOKIE_CUSTOMER_LOGGED_IN);
            }
        } else {
            $this->delete(self::COOKIE_CUSTOMER);
            $this->delete(self::COOKIE_CUSTOMER_GROUP);
            $this->delete(self::COOKIE_CUSTOMER_LOGGED_IN);
        }
        
        $this->setObscure(self::COOKIE_CUSTOMER_ADDRESS, 'customer_address_'.$customerAddressHash);
        $this->setObscure(self::COOKIE_CUSTOMER_STOCK_ID, 'customer_address_'.$customerStockId);
        $this->setObscure(self::COOKIE_CUSTOMER_PRODUCT_STOCK_IDS, 'customer_address_'.$customerProductStockIdsHash);
        
        return $this;
    }
}