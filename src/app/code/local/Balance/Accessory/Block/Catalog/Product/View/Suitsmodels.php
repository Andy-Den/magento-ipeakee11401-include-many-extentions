<?php

class Balance_Accessory_Block_Catalog_Product_View_Suitsmodels
    extends Mage_Core_Block_Template
{

    protected $_product = null;


    public function getModels () {
        if (!$this->_product) {
            $this->_product = Mage::registry('product');
        }

        $attr = $this->_product->getResource()->getAttribute(Balance_Accessory_Helper_Data::ATTR_CODE_SUITS_MODELS);
        if (!$attr) return;

        $rawModels = trim($attr->getFrontend()->getValue($this->_product));
        if (!$rawModels) return;

        $models = explode("\r\n", $rawModels);
        return $models;
    }


}
