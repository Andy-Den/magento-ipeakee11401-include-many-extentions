<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Connector_Xfabric_Amazon_Product_HelperVariations
{
    // ########################################

    public function getRequestData(Ess_M2ePro_Model_Listing_Product $listingProduct)
    {
        if ($listingProduct->getMagentoProduct()->isSimpleTypeWithoutCustomOptions()) {
            return array();
        }

        $requestData = array();

        // TODO

        return $requestData;
    }

    // ########################################

    public function updateAfterAction(Ess_M2ePro_Model_Listing_Product $listingProduct)
    {
        if ($listingProduct->getMagentoProduct()->isSimpleTypeWithoutCustomOptions()) {
            return;
        }

        // TODO
    }

    // ########################################
}