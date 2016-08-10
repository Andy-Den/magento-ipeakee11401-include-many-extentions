<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_List_Multiple extends Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Requester
{
    // ########################################

    protected function getActionIdentifier()
    {
        return 'list';
    }

    protected function getTopicPath()
    {
        return array('listing','create');
    }

    protected function getResponserModel()
    {
        return 'Amazon_Product_List_MultipleResponser';
    }

    protected function getListingsLogsCurrentAction()
    {
        return Ess_M2ePro_Model_Listing_Log::ACTION_LIST_PRODUCT_ON_COMPONENT;
    }

    // ########################################

    protected function prepareListingsProducts($listingsProducts)
    {
        $tempListingsProducts = array();

        foreach ($listingsProducts as $listingProduct) {

            /** @var $listingProduct Ess_M2ePro_Model_Listing_Product */

            if (!$listingProduct->isNotListed()) {

                // Parser hack -> Mage::helper('M2ePro')->__('Item is listed or not available');
                $this->addListingsProductsLogsMessage($listingProduct, 'Item is listed or not available',
                                                      Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR,
                                                      Ess_M2ePro_Model_Log_Abstract::PRIORITY_MEDIUM);

                continue;
            }

            if ($listingProduct->isLockedObject(NULL) ||
                $listingProduct->isLockedObject('in_action') ||
                $listingProduct->isLockedObject($this->getActionIdentifier().'_action')) {

                // Parser hack -> Mage::helper('M2ePro')->__('Item is locked by other processing action');
                $this->addListingsProductsLogsMessage($listingProduct, 'Item is locked by other processing action',
                                                      Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR,
                                                      Ess_M2ePro_Model_Log_Abstract::PRIORITY_MEDIUM);

                continue;
            }

            $addingSku = $listingProduct->getChildObject()->getSku();
            is_null($addingSku) && $addingSku = $listingProduct->getChildObject()->getAddingSku();

            $addingGeneralId = $listingProduct->getChildObject()->getGeneralId();
            is_null($addingGeneralId) && $addingGeneralId = $listingProduct->getChildObject()->getAddingGeneralId();

            if (empty($addingSku) || empty($addingGeneralId)) {

                // Parser hack -> Mage::helper('M2ePro')->__('Sku or ASIN/ISBN is empty');
                $this->addListingsProductsLogsMessage($listingProduct, 'Sku or ASIN/ISBN is empty',
                                                      Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR,
                                                      Ess_M2ePro_Model_Log_Abstract::PRIORITY_MEDIUM);

                continue;
            }

            if (strlen($addingGeneralId) != 10) {

                // Parser hack -> Mage::helper('M2ePro')->__('ASIN/ISBN has wrong format');
                $this->addListingsProductsLogsMessage($listingProduct, 'ASIN/ISBN has wrong format',
                                                      Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR,
                                                      Ess_M2ePro_Model_Log_Abstract::PRIORITY_MEDIUM);

                continue;
            }

            $tempListingsProducts[] = $listingProduct;
        }

        return $tempListingsProducts;
    }

    // ########################################

    protected function getRequestData()
    {
        $output = array(
            'listings' => array()
        );

        foreach ($this->listingsProducts as $listingProduct) {

            /** @var $listingProduct Ess_M2ePro_Model_Listing_Product */

            $productVariations = $listingProduct->getVariations(true);

            foreach ($productVariations as $variation) {
                /** @var $variation Ess_M2ePro_Model_Listing_Product_Variation */
                $variation->deleteInstance();
            }

            $requestData = Mage::getModel('M2ePro/Connector_Xfabric_Amazon_Product_Helper')
                                         ->getListRequestData($listingProduct,$this->params);

            $listing = array(
                'xId' => $listingProduct->getId(),
                'marketItemId' => $requestData['general_id'],
                'product' => array(
                    'xId' => null,
                    'xProductTypeId' => null,
                    'sku' => $requestData['sku'],
                    'manufacturer' => null,
                    'mpn' => null,
                    'brand' => null,
                    'msrp' => null,
                    'minimumAdvertisedPrice' => null,
                    'imageURL' => null,
                    'description' => null,
                    'attributes' => null,
                ),
                'startTime' => NULL,
                'price' => array(
                    'amount' => $requestData['price'],
                    'code' => $requestData['currency']
                ),
                'quantity' => $requestData['qty'],
                'title' => null,
                'listingURL' => null,
                'status' => null,
                'subTitle' => null,
                'giftWrapAvailable' => null,
                'marketCategories' => null,
                'customCategories' => null,
                'payment' => null,
                'shipping' => null,
                'returnPolicy' => null,
                'embeddedMessage' => array(
                    'payload' => array(
                        'salePrice' => null
                    )
                )
            );

            if ($requestData['sale_price'] > 0) {
                $listing['embeddedMessage']['payload']['salePrice'] = array(
                    'price' => array(
                        'amount' => $requestData['sale_price'],
                        'code' => $requestData['currency']
                    ),
                    'startDate' => Mage::helper('M2ePro')->getDate($requestData['sale_price_start_date'], true),
                    'endDate' => Mage::helper('M2ePro')->getDate($requestData['sale_price_end_date'], true)
                );
            }

            $this->listingProductRequestsData[$listingProduct->getId()] = array(
                'native_data' => $requestData,
                'xfabric_data' => $listing
            );

            if ($this->contentType == parent::CONTENT_TYPE_AVRO) {

                $listing['embeddedMessage']['schemaVersion'] = '1.0.0';
                $postfix = 'public/xfabric/avro/schema/1.0.0/listing/create.avpr';
                $listing['embeddedMessage']['schemaUri'] = Mage::helper('M2ePro/Module')->getServerScriptsPath().$postfix;

                $listing['embeddedMessage']['payload'] = $this->encodeEmbeddedMessage($listing['embeddedMessage']);
            }

            $output['listings'][] = $listing;
        }

        return $output;
    }

    // ########################################

    protected function getAccountIdKey()
    {
        return 'xProfileId';
    }

    protected function getAvroSchemaVersion()
    {
        return '1.0.1';
    }

    // ########################################
}