<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Amazon_Synchronization_Tasks_Templates_Revise extends Ess_M2ePro_Model_Amazon_Synchronization_Tasks
{
    const PERCENTS_START = 20;
    const PERCENTS_END = 35;
    const PERCENTS_INTERVAL = 15;

    private $_synchronizations = array();

    //####################################

    public function __construct()
    {
        parent::__construct();
        $this->_synchronizations = Mage::helper('M2ePro')->getGlobalValue('synchTemplatesArray');
    }

    //####################################

    public function process()
    {
        // PREPARE SYNCH
        //---------------------------
        $this->prepareSynch();
        //---------------------------

        // RUN SYNCH
        //---------------------------
        $this->execute();
        //---------------------------

        // CANCEL SYNCH
        //---------------------------
        $this->cancelSynch();
        //---------------------------
    }

    //####################################

    private function prepareSynch()
    {
        $this->_lockItem->activate();

        if (count(Mage::helper('M2ePro/Component')->getEnabledComponents()) > 1) {
            $componentName = Ess_M2ePro_Helper_Component_Amazon::TITLE.' ';
        } else {
            $componentName = '';
        }

        $this->_profiler->addEol();
        $this->_profiler->addTitle($componentName.'Revise Actions');
        $this->_profiler->addTitle('--------------------------');
        $this->_profiler->addTimePoint(__CLASS__,'Total time');
        $this->_profiler->increaseLeftPadding(5);

        $this->_lockItem->setPercents(self::PERCENTS_START);
        $this->_lockItem->setStatus(Mage::helper('M2ePro')->__('The "Revise" action is started. Please wait...'));
    }

    private function cancelSynch()
    {
        $this->_lockItem->setPercents(self::PERCENTS_END);
        $this->_lockItem->setStatus(Mage::helper('M2ePro')->__('The "Revise" action is finished. Please wait...'));

        $this->_profiler->decreaseLeftPadding(5);
        $this->_profiler->addTitle('--------------------------');
        $this->_profiler->saveTimePoint(__CLASS__);

        $this->_lockItem->activate();
    }

    //####################################

    private function execute()
    {
        $this->executeQtyChanged();

        $this->_lockItem->setPercents(self::PERCENTS_START + 1*self::PERCENTS_INTERVAL/8);
        $this->_lockItem->activate();

        $this->executePriceChanged();

        $this->_lockItem->setPercents(self::PERCENTS_START + 2*self::PERCENTS_INTERVAL/8);
        $this->_lockItem->activate();

        //-------------------------

        $this->executeVariationStatusChanged();

        $this->_lockItem->setPercents(self::PERCENTS_START + 3*self::PERCENTS_INTERVAL/8);
        $this->_lockItem->activate();

        $this->executeVariationQtyIsInStockChanged();

        $this->_lockItem->setPercents(self::PERCENTS_START + 4*self::PERCENTS_INTERVAL/8);
        $this->_lockItem->activate();

        $this->executeSpecialPriceIntervalChanged();

        $this->_lockItem->setPercents(self::PERCENTS_START + 5*self::PERCENTS_INTERVAL/8);
        $this->_lockItem->activate();

        //-------------------------

        $this->executeSellingFormatTemplatesChanged();

        $this->_lockItem->setPercents(self::PERCENTS_START + 6*self::PERCENTS_INTERVAL/8);
        $this->_lockItem->activate();

        $this->executeDescriptionsTemplatesChanged();

        $this->_lockItem->setPercents(self::PERCENTS_START + 7*self::PERCENTS_INTERVAL/8);
        $this->_lockItem->activate();

        $this->executeGeneralsTemplatesChanged();
    }

    //####################################

    private function executeQtyChanged()
    {
        $this->_profiler->addTimePoint(__METHOD__,'Update quantity');

        // Get attributes for products changes
        //------------------------------------
        $attributesForProductChange = array();

        foreach ($this->_synchronizations as &$synchronization) {

            if (!$synchronization['instance']->getChildObject()->isReviseWhenChangeQty()) {
                continue;
            }

            foreach ($synchronization['listings'] as &$listing) {

                /** @var $listing Ess_M2ePro_Model_Listing */
                
                if (!$listing->isSynchronizationNowRun()) {
                    continue;
                }

                $src = $listing->getSellingFormatTemplate()->getChildObject()->getQtySource();
                if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::QTY_MODE_PRODUCT) {
                    $attributesForProductChange[] = 'qty';
                }
                if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::QTY_MODE_ATTRIBUTE) {
                    $attributesForProductChange[] = $src['attribute'];
                }
            }
        }

        $attributesForProductChange = array_unique($attributesForProductChange);
        //------------------------------------

        // Get changed listings products
        //------------------------------------
        $changedListingsProducts = Mage::getModel('M2ePro/Listing_Product')->getChangedItemsByAttributes($attributesForProductChange);
        //------------------------------------

        // Filter only needed listings products
        //------------------------------------
        foreach ($changedListingsProducts as $changedListingProduct) {

            /** @var $listingProduct Ess_M2ePro_Model_Listing_Product */

            $listingProduct = Mage::helper('M2ePro/Component_Amazon')->getObject('Listing_Product',$changedListingProduct['id']);

            if (!$listingProduct->isListed()) {
                continue;
            }

            if ($this->_runnerActions->isExistProductAction($listingProduct,Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_REVISE,array())) {
                continue;
            }

            if (!$listingProduct->getListing()->isSynchronizationNowRun()) {
                continue;
            }

            $attributeNeeded = '';

            $src = $listingProduct->getSellingFormatTemplate()->getChildObject()->getQtySource();
            if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::QTY_MODE_PRODUCT) {
                $attributeNeeded = 'qty';
            }
            if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::QTY_MODE_ATTRIBUTE) {
                $attributeNeeded = $src['attribute'];
            }

            if ($attributeNeeded != $changedListingProduct['pc_attribute']) {
                continue;
            }

            if (!$listingProduct->getSynchronizationTemplate()->getChildObject()->isReviseWhenChangeQty()) {
                continue;
            }

            if ($listingProduct->getChildObject()->getQty() <= 0) {
                continue;
            }

            if (!$listingProduct->isRevisable()) {
                continue;
            }

            if ($listingProduct->isLockedObject(NULL) ||
                $listingProduct->isLockedObject('in_action')) {
                continue;
            }

            $this->_runnerActions->setProduct($listingProduct,Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_REVISE,array());
        }
        //------------------------------------

        // Get changed listings products variations options
        //------------------------------------
        $changedListingsProductsVariationsOptions = Mage::getModel('M2ePro/Listing_Product_Variation_Option')->getChangedItemsByAttributes($attributesForProductChange);
        //------------------------------------

        // Filter only needed listings products variations options
        //------------------------------------
        foreach ($changedListingsProductsVariationsOptions as $changedListingProductVariationOption) {

            /** @var $listingProductVariationOption Ess_M2ePro_Model_Listing_Product_Variation_Option */

            $listingProductVariationOption = Mage::helper('M2ePro/Component_Amazon')->getObject('Listing_Product_Variation_Option',$changedListingProductVariationOption['id']);

            if (!$listingProductVariationOption->getListingProduct()->isListed()) {
                continue;
            }

            if ($this->_runnerActions->isExistProductAction($listingProductVariationOption->getListingProduct(),Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_REVISE,array())) {
                continue;
            }

            if (!$listingProductVariationOption->getListing()->isSynchronizationNowRun()) {
                continue;
            }

            $attributeNeeded = '';

            $src = $listingProductVariationOption->getSellingFormatTemplate()->getChildObject()->getQtySource();
            if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::QTY_MODE_PRODUCT) {
                $attributeNeeded = 'qty';
            }
            if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::QTY_MODE_ATTRIBUTE) {
                $attributeNeeded = $src['attribute'];
            }

            if ($attributeNeeded != $changedListingProductVariationOption['pc_attribute']) {
                continue;
            }

            if (!$listingProductVariationOption->getSynchronizationTemplate()->getChildObject()->isReviseWhenChangeQty()) {
                continue;
            }

            if ($listingProductVariationOption->getListingProduct()->getChildObject()->getQty() <= 0) {
                continue;
            }

            if (!$listingProductVariationOption->getListingProduct()->isRevisable()) {
                continue;
            }

            if ($listingProductVariationOption->getListingProduct()->isLockedObject(NULL) ||
                $listingProductVariationOption->getListingProduct()->isLockedObject('in_action')) {
                continue;
            }

            $this->_runnerActions->setProduct($listingProductVariationOption->getListingProduct(),Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_REVISE,array());
        }
        //------------------------------------

        $this->_profiler->saveTimePoint(__METHOD__);
    }

    private function executePriceChanged()
    {
        $this->_profiler->addTimePoint(__METHOD__,'Update price');

        // Get attributes for products changes
        //------------------------------------
        $attributesForProductChange = array();

        foreach ($this->_synchronizations as &$synchronization) {

            if (!$synchronization['instance']->getChildObject()->isReviseWhenChangePrice()) {
                continue;
            }

            foreach ($synchronization['listings'] as &$listing) {

                /** @var $listing Ess_M2ePro_Model_Listing */
                
                if (!$listing->isSynchronizationNowRun()) {
                    continue;
                }

                $src = $listing->getSellingFormatTemplate()->getChildObject()->getPriceSource();
                if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_PRODUCT) {
                    $attributesForProductChange[] = 'price';
                }
                if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_SPECIAL) {
                    $attributesForProductChange[] = 'special_price';
                }
                if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_ATTRIBUTE) {
                    $attributesForProductChange[] = $src['attribute'];
                }

                $src = $listing->getSellingFormatTemplate()->getChildObject()->getSalePriceSource();
                if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_PRODUCT) {
                    $attributesForProductChange[] = 'price';
                }
                if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_SPECIAL) {
                    $attributesForProductChange[] = 'special_price';
                }
                if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_ATTRIBUTE) {
                    $attributesForProductChange[] = $src['attribute'];
                }
            }
        }

        $attributesForProductChange = array_unique($attributesForProductChange);
        //------------------------------------

        // Get changed listings products
        //------------------------------------
        $changedListingsProducts = Mage::getModel('M2ePro/Listing_Product')->getChangedItemsByAttributes($attributesForProductChange);
        //------------------------------------

        // Filter only needed listings products
        //------------------------------------
        foreach ($changedListingsProducts as $changedListingProduct) {

            /** @var $listingProduct Ess_M2ePro_Model_Listing_Product */

            $listingProduct = Mage::helper('M2ePro/Component_Amazon')->getObject('Listing_Product',$changedListingProduct['id']);

            if (!$listingProduct->isListed()) {
                continue;
            }

            if ($this->_runnerActions->isExistProductAction($listingProduct,Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_REVISE,array())) {
                continue;
            }

            if (!$listingProduct->getListing()->isSynchronizationNowRun()) {
                continue;
            }

            $attributeNeeded = '';

            $src = $listingProduct->getSellingFormatTemplate()->getChildObject()->getPriceSource();
            if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_PRODUCT) {
                $attributeNeeded = 'price';
            }
            if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_SPECIAL) {
                $attributeNeeded = 'special_price';
            }
            if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_ATTRIBUTE) {
                $attributeNeeded = $src['attribute'];
            }

            if ($attributeNeeded != $changedListingProduct['pc_attribute'] &&
                !($attributeNeeded == 'special_price' && $changedListingProduct['pc_attribute'] == 'price')) {

                $src = $listingProduct->getSellingFormatTemplate()->getChildObject()->getSalePriceSource();
                if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_PRODUCT) {
                    $attributeNeeded = 'price';
                }
                if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_SPECIAL) {
                    $attributeNeeded = 'special_price';
                }
                if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_ATTRIBUTE) {
                    $attributeNeeded = $src['attribute'];
                }
            }

            if (!$listingProduct->getSynchronizationTemplate()->getChildObject()->isReviseWhenChangePrice()) {
                continue;
            }

            if (!$listingProduct->isRevisable()) {
                continue;
            }

            if ($listingProduct->isLockedObject(NULL) ||
                $listingProduct->isLockedObject('in_action')) {
                continue;
            }

            $this->_runnerActions->setProduct($listingProduct,Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_REVISE,array());
        }
        //------------------------------------

        // Get changed listings products variations options
        //------------------------------------
        $changedListingsProductsVariationsOptions = Mage::getModel('M2ePro/Listing_Product_Variation_Option')->getChangedItemsByAttributes($attributesForProductChange);
        //------------------------------------

        // Filter only needed listings products variations options
        //------------------------------------
        foreach ($changedListingsProductsVariationsOptions as $changedListingProductVariationOption) {

            /** @var $listingProductVariationOption Ess_M2ePro_Model_Listing_Product_Variation_Option */

            $listingProductVariationOption = Mage::helper('M2ePro/Component_Amazon')->getObject('Listing_Product_Variation_Option',$changedListingProductVariationOption['id']);

            if (!$listingProductVariationOption->getListingProduct()->isListed()) {
                continue;
            }

            if ($this->_runnerActions->isExistProductAction($listingProductVariationOption->getListingProduct(),Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_REVISE,array())) {
                continue;
            }

            if (!$listingProductVariationOption->getListing()->isSynchronizationNowRun()) {
                continue;
            }

            $attributeNeeded = '';

            $src = $listingProductVariationOption->getSellingFormatTemplate()->getChildObject()->getPriceSource();
            if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_PRODUCT) {
                $attributeNeeded = 'price';
            }
            if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_SPECIAL) {
                $attributeNeeded = 'special_price';
            }
            if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_ATTRIBUTE) {
                $attributeNeeded = $src['attribute'];
            }

            if ($attributeNeeded != $changedListingProductVariationOption['pc_attribute'] &&
                !($attributeNeeded == 'special_price' && $changedListingProductVariationOption['pc_attribute'] == 'price')) {

                $src = $listingProductVariationOption->getSellingFormatTemplate()->getChildObject()->getSalePriceSource();
                if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_PRODUCT) {
                    $attributeNeeded = 'price';
                }
                if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_SPECIAL) {
                    $attributeNeeded = 'special_price';
                }
                if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_ATTRIBUTE) {
                    $attributeNeeded = $src['attribute'];
                }
            }

            if (!$listingProductVariationOption->getSynchronizationTemplate()->getChildObject()->isReviseWhenChangePrice()) {
                continue;
            }

            if (!$listingProductVariationOption->getListingProduct()->isRevisable()) {
                continue;
            }

            if ($listingProductVariationOption->getListingProduct()->isLockedObject(NULL) ||
                $listingProductVariationOption->getListingProduct()->isLockedObject('in_action')) {
                continue;
            }

            $this->_runnerActions->setProduct($listingProductVariationOption->getListingProduct(),Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_REVISE,array());
        }
        //------------------------------------

        $this->_profiler->saveTimePoint(__METHOD__);
    }

    //####################################
    
    private function executeVariationStatusChanged()
    {
        $this->_profiler->addTimePoint(__METHOD__,'Update variation status');

        // Get attributes for products changes
        //------------------------------------
        $attributesForProductChange = array();
        $attributesForProductChange[] = 'status';
        //------------------------------------

        // Get changed listings products variations options
        //------------------------------------
        $changedListingsProductsVariationsOptions = Mage::getModel('M2ePro/Listing_Product_Variation_Option')->getChangedItemsByAttributes($attributesForProductChange);
        //------------------------------------

        // Filter only needed listings products variations options
        //------------------------------------
        foreach ($changedListingsProductsVariationsOptions as $changedListingProductVariationOption) {

            /** @var $listingProductVariationOption Ess_M2ePro_Model_Listing_Product_Variation_Option */

            $listingProductVariationOption = Mage::helper('M2ePro/Component_Amazon')->getObject('Listing_Product_Variation_Option',$changedListingProductVariationOption['id']);

            if (!$listingProductVariationOption->getListingProduct()->isListed()) {
                continue;
            }

            if ($this->_runnerActions->isExistProductAction($listingProductVariationOption->getListingProduct(),Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_REVISE,array())) {
                continue;
            }

            if (!$listingProductVariationOption->getListing()->isSynchronizationNowRun()) {
                continue;
            }

            if (!$listingProductVariationOption->getSynchronizationTemplate()->getChildObject()->isReviseWhenChangeQty()) {
                continue;
            }

            if ($listingProductVariationOption->getListingProduct()->getChildObject()->getQty() <= 0) {
                continue;
            }

            if (!$listingProductVariationOption->getListingProduct()->isRevisable()) {
                continue;
            }

            if ($listingProductVariationOption->getListingProduct()->isLockedObject(NULL) ||
                $listingProductVariationOption->getListingProduct()->isLockedObject('in_action')) {
                continue;
            }

            $this->_runnerActions->setProduct($listingProductVariationOption->getListingProduct(),Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_REVISE,array());
        }
        //------------------------------------

        $this->_profiler->saveTimePoint(__METHOD__);
    }

    private function executeVariationQtyIsInStockChanged()
    {
        $this->_profiler->addTimePoint(__METHOD__,'Update variation stock availability');

        // Get attributes for products changes
        //------------------------------------
        $attributesForProductChange = array();
        $attributesForProductChange[] = 'stock_availability';
        //------------------------------------

        // Get changed listings products variations options
        //------------------------------------
        $changedListingsProductsVariationsOptions = Mage::getModel('M2ePro/Listing_Product_Variation_Option')->getChangedItemsByAttributes($attributesForProductChange);
        //------------------------------------

        // Filter only needed listings products variations options
        //------------------------------------
        foreach ($changedListingsProductsVariationsOptions as $changedListingProductVariationOption) {

            /** @var $listingProductVariationOption Ess_M2ePro_Model_Listing_Product_Variation_Option */

            $listingProductVariationOption = Mage::helper('M2ePro/Component_Amazon')->getObject('Listing_Product_Variation_Option',$changedListingProductVariationOption['id']);

            if (!$listingProductVariationOption->getListingProduct()->isListed()) {
                continue;
            }

            if ($this->_runnerActions->isExistProductAction($listingProductVariationOption->getListingProduct(),Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_REVISE,array())) {
                continue;
            }
            
            if (!$listingProductVariationOption->getListing()->isSynchronizationNowRun()) {
                continue;
            }

            if (!$listingProductVariationOption->getSynchronizationTemplate()->getChildObject()->isReviseWhenChangeQty()) {
                continue;
            }

            if ($listingProductVariationOption->getListingProduct()->getChildObject()->getQty() <= 0) {
                continue;
            }

            if (!$listingProductVariationOption->getListingProduct()->isRevisable()) {
                continue;
            }

            if ($listingProductVariationOption->getListingProduct()->isLockedObject(NULL) ||
                $listingProductVariationOption->getListingProduct()->isLockedObject('in_action')) {
                continue;
            }

            $this->_runnerActions->setProduct($listingProductVariationOption->getListingProduct(),Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_REVISE,array());
        }
        //------------------------------------

        $this->_profiler->saveTimePoint(__METHOD__);
    }

    private function executeSpecialPriceIntervalChanged()
    {
        $this->_profiler->addTimePoint(__METHOD__,'Update special price interval');

        // Get attributes for products changes
        //------------------------------------
        $attributesForProductChange = array();
        $attributesForProductChange[] = 'special_price_from_date';
        $attributesForProductChange[] = 'special_price_to_date';
        //------------------------------------

        // Get changed listings products
        //------------------------------------
        $changedListingsProducts = Mage::getModel('M2ePro/Listing_Product')->getChangedItemsByAttributes($attributesForProductChange);
        //------------------------------------

        // Filter only needed listings products
        //------------------------------------
        foreach ($changedListingsProducts as $changedListingProduct) {

            /** @var $listingProduct Ess_M2ePro_Model_Listing_Product */

            $listingProduct = Mage::helper('M2ePro/Component_Amazon')->getObject('Listing_Product',$changedListingProduct['id']);

            if (!$listingProduct->isListed()) {
                continue;
            }

            if ($this->_runnerActions->isExistProductAction($listingProduct,Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_REVISE,array())) {
                continue;
            }

            if (!$listingProduct->getListing()->isSynchronizationNowRun()) {
                continue;
            }

            $attributeNeeded = '';

            $src = $listingProduct->getSellingFormatTemplate()->getChildObject()->getPriceSource();
            if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_PRODUCT) {
                $attributeNeeded = 'price';
            }
            if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_SPECIAL) {
                $attributeNeeded = 'special_price';
            }
            if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_ATTRIBUTE) {
                $attributeNeeded = $src['attribute'];
            }

            if ($attributeNeeded != 'special_price') {

                $src = $listingProduct->getSellingFormatTemplate()->getChildObject()->getSalePriceSource();
                if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_PRODUCT) {
                    $attributeNeeded = 'price';
                }
                if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_SPECIAL) {
                    $attributeNeeded = 'special_price';
                }
                if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_ATTRIBUTE) {
                    $attributeNeeded = $src['attribute'];
                }

                if ($attributeNeeded != 'special_price') {
                    continue;
                }
            }

            if (!$listingProduct->getSynchronizationTemplate()->getChildObject()->isReviseWhenChangePrice()) {
                continue;
            }

            if (!$listingProduct->isRevisable()) {
                continue;
            }

            if ($listingProduct->isLockedObject(NULL) ||
                $listingProduct->isLockedObject('in_action')) {
                continue;
            }

            $this->_runnerActions->setProduct($listingProduct,Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_REVISE,array());
        }
        //------------------------------------

        // Get changed listings products variations options
        //------------------------------------
        $changedListingsProductsVariationsOptions = Mage::getModel('M2ePro/Listing_Product_Variation_Option')->getChangedItemsByAttributes($attributesForProductChange);
        //------------------------------------

        // Filter only needed listings products variations options
        //------------------------------------
        foreach ($changedListingsProductsVariationsOptions as $changedListingProductVariationOption) {

            /** @var $listingProductVariationOption Ess_M2ePro_Model_Listing_Product_Variation_Option */

            $listingProductVariationOption = Mage::helper('M2ePro/Component_Amazon')->getObject('Listing_Product_Variation_Option',$changedListingProductVariationOption['id']);

            if (!$listingProductVariationOption->getListingProduct()->isListed()) {
                continue;
            }

            if ($this->_runnerActions->isExistProductAction($listingProductVariationOption->getListingProduct(),Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_REVISE,array())) {
                continue;
            }

            if (!$listingProductVariationOption->getListingProduct()->getMagentoProduct()->isSimpleTypeWithCustomOptions() &&
                !$listingProductVariationOption->getListingProduct()->getMagentoProduct()->isGroupedType()) {
                continue;
            }

            if (!$listingProductVariationOption->getListing()->isSynchronizationNowRun()) {
                continue;
            }

            $attributeNeeded = '';

            $src = $listingProductVariationOption->getSellingFormatTemplate()->getChildObject()->getSalePriceSource();
            if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_PRODUCT) {
                $attributeNeeded = 'price';
            }
            if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_SPECIAL) {
                $attributeNeeded = 'special_price';
            }
            if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_ATTRIBUTE) {
                $attributeNeeded = $src['attribute'];
            }

            if ($attributeNeeded != 'special_price') {

                $src = $listingProductVariationOption->getSellingFormatTemplate()->getChildObject()->getSalePriceSource();
                if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_PRODUCT) {
                    $attributeNeeded = 'price';
                }
                if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_SPECIAL) {
                    $attributeNeeded = 'special_price';
                }
                if ($src['mode'] == Ess_M2ePro_Model_Amazon_Template_SellingFormat::PRICE_ATTRIBUTE) {
                    $attributeNeeded = $src['attribute'];
                }

                if ($attributeNeeded != 'special_price') {
                    continue;
                }
            }

            if (!$listingProductVariationOption->getSynchronizationTemplate()->getChildObject()->isReviseWhenChangePrice()) {
                continue;
            }

            if (!$listingProductVariationOption->getListingProduct()->isRevisable()) {
                continue;
            }

            if ($listingProductVariationOption->getListingProduct()->isLockedObject(NULL) ||
                $listingProductVariationOption->getListingProduct()->isLockedObject('in_action')) {
                continue;
            }

            $this->_runnerActions->setProduct($listingProductVariationOption->getListingProduct(),Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_REVISE,array());
        }
        //------------------------------------

        $this->_profiler->saveTimePoint(__METHOD__);
    }

    //####################################

    private function executeSellingFormatTemplatesChanged()
    {
        $this->_profiler->addTimePoint(__METHOD__,'Update Selling Format Template');

        // Get changed templates
        //------------------------------------
        $templatesCollection = Mage::helper('M2ePro/Component_Amazon')->getModel('Template_SellingFormat')->getCollection();
        $templatesCollection->getSelect()->where('`main_table`.`update_date` != `main_table`.`synch_date`');
        $templatesCollection->getSelect()->orWhere('`main_table`.`synch_date` IS NULL');
        $templatesArray = $templatesCollection->toArray();
        //------------------------------------
        
        // Set Amazon actions for listed products
        //------------------------------------
        foreach ($templatesArray['items'] as $templateArray) {

            /** @var $template Ess_M2ePro_Model_Template_SellingFormat */
            $template = Mage::helper('M2ePro/Component_Amazon')->getObject('Template_SellingFormat',$templateArray['id']);

            $listings = $template->getListings(true);

            foreach ($listings as $listing) {

                /** @var $listing Ess_M2ePro_Model_Listing */
                
                if (!$listing->isSynchronizationNowRun()) {
                    continue;
                }

                $listing->setSellingFormatTemplate($template);

                if (!$listing->getSynchronizationTemplate()->isReviseSellingFormatTemplate()) {
                    continue;
                }

                $listingsProducts = $listing->getProducts(true,array('status'=>array('in'=>array(Ess_M2ePro_Model_Listing_Product::STATUS_LISTED))));

                foreach ($listingsProducts as $listingProduct) {

                    /** @var $listingProduct Ess_M2ePro_Model_Listing_Product */

                    if (!$listingProduct->isListed()) {
                        continue;
                    }

                    if ($this->_runnerActions->isExistProductAction($listingProduct,Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_REVISE,array())) {
                        continue;
                    }

                    $listingProduct->setListing($listing);

                    if (!$listingProduct->isRevisable()) {
                        continue;
                    }

                    if ($listingProduct->isLockedObject(NULL) ||
                        $listingProduct->isLockedObject('in_action')) {
                        continue;
                    }

                    $this->_runnerActions->setProduct($listingProduct,Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_REVISE,array());
                }
            }

            $template->addData(array('synch_date'=>$template->getData('update_date')))->save();
        }
        //------------------------------------

        $this->_profiler->saveTimePoint(__METHOD__);
    }

    private function executeDescriptionsTemplatesChanged()
    {
        $this->_profiler->addTimePoint(__METHOD__,'Update description template');

        // Get changed templates
        //------------------------------------
        $templatesCollection = Mage::helper('M2ePro/Component_Amazon')->getModel('Template_Description')->getCollection();
        $templatesCollection->getSelect()->where('`main_table`.`update_date` != `main_table`.`synch_date`');
        $templatesCollection->getSelect()->orWhere('`main_table`.`synch_date` IS NULL');
        $templatesArray = $templatesCollection->toArray();
        //------------------------------------

        // Set Amazon actions for listed products
        //------------------------------------
        foreach ($templatesArray['items'] as $templateArray) {

            /** @var $template Ess_M2ePro_Model_Template_Description */
            $template = Mage::helper('M2ePro/Component_Amazon')->getObject('Template_Description',$templateArray['id']);

            $listings = $template->getListings(true);

            foreach ($listings as $listing) {

                /** @var $listing Ess_M2ePro_Model_Listing */

                if (!$listing->isSynchronizationNowRun()) {
                    continue;
                }

                $listing->setDescriptionTemplate($template);

                if (!$listing->getSynchronizationTemplate()->isReviseDescriptionTemplate()) {
                    continue;
                }

                $listingsProducts = $listing->getProducts(true,array('status'=>array('in'=>array(Ess_M2ePro_Model_Listing_Product::STATUS_LISTED))));

                foreach ($listingsProducts as $listingProduct) {

                    /** @var $listingProduct Ess_M2ePro_Model_Listing_Product */

                    if (!$listingProduct->isListed()) {
                        continue;
                    }

                    if ($this->_runnerActions->isExistProductAction($listingProduct,Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_REVISE,array())) {
                        continue;
                    }

                    $listingProduct->setListing($listing);

                    if (!$listingProduct->isRevisable()) {
                        continue;
                    }

                    if ($listingProduct->isLockedObject(NULL) ||
                        $listingProduct->isLockedObject('in_action')) {
                        continue;
                    }

                    $this->_runnerActions->setProduct($listingProduct,Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_REVISE,array());
                }
            }

            $template->addData(array('synch_date'=>$template->getData('update_date')))->save();
        }
        //------------------------------------

        $this->_profiler->saveTimePoint(__METHOD__);
    }

    private function executeGeneralsTemplatesChanged()
    {
        $this->_profiler->addTimePoint(__METHOD__,'Update general template');

        // Get changed templates
        //------------------------------------
        $templatesCollection = Mage::helper('M2ePro/Component_Amazon')->getModel('Template_General')->getCollection();
        $templatesCollection->getSelect()->where('`main_table`.`update_date` != `main_table`.`synch_date`');
        $templatesCollection->getSelect()->orWhere('`main_table`.`synch_date` IS NULL');
        $templatesArray = $templatesCollection->toArray();
        //------------------------------------

        // Set Amazon actions for listed products
        //------------------------------------
        foreach ($templatesArray['items'] as $templateArray) {

            /** @var $template Ess_M2ePro_Model_Template_General */
            $template = Mage::helper('M2ePro/Component_Amazon')->getObject('Template_General',$templateArray['id']);

            $listings = $template->getListings(true);

            foreach ($listings as $listing) {

                /** @var $listing Ess_M2ePro_Model_Listing */

                if (!$listing->isSynchronizationNowRun()) {
                    continue;
                }

                $listing->setGeneralTemplate($template);

                if (!$listing->getSynchronizationTemplate()->isReviseGeneralTemplate()) {
                    continue;
                }

                $listingsProducts = $listing->getProducts(true,array('status'=>array('in'=>array(Ess_M2ePro_Model_Listing_Product::STATUS_LISTED))));

                foreach ($listingsProducts as $listingProduct) {

                    /** @var $listingProduct Ess_M2ePro_Model_Listing_Product */
                    
                    if (!$listingProduct->isListed()) {
                        continue;
                    }

                    if ($this->_runnerActions->isExistProductAction($listingProduct,Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_REVISE,array())) {
                        continue;
                    }

                    $listingProduct->setListing($listing);

                    if (!$listingProduct->isRevisable()) {
                        continue;
                    }

                    if ($listingProduct->isLockedObject(NULL) ||
                        $listingProduct->isLockedObject('in_action')) {
                        continue;
                    }

                    $this->_runnerActions->setProduct($listingProduct,Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_REVISE,array());
                }
            }

            $template->addData(array('synch_date'=>$template->getData('update_date')))->save();
        }
        //------------------------------------

        $this->_profiler->saveTimePoint(__METHOD__);
    }

    //####################################   
}