<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Amazon_Synchronization_Tasks_Orders_Responser extends Ess_M2ePro_Model_Connector_Xfabric_Amazon_Orders_Get_ItemsResponser
{
    /** @var $synchronizationLog Ess_M2ePro_Model_Synchronization_Log */
    private $synchronizationLog = NULL;

    // ########################################

    protected function unsetLocks($fail = false, $message = NULL)
    {
        /** @var $lockItem Ess_M2ePro_Model_LockItem */
        $lockItem = Mage::getModel('M2ePro/LockItem');

        $tempNick = Ess_M2ePro_Model_Amazon_Synchronization_Tasks_Orders::LOCK_ITEM_PREFIX;
        $tempNick .= '_'.$this->params['account_id'].'_'.$this->params['marketplace_id'];

        $lockItem->setNick($tempNick);
        $lockItem->remove();

        $this->getAccount()->deleteObjectLocks(NULL,$this->messageHash);
        $this->getAccount()->deleteObjectLocks('synchronization',$this->messageHash);
        $this->getAccount()->deleteObjectLocks('synchronization_amazon',$this->messageHash);
        $this->getAccount()->deleteObjectLocks(Ess_M2ePro_Model_Amazon_Synchronization_Tasks_Orders::LOCK_ITEM_PREFIX,$this->messageHash);

        $this->getMarketplace()->deleteObjectLocks(NULL,$this->messageHash);
        $this->getMarketplace()->deleteObjectLocks('synchronization',$this->messageHash);
        $this->getMarketplace()->deleteObjectLocks('synchronization_amazon',$this->messageHash);
        $this->getMarketplace()->deleteObjectLocks(Ess_M2ePro_Model_Amazon_Synchronization_Tasks_Orders::LOCK_ITEM_PREFIX,$this->messageHash);

        $fail && $this->getSynchLogModel()->addMessage(Mage::helper('M2ePro')->__('Orders were not received. Reason: "'.$message.'"'),
                                                       Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR,
                                                       Ess_M2ePro_Model_Log_Abstract::PRIORITY_HIGH);
    }

    protected function processSucceededResponseData($response)
    {
        Mage::helper('M2ePro/Exception')->setFatalErrorHandler();

        try {

            $account = $this->getAccount();

            if (!$account->getChildObject()->isOrdersModeEnabled()) {
                return;
            }

            $receivedOrders = parent::processSucceededResponseData($response);

            $orders = array();
            $lastOrderDate = NULL;

            // Create m2e orders
            //---------------------------
            foreach ($receivedOrders as $orderData) {
                if (is_null($lastOrderDate) || strtotime($lastOrderDate) < $orderData['purchase_update_date']) {
                    $lastOrderDate = $orderData['purchase_update_date'];
                }

                /** @var $orderBuilder Ess_M2ePro_Model_Amazon_Order_Builder */
                $orderBuilder = Mage::getModel('M2ePro/Amazon_Order_Builder');
                $orderBuilder->setAccount($account)
                             ->initialize($orderData);

                $orders[] = $orderBuilder->process();
            }
            //---------------------------

            if (!is_null($lastOrderDate)) {
                $account->setData('orders_last_synchronization', $lastOrderDate)->save();
            }

            $orders = array_filter($orders);

            if (count($orders) == 0) {
                return;
            }

            // Create magento orders
            //---------------------------
            foreach ($orders as $order) {
                /** @var $order Ess_M2ePro_Model_Order */
                $order->createMagentoOrder();
                $order->updateMagentoOrderStatus();
                $order->createInvoice();
                $order->createShipment();
            }
            //---------------------------

        } catch (Exception $exception) {

            Mage::helper('M2ePro/Exception')->process($exception,true);

            $this->getSynchLogModel()->addMessage(Mage::helper('M2ePro')->__($exception->getMessage()),
                                                  Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR,
                                                  Ess_M2ePro_Model_Log_Abstract::PRIORITY_HIGH);
        }
    }

    // ########################################

    /**
     * @return Ess_M2ePro_Model_Account
     */
    protected function getAccount()
    {
        return $this->getObjectByParam('Account','account_id');
    }

    /**
     * @return Ess_M2ePro_Model_Marketplace
     */
    protected function getMarketplace()
    {
        return $this->getObjectByParam('Marketplace','marketplace_id');
    }

    //-----------------------------------------

    protected function getSynchLogModel()
    {
        if (!is_null($this->synchronizationLog)) {
            return $this->synchronizationLog;
        }

        /** @var $runs Ess_M2ePro_Model_Synchronization_Run */
        $runs = Mage::getModel('M2ePro/Synchronization_Run');
        $runs->start(Ess_M2ePro_Model_Synchronization_Run::INITIATOR_UNKNOWN);
        $runsId = $runs->getLastId();
        $runs->stop();

        /** @var $logs Ess_M2ePro_Model_Synchronization_Log */
        $logs = Mage::getModel('M2ePro/Synchronization_Log');
        $logs->setSynchronizationRun($runsId);
        $logs->setComponentMode(Ess_M2ePro_Helper_Component_Amazon::NICK);
        $logs->setInitiator(Ess_M2ePro_Model_Synchronization_Run::INITIATOR_UNKNOWN);
        $logs->setSynchronizationTask(Ess_M2ePro_Model_Synchronization_Log::SYNCH_TASK_ORDERS);

        $this->synchronizationLog = $logs;

        return $this->synchronizationLog;
    }

    // ########################################
}