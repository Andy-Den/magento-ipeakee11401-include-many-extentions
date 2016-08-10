<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

abstract class Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Responser extends Ess_M2ePro_Model_Connector_Xfabric_Amazon_Responser
{
    protected $listingsProducts = array();

    protected $failedListingsProducts = array();
    protected $succeededListingsProducts = array();

    // ########################################

    public function __construct(Ess_M2ePro_Model_Xfabric_Request $xfabricRequest)
    {
        parent::__construct($xfabricRequest);

        foreach ($this->params['products'] as $listingProductData) {
            if (!isset($listingProductData['id'])) {
                continue;
            }
            try {
                $this->listingsProducts[] = Mage::helper('M2ePro/Component_Amazon')
                                                    ->getObject('Listing_Product',
                                                                (int)$listingProductData['id']);
            } catch (Exception $exception) {}
        }
    }

    protected function unsetLocks($fail = false, $message = NULL)
    {
        $actionIdentifier = $this->getActionIdentifier();

        $tempListings = array();
        foreach ($this->listingsProducts as $listingProduct) {

            /** @var $listingProduct Ess_M2ePro_Model_Listing_Product */

            $listingProduct->deleteObjectLocks(NULL,$this->messageHash);
            $listingProduct->deleteObjectLocks('in_action',$this->messageHash);
            $listingProduct->deleteObjectLocks($actionIdentifier.'_action',$this->messageHash);

            if (isset($tempListings[$listingProduct->getListingId()])) {
                continue;
            }

            $listingProduct->getListing()->deleteObjectLocks(NULL,$this->messageHash);
            $listingProduct->getListing()->deleteObjectLocks('products_in_action',$this->messageHash);
            $listingProduct->getListing()->deleteObjectLocks('products_'.$actionIdentifier.'_action',$this->messageHash);

            $tempListings[$listingProduct->getListingId()] = true;
        }

        $this->getAccount()->deleteObjectLocks('products_in_action',$this->messageHash);
        $this->getAccount()->deleteObjectLocks('products_'.$actionIdentifier.'_action',$this->messageHash);

        $this->getMarketplace()->deleteObjectLocks('products_in_action',$this->messageHash);
        $this->getMarketplace()->deleteObjectLocks('products_'.$actionIdentifier.'_action',$this->messageHash);

        if ($fail) {

            $logModel = Mage::getModel('M2ePro/Listing_Log');
            $logModel->setComponentMode(Ess_M2ePro_Helper_Component_Amazon::NICK);

            $tempListings = array();
            foreach ($this->listingsProducts as $product) {
                /** @var $product Ess_M2ePro_Model_Listing_Product */
                if (isset($tempListings[$product->getListingId()])) {
                    continue;
                }
                $this->addListingsLogsMessage($product,$message,
                                              Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR,
                                              Ess_M2ePro_Model_Log_Abstract::PRIORITY_HIGH);
                $tempListings[$product->getListingId()] = true;
            }
        }

        $this->inspectProducts();
    }

    protected function inspectProducts()
    {
        /** @var $inspector Ess_M2ePro_Model_Amazon_Template_Synchronization_ProductInspector */
        $inspector = Mage::getModel('M2ePro/Amazon_Template_Synchronization_ProductInspector');
        $inspector->processProducts($this->succeededListingsProducts);
    }

    // ########################################

    protected function addListingsProductsLogsMessage(Ess_M2ePro_Model_Listing_Product $listingProduct,
                                                      $text, $type = Ess_M2ePro_Model_Log_Abstract::TYPE_NOTICE,
                                                      $priority = Ess_M2ePro_Model_Log_Abstract::PRIORITY_MEDIUM)
    {
        $this->addBaseListingsLogsMessage($listingProduct,$text,$type,$priority,false);
    }

    protected function addListingsLogsMessage(Ess_M2ePro_Model_Listing_Product $listingProduct,
                                              $text, $type = Ess_M2ePro_Model_Log_Abstract::TYPE_NOTICE,
                                              $priority = Ess_M2ePro_Model_Log_Abstract::PRIORITY_MEDIUM)
    {
        $this->addBaseListingsLogsMessage($listingProduct,$text,$type,$priority,true);
    }

