<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Amazon_Synchronization_Tasks_OtherListings_Responser extends Ess_M2ePro_Model_Connector_Xfabric_Amazon_Inventory_Get_ItemsResponser
{
    private $mappingSettings = NULL;
    private $synchronizationLog = NULL;
    private $tempObjectsCache = array();

    // ########################################

    protected function unsetLocks($fail = false, $message = NULL)
    {
        /** @var $lockItem Ess_M2ePro_Model_LockItem */
        $lockItem = Mage::getModel('M2ePro/LockItem');

        $tempNick = Ess_M2ePro_Model_Amazon_Synchronization_Tasks_OtherListings::LOCK_ITEM_PREFIX;
        $tempNick .= '_'.$this->params['account_id'].'_'.$this->params['marketplace_id'];

        $lockItem->setNick($tempNick);
        $lockItem->remove();

        $this->getAccount()->deleteObjectLocks(NULL,$this->messageHash);
        $this->getAccount()->deleteObjectLocks('synchronization',$this->messageHash);
        $this->getAccount()->deleteObjectLocks('synchronization_amazon',$this->messageHash);
        $this->getAccount()->deleteObjectLocks(Ess_M2ePro_Model_Amazon_Synchronization_Tasks_OtherListings::LOCK_ITEM_PREFIX,$this->messageHash);

        $this->getMarketplace()->deleteObjectLocks(NULL,$this->messageHash);
        $this->getMarketplace()->deleteObjectLocks('synchronization',$this->messageHash);
        $this->getMarketplace()->deleteObjectLocks('synchronization_amazon',$this->messageHash);
        $this->getMarketplace()->deleteObjectLocks(Ess_M2ePro_Model_Amazon_Synchronization_Tasks_OtherListings::LOCK_ITEM_PREFIX,$this->messageHash);

        $fail && $this->getSynchLogModel()->addMessage(Mage::helper('M2ePro')->__('Update 3rd party listings is failed. Reason: "'.$message.'"'),
                                                       Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR,
                                                       Ess_M2ePro_Model_Log_Abstract::PRIORITY_HIGH);
    }

    protected function processSucceededResponseData($response)
    {
        Mage::helper('M2ePro/Exception')->setFatalErrorHandler();

        $receivedItems = parent::processSucceededResponseData($response);

        // Prepare extension items
        //----------------------
        /** @var $collection Mage_Core_Model_Mysql4_Collection_Abstract */
        $collection = Mage::helper('M2ePro/Component_Amazon')->getCollection('Listing_Product');
        $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns(array('second_table.sku'));

        $listingTable = Mage::getResourceModel('M2ePro/Listing')->getMainTable();
        $generalTemplateTable = Mage::getResourceModel('M2ePro/Template_General')->getMainTable();

        $collection->getSelect()->join(array('l' => $listingTable), 'main_table.listing_id = l.id', array());
        $collection->getSelect()->join(array('gt' => $generalTemplateTable), 'l.template_general_id = gt.id', array());
        $collection->getSelect()->where('gt.marketplace_id = ?',(int)$this->getMarketplace()->getId());
        $collection->getSelect()->where('gt.account_id = ?',(int)$this->getAccount()->getId());

        $tempItems = $collection->toArray();

        $extensionListingsProducts = array();
        for ($i=0;$i<$tempItems['totalRecords'];$i++) {
            if (empty($tempItems['items'][$i]['sku'])) {
                continue;
            }
            $extensionListingsProducts[] = $tempItems['items'][$i]['sku'];
        }
        //----------------------

        // Prepare received items
        //----------------------
        $tempItems = array();
        $tempCount = count($receivedItems);
        for ($i=0;$i<$tempCount;$i++) {
            if (empty($receivedItems[$i]['identifiers']['sku'])) {
                continue;
            }
            if (in_array($receivedItems[$i]['identifiers']['sku'],$extensionListingsProducts)) {
                continue;
            }
            $tempItems[$receivedItems[$i]['identifiers']['sku']] = $receivedItems[$i];
        }
        $receivedItems = $tempItems;
        //----------------------

        // Prepare existing items
        //----------------------
        /** @var $collection Mage_Core_Model_Mysql4_Collection_Abstract */
        $collection = Mage::helper('M2ePro/Component_Amazon')->getCollection('Listing_Other');
        $collection->addFieldToFilter('account_id',(int)$this->params['account_id']);
        $collection->addFieldToFilter('marketplace_id',(int)$this->params['marketplace_id']);
        //$tempColumns = array('item_id','online_qty','online_price','sku');
        //$collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns($tempColumns);
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

            $this->updateReceivedOtherListings($receivedItems,$existingListings);
            $this->updateNotReceivedOtherListings($receivedItems,$existingListings);
            $this->createNotExistOtherListings($receivedItems,$existingListings);

        } catch (Exception $exception) {

            Mage::helper('M2ePro/Exception')->process($exception,true);

            $this->getSynchLogModel()->addMessage(Mage::helper('M2ePro')->__($exception->getMessage()),
                                                  Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR,
                                                  Ess_M2ePro_Model_Log_Abstract::PRIORITY_HIGH);
        }
    }

    // ########################################

    protected function updateReceivedOtherListings(&$receivedItems, &$existingItems)
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
                'title' => (string)$receivedItem['title'],
                'description' => (string)$receivedItem['description'],
                'notice' => (string)$receivedItem['notice'],
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
                'title' => (string)$existingItem['title'],
                'description' => (string)$existingItem['description'],
                'notice' => (string)$existingItem['notice'],
                'online_price' => (float)$existingItem['online_price'],
                'online_qty' => (int)$existingItem['online_qty'],
                'start_date' => (string)$existingItem['start_date'],
                'is_afn_channel' => (bool)$existingItem['is_afn_channel'],
                'is_isbn_general_id' => (bool)$existingItem['is_isbn_general_id']
            );

            if ($newData == $existingData) {
                continue;
            }

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

            $listingOtherId = (int)$existingItem['listing_other_id'];
            $listingOtherObj = Mage::helper('M2ePro/Component_Amazon')->getObject('Listing_Other',$listingOtherId);
            $listingOtherObj->addData($newData)->save();
        }
    }

    protected function updateNotReceivedOtherListings(&$receivedItems, &$existingItems)
    {
        foreach ($existingItems as &$existingItem) {

            if (isset($receivedItems[$existingItem['sku']])) {
                continue;
            }

            if ($existingItem['status'] == Ess_M2ePro_Model_Listing_Product::STATUS_STOPPED) {
                continue;
            }

            $listingOtherId = (int)$existingItem['listing_other_id'];
            $listingOtherObj = Mage::helper('M2ePro/Component_Amazon')->getObject('Listing_Other',$listingOtherId);
            $listingOtherObj->addData(array('status'=>Ess_M2ePro_Model_Listing_Product::STATUS_STOPPED))->save();
        }
    }

    protected function createNotExistOtherListings(&$receivedItems, &$existingItems)
    {
        foreach ($receivedItems as &$receivedItem) {

            if (isset($existingItems[$receivedItem['identifiers']['sku']])) {
                continue;
            }

            $newData = array(
                'account_id' => (int)$this->params['account_id'],
                'marketplace_id' => (int)$this->params['marketplace_id'],
                'porduct_id' => NULL,

                'item_id' => (string)$receivedItem['identifiers']['item_id'],
                'general_id' => (string)$receivedItem['identifiers']['product_id'],
                'sku' => (string)$receivedItem['identifiers']['sku'],
                'title' => (string)$receivedItem['title'],
                'description' => (string)$receivedItem['description'],
                'notice' => (string)$receivedItem['notice'],
                'online_price' => (float)$receivedItem['price'],
                'online_qty' => (int)$receivedItem['qty'],
                'start_date' => (string)$receivedItem['start_date'],
                'end_date' => NULL,
                'is_afn_channel' => (bool)$receivedItem['channel']['is_afn'],
                'is_isbn_general_id' => (bool)$receivedItem['identifiers']['is_isbn']
            );

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

            $listingOtherModel = Mage::helper('M2ePro/Component_Amazon')->getModel('Listing_Other');
            $listingOtherModel->setData($newData)->save();

            $logModel = Mage::getModel('M2ePro/Listing_Other_Log');
            $logModel->setComponentMode(Ess_M2ePro_Helper_Component_Amazon::NICK);
            $logModel->addProductMessage($listingOtherModel->getId(),
                                         Ess_M2ePro_Model_Log_Abstract::INITIATOR_EXTENSION,
                                         NULL,
                                         Ess_M2ePro_Model_Listing_Other_Log::ACTION_ADD_LISTING,
                                         // Parser hack -> Mage::helper('M2ePro')->__('Item was successfully added');
                                         'Item was successfully added',
                                         Ess_M2ePro_Model_Log_Abstract::TYPE_NOTICE,
                                         Ess_M2ePro_Model_Log_Abstract::PRIORITY_LOW);

            $this->mapJustCreatedOtherListing($listingOtherModel);
        }
    }

    // ########################################

    protected function mapJustCreatedOtherListing(Ess_M2ePro_Model_Listing_Other $otherListing)
    {
        if (!$this->getAccount()->getChildObject()->isOtherListingsMappingEnabled()) {
            return;
        }

        $mappingSettings = $this->getMappingRulesByPriority();

        foreach ($mappingSettings as $type) {

            $magentoProductId = NULL;

            if ($type == 'general_id') {
                $magentoProductId = $this->getGeneralIdMappedMagentoProductId($otherListing);
            }

            if ($type == 'sku') {
                $magentoProductId = $this->getSkuMappedMagentoProductId($otherListing);
            }

            if ($type == 'title') {
                $magentoProductId = $this->getTitleMappedMagentoProductId($otherListing);
            }

            if (is_null($magentoProductId)) {
                continue;
            }

            $otherListing->addData(array('product_id'=>$magentoProductId))->save();

            $dataForAdd = array(
                'account_id' => $this->getAccount()->getId(),
                'marketplace_id' => $this->getMarketplace()->getId(),
                'sku' => $otherListing->getChildObject()->getSku(),
                'product_id' => $otherListing->getProductId(),
                'store_id' => $otherListing->getChildObject()->getRelatedStoreId()
            );

            Mage::getModel('M2ePro/Amazon_Item')->setData($dataForAdd)->save();

            $logModel = Mage::getModel('M2ePro/Listing_Other_Log');
            $logModel->setComponentMode(Ess_M2ePro_Helper_Component_Amazon::NICK);
            $logModel->addProductMessage($otherListing->getId(),
                                         Ess_M2ePro_Model_Log_Abstract::INITIATOR_EXTENSION,
                                         NULL,
                                         Ess_M2ePro_Model_Listing_Other_Log::ACTION_MAP_LISTING,
                                         // Parser hack -> Mage::helper('M2ePro')->__('Item was successfully mapped');
                                         'Item was successfully mapped',
                                         Ess_M2ePro_Model_Log_Abstract::TYPE_NOTICE,
                                         Ess_M2ePro_Model_Log_Abstract::PRIORITY_MEDIUM);

            $this->moveJustMappedOtherListing($otherListing);

            break;
        }
    }

    protected function getMappingRulesByPriority()
    {
        if (!is_null($this->mappingSettings)) {
            return $this->mappingSettings;
        }

        $this->mappingSettings = array();

        foreach ($this->getAccount()->getChildObject()->getOtherListingsMappingSettings() as $key=>$value) {
            if ((int)$value['mode'] == 0) {
                continue;
            }
            for($i=0;$i<10;$i++) {
                if (!isset($this->mappingSettings[(int)$value['priority']+$i])) {
                    $this->mappingSettings[(string)$value['priority']+$i] = (string)$key;
                    break;
                }
            }
        }

        ksort($this->mappingSettings);

        return $this->mappingSettings;
    }

    //-----------------------------------------

    protected function getGeneralIdMappedMagentoProductId(Ess_M2ePro_Model_Listing_Other $otherListing)
    {
        if ($this->getAccount()->getChildObject()->isOtherListingsMappingGeneralIdModeCustomAttribute()) {

            $storeId = $otherListing->getChildObject()->getRelatedStoreId();
            $attributeCode = $this->getAccount()->getChildObject()->getOtherListingsMappingGeneralIdAttribute();
            $attributeValue = trim($otherListing->getChildObject()->getGeneralId());

            $productObj = Mage::getModel('catalog/product')->setStoreId($storeId);
            $productObj = $productObj->loadByAttribute($attributeCode, $attributeValue);

            if ($productObj && $productObj->getId()) {
                return $productObj->getId();
            }
        }

        return NULL;
    }

    protected function getSkuMappedMagentoProductId(Ess_M2ePro_Model_Listing_Other $otherListing)
    {
        $attributeCode = NULL;

        if ($this->getAccount()->getChildObject()->isOtherListingsMappingSkuModeDefault()) {
            $attributeCode = 'sku';
        }

        if ($this->getAccount()->getChildObject()->isOtherListingsMappingSkuModeCustomAttribute()) {
            $attributeCode = $this->getAccount()->getChildObject()->getOtherListingsMappingSkuAttribute();
        }

        if (is_null($attributeCode)) {
            return NULL;
        }

        $storeId = $otherListing->getChildObject()->getRelatedStoreId();
        $attributeValue = trim($otherListing->getChildObject()->getSku());

        $productObj = Mage::getModel('catalog/product')->setStoreId($storeId);
        $productObj = $productObj->loadByAttribute($attributeCode, $attributeValue);

        if ($productObj && $productObj->getId()) {
            return $productObj->getId();
        }

        return NULL;
    }

    protected function getTitleMappedMagentoProductId(Ess_M2ePro_Model_Listing_Other $otherListing)
    {
        $attributeCode = NULL;

        if ($this->getAccount()->getChildObject()->isOtherListingsMappingTitleModeDefault()) {
            $attributeCode = 'name';
        }

        if ($this->getAccount()->getChildObject()->isOtherListingsMappingTitleModeCustomAttribute()) {
            $attributeCode = $this->getAccount()->getChildObject()->getOtherListingsMappingTitleAttribute();
        }

        if (is_null($attributeCode)) {
            return NULL;
        }

        $storeId = $otherListing->getChildObject()->getRelatedStoreId();
        $attributeValue = trim($otherListing->getChildObject()->getTitle());

        $productObj = Mage::getModel('catalog/product')->setStoreId($storeId);
        $productObj = $productObj->loadByAttribute($attributeCode, $attributeValue);

        if ($productObj && $productObj->getId()) {
            return $productObj->getId();
        }

        $findCount = preg_match('/^.+(\[(.+)\])$/',$attributeValue,$tempMatches);
        if ($findCount > 0 && isset($tempMatches[1])) {
            $attributeValue = trim(str_replace($tempMatches[1],'',$attributeValue));
            $productObj = Mage::getModel('catalog/product')->setStoreId($storeId);
            $productObj = $productObj->loadByAttribute($attributeCode, $attributeValue);
            if ($productObj && $productObj->getId()) {
                return $productObj->getId();
            }
        }

        return NULL;
    }

    // ########################################

    protected function moveJustMappedOtherListing(Ess_M2ePro_Model_Listing_Other $otherListing)
    {
        if (!$this->getAccount()->getChildObject()->isOtherListingsMoveToListingsEnabled()) {
            return;
        }

        $listing = $this->getDefaultListing($otherListing);

        if (!($listing instanceof Ess_M2ePro_Model_Listing)) {
            return;
        }

        $listingProduct = $listing->addProduct($otherListing->getProductId());

        if (!($listingProduct instanceof Ess_M2ePro_Model_Listing_Product)) {
            return;
        }

        $dataForUpdate = array(
            'item_id' => $otherListing->getChildObject()->getItemId(),
            'general_id' => $otherListing->getChildObject()->getGeneralId(),
            'sku' => $otherListing->getChildObject()->getSku(),
            'online_price' => $otherListing->getChildObject()->getOnlinePrice(),
            'online_qty' => $otherListing->getChildObject()->getOnlineQty(),
            'is_afn_channel' => (int)$otherListing->getChildObject()->isAfnChannel(),
            'is_isbn_general_id' => (int)$otherListing->getChildObject()->isIsbnGeneralId(),
            'start_date' => $otherListing->getChildObject()->getStartDate(),
            'end_date' => $otherListing->getChildObject()->getEndDate(),
            'status' => $otherListing->getStatus(),
            'status_changer' => Ess_M2ePro_Model_Listing_Product::STATUS_CHANGER_COMPONENT
        );

        $listingProduct->addData($dataForUpdate)->save();

        $logModel = Mage::getModel('M2ePro/Listing_Other_Log');
        $logModel->setComponentMode(Ess_M2ePro_Helper_Component_Amazon::NICK);
        $logModel->addProductMessage($otherListing->getId(),
                                     Ess_M2ePro_Model_Log_Abstract::INITIATOR_EXTENSION,
                                     NULL,
                                     Ess_M2ePro_Model_Listing_Other_Log::ACTION_MOVE_LISTING,
                                     // Parser hack -> Mage::helper('M2ePro')->__('Item was successfully moved');
                                     'Item was successfully moved',
                                     Ess_M2ePro_Model_Log_Abstract::TYPE_NOTICE,
                                     Ess_M2ePro_Model_Log_Abstract::PRIORITY_MEDIUM);

        $tempLog = Mage::getModel('M2ePro/Listing_Log');
        $tempLog->setComponentMode(Ess_M2ePro_Helper_Component_Amazon::NICK);
        $tempLog->addProductMessage($listingProduct->getListingId(),
                                     $otherListing->getProductId(),
                                     Ess_M2ePro_Model_Log_Abstract::INITIATOR_EXTENSION,
                                     NULL,
                                     Ess_M2ePro_Model_Listing_Log::ACTION_MOVE_FROM_OTHER_LISTING,
                                     // Parser hack -> Mage::helper('M2ePro')->__('Item was successfully moved');
                                     'Item was successfully moved',
                                     Ess_M2ePro_Model_Log_Abstract::TYPE_NOTICE,
                                     Ess_M2ePro_Model_Log_Abstract::PRIORITY_MEDIUM);

        $otherListing->deleteInstance();
    }

    //-----------------------------------------

    /**
     * @param Ess_M2ePro_Model_Listing_Other $otherListing
     * @return Ess_M2ePro_Model_Listing
     */
    protected function getDefaultListing(Ess_M2ePro_Model_Listing_Other $otherListing)
    {
        $accountId = $this->getAccount()->getId();
        $marketplaceId = $this->getMarketplace()->getId();

        if (isset($this->tempObjectsCache['listing_'.$accountId.'_'.$marketplaceId])) {
            return $this->tempObjectsCache['listing_'.$accountId.'_'.$marketplaceId];
        }

        $tempCollection = Mage::helper('M2ePro/Component_Amazon')->getCollection('Listing');
        $tempCollection->addFieldToFilter('main_table.title','Default ('.$this->getAccount()->getTitle().' - '.$this->getMarketplace()->getTitle().')');
        $tempItem = $tempCollection->getFirstItem();

        if (!is_null($tempItem->getId())) {
            $this->tempObjectsCache['listing_'.$accountId.'_'.$marketplaceId] = $tempItem;
            return $tempItem;
        }

        $tempModel = Mage::helper('M2ePro/Component_Amazon')->getModel('Listing');

        $dataForAdd = array(
            'title' => 'Default ('.$this->getAccount()->getTitle().' - '.$this->getMarketplace()->getTitle().')',
            'store_id' => $otherListing->getChildObject()->getRelatedStoreId(),

            'template_general_id'         => $this->createDefaultGeneralTemplate($otherListing)->getId(),
            'template_selling_format_id'  => $this->getDefaultSellingFormatTemplate($otherListing)->getId(),
            'template_description_id'     => $this->getDefaultDescriptionTemplate($otherListing)->getId(),
            'template_synchronization_id' => $this->getDefaultSynchronizationTemplate($otherListing)->getId(),

            'synchronization_start_type' => Ess_M2ePro_Model_Listing::SYNCHRONIZATION_START_TYPE_IMMEDIATELY,
            'synchronization_start_through_metric' => Ess_M2ePro_Model_Listing::SYNCHRONIZATION_START_THROUGH_METRIC_DAYS,
            'synchronization_start_through_value' => 1,
            'synchronization_start_date' => Mage::helper('M2ePro')->getCurrentGmtDate(),

            'synchronization_stop_type' => Ess_M2ePro_Model_Listing::SYNCHRONIZATION_STOP_TYPE_NEVER,
            'synchronization_stop_through_metric' => Ess_M2ePro_Model_Listing::SYNCHRONIZATION_STOP_THROUGH_METRIC_DAYS,
            'synchronization_stop_through_value' => 1,
            'synchronization_stop_date' => Mage::helper('M2ePro')->getCurrentGmtDate(),

            'source_products' => Ess_M2ePro_Model_Listing::SOURCE_PRODUCTS_CUSTOM,
            'categories_add_action' => Ess_M2ePro_Model_Listing::CATEGORIES_ADD_ACTION_NONE,
            'categories_delete_action' => Ess_M2ePro_Model_Listing::CATEGORIES_DELETE_ACTION_NONE,
            'hide_products_others_listings' => Ess_M2ePro_Model_Listing::HIDE_PRODUCTS_OTHERS_LISTINGS_NO
        );

        $tempModel->addData($dataForAdd)->save();
        $this->tempObjectsCache['listing_'.$accountId.'_'.$marketplaceId] = $tempModel;

        $attributesSets = Mage::helper('M2ePro/Magento')->getAttributeSets();
        foreach ($attributesSets as $attributeSet) {
            $dataForAdd = array(
                'object_type' => Ess_M2ePro_Model_AttributeSet::OBJECT_TYPE_LISTING,
                'object_id' => (int)$tempModel->getId(),
                'attribute_set_id' => (int)$attributeSet['attribute_set_id']
            );
            Mage::getModel('M2ePro/AttributeSet')->setData($dataForAdd)->save();
        }

        return $tempModel;
    }

    //-----------------------------------------

    /**
     * @param Ess_M2ePro_Model_Listing_Other $otherListing
     * @return Ess_M2ePro_Model_Template_General
     */
    protected function createDefaultGeneralTemplate(Ess_M2ePro_Model_Listing_Other $otherListing)
    {
        $accountId = $this->getAccount()->getId();
        $marketplaceId = $this->getMarketplace()->getId();

        if (isset($this->tempObjectsCache['general_'.$accountId.'_'.$marketplaceId])) {
            return $this->tempObjectsCache['general_'.$accountId.'_'.$marketplaceId];
        }

        $tempModel = Mage::helper('M2ePro/Component_Amazon')->getModel('Template_General');

        $dataForAdd = array(
            'title' => 'Default ('.$this->getAccount()->getTitle().' - '.$this->getMarketplace()->getTitle().')',
            'marketplace_id' => $marketplaceId,
            'account_id' => $accountId,
            'sku_mode' => Ess_M2ePro_Model_Amazon_Template_General::SKU_MODE_NONE,
            'sku_custom_attribute' => '',
            'general_id_mode' => Ess_M2ePro_Model_Amazon_Template_General::GENERAL_ID_MODE_NONE,
            'general_id_custom_attribute' => ''
        );

        $tempModel->addData($dataForAdd)->save();
        $this->tempObjectsCache['general_'.$accountId.'_'.$marketplaceId] = $tempModel;

        $attributesSets = Mage::helper('M2ePro/Magento')->getAttributeSets();
        foreach ($attributesSets as $attributeSet) {
            $dataForAdd = array(
                'object_type' => Ess_M2ePro_Model_AttributeSet::OBJECT_TYPE_TEMPLATE_GENERAL,
                'object_id' => (int)$tempModel->getId(),
                'attribute_set_id' => (int)$attributeSet['attribute_set_id']
            );
            Mage::getModel('M2ePro/AttributeSet')->setData($dataForAdd)->save();
        }

        return $tempModel;
    }

    /**
     * @param Ess_M2ePro_Model_Listing_Other $otherListing
     * @return Ess_M2ePro_Model_Template_Synchronization
     */
    protected function getDefaultSynchronizationTemplate(Ess_M2ePro_Model_Listing_Other $otherListing)
    {
        $marketplaceId = $this->getMarketplace()->getId();

        if (isset($this->tempObjectsCache['synchronization_'.$marketplaceId])) {
            return $this->tempObjectsCache['synchronization_'.$marketplaceId];
        }

        $tempCollection = Mage::helper('M2ePro/Component_Amazon')->getCollection('Template_Synchronization');
        $tempCollection->addFieldToFilter('main_table.title','Default ('.$this->getMarketplace()->getTitle().')');
        $tempItem = $tempCollection->getFirstItem();

        if (!is_null($tempItem->getId())) {
            $this->tempObjectsCache['synchronization_'.$marketplaceId] = $tempItem;
            return $tempItem;
        }

        $tempModel = Mage::helper('M2ePro/Component_Amazon')->getModel('Template_Synchronization');

        $dataForAdd = array(
            'title' => 'Default ('.$this->getMarketplace()->getTitle().')',
            'start_auto_list' => Ess_M2ePro_Model_Amazon_Template_Synchronization::START_AUTO_LIST_NONE,
            'end_auto_stop' => Ess_M2ePro_Model_Amazon_Template_Synchronization::END_AUTO_STOP_NONE,
            'relist_mode' => Ess_M2ePro_Model_Amazon_Template_Synchronization::RELIST_MODE_NONE,
            'relist_list_mode' => Ess_M2ePro_Model_Amazon_Template_Synchronization::RELIST_LIST_MODE_NONE,
            'relist_filter_user_lock' => Ess_M2ePro_Model_Amazon_Template_Synchronization::RELIST_FILTER_USER_LOCK_YES,
            'relist_status_enabled' => Ess_M2ePro_Model_Amazon_Template_Synchronization::RELIST_STATUS_ENABLED_YES,
            'relist_is_in_stock' => Ess_M2ePro_Model_Amazon_Template_Synchronization::RELIST_IS_IN_STOCK_YES,
            'relist_qty' => Ess_M2ePro_Model_Amazon_Template_Synchronization::RELIST_QTY_NONE,
            'relist_qty_value' => '1',
            'relist_qty_value_max' => '10',
            'relist_schedule_type' => Ess_M2ePro_Model_Amazon_Template_Synchronization::RELIST_SCHEDULE_TYPE_IMMEDIATELY,
            'relist_schedule_through_value' => 0,
            'relist_schedule_through_metric' => Ess_M2ePro_Model_Amazon_Template_Synchronization::RELIST_SCHEDULE_THROUGH_METRIC_DAYS,
            'relist_schedule_week_mo' => 0,
            'relist_schedule_week_tu' => 0,
            'relist_schedule_week_we' => 0,
            'relist_schedule_week_th' => 0,
            'relist_schedule_week_fr' => 0,
            'relist_schedule_week_sa' => 0,
            'relist_schedule_week_su' => 0,
            'relist_schedule_week_start_time' => NULL,
            'relist_schedule_week_end_time' => NULL,
            'revise_update_qty' => Ess_M2ePro_Model_Amazon_Template_Synchronization::REVISE_UPDATE_QTY_NONE,
            'revise_update_price' => Ess_M2ePro_Model_Amazon_Template_Synchronization::REVISE_UPDATE_PRICE_NONE,
            'revise_change_selling_format_template' => Ess_M2ePro_Model_Template_Synchronization::REVISE_CHANGE_SELLING_FORMAT_TEMPLATE_NONE,
            'revise_change_description_template' => Ess_M2ePro_Model_Template_Synchronization::REVISE_CHANGE_DESCRIPTION_TEMPLATE_NONE,
            'revise_change_general_template' => Ess_M2ePro_Model_Template_Synchronization::REVISE_CHANGE_GENERAL_TEMPLATE_NONE,
            'stop_status_disabled' => Ess_M2ePro_Model_Amazon_Template_Synchronization::STOP_STATUS_DISABLED_NONE,
            'stop_out_off_stock' => Ess_M2ePro_Model_Amazon_Template_Synchronization::STOP_OUT_OFF_STOCK_NONE,
            'stop_qty' => Ess_M2ePro_Model_Amazon_Template_Synchronization::STOP_QTY_NONE,
            'stop_qty_value' => '0',
            'stop_qty_value_max' => '10'
        );

        $tempModel->addData($dataForAdd)->save();
        $this->tempObjectsCache['synchronization_'.$marketplaceId] = $tempModel;

        return $tempModel;
    }

    /**
     * @param Ess_M2ePro_Model_Listing_Other $otherListing
     * @return Ess_M2ePro_Model_Template_Description
     */
    protected function getDefaultDescriptionTemplate(Ess_M2ePro_Model_Listing_Other $otherListing)
    {
        $marketplaceId = $this->getMarketplace()->getId();

        if (isset($this->tempObjectsCache['description_'.$marketplaceId])) {
            return $this->tempObjectsCache['description_'.$marketplaceId];
        }

        $tempCollection = Mage::helper('M2ePro/Component_Amazon')->getCollection('Template_Description');
        $tempCollection->addFieldToFilter('main_table.title','Default ('.$this->getMarketplace()->getTitle().')');
        $tempItem = $tempCollection->getFirstItem();

        if (!is_null($tempItem->getId())) {
            $this->tempObjectsCache['description_'.$marketplaceId] = $tempItem;
            return $tempItem;
        }

        $tempModel = Mage::helper('M2ePro/Component_Amazon')->getModel('Template_Description');

        $dataForAdd = array(
            'title' => 'Default ('.$this->getMarketplace()->getTitle().')'
        );

        $tempModel->addData($dataForAdd)->save();
        $this->tempObjectsCache['description_'.$marketplaceId] = $tempModel;

        $attributesSets = Mage::helper('M2ePro/Magento')->getAttributeSets();
        foreach ($attributesSets as $attributeSet) {
            $dataForAdd = array(
                'object_type' => Ess_M2ePro_Model_AttributeSet::OBJECT_TYPE_TEMPLATE_DESCRIPTION,
                'object_id' => (int)$tempModel->getId(),
                'attribute_set_id' => (int)$attributeSet['attribute_set_id']
            );
            Mage::getModel('M2ePro/AttributeSet')->setData($dataForAdd)->save();
        }

        return $tempModel;
    }

    /**
     * @param Ess_M2ePro_Model_Listing_Other $otherListing
     * @return Ess_M2ePro_Model_Template_SellingFormat
     */
    protected function getDefaultSellingFormatTemplate(Ess_M2ePro_Model_Listing_Other $otherListing)
    {
        $marketplaceId = $this->getMarketplace()->getId();

        if (isset($this->tempObjectsCache['selling_format_'.$marketplaceId])) {
            return $this->tempObjectsCache['selling_format_'.$marketplaceId];
        }

        $tempCollection = Mage::helper('M2ePro/Component_Amazon')->getCollection('Template_SellingFormat');
        $tempCollection->addFieldToFilter('main_table.title','Default ('.$this->getMarketplace()->getTitle().')');
        $tempItem = $tempCollection->getFirstItem();

        if (!is_null($tempItem->getId())) {
            $this->tempObjectsCache['selling_format_'.$marketplaceId] = $tempItem;
            return $tempItem;
        }

        $tempModel = Mage::helper('M2ePro/Component_Amazon')->getModel('Template_SellingFormat');

        $dataForAdd = array(
            'title' => 'Default ('.$this->getMarketplace()->getTitle().')',

            'qty_mode' => Ess_M2ePro_Model_Amazon_Template_SellingFormat::QTY_MODE_PRODUCT,
            'qty_custom_value' => 1,
            'qty_custom_attribute' => '',
            'qty_coefficient' => '',

            'currency' => $this->getMarketplace()->getChildObject()->getDefaultCurrency(),

            'price_mode' => Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_PRODUCT,
            'price_coefficient' => '',
            'price_custom_attribute' => '',

            'sale_price_mode' => Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_NONE,
            'sale_price_coefficient' => '',
            'sale_price_custom_attribute' => '',

            'sale_price_start_date' => Mage::helper('M2ePro')->getCurrentGmtDate(),
            'sale_price_end_date' => Mage::helper('M2ePro')->getCurrentGmtDate()
        );

        $tempModel->addData($dataForAdd)->save();
        $this->tempObjectsCache['selling_format_'.$marketplaceId] = $tempModel;

        $attributesSets = Mage::helper('M2ePro/Magento')->getAttributeSets();
        foreach ($attributesSets as $attributeSet) {
            $dataForAdd = array(
                'object_type' => Ess_M2ePro_Model_AttributeSet::OBJECT_TYPE_TEMPLATE_SELLING_FORMAT,
                'object_id' => (int)$tempModel->getId(),
                'attribute_set_id' => (int)$attributeSet['attribute_set_id']
            );
            Mage::getModel('M2ePro/AttributeSet')->setData($dataForAdd)->save();
        }

        return $tempModel;
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
        $logs->setSynchronizationTask(Ess_M2ePro_Model_Synchronization_Log::SYNCH_TASK_OTHER_LISTINGS);

        $this->synchronizationLog = $logs;

        return $this->synchronizationLog;
    }

    // ########################################
}