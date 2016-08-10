<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Amazon_Synchronization_Tasks_Defaults_UpdateListingsProducts_Responser extends Ess_M2ePro_Model_Connector_Xfabric_Amazon_Inventory_Get_ItemsResponser
{
    private $synchronizationLog = NULL;

    // ########################################

    protected function unsetLocks($fail = false, $message = NULL)
    {
        /** @var $lockItem Ess_M2ePro_Model_LockItem */
        $lockItem = Mage::getModel('M2ePro/LockItem');

        $tempNick = Ess_M2ePro_Model_Amazon_Synchronization_Tasks_Defaults_UpdateListingsProducts::LOCK_ITEM_PREFIX;
        $tempNick .= '_'.$this->params['account_id'].'_'.$this->params['marketplace_id'];

        $lockItem->setNick($tempNick);
        $lockItem->remove();

        $this->getAccount()->deleteObjectLocks(NULL,$this->messageHash);
        $this->getAccount()->deleteObjectLocks('synchronization',$this->messageHash);
        $this->getAccount()->deleteObjectLocks('synchronization_amazon',$this->messageHash);
        $this->getAccount()->deleteObjectLocks(Ess_M2ePro_Model_Amazon_Synchronization_Tasks_Defaults_UpdateListingsProducts::LOCK_ITEM_PREFIX,$this->messageHash);

        $this->getMarketplace()->deleteObjectLocks(NULL,$this->messageHash);
        $this->getMarketplace()->deleteObjectLocks('synchronization',$this->messageHash);
        $this->getMarketplace()->deleteObjectLocks('synchronization_amazon',$this->messageHash);
        $this->getMarketplace()->deleteObjectLocks(Ess_M2ePro_Model_Amazon_Synchronization_Tasks_Defaults_UpdateListingsProducts::LOCK_ITEM_PREFIX,$this->messageHash);

        $fail && $this->getSynchLogModel()->addMessage(Mage::helper('M2ePro')->__('Update listings products is failed. Reason: "'.$message.'"'),
                                                       Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR,
                                                       Ess_M2ePro_Model_Log_Abstract::PRIORITY_HIGH);
    }

    protected function processSucceededResponseData($response)
    {
        Mage::helper('M2ePro/Exception')->setFatalErrorHandler();

        $receivedItems = parent::processSucceededResponseData($response);

        // Prepare received items
        //----------------------
        $tempItems = array();
        $tempCount = count($receivedItems);
        for ($i=0;$i<$tempCount;$i++) {
            if (empty($receivedItems[$i]['identifiers']['sku'])) {
                continue;
            }
            $tempItems[$receivedItems[$i]['identifiers']['sku']] = $receivedItems[$i];
        }
        $receivedItems = $tempItems;
        //----------------------

        // Prepare extension items
        //----------------------
        /** @var $collection Mage_Core_Model_Mysql4_Collection_Abstract */
        $collection = Mage::helper('M2ePro/Component_Amazon')->getCollection('Listing_Product');

        $listingTable = Mage::getResourceModel('M2ePro/Listing')->getMainTable();
        $generalTemplateTable = Mage::getResourceModel('M2ePro/Template_General')->getMainTable();

        $collection->getSelect()->join(array('l' => $listingTable), 'main_table.listing_id = l.id', array());
        $collection->getSelect()->join(array('gt' => $generalTemplateTable), 'l.template_general_id = gt.id', array());
        $collection->getSelect()->where('gt.marketplace_id = ?',(int)$this->getMarketplace()->getId());
        $collection->getSelect()->where('gt.account_id = ?',(int)$this->getAccount()->getId());

        $tempItems = $collection->toArray();

        $existingListings = array();
        for ($i=0;$i<$tempItems['totalRecords'];$i++) {
            if (empty($tempItems['items'][$i]['sku'])) {
                continue;
            }
            $existingListings[$tempItems['items'][$i]['sku']] = $tempItems['items'][$i];
        }
        //----------------------

        try {

            $this->updateReceivedListingsProducts($receivedItems,$existingListings);
            $this->updateNotReceivedListingsProducts($receivedItems,$existingListings);

        } catch (Exception $exception) {

            Mage::helper('M2ePro/Exception')->process($exception,true);

            $this->getSynchLogModel()->addMessage(Mage::helper('M2ePro')->__($exception->getMessage()),
                                                  Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR,
                                                  Ess_M2ePro_Model_Log_Abstract::PRIORITY_HIGH);
        }
    }

    // ########################################

    protected function updateReceivedListingsProducts(&$receivedItems, &$existingItems)
    {
        foreach ($existingItems as &$existingItem) {

            if (!isset($receivedItems[$existingItem['sku']])) {
                continue;
            }

            $receivedItem = $receivedItems[$existingItem['sku']];

            $newData = array(
                'item_id' => (string)$receivedItem['identifiers']['item_id'],
                'general_id' => (string)$receivedItem['identifiers']['product_id'],
                'sku' => (string)$receivedItem['identifiers']['sku'],
                'online_price' => (float)$receivedItem['price'],
                'online_qty' => (int)$receivedItem['qty'],
                'start_date' => (string)$receivedItem['start_date'],
                'is_afn_channel' => (bool)$receivedItem['channel']['is_afn'],
                'is_isbn_general_id' => (bool)$receivedItem['identifiers']['is_isbn']
            );

            $existingData = array(
                'item_id' => (string)$existingItem['item_id'],
                'general_id' => (string)$existingItem['general_id'],
                'sku' => (string)$existingItem['sku'],
                'online_price' => (float)$existingItem['online_price'],
                'online_qty' => (int)$existingItem['online_qty'],
                'start_date' => (string)$existingItem['start_date'],
                'is_afn_channel' => (bool)$existingItem['is_afn_channel'],
                'is_isbn_general_id' => (bool)$existingItem['is_isbn_general_id']
            );

            if ($newData == $existingData) {
                continue;
            }

            $newData['status_changer'] = Ess_M2ePro_Model_Listing_Product::STATUS_CHANGER_COMPONENT;

            if ((bool)$newData['is_afn_channel']) {
                $newData['status'] = Ess_M2ePro_Model_Listing_Product::STATUS_UNKNOWN;
            } else {
                if ((int)$newData['online_qty'] > 0) {
                    $newData['status'] = Ess_M2ePro_Model_Listing_Product::STATUS_LISTED;
                    $newData['end_date'] = NULL;
                } else {
                    $newData['status'] = Ess_M2ePro_Model_Listing_Product::STATUS_STOPPED;
                    $newData['end_date'] = Mage::helper('M2ePro')->getCurrentGmtDate();
                }
            }

            if (isset($newData['status']) && $newData['status'] != $existingItem['status']) {

                Mage::getModel('M2ePro/ProductChange')
                        ->updateAttribute( $existingItem['product_id'],
                                           'amazon_listing_product_status',
                                           'listing_product_'.$existingItem['listing_product_id'].'_status_'.$existingItem['status'],
                                           'listing_product_'.$existingItem['listing_product_id'].'_status_'.$newData['status'] ,
                                           Ess_M2ePro_Model_ProductChange::CREATOR_TYPE_SYNCHRONIZATION );
            }

            $listingProductId = (int)$existingItem['listing_product_id'];
            $listingProductObj = Mage::helper('M2ePro/Component_Amazon')->getObject('Listing_Product',$listingProductId);
            $listingProductObj->addData($newData)->save();
        }
    }

    protected function updateNotReceivedListingsProducts(&$receivedItems, &$existingItems)
    {
        foreach ($existingItems as &$existingItem) {

            if (isset($receivedItems[$existingItem['sku']])) {
                continue;
            }

            if ($existingItem['status'] == Ess_M2ePro_Model_Listing_Product::STATUS_STOPPED) {
                continue;
            }

            Mage::getModel('M2ePro/ProductChange')
                    ->updateAttribute( $existingItem['product_id'],
                                       'amazon_listing_product_status',
                                       'listing_product_'.$existingItem['listing_product_id'].'_status_'.$existingItem['status'],
                                       'listing_product_'.$existingItem['listing_product_id'].'_status_'.Ess_M2ePro_Model_Listing_Product::STATUS_STOPPED ,
                                       Ess_M2ePro_Model_ProductChange::CREATOR_TYPE_SYNCHRONIZATION );

            $listingProductId = (int)$existingItem['listing_product_id'];
            $listingProductObj = Mage::helper('M2ePro/Component_Amazon')->getObject('Listing_Product',$listingProductId);
            $listingProductObj->addData(array('status'=>Ess_M2ePro_Model_Listing_Product::STATUS_STOPPED))->save();
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
        $logs->setSynchronizationTask(Ess_M2ePro_Model_Synchronization_Log::SYNCH_TASK_DEFAULTS);

        $this->synchronizationLog = $logs;

        return $this->synchronizationLog;
    }

    // ########################################
}