<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Description of Abstract
 *
 * @author user
 */
abstract class Balance_FeaturedProduct_Model_Mysql4_Displaymethod_Abstract {
    
    const XML_PATH_MAX_PRODUCTS = 'featuredproduct/settings/max_products';
    
    protected function __construct() {
       $this->_maxProducts = Mage::getStoreConfig(self::XML_PATH_MAX_PRODUCTS);             
    }
    
    public function getMaxProducts(){
        return $this->_maxProducts;
    }
    
    public function getDefaultVisibility(){
        return array(
                        Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG,
                        Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH
                    );
    }

    public function getDefaultAttributes(){
       return  Mage::getSingleton('catalog/config')->getProductAttributes();
    }

    public function getDefaultFilterFields(){
        return array( 'category_id','store_id','visibility');
    }
    
    protected abstract function _applyDisplayMethod($collection,$filters,$attributes = null);
    
    protected function _prepareProductCollection($filters,$attributes = null){
      
       if (!isset($filters['category_id'])) {
            return false;
       }

       //set default filter fields
       if(!isset($filters['visibility'])){
           $filters['visibility'] = self::getDefaultVisibility();
       }
       
       if (!isset($filters['store_id'])){
           $filters['store_id'] = Mage::app()->getStore()->getId();
       }
       
       //addtional attributes to select
       if (is_array($attributes)){
           $attributes = array_merge($this->getDefaultAttributes(),$attributes);
       }
       else{
           $attributes = $this->getDefaultAttributes();
       }
       
       $collection = Mage::getResourceModel('catalog/product_collection')
               ->setStoreId($filters['store_id'])
               ->addAttributeToSelect($attributes);
                     
       //Filter fields
       $defaultFilterFields = $this->getDefaultFilterFields();
       foreach ($filters as $key => $value) {
           if (!in_array($key, $defaultFilterFields)){
               $collection->addFieldToFilter($key,$value);
           }
       }
       
       $connection = $collection->getSelect()->getAdapter();
       
       $resource    = $collection->getResource(); 
             
       $conditions = array(
            'featured.product_id=e.entity_id',
            $connection->quoteInto('featured.store_id=?', $filters['store_id'])
        );
        
       
       $conditions[] = $connection
           ->quoteInto('featured.visibility IN(?)', $filters['visibility']);
       
        
        $conditions[] = $connection
            ->quoteInto('featured.category_id=?', $filters['category_id']);
       
              
        $joinCond = join(' AND ', $conditions);
        $fromPart = $collection->getSelect()->getPart(Zend_Db_Select::FROM);
        if (isset($fromPart['featured'])) {
            $fromPart['featured']['joinCondition'] = $joinCond;
            $collection->getSelect()->setPart(Zend_Db_Select::FROM, $fromPart);
        }
        else {
            $collection->getSelect()->join(
                array('featured' => $resource->getTable('featuredproduct/featuredproduct_index')),
                $joinCond,
                array('featured_position' => 'position')
            );
        }
                      
        return $collection;    
    }
    
    protected function _applyOtherFilters($collection,$filters){
        if ($collection){
             $collection ->addMinimalPrice()
                           ->addFinalPrice()
                           ->addTaxPercents()
                           ->addUrlRewrite($filters['category_id']);
             //limit 
             $collection->getSelect()->limit($this->getMaxProducts());
        }
    }
    public function getProductCollection($filters,$attributes = null){        
        $collection = $this->_prepareProductCollection($filters,$attributes); 
        $this->_applyDisplayMethod($collection,$filters,$attributes);
        $this->_applyOtherFilters($collection,$filters);
        return $collection;
    }
    
}
