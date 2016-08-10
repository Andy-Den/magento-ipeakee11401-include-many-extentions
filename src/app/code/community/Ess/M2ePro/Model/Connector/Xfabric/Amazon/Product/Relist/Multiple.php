<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Relist_Multiple extends Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Requester
{
    // ########################################

    protected function getActionIdentifier()
    {
        return 'relist';
    }

    protected function getTopicPath()
    {
        return array('listing','update');
    }

    protected function getResponserModel()
    {
        return 'Amazon_Product_Relist_MultipleResponser';
    }

    protected function getListingsLogsCurrentAction()
    {
        return Ess_M2ePro_Model_Listing_Log::ACTION_RELIST_PRODUCT_ON_COMPONENT;
    }

    // ########################################

    protected function prepareListingsProducts($listingsProducts)
    {
        $tempListingsProducts = array();

        foreach ($listingsProducts as $listingProduct) {

            /** @var $listingProduct Ess_M2ePro_Model_Listing_Product */

            if (!$listingProduct->isStopped()) {

                // Parser hack -> Mage::helper('M2ePro')->__('The item either is listed, or not listed yet or not available');
                $this->addListingsProductsLogsMessage($listingProduct, 'The item either is listed, or not listed yet or not available',
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

            $tempListingsProducts[] = $listingProduct;
        }

        return $tempListingsProducts;
    }

    // ########################################

    protected function getRequestData()
    {
        $output = array(
            'updates' => array()
        );

        foreach ($this->listingsProducts as $listingProduct) {

            /** @var $listingProduct Ess_M2ePro_Model_Listing_Product */

            $requestData = Mage::getModel('M2ePro/Connector_Xfabric_Amazon_Product_Helper')
                                         ->getRelistRequestData($listingProduct,$this->params);

            $update = array(
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
                $update['embeddedMessage']['payload']['salePrice'] = array(
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
                'xfabric_data' => $update
            );

            if ($this->contentType == parent::CONTENT_TYPE_AVRO) {

                $update['embeddedMessage']['schemaVersion'] = '1.0.0';
                $postfix = 'public/xfabric/avro/schema/1.0.0/listing/create.avpr';
                $update['embeddedMessage']['schemaUri'] = Mage::helper('M2ePro/Module')->getServerScriptsPath().$postfix;

                $update['embeddedMessage']['payload'] = $this->encodeEmbeddedMessage($update['embeddedMessage']);
            }

            $output['updates'][] = $update;
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