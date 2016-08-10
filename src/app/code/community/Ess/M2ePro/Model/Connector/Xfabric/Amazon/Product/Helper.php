<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_Helper
{
    // ########################################

    public function getListRequestData(Ess_M2ePro_Model_Listing_Product $listingProduct, array $params = array())
    {
        $requestData = $this->getReviseRequestData($listingProduct,$params);

        if (empty($requestData['sku'])) {
            if ($listingProduct->getChildObject()->getSku()) {
                $requestData['sku'] = $listingProduct->getChildObject()->getSku();
            } else {
                $requestData['sku'] = $listingProduct->getChildObject()->getAddingSku();
            }
        }

        if (empty($requestData['general_id'])) {
            if ($listingProduct->getChildObject()->getGeneralId()) {
                $requestData['general_id'] = $listingProduct->getChildObject()->getGeneralId();
            } else {
                $requestData['general_id'] = $listingProduct->getChildObject()->getAddingGeneralId();
            }
        }

        return $requestData;
    }

    public function updateAfterListAction(Ess_M2ePro_Model_Listing_Product $listingProduct, array $params = array())
    {
        // Update Listing Product
        //---------------------
        $dataForUpdate = array(
            'status' => Ess_M2ePro_Model_Listing_Product::STATUS_LISTED,
            'status_changer' => $params['status_changer'],
            'general_id' => $params['general_id'],
            'sku' => $params['sku'],
            'online_qty' => $params['qty'],
            'online_price' => $params['price'],
            'is_afn_channel' => Ess_M2ePro_Model_Amazon_Listing_Product::IS_AFN_CHANNEL_NO,
            'is_isbn_general_id' => (int)Mage::helper('M2ePro/Component_Amazon')->isISBN($params['general_id']),
            'start_date' => Mage::helper('M2ePro')->getCurrentGmtDate(),
            'end_date' => NULL
        );

        if ((int)$dataForUpdate['online_qty'] > 0) {
            $newData['status'] = Ess_M2ePro_Model_Listing_Product::STATUS_LISTED;
            $newData['end_date'] = NULL;
        } else {
            $newData['status'] = Ess_M2ePro_Model_Listing_Product::STATUS_STOPPED;
            $newData['end_date'] = Mage::helper('M2ePro')->getCurrentGmtDate();
        }

        $listingProduct->addData($dataForUpdate)->save();
        //---------------------

        // Add Amazon Item record
        //---------------------
        $dataForAdd = array(
            'account_id' => $listingProduct->getListing()->getGeneralTemplate()->getAccountId(),
            'marketplace_id' => $listingProduct->getListing()->getGeneralTemplate()->getMarketplaceId(),
            'sku' => $listingProduct->getChildObject()->getSku(),
            'product_id' => $listingProduct->getProductId(),
            'store_id' => $listingProduct->getListing()->getStoreId()
        );

        Mage::getModel('M2ePro/Amazon_Item')->setData($dataForAdd)->save();
        //---------------------

        // Update Variations
        //---------------------
        Mage::getModel('M2ePro/Connector_Xfabric_Amazon_Product_HelperVariations')
                   ->updateAfterAction($listingProduct);
        //---------------------
    }

    //----------------------------------------

    public function getRelistRequestData(Ess_M2ePro_Model_Listing_Product $listingProduct, array $params = array())
    {
        return $this->getReviseRequestData($listingProduct,$params);
    }

    public function updateAfterRelistAction(Ess_M2ePro_Model_Listing_Product $listingProduct, array $params = array())
    {
        // Update Listing Product
        //---------------------
        $dataForUpdate = array(
            'status' => Ess_M2ePro_Model_Listing_Product::STATUS_LISTED,
            'status_changer' => $params['status_changer'],
            'online_qty' => $params['qty'],
            'online_price' => $params['price'],
            'is_afn_channel' => Ess_M2ePro_Model_Amazon_Listing_Product::IS_AFN_CHANNEL_NO
        );

        if ((int)$dataForUpdate['online_qty'] > 0) {
            $newData['status'] = Ess_M2ePro_Model_Listing_Product::STATUS_LISTED;
            $newData['end_date'] = NULL;
        } else {
            $newData['status'] = Ess_M2ePro_Model_Listing_Product::STATUS_STOPPED;
            $newData['end_date'] = Mage::helper('M2ePro')->getCurrentGmtDate();
        }

        $listingProduct->addData($dataForUpdate)->save();
        //---------------------

        // Update Variations
        //---------------------
        Mage::getModel('M2ePro/Connector_Xfabric_Amazon_Product_HelperVariations')
                   ->updateAfterAction($listingProduct);
        //---------------------
    }

    //----------------------------------------

    public function getReviseRequestData(Ess_M2ePro_Model_Listing_Product $listingProduct, array $params = array())
    {
        // Set permissions
        //-----------------
        $permissions = array(
            'general'=>true,
            'variations'=>true,
            'qty'=>true,
            'price'=>true
        );

        if (isset($params['only_data'])) {
            foreach ($permissions as &$value) {
                $value = false;
            }
            $permissions = array_merge($permissions,$params['only_data']);
        }

        if (isset($params['all_data'])) {
            foreach ($permissions as &$value) {
                $value = true;
            }
        }
        //-----------------

        $requestData = array();

        // Prepare Variations
        //-------------------
        Mage::getModel('M2ePro/Amazon_Listing_Product_Variation_Updater')->updateVariations($listingProduct);
        $tempVariations = Mage::getModel('M2ePro/Connector_Xfabric_Amazon_Product_HelperVariations')
                                                ->getRequestData($listingProduct);

        $requestData['is_variation_item'] = false;
        if (is_array($tempVariations) && count($tempVariations) > 0) {
            $requestData['is_variation_item'] = true;
        }
        //-------------------

        // Get Variations
        //-------------------
        if ($permissions['variations'] && $requestData['is_variation_item']) {
            $requestData['variation'] = $tempVariations;
        }
        //-------------------

        // Get Main Data
        //-------------------
        $requestData['sku'] = $listingProduct->getChildObject()->getSku();
        $requestData['general_id'] = $listingProduct->getChildObject()->getGeneralId();

        if ($permissions['qty'] && !$requestData['is_variation_item']) {
            $requestData['qty'] = $listingProduct->getChildObject()->getQty();
        }

        if ($permissions['price'] && !$requestData['is_variation_item']) {

            $requestData['price'] = $listingProduct->getChildObject()->getPrice();
            $requestData['sale_price'] = $listingProduct->getChildObject()->getSalePrice();
            $requestData['currency'] = $listingProduct->getSellingFormatTemplate()->getChildObject()->getCurrency();

            if ($requestData['sale_price'] > 0) {
                $requestData['sale_price_start_date'] = $listingProduct->getSellingFormatTemplate()->getChildObject()->getSalePriceStartDate();
                $requestData['sale_price_end_date'] = $listingProduct->getSellingFormatTemplate()->getChildObject()->getSalePriceEndDate();
            }
        }
        //-------------------

        return $requestData;
    }

    public function updateAfterReviseAction(Ess_M2ePro_Model_Listing_Product $listingProduct, array $params = array())
    {
        // Update Listing Product
        //---------------------
        $dataForUpdate = array(
            'status' => Ess_M2ePro_Model_Listing_Product::STATUS_LISTED,
            'status_changer' => $params['status_changer'],
            'online_qty' => $params['qty'],
            'online_price' => $params['price'],
            'is_afn_channel' => Ess_M2ePro_Model_Amazon_Listing_Product::IS_AFN_CHANNEL_NO
        );

        if ((int)$dataForUpdate['online_qty'] > 0) {
            $newData['status'] = Ess_M2ePro_Model_Listing_Product::STATUS_LISTED;
            $newData['end_date'] = NULL;
        } else {
            $newData['status'] = Ess_M2ePro_Model_Listing_Product::STATUS_STOPPED;
            $newData['end_date'] = Mage::helper('M2ePro')->getCurrentGmtDate();
        }

        $listingProduct->addData($dataForUpdate)->save();
        //---------------------

        // Update Variations
        //---------------------
        Mage::getModel('M2ePro/Connector_Xfabric_Amazon_Product_HelperVariations')
                   ->updateAfterAction($listingProduct);
        //---------------------
    }

    //----------------------------------------

    public function getStopRequestData(Ess_M2ePro_Model_Listing_Product $listingProduct, array $params = array())
    {
        $requestData = array();
        
        // Get eBay Item Info
        //-------------------
        $requestData['sku'] = $listingProduct->getChildObject()->getSku();
        $requestData['general_id'] = $listingProduct->getChildObject()->getGeneralId();
        $requestData['qty'] = 0;
        //-------------------

        return $requestData;
    }

    public function updateAfterStopAction(Ess_M2ePro_Model_Listing_Product $listingProduct, array $params = array())
    {
        // Update Listing Product
        //---------------------
        $dataForUpdate = array(
            'status' => Ess_M2ePro_Model_Listing_Product::STATUS_STOPPED,
            'status_changer' => $params['status_changer'],
            'online_qty' => $params['qty'],
            'is_afn_channel' => Ess_M2ePro_Model_Amazon_Listing_Product::IS_AFN_CHANNEL_NO
        );

        $listingProduct->addData($dataForUpdate)->save();
        //---------------------

        // Update Variations
        //---------------------
        Mage::getModel('M2ePro/Connector_Xfabric_Amazon_Product_HelperVariations')
                   ->updateAfterAction($listingProduct);
        //---------------------
    }

    // ########################################
}