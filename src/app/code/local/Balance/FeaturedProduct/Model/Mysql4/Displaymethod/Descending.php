<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Ascending
 *
 * @author user
 */
class Balance_FeaturedProduct_Model_Mysql4_Displaymethod_Descending  
        extends Balance_FeaturedProduct_Model_Mysql4_Displaymethod_Abstract{
   
    public function __construct() {
        parent::__construct();
                
    }
    
    protected function _applyDisplayMethod($collection,$filters,$attributes = null) {
        
        if ($collection) {
            $collection->getSelect()->order('position DESC');                                             
        }           
    }
}
