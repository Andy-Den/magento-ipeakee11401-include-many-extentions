<?php
$moduldeStatus = Mage::helper('core')->isModuleEnabled('Infortis_Ultimo');
if($moduldeStatus){
class Balance_Allajax_Block_Ultimo_Product_List_Featured extends Infortis_Ultimo_Block_Product_List_Featured
{
    	/**
     * Initialize block's cache
     */
    protected $_template = 'allajax/catalog/product/list_featured_slider.phtml';
    
    protected function _construct()
    {
        parent::_construct();

        $this->addData(array(
            'cache_lifetime'    => 99999999,
            'cache_tags'        => array(Mage_Catalog_Model_Product::CACHE_TAG),
        ));
        //$this->setTemplate('allajax/catalog/product/list_featured_slider.phtml');
    }
    public function setTemplate($template){
        
        
    }
    
}
}else{
    class Balance_Allajax_Block_Ultimo_Product_List_Featured extends Mage_Core_Block_Template{
        
    }
    
}		