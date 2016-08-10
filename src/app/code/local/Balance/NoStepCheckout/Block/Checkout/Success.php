<?php

class Balance_NoStepCheckout_Block_Checkout_Success extends Mage_Checkout_Block_Onepage_Success {
    
    protected function _beforeToHtml()
    {
        $this->_prepareLastOrder();
//        $this->_prepareLastBillingAgreement();
//        $this->_prepareLastRecurringProfiles();
        return Mage_Core_Block_Template::_beforeToHtml();
    }
    
    protected function _prepareLastOrder()
    {
        $orderId = Mage::getSingleton('nostepcheckout/session')->getLastNscOrderId();
        if ($orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            if ($order->getId()) {
                $isVisible = !in_array($order->getState(),
                    Mage::getSingleton('sales/order_config')->getInvisibleOnFrontStates());
                $this->addData(array(
                    'is_order_visible' => $isVisible,
                    'view_order_id' => $this->getUrl('sales/order/view/', array('order_id' => $orderId)),
                    'print_url' => $this->getUrl('sales/order/print', array('order_id'=> $orderId)),
                    'can_print_order' => $isVisible,
                    'can_view_order'  => Mage::getSingleton('customer/session')->isLoggedIn() && $isVisible,
                    'order_id'  => $order->getIncrementId(),
                ));
            }
        }
    }
}