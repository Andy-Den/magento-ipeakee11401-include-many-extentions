<?php
/**
 * Review the registration before submission
 *
 * @category   Balance
 * @package    Balance_Warranty
 * @author     Carey Sizer <carey@balanceinternet.com.au>
 */
class Balance_Warranty_Block_Registration_Product extends Balance_Warranty_Block_Registration_Abstract
{
    /**
     * The config path to the name of the make attribute
     */
    const XML_PATH_MAKE_SELECT_ATTRIBUTE = 'warranty/settings/brand_attribute';
    
    /**
     * The config path to the name of the make attribute
     */
    const XML_PATH_WARRANTY_SELECT_ATTRIBUTE = 'warranty/settings/warranty_attribute';
    
    protected function _construct()
    {
        $this->getRegistration()->setStepData('product', array(
            'label'     => Mage::helper('warranty')->__('Product Registration'),
            'is_show'   => $this->isShow()
        ));
        parent::_construct();
    }
    
    public function getNextStep()
    {
        return 'contact';
    }
    
    public function getPreviousStep()
    {
        return 'login';
    }
    
    /**
     * Get the html for the 'Select make' dropdown
     * @param string $type
     * @return string 
     */
    public function getMakeHtmlSelect($type)
    {
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName($type.'[make]')
            ->setId($type.':make')
            ->setTitle(Mage::helper('warranty')->__('Make'))
            ->setClass('validate-select')
            //->setValue($countryId)
            ->setOptions($this->_getMakeOptions());
        return $select->getHtml();
    }
    
    /**
     * Get options for the 'Select Make' (which is really brand) dropdown
     * @return array
     */
    protected function _getMakeOptions()
    {
        $attributeId = Mage::getResourceModel('eav/entity_attribute')
            ->getIdByCode('catalog_product',Mage::getStoreConfig(self::XML_PATH_MAKE_SELECT_ATTRIBUTE));
        $options = Mage::getModel('catalog/resource_eav_attribute')
                    ->load($attributeId)
                    ->getSource()
                    ->getAllOptions();
        $values = array();
        foreach($options as $option){
            $values[$option['label']] = $option['label'];
        }
        return $values;
    }
    
    /**
     * Get the warranty select html
     * @param string $type
     * @return string 
     */
    public function getWarrantyHtmlSelect($type)
    {
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName($type.'[warranty_term]')
            ->setId($type.':warranty_term')
            ->setTitle(Mage::helper('warranty')->__('Warranty Term'))
            ->setClass('validate-select')
            //->setValue($countryId)
            ->setOptions($this->_getWarrantyOptions());
        return $select->getHtml();
    }
    
    /**
     * Get the options for the warranty drop down
     * @return array 
     */
    protected function _getWarrantyOptions()
    {
        $attributeId = Mage::getResourceModel('eav/entity_attribute')
            ->getIdByCode('catalog_product',Mage::getStoreConfig(self::XML_PATH_WARRANTY_SELECT_ATTRIBUTE));
        $options = Mage::getModel('catalog/resource_eav_attribute')
                ->load($attributeId)
                ->getSource()
                ->getAllOptions();
        $values = array();
        foreach($options as $option){
            $values[$option['label']] = $option['label'];
        }
        return $values;
    }
}