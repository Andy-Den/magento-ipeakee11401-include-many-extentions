<?php

class Balance_Accessory_Model_Catalog_Product_Accessory extends Varien_Object {
    
    /**
     * The category ID that will be used for accessories
     */
    const XML_PATH_ACCESSORY_CATEGORY_ID = 'accessory/settings/category';
    
    /**
     * The XML path that points to the config option that holds brand info on products
     */
    const XML_PATH_PRODUCT_BRAND_ATTRIBUTE_CODE = 'accessory/settings/catalog_brand_attribute';
    
    /**
     * The XML path that points to the config option that holds model info on products
     */
    const XML_PATH_PRODUCT_MODEL_ATTRIBUTE_CODE = 'accessory/settings/catalog_model_attribute';

    public function _construct()
    {
        parent::_construct();
    }

    /**
     * Get the category id for accessories
     * @return int 
     */
    public function getCategoryId()
    {
        return Mage::getStoreConfig(self::XML_PATH_ACCESSORY_CATEGORY_ID);
    }

    /**
     * Get the category for accessories
     * @return Mage_Catalog_Model_Category
     */
    public function getCategory()
    {
        if (!is_numeric($this->getCategoryId())) {
            Mage::throwException("The category ID to store accessories in needs to be set under Configuration -> Accessory");
        }
        return Mage::getModel('catalog/category')->load($this->getCategoryId());
    }

    /**
     * Get the attribute that holds the applicable brands for an attribute
     * @return Mage_Eav_Entity_Attribute
     */
    public function getAccessoryBrandsAttribute()
    {
        return $this->_getAttribute(
                        Mage::getStoreConfig(
                                Balance_Accessory_Model_Catalog_Layer_Filter_Accessory_Brand::XML_PATH_ATTRIBUTE_CODE
                        ));
    }

    /**
     * Get the attribute that holds the applicable brands for an attribute
     * @return Mage_Eav_Entity_Attribute
     */
    public function getAccessoryModelsAttribute()
    {
        return $this->_getAttribute(
                        Mage::getStoreConfig(
                                Balance_Accessory_Model_Catalog_Layer_Filter_Accessory_Model::XML_PATH_ATTRIBUTE_CODE
                        ));
    }
    
    /**
     * Get the attribute that holds the applicable brands for an attribute
     * @return Mage_Eav_Entity_Attribute
     */
    public function getCatalogBrandAttribute()
    {
        return $this->_getAttribute(
                        Mage::getStoreConfig(
                                self::XML_PATH_PRODUCT_BRAND_ATTRIBUTE_CODE
                        ));
    }

    /**
     * Get the attribute that holds the applicable brands for an attribute
     * @return Mage_Eav_Entity_Attribute
     */
    public function getCatalogModelAttribute()
    {
        return $this->_getAttribute(
                        Mage::getStoreConfig(
                                self::XML_PATH_PRODUCT_MODEL_ATTRIBUTE_CODE
                        ));
    }

    /**
     * Get the attribute for the filter
     * @return Mage_Eav_Entity_Attribute
     */
    protected function _getAttribute($code)
    {
        $attributeId = Mage::getResourceModel('eav/entity_attribute')
                ->getIdByCode('catalog_product', $code);
        return Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
    }
    
    /**
     * Retrieve array of attributes used in the mini search block
     *
     * @return array
     */
    public function getMiniSearchAttributes()
    {
        
        return $this->getFullSearchAttributes();
        
        /* @var $attributes Mage_Catalog_Model_Resource_Eav_Resource_Product_Attribute_Collection */
//        $attribute = $this->getAccessoryBrandsAttribute();
//        
//            $product = Mage::getModel('catalog/product');
//            $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
//                ->addFieldToFilter('main_table.attribute_id', array('in' => $attribute->getId()))
//                ->setOrder('main_table.attribute_id', 'asc')
//                ->load();
//            foreach ($attributes as $attribute) {
//                $attribute->setEntity($product->getResource());
//            }
//        return $attributes;
    }
    
    /**
     * Retrieve array of attributes used in the full search block
     *
     * @return array
     */
    public function getFullSearchAttributes()
    {
        /* @var $attributes Mage_Catalog_Model_Resource_Eav_Resource_Product_Attribute_Collection */
        $brandAttribute = $this->getAccessoryBrandsAttribute();
        $modelAttribute = $this->getAccessoryModelsAttribute();
            $product = Mage::getModel('catalog/product');
            $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
                ->addFieldToFilter('main_table.attribute_id', array('in' => array($brandAttribute->getId(),$modelAttribute->getId())))
                ->setOrder('main_table.attribute_id', 'asc')
                ->load();
            foreach ($attributes as $attribute) {
                $attribute->setEntity($product->getResource());
            }
        return $attributes;
    }

}