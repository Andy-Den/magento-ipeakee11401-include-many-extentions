<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * This shows products that match an accessory
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Balance_Accessory_Block_Catalog_Product_List_Accessory_Related extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_noform';

    protected $_itemCollection;


    protected function _prepareData()
    {
        $product = Mage::registry('product');
        /* @var $product Mage_Catalog_Model_Product */
        
        $this->_itemCollection = $product->getRelatedProductCollection()
                ->load();
        return $this;
    }

    protected function _beforeToHtml()
    {
        $this->_prepareData();
        return parent::_beforeToHtml();
    }

    public function getItems()
    {
        if(!$this->_itemCollection){
            $this->_prepareData();
        }
        return $this->_itemCollection;
    }
    
    public function isAccessoryProduct()
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::registry('product');
        return in_array(Mage::getModel('accessory/catalog_product_accessory')->getCategoryId(), $product->getCategoryIds());
    }
    public function getProduct()
    {
        $product = Mage::registry('product');
        return $product;
    }
    public function getAttributeCode()
    {
        $_product = $this->getProduct();
        $setId = $_product->getAttributeSetId();
        $groups = Mage::getModel('eav/entity_attribute_group')
            ->getResourceCollection()
            ->setAttributeSetFilter($setId)
            ->setSortOrder()
            ->load();
        $attributeCodes = array();
        foreach ($groups as $group) {
            if($group->getAttributeGroupName() == 'Accessory Details'){ 
            $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
                ->setAttributeGroupFilter($group->getId())
                ->addVisibleFilter()
                ->checkConfigurableProducts()
                ->load();
                if ($attributes->getSize() > 0) {
                    foreach ($attributes->getItems() as $attribute) {
                        $attributeCodes[] = $attribute->getAttributeCode();                     
                    }
                } 
            }
        }
        return $attributeCodes;
    }
    public function sortAttribute()
    {
        $attributeCodes = $this->getAttributeCode();
        $_product = $this->getProduct();
        $attributeText = array();
        if(count($attributeCodes) > 0){
            foreach ($attributeCodes as $value){
                $attr_src = $_product->getResource()->getAttribute($value);
                $attr_ids = explode(',', $_product->getData($value));
                $list_value = array();
                foreach ($attr_ids as $attr_id) {
                    $list_value[] = $attr_src->getSource()->getOptionText($attr_id);
                }
                $attributeText[] = $list_value;
            }
        }        
        //sort($attributeText, SORT_ASC);
        return $attributeText;
    }

    public function combination($array, $str = '') 
    {
        $html = '';
        $current = array_shift($array);
        if(count($array) > 0) {
            foreach($current as $element) {
                if($str != '')
                $this->combination($array, $str .' - '. $element);
                else
                $this->combination($array, $str . $element);   
            }
        } else{
            foreach($current as $element) {
                if($str != '' && $element != '')
                $html .= '<li class="item">' . trim($str) . ' - ' . trim($element) . '</li>';
            }
       }       
       echo $html;
    }
}
