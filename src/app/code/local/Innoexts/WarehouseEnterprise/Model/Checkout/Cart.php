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
 * Admin cart
 *
 * @category   Innoexts
 * @package    Innoexts_WarehouseEnterprise
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_WarehouseEnterprise_Model_Checkout_Cart extends Enterprise_Checkout_Model_Cart
{
    /**
     * Remove items from quote or move them to wishlist etc.
     *
     * @param array $data Array of items
     * @return Enterprise_Checkout_Model_Cart
     */
    public function updateQuoteItems($data)
    {
        if (!$this->getQuote()->getId() || !is_array($data)) {
            return $this;
        }
        foreach ($data as $itemId => $info) {
            if (!empty($info['configured'])) {
                $item = $this->getQuote()->updateItem($itemId, new Varien_Object($info));
                $itemQty = (float) $item->getQty();
            } else {
                $item = $this->getQuote()->getItemById($itemId);
                $itemQty = (float) $info['qty'];
            }
            if (isset($info['stock_id'])) {
                $stockId = intval($info['stock_id']);
                if ($stockId) {
                    $item->setStockId($stockId);
                    if ($item->isParentItem()) {
                        $child = $item->getChild();
                        if ($child) {
                            $child->setStockId($stockId);
                        }
                    }
                }
            }
            $stockItem = ($item && $item->getStockItem()) ? $item->getStockItem() : null;
            if ($stockItem) {
                if (!$stockItem->getIsQtyDecimal()) {
                    $itemQty = (int) $itemQty;
                } else {
                    $item->setIsQtyDecimal(1);
                }
            }
            $itemQty = ($itemQty > 0) ? $itemQty : 1;
            if (isset($info['custom_price'])) {
                $itemPrice = $this->_parseCustomPrice($info['custom_price']);
            } else {
                $itemPrice = null;
            }
            $noDiscount = !isset($info['use_discount']);
            if (empty($info['action']) || !empty($info['configured'])) {
                if ($item) {
                    $item->setQty($itemQty);
                    $item->setCustomPrice($itemPrice);
                    $item->setOriginalCustomPrice($itemPrice);
                    $item->setNoDiscount($noDiscount);
                    $item->getProduct()->setIsSuperMode(true);
                    $item->checkData();
                }
            } else {
                $this->moveQuoteItem($item->getId(), $info['action'], $itemQty);
            }
        }
        if ($this->_needCollectCart === true) {
            $this->getCustomerCart()
                ->collectTotals()
                ->save();
        }
        $this->setRecollect(true);
        return $this;
    }
}