<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Amazon_Synchronization_Tasks_Templates_Stop extends Ess_M2ePro_Model_Amazon_Synchronization_Tasks
{
    const PERCENTS_START = 55;
    const PERCENTS_END = 70;
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
        $this->_profiler->addTitle($componentName.'Stop Actions');
        $this->_profiler->addTitle('--------------------------');
        $this->_profiler->addTimePoint(__CLASS__,'Total time');
        $this->_profiler->increaseLeftPadding(5);

        $this->_lockItem->setPercents(self::PERCENTS_START);
        $this->_lockItem->setStatus(Mage::helper('M2ePro')->__('The "Stop" action is started. Please wait...'));
    }

    private function cancelSynch()
    {
        $this->_lockItem->setPercents(self::PERCENTS_END);
        $this->_lockItem->setStatus(Mage::helper('M2ePro')->__('The "Stop" action is finished. Please wait...'));

        $this->_profiler->decreaseLeftPadding(5);
        $this->_profiler->addTitle('--------------------------');
        $this->_profiler->saveTimePoint(__CLASS__);

        $this->_lockItem->activate();
    }

    //####################################

    private function execute()
    {
        $this->_profiler->addTimePoint(__METHOD__,'Immediately when product was changed');

        // Get attributes for products changes
        //------------------------------------
        $attributesForProductsChanges = array();
        $attributesForProductsChanges[] = 'product_instance';
        //------------------------------------

        // Get changed listings products
        //------------------------------------
        $changedListingsProducts = Mage::getModel('M2ePro/Listing_Product')->getChangedItemsByAttributes($attributesForProductsChanges);
        //------------------------------------

        // Filter only needed listings products
        //------------------------------------
        foreach ($changedListingsProducts as $changedListingProduct) {

            /** @var $listingProduct Ess_M2ePro_Model_Listing_Product */
            $listingProduct = Mage::helper('M2ePro/Component_Amazon')->getObject('Listing_Product',$changedListingProduct['id']);

            $tempParams = array('runner_actions'=>$this->_runnerActions);
            /** @var $inspector Ess_M2ePro_Model_Amazon_Template_Synchronization_ProductInspector */
            $inspector = Mage::getModel('M2ePro/Amazon_Template_Synchronization_ProductInspector',$tempParams);

            if (!$inspector->isMeetStopRequirements($listingProduct)) {
                continue;
            }

            $this->_runnerActions->setProduct($listingProduct,Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_STOP,array());
        }
        //------------------------------------

        // Get changed listings products variations options
        //------------------------------------
        $changedListingsProductsVariationsOptions = Mage::getModel('M2ePro/Listing_Product_Variation_Option')->getChangedItemsByAttributes($attributesForProductsChanges);
        //------------------------------------

        // Filter only needed listings products variations options
        //------------------------------------
        foreach ($changedListingsProductsVariationsOptions as $changedListingProductVariationOption) {

            /** @var $listingProductVariationOption Ess_M2ePro_Model_Listing_Product_Variation_Option */
            $listingProductVariationOption = Mage::helper('M2ePro/Component_Amazon')->getObject('Listing_Product_Variation_Option',$changedListingProductVariationOption['id']);

            $tempParams = array('runner_actions'=>$this->_runnerActions);
            /** @var $inspector Ess_M2ePro_Model_Amazon_Template_Synchronization_ProductInspector */
            $inspector = Mage::getModel('M2ePro/Amazon_Template_Synchronization_ProductInspector',$tempParams);

            if (!$inspector->isMeetStopRequirements($listingProductVariationOption->getListingProduct())) {
                continue;
            }

            $this->_runnerActions->setProduct($listingProductVariationOption->getListingProduct(),Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_STOP,array());
        }
        //------------------------------------

        $this->_profiler->saveTimePoint(__METHOD__);
    }

    //####################################
}