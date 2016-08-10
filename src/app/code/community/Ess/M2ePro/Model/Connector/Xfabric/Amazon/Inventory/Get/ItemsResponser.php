<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Connector_Xfabric_Amazon_Inventory_Get_ItemsResponser extends Ess_M2ePro_Model_Connector_Xfabric_Amazon_Responser
{
    // ########################################

    protected function unsetLocks($fail = false, $message = NULL)
    {

    }

    // ########################################

    protected function validateSucceededResponseData($response)
    {
        if (!isset($response['listings'])) {
            return false;
        }

        return true;
    }

    protected function validateFailedResponseData($response)
    {
        if (!$this->validateErrorData($response)) {
            return false;
        }

        return true;
    }

    //-----------------------------------------

    protected function processSucceededResponseData($response)
    {
        $listings = $response['listings'];

        $items = array();

        foreach ($listings as $listing) {
            $embedded = $this->decodeEmbeddedMessage($listing['embeddedMessage']);

            $item = array();

            $item['description'] = $listing['product']['description'] ;
            $item['identifiers']['sku'] = $listing['product']['sku'];

            $item['price'] = $listing['price']['amount']  ;
            $item['qty'] = $listing['quantity']  ;
            $item['start_date'] = date('Y-m-d H:i:s',$listing['startTime']);

            $item['title'] = $listing['title']  ;
            $item['notice'] = $listing['subTitle']  ;

            $item['identifiers']['item_id'] = $listing['xId'];
            $item['identifiers']['product_id'] = $listing['marketItemId'];
            $item['identifiers']['is_asin'] = $embedded['identifierType'] == 'ASIN' ? 1 : 0;
            $item['identifiers']['is_isbn'] = $embedded['identifierType'] == 'ISBN' ? 1 : 0;

            $item['channel']['is_mfn'] = $embedded['fulfillmentChannel'] == 'MFN' ? 1 : 0;
            $item['channel']['is_afn'] = $embedded['fulfillmentChannel'] == 'AFN' ? 1 : 0;

            $items[] = $item;
        }

        return $items;
    }

    protected function processFailedResponseData($response)
    {
        return false;
    }

    // ########################################
}