    protected function addBaseListingsLogsMessage(Ess_M2ePro_Model_Listing_Product $listingProduct,
                                                  $text, $type = Ess_M2ePro_Model_Log_Abstract::TYPE_NOTICE,
                                                  $priority = Ess_M2ePro_Model_Log_Abstract::PRIORITY_MEDIUM,
                                                  $isListingMode = true)
    {
        $action = $this->getListingsLogsCurrentAction();
        is_null($action) && $action = Ess_M2ePro_Model_Listing_Log::ACTION_UNKNOWN;

        $initiator = Ess_M2ePro_Model_Log_Abstract::INITIATOR_UNKNOWN;
        if ($this->getStatusChanger() == Ess_M2ePro_Model_Listing_Product::STATUS_CHANGER_UNKNOWN) {
            $initiator = Ess_M2ePro_Model_Log_Abstract::INITIATOR_UNKNOWN;
        } else if ($this->getStatusChanger() == Ess_M2ePro_Model_Listing_Product::STATUS_CHANGER_USER) {
            $initiator = Ess_M2ePro_Model_Log_Abstract::INITIATOR_USER;
        } else {
            $initiator = Ess_M2ePro_Model_Log_Abstract::INITIATOR_EXTENSION;
        }

        $logModel = Mage::getModel('M2ePro/Listing_Log');
        $logModel->setComponentMode(Ess_M2ePro_Helper_Component_Amazon::NICK);

        if ($isListingMode) {
            $logModel->addListingMessage($listingProduct->getListingId() ,
                                         $initiator ,
                                         $this->getLogsActionId() ,
                                         $action , $text, $type , $priority);
        } else {
            $logModel->addProductMessage($listingProduct->getListingId() ,
                                         $listingProduct->getProductId() ,
                                         $initiator ,
                                         $this->getLogsActionId() ,
                                         $action , $text, $type , $priority);
        }
    }

    // ########################################

    protected function validateFailedResponseData($response)
    {
        if (empty($response[parent::ERRORS_KEY])) {
            return false;
        }

        foreach ($response[parent::ERRORS_KEY] as $error) {
            if (!isset($error['listing'])) {
                return false;
            }

            if (!isset($error['listing']['xId'])) {
                return false;
            }

            if (!$this->validateErrorData($error)) {
                return false;
            }
        }

        return true;
    }

    //-----------------------------------------

    protected function processFailedResponseData($response)
    {
        $succeededListingsProducts = array();
        $failedListingsProductsIds = array();

        foreach ($response[parent::ERRORS_KEY] as $errorListing) {

            $findedListingProduct = NULL;
            $listingProductId = (int)$errorListing['listing']['xId'];

            foreach ($this->listingsProducts as $listingProduct) {
                if ($listingProduct->getId() == $listingProductId) {
                    $findedListingProduct = $listingProduct;
                    break;
                }
            }

            if (is_null($findedListingProduct)) {
                continue;
            }

            foreach ($errorListing[parent::ERRORS_KEY] as $error) {
                $this->addListingsProductsLogsMessage($findedListingProduct,$error[parent::ERROR_TEXT_KEY],
                                                      Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR,
                                                      Ess_M2ePro_Model_Log_Abstract::PRIORITY_HIGH);
            }

            $this->failedListingsProducts[] = $findedListingProduct;
            $failedListingsProductsIds[] = $findedListingProduct->getId();
        }

        foreach ($this->listingsProducts as $listingProduct) {
            if (in_array($listingProduct->getId(),$failedListingsProductsIds)) {
                continue;
            }
            $succeededListingsProducts[] = $listingProduct;
        }

        $this->succeededListingsProducts = $succeededListingsProducts;
        $this->processSucceededListingsProducts($succeededListingsProducts);
    }

    protected function processSucceededResponseData($response)
    {
        $this->succeededListingsProducts = $this->listingsProducts;
        $this->processSucceededListingsProducts($this->listingsProducts);
    }

    //----------------------------------------

    protected abstract function processSucceededListingsProducts(array $listingsProducts = array());

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

    //---------------------------------------

    protected function getActionIdentifier()
    {
        return $this->params['action_identifier'];
    }

    protected function getStatusChanger()
    {
        return (int)$this->params['status_changer'];
    }

    protected function getLogsActionId()
    {
        return (int)$this->params['logs_action_id'];
    }

    protected function getListingsLogsCurrentAction()
    {
        return $this->params['listing_log_action'];
    }

    //---------------------------------------

    protected function getListingProductRequestNativeData(Ess_M2ePro_Model_Listing_Product $listingProduct)
    {
        return (array)$this->params['products'][$listingProduct->getId()]['request']['native_data'];
    }

    protected function getListingProductXfabricNativeData(Ess_M2ePro_Model_Listing_Product $listingProduct)
    {
        return (array)$this->params['products'][$listingProduct->getId()]['request']['xfabric_data'];
    }

    // ########################################
}