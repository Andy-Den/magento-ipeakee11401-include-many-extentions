<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Featuredproduct
 *
 * @author user
 */
class Balance_FeaturedProduct_Model_Featuredproduct {
  
    public function getProductCollection($category,$filters = null,$attributes = null){
        
        if (!$category || !$category->getId())
            return false;
        
        if (!is_array($filters)){
            $filters = array();            
        }
        $filters['category_id'] = $category->getId();
       
        $displayMethod = $category->getData('featuredproduct_displaymethod'); 
        
        $class = Mage::getSingleton('featuredproduct/attribute_source_displaymethod')
                                ->getClass($displayMethod);
       
        $resourceModel  = Mage::getResourceSingleton('featuredproduct/displaymethod_'.$class);        
        return $resourceModel->getProductCollection($filters,$attributes);
        
    }
}
