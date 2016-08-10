<?php
/**
 * Catalog product link api
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Balance_Accessory_Model_Catalog_Product_Link_Api extends Mage_Catalog_Model_Product_Link_Api
{
    
    public function __construct()
    {
        $this->_storeIdSessionField = 'product_store_id';
        // adds in accessory as a valid type map
        $this->_typeMap['accessory'] = Balance_Accessory_Model_Catalog_Product_Link::LINK_TYPE_ACCESSORY;
    }

} // Class Balance_Accessory_Model_Catalog_Product_Link_Api End