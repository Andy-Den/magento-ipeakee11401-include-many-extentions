<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Mage
 * @package     SOAP_ShoppingAnalytics
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * ShoppingAnalytics Page Block
 *
 * @category   Mage
 * @package    SOAP_ShoppingAnalytics
 * @author     SOAP Media
 */
class SOAP_ShoppingAnalytics_Block_Sa extends Mage_Core_Block_Template
{
    public function getPageName()
    {
        return $this->_getData('page_name');
    }

    protected function _getPageTrackingCode($accountId)
    {
        return "
            _roi.push(['_setMerchantId', '{$this->jsQuoteEscape($accountId)}']);
            ";
    }

    public function getOrdersTrackingCode($accountId = 0)
    {
        $categoryModel = Mage::getModel('catalog/category');
        
        $orderIds = $this->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }
        $collection = Mage::getResourceModel('sales/order_collection')->addFieldToFilter('entity_id', array('in' => $orderIds));
        $result = array();
        foreach ($collection as $order) {
            if ($order->getIsVirtual()) {
                $address = $order->getBillingAddress();
            } else {
                $address = $order->getShippingAddress();
            }
            $result[] = sprintf("_roi.push(['_setMerchantId', '%s']);", $this->jsQuoteEscape($accountId));
            $result[] = sprintf("_roi.push(['_setOrderId', '%s']);", $order->getIncrementId());
            $result[] = sprintf("_roi.push(['_setOrderAmount', '%s']);", $order->getBaseGrandTotal());
            $result[] = sprintf("_roi.push(['_setOrderNotes', '%s']);", 'Notes');
                
            foreach ($order->getAllVisibleItems() as $item) {
                //Cat IDs                
                $catIDs = $item->getProduct()->getCategoryIds();
                $category = $categoryModel->load($catIDs[0]);
                $catName = $category->getName();

                $result[] = sprintf("_roi.push(['_addItem', '%s', '%s', '%s', '%s', '%s', '%s']);",
                    $this->jsQuoteEscape($item->getSku()),
                    $this->jsQuoteEscape($item->getName()), 
                    $catIDs[0] ? $catIDs[0] : '',
                    $catName ? $catName : '',
                    $item->getBasePrice(), 
                    $item->getQtyOrdered()
                );
            }
            $result[] = "_roi.push(['_trackTrans']);";
        }
        return implode("\n", $result);
    }    
}



