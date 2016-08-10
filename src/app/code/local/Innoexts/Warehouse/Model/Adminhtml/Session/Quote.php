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
 * Quote session
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Adminhtml_Session_Quote extends Mage_Adminhtml_Model_Session_Quote 
{
    /**
     * Retrieve quote model object
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        if (is_null($this->_quote)) {
            $this->_quote = Mage::getModel('sales/quote');
            if ($this->getStoreId() && $this->getQuoteId()) {
                $this->_quote->setStoreId($this->getStoreId())
                    ->setStockId(intval($this->getStockId()));
                if ($stockId = (int) $this->getStockId()) {
                    $this->_quote->setStockId($stockId);
                }
                if ($isStockIdStatic = (int) $this->getIsStockIdStatic()) {
                    $this->_quote->setIsStockIdStatic($isStockIdStatic);
                }
                $this->_quote->load($this->getQuoteId());
            } elseif($this->getStoreId() && $this->hasCustomerId()) {
                $this->_quote->setStoreId($this->getStoreId())
                    ->setStockId(intval($this->getStockId()))
                    ->setCustomerGroupId(Mage::getStoreConfig(self::XML_PATH_DEFAULT_CREATEACCOUNT_GROUP))
                    ->assignCustomer($this->getCustomer())
                    ->setIsActive(false);
                if ($stockId = (int) $this->getStockId()) {
                    $this->_quote->setStockId($stockId);
                }
                if ($isStockIdStatic = (int) $this->getIsStockIdStatic()) {
                    $this->_quote->setIsStockIdStatic($isStockIdStatic);
                }
                $this->_quote->save();
                $this->setQuoteId($this->_quote->getId());
            }
            if ($stockId = (int) $this->getStockId()) {
                $this->_quote->setStockId($stockId);
            }
            if ($isStockIdStatic = (int) $this->getIsStockIdStatic()) {
                $this->_quote->setIsStockIdStatic($isStockIdStatic);
            }
            $this->_quote->setIgnoreOldQty(true);
            $this->_quote->setIsSuperMode(true);
        }
        return $this->_quote;
    }
}