<?php
/**
 * Vacspare_Optionimages Extension
 *
 * @category    Local
 * @package     Vacspare_Optionimages
 * @author      dungnv (dungnv@arrowhitech.com)
 * @copyright   Copyright(c) 2011 Arrowhitech Inc. (http://www.arrowhitech.com)
 *
 */

/**
 *
 * @category   Local
 * @package    Vacspare_Optionimages
 * @author     dungnv <dungnv@arrowhitech.com>
 */

class Vacspare_Optionimages_Model_Adminhtml_System_Config_Source_Attribute
{
    public function toOptionArray()
    {
        $attributesObj = Mage::getResourceModel('eav/entity_attribute_collection')->loadData();
        $options = array();
        foreach($attributesObj as $attributeObj) {
            if($attributeObj->getFrontendInput() == 'multiselect' || $attributeObj->getFrontendInput() == 'select' && $attributeObj->getIsUserDefined() == 1) {
                array_unshift($options, array('value'=> $attributeObj->getAttributeCode(), 'label'=> $attributeObj->getFrontendLabel()));       
            }
        }
        return $options;
        
    }
}
