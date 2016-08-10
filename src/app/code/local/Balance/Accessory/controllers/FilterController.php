<?php
class Balance_Accessory_FilterController extends Mage_Core_Controller_Front_Action
{
        
    /**
     * Narrow the models list by a given brand
     */
    public function byBrandAction()
    {
        $brand = $this->getRequest()->getParam(
                Mage::getStoreConfig(Balance_Accessory_Model_Catalog_Layer_Filter_Accessory_Brand::XML_PATH_ATTRIBUTE_CODE) // gets 'accessory_brands'
                );
        $this->addActionLayoutHandles();
        $this->loadLayout();
        $this->getLayout()
                ->getBlock('accessory.search.model.dropdown')
                ->setBrand($brand);
        $this->renderLayout();
    }
}