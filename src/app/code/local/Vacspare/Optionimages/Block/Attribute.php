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
class Vacspare_Optionimages_Block_Attribute extends Mage_Core_Block_Template
{
    protected $_attributeCode   = NULL;

    public function getFileNameImageImages($optionId)
    {
        return Mage::getModel('optionimages/images')->getFileNameImageImages($optionId);
    }

    public function setAttributeCode($varName)
    {
        $this->_attributeCode = $varName;
        return $this;
    }

    public function getAttributeCode()
    {
        if(!$this->_attributeCode)
            $this->_attributeCode = $this->_getData('attribute_code');
        return $this->_attributeCode;
    }

    public function getAllOptionValue()
    {
        $storeId = Mage::app()->getStore()->getId();
        $attributeInfo = Mage::getResourceModel('eav/entity_attribute_collection')
                            ->setCodeFilter($this->getAttributeCode())
                            ->getFirstItem();

        $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                        ->setAttributeFilter($attributeInfo->getAttributeId())
                        ->setStoreFilter($storeId)
                        ->setPositionOrder('asc', true)->load();
						
        $data = array();
        $i = 0;
        foreach ($optionCollection as $option) {
			if($this->getFileNameImageImages($option->getOptionId())!='' && $i<=9){
				$data[$i]['option_id'] = $option->getOptionId();
				$data[$i]['value'] = $option->getValue();
				$data[$i]['sort_order'] = $option->getSortOrder();
				$data[$i]['filename'] = $this->getFileNameImageImages($option->getOptionId());
				$i++;
			}
        }
        return $data;
    }
}
