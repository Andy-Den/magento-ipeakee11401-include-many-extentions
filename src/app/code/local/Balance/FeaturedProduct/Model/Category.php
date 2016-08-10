<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Balance_FeaturedProduct_Model_Category extends Mage_Catalog_Model_Category{
     public function getFeaturedProductsPosition() {	
    	return $this->getResource()->getFeaturedProductsPosition($this);
    }
    
     
}