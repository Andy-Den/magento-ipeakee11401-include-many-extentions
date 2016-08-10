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
 * @package     Innoexts_Balancenet
 * @copyright   Copyright (c) 2012 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */
require_once 'Mage/Adminhtml/controllers/Sales/Order/CreateController.php';
/**
 * Orders creation process controller
 * 
 * @category   Innoexts
 * @package    Innoexts_Balancenet
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Balancenet_Adminhtml_Sales_Order_CreateController 
    extends Mage_Adminhtml_Sales_Order_CreateController 
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
     * Initialize order creation session data
     * 
     * @return Mage_Adminhtml_Sales_Order_CreateController
     */
    protected function _initSession()
    {
        $helper = $this->getWarehouseHelper();
        parent::_initSession();
        if (!$helper->getConfig()->isMultipleMode()) {
            if ($stockId = (int) $this->getRequest()->getParam('stock_id')) {
                $this->_getSession()->setStockId($stockId);
                $this->_getOrderCreateModel()->setRecollect(true);
            }
        } else {
            if ($isStockIdStatic = (int) $this->getRequest()->getParam('is_stock_id_static')) {
                $this->_getSession()->setIsStockIdStatic($isStockIdStatic);
                $this->_getOrderCreateModel()->setRecollect(true);
            }
        }
        return $this;
    }
    /**
     * Saving quote and create order
     */
    public function saveAction()
    {
        $helper = $this->getWarehouseHelper();
        try {
            if ($helper->getVersionHelper()->isGe1510()) {
                $this->_processActionData('save');
            } else {
                $this->_processData('save');
            }
            if ($paymentData = $this->getRequest()->getPost('payment')) {
                $this->_getOrderCreateModel()->setPaymentData($paymentData);
                $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($paymentData);
            }
            
            /*
            $orders = $this->_getOrderCreateModel()->setIsValidate(true)
                ->importPostData($this->getRequest()->getPost('order'))->saveQuote()->createOrder();
            */

            $orderCreate = $this->_getOrderCreateModel()->setIsValidate(true)->importPostData($this->getRequest()->getPost('order'));
            $orderCreate->getQuote()->reapplyStocks();
            $orderCreate->getQuote()->collectTotals();
            $orders = $orderCreate->createOrder();
            
            $this->_getSession()->clear();
            if (count($orders) > 1) {
                Mage::getSingleton('adminhtml/session')->addSuccess($helper->__('The orders has been created.'));
                $this->_redirect('*/sales_order');
            } else {
                $order = array_shift($orders);
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The order has been created.'));
                $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
            }
        } catch (Mage_Payment_Model_Info_Exception $e) {
            $this->_getOrderCreateModel()->saveQuote();
            $message = $e->getMessage();
            if( !empty($message) ) {
                $this->_getSession()->addError($message);
            }
            $this->_redirect('*/*/');
        } catch (Mage_Core_Exception $e){
            $message = $e->getMessage();
            if( !empty($message) ) {
                $this->_getSession()->addError($message);
            }
            $this->_redirect('*/*/');
        }
        catch (Exception $e){
            $this->_getSession()->addException($e, $this->__('Order saving error: %s', $e->getMessage()));
            $this->_redirect('*/*/');
        }
    }
}