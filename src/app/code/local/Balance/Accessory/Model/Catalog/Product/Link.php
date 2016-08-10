<?php

/**
 * Catalog product link model for accessories
 *
 * @method Mage_Catalog_Model_Resource_Product_Link _getResource()
 * @method Mage_Catalog_Model_Resource_Product_Link getResource()
 * @method int getProductId()
 * @method Mage_Catalog_Model_Product_Link setProductId(int $value)
 * @method int getLinkedProductId()
 * @method Mage_Catalog_Model_Product_Link setLinkedProductId(int $value)
 * @method int getLinkTypeId()
 * @method Mage_Catalog_Model_Product_Link setLinkTypeId(int $value)
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Balance_Accessory_Model_Catalog_Product_Link extends Mage_Catalog_Model_Product_Link
{
    const LINK_TYPE_ACCESSORY   = 6;
    
    /**
     * Initialize resource
     */
    protected function _construct()
    {
        parent::_construct();
    }

    public function useAccessoryLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_ACCESSORY);
        return $this;
    }

    /**
     * Save data for product accessory
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  Mage_Catalog_Model_Product_Link
     */
    public function saveProductRelations($product)
    {
        parent::saveProductRelations($product);
        $data = $product->getAccessoryLinkData();
        if (!is_null($data)) {
            $this->_getResource()->saveProductLinks($product, $data, self::LINK_TYPE_ACCESSORY);
        }
        return $this;
    }
}