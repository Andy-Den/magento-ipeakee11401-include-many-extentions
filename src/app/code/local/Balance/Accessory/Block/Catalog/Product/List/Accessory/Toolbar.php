<?php
/**
 * Product list accessory  toolbar
 *
 * @category    Balance
 * @package     Balance_Accessory
 * @author      Carey Sizer <carey@balanceinternet.com.au>
 */
class Balance_Accessory_Block_Catalog_Product_List_Accessory_Toolbar extends Mage_Core_Block_Template
{
    
    /**
     * Get the options for the model attribute
     */
    public function getModelFilter()
    {
        $attributeCode = Mage::getModel('accessory/catalog_layer_filter_accessory_model')
                ->getAttributeCode();
    }
    
    /**
     * Get brand options
     */
    public function getBrandFilter()
    {
        $attributeCode = Mage::getModel('accessory/catalog_layer_filter_accessory_brand')
                ->getAttributeCode();
        $filterView = Mage::getBlockSingleton('accessory/catalog_layer_view')
                ->getFilter($attributeCode);
    }
}