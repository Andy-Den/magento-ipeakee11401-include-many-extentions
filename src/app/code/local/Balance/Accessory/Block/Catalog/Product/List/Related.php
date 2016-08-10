<?php

/**
 * Rewrites the related block to include both accessories and related. 
 * We are overwriting the TargetRule one, but if it was community you would be overwriting Catalog_Product_List_Related
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Balance_Accessory_Block_Catalog_Product_List_Related extends Enterprise_TargetRule_Block_Catalog_Product_List_Related {

    /**
     * Group the two together
     * @return Balance_Accessory_Block_Catalog_Product_List_Related 
     */
    protected function _getGroupedRelatedProductCollection()
    {
        $product = $this->getProduct();
        /* @var $product Mage_Catalog_Model_Product */

        $relatedCollection = $product->getRelatedProductCollection()
                ->addAttributeToSelect('entity_id')
                ->addAttributeToSelect('position')
                ->addAttributeToSort('position', Varien_Db_Select::SQL_ASC)
                ->addStoreFilter();

        $accessoryCollection = $product->getAccessoryProductCollection()
                ->addAttributeToSelect('entity_id')
                ->addAttributeToSelect('position')
                ->addAttributeToSort('position', Varien_Db_Select::SQL_ASC)
                ->addStoreFilter();

        $relatedIds = array();
        foreach ($relatedCollection as $relatedProduct) {
            $relatedIds[] = $relatedProduct->getId();
        }
        $accessoryIds = array();
        foreach ($accessoryCollection as $accessoryProduct) {
            $accessoryIds[] = $accessoryProduct->getId();
        }
        $mergedIds = array_merge($relatedIds, $accessoryIds);

        $mergedCollection = Mage::getModel('catalog/product')->getCollection()
                        ->addAttributeToSelect('*')
                        ->addFieldToFilter('entity_id', array('in' => $mergedIds))->load();

        return $mergedCollection;
    }

    /**
     * Get link collection with limit parameter
     *
     * @throws Mage_Core_Exception
     * @param null|int $limit
     * @return Mage_Catalog_Model_Resource_Product_Link_Product_Collection|null
     */
    protected function _getPreparedTargetLinkCollection($limit = null)
    {
        $linkCollection = null;
        switch ($this->getType()) {
            case Enterprise_TargetRule_Model_Rule::RELATED_PRODUCTS:
                $linkCollection = $this->_getGroupedRelatedProductCollection();
                break;

            case Enterprise_TargetRule_Model_Rule::UP_SELLS:
                $linkCollection = $this->getProduct()
                        ->getUpSellProductCollection();
                break;

            default:
                Mage::throwException(
                        Mage::helper('enterprise_targetrule')->__('Undefined Catalog Product List Type')
                );
        }

        if (!is_null($limit)) {
            $this->_addProductAttributesAndPrices($linkCollection);
            $linkCollection->setPageSize($limit);
        }

        Mage::getSingleton('catalog/product_visibility')
                ->addVisibleInCatalogFilterToCollection($linkCollection);

        $linkCollection->setFlag('do_not_use_category_id', true);

        $excludeProductIds = $this->getExcludeProductIds();
        if ($excludeProductIds) {
            $linkCollection->addAttributeToFilter('entity_id', array('nin' => $excludeProductIds));
        }


        return $linkCollection;
    }

}