<?php

class Balance_Accessory_Model_Catalog_Layer_Filter_Accessory_Model extends Balance_Accessory_Model_Catalog_Layer_Filter_Accessory_Abstract 
{
    const XML_PATH_ATTRIBUTE_CODE = 'accessory/settings/model_attribute';
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->_attributeCode = Mage::getStoreConfig(self::XML_PATH_ATTRIBUTE_CODE);
    }
}