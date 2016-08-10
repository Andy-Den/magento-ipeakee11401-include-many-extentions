<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Amazon_Template_Synchronization_ProductInspector
{
    /**
     * @var Ess_M2ePro_Model_Amazon_Synchronization_RunnerActions
     */
    protected $_runnerActions = NULL;

    //####################################

    public function __construct()
    {
        $args = func_get_args();
        empty($args[0]) && $args[0] = array();
        $params = $args[0];

        if (isset($params['runner_actions'])) {
            $this->_runnerActions = $params['runner_actions'];
        } else {
            $runnerActionsModel = Mage::getModel('M2ePro/Amazon_Synchronization_RunnerActions');
            $runnerActionsModel->removeAllProducts();
            $this->_runnerActions = $runnerActionsModel;
        }
    }

    //####################################

    public function processProduct(Ess_M2ePro_Model_Listing_Product $listingProduct)
    {
        $this->processProducts(array($listingProduct));
    }

    public function processProducts(array $listingsProducts = array())
    {
        $this->_runnerActions->removeAllProducts();

        foreach ($listingsProducts as $listingProduct) {

            if (!($listingProduct instanceof Ess_M2ePro_Model_Listing_Product)) {
                continue;
            }

            /** @var $listingProduct Ess_M2ePro_Model_Listing_Product */
            $this->processItem($listingProduct);
        }

        $this->_runnerActions->execute();
        $this->_runnerActions->removeAllProducts();
    }

    //-------------------------------------

    private function processItem(Ess_M2ePro_Model_Listing_Product $listingProduct)
    {
        if ($listingProduct->isListed()) {

            // Check Stop Requirements
            //-------------------------------
            $tempResult = $this->isMeetStopRequirements($listingProduct);
            $tempResult && $this->_runnerActions->setProduct($listingProduct,
                                                             Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_STOP,
                                                             array());
            //-------------------------------

            if (!$tempResult) {

                // Check Revise Requirements
                //-------------------------------
                $tempResult = $this->isMeetReviseRequirements($listingProduct);
                $tempResult && $this->_runnerActions->setProduct($listingProduct,
                                                                 Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_REVISE,
                                                                 array());
                //-------------------------------
            }

        } else {

            // Check Relist Requirements
            //-------------------------------
            $tempResult = $this->isMeetRelistRequirements($listingProduct);
            if ($tempResult) {
                if ($listingProduct->isStopped()) {

                    $this->_runnerActions->setProduct($listingProduct,
                                                      Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_RELIST,
                                                      array());

                } else if ($listingProduct->isNotListed()) {

                    $this->_runnerActions->setProduct($listingProduct,
                                                      Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_LIST,
                                                      array());
                }
            }
            //-------------------------------

        }
    }

    //####################################

    public function isMeetStopRequirements(Ess_M2ePro_Model_Listing_Product $listingProduct)
    {
        // Amazon available status
        //--------------------
        if (!$listingProduct->isListed()) {
            return false;
        }

        if (!$listingProduct->isStoppable()) {
            return false;
        }

        if ($this->_runnerActions->isExistProductAction($listingProduct,Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_STOP,array())) {
            return false;
        }

        if ($listingProduct->isLockedObject(NULL) ||
            $listingProduct->isLockedObject('in_action')) {
           return false;
        }
        //--------------------

        // Correct synchronization
        //--------------------
        if (!$listingProduct->getListing()->isSynchronizationNowRun()) {
            return false;
        }
        //--------------------

        // Check filters
        //--------------------
        if ($listingProduct->getSynchronizationTemplate()->getChildObject()->isStopStatusDisabled()) {
            if ($listingProduct->getMagentoProduct()->getStatus() == Mage_Catalog_Model_Product_Status::STATUS_DISABLED) {
                return true;
            }
        }

        if ($listingProduct->getSynchronizationTemplate()->getChildObject()->isStopOutOfStock()) {
            if (!$listingProduct->getMagentoProduct()->getStockAvailability()) {
                return true;
            }
        }

        if ($listingProduct->getSynchronizationTemplate()->getChildObject()->isStopWhenQtyHasValue()) {

            $productQty = (int)$listingProduct->getChildObject()->getQty(true);

            $typeQty = (int)$listingProduct->getSynchronizationTemplate()->getChildObject()->getStopWhenQtyHasValueType();
            $minQty = (int)$listingProduct->getSynchronizationTemplate()->getChildObject()->getStopWhenQtyHasValueMin();
            $maxQty = (int)$listingProduct->getSynchronizationTemplate()->getChildObject()->getStopWhenQtyHasValueMax();

            if ($typeQty == Ess_M2ePro_Model_Amazon_Template_Synchronization::STOP_QTY_LESS &&
                $productQty <= $minQty) {
                return true;
            }

            if ($typeQty == Ess_M2ePro_Model_Amazon_Template_Synchronization::STOP_QTY_MORE &&
                $productQty >= $minQty) {
                return true;
            }

            if ($typeQty == Ess_M2ePro_Model_Amazon_Template_Synchronization::STOP_QTY_BETWEEN &&
                $productQty >= $minQty && $productQty <= $maxQty) {
                return true;
            }
        }
        //--------------------

        return false;
    }

    public function isMeetRelistRequirements(Ess_M2ePro_Model_Listing_Product $listingProduct)
    {
        // Amazon available status
        //--------------------
        if (!$listingProduct->isNotListed() && !$listingProduct->isStopped()) {
            return false;
        }

        if (!$listingProduct->isListable() && !$listingProduct->isRelistable()) {
            return false;
        }

        if ($listingProduct->isNotListed() && !$listingProduct->getSynchronizationTemplate()->getChildObject()->isRelistListMode()) {
            return false;
        }

        if ($listingProduct->isStopped() && $this->_runnerActions->isExistProductAction($listingProduct,Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_RELIST,array())) {
            return false;
        } else if ($listingProduct->isNotListed() && $this->_runnerActions->isExistProductAction($listingProduct,Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_LIST,array())) {
            return false;
        }

        if ($listingProduct->isLockedObject(NULL) ||
            $listingProduct->isLockedObject('in_action')) {
           return false;
        }
        //--------------------

        // Correct synchronization
        //--------------------
        if (!$listingProduct->getListing()->isSynchronizationNowRun()) {
            return false;
        }

        if(!$listingProduct->getSynchronizationTemplate()->getChildObject()->isRelistMode()) {
            return false;
        }

        if ($listingProduct->getSynchronizationTemplate()->getChildObject()->isRelistFilterUserLock() &&
            $listingProduct->getStatusChanger() == Ess_M2ePro_Model_Listing_Product::STATUS_CHANGER_USER) {
            return false;
        }
        //--------------------

        // Check filters
        //--------------------
        if($listingProduct->getSynchronizationTemplate()->getChildObject()->isRelistStatusEnabled()) {
            if ($listingProduct->getMagentoProduct()->getStatus() != Mage_Catalog_Model_Product_Status::STATUS_ENABLED) {
                return false;
            }
        }

        if($listingProduct->getSynchronizationTemplate()->getChildObject()->isRelistIsInStock()) {
            if (!$listingProduct->getMagentoProduct()->getStockAvailability()) {
                return false;
            }
        }

        if($listingProduct->getSynchronizationTemplate()->getChildObject()->isRelistWhenQtyHasValue()) {

            $result = false;
            $productQty = (int)$listingProduct->getChildObject()->getQty(true);

            $typeQty = (int)$listingProduct->getSynchronizationTemplate()->getChildObject()->getRelistWhenQtyHasValueType();
            $minQty = (int)$listingProduct->getSynchronizationTemplate()->getChildObject()->getRelistWhenQtyHasValueMin();
            $maxQty = (int)$listingProduct->getSynchronizationTemplate()->getChildObject()->getRelistWhenQtyHasValueMax();

            if ($typeQty == Ess_M2ePro_Model_Amazon_Template_Synchronization::RELIST_QTY_LESS &&
                $productQty <= $minQty) {
                $result = true;
            }

            if ($typeQty == Ess_M2ePro_Model_Amazon_Template_Synchronization::RELIST_QTY_MORE &&
                $productQty >= $minQty) {
                $result = true;
            }

            if ($typeQty == Ess_M2ePro_Model_Amazon_Template_Synchronization::RELIST_QTY_BETWEEN &&
                $productQty >= $minQty && $productQty <= $maxQty) {
                $result = true;
            }

            if (!$result) {
                return false;
            }
        }
        //--------------------

        return true;
    }

    public function isMeetReviseRequirements(Ess_M2ePro_Model_Listing_Product $listingProduct)
    {
        // Amazon available status
        //--------------------
        if (!$listingProduct->isListed()) {
            return false;
        }

        if (!$listingProduct->isRevisable()) {
            return false;
        }

        if ($this->_runnerActions->isExistProductAction($listingProduct,Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Dispatcher::ACTION_REVISE,array())) {
            return false;
        }

        if ($listingProduct->isLockedObject(NULL) ||
            $listingProduct->isLockedObject('in_action')) {
           return false;
        }
        //--------------------

        // Correct synchronization
        //--------------------
        if (!$listingProduct->getListing()->isSynchronizationNowRun()) {
            return false;
        }
        //--------------------

        // Check filters
        //--------------------
        $tempQty = $listingProduct->getChildObject()->getQty();
        if ($tempQty > 0 && $tempQty != $listingProduct->getChildObject()->getOnlineQty()) {
            return true;
        }

        $tempPrice = $listingProduct->getChildObject()->getPrice();
        if ($tempPrice != $listingProduct->getChildObject()->getOnlinePrice()) {
            return true;
        }
        //--------------------

        return false;
    }

    //####################################
}