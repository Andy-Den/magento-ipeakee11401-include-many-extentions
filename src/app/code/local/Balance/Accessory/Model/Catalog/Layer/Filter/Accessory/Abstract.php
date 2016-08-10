<?php

abstract class Balance_Accessory_Model_Catalog_Layer_Filter_Accessory_Abstract extends Mage_Catalog_Model_Layer_Filter_Abstract {

    const XML_PATH_ATTRIBUTE_CODE = null;
    
    /**
     * The attribute code the the brand / model
     * @var string 
     */
    protected $_attributeCode = null;
    
    
    
    /**
     * Get the brand attribute code
     * @return string 
     */
    public function getAttributeCode()
    {
        if(!$this->_attributeCode){
            $this->_attributeCode = Mage::getStoreConfig(self::XML_PATH_ATTRIBUTE_CODE);
        }
        return $this->_attributeCode;
    }
    
    /**
     * Get the attribute for the filter
     * @return Mage_Eav_Entity_Attribute
     */
    public function getAttribute()
    {
        $attributeId = Mage::getResourceModel('eav/entity_attribute')
            ->getIdByCode('catalog_product',$this->_attributeCode);
        return Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
    }
}