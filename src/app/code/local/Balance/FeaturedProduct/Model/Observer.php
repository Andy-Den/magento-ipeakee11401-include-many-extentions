<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Balance_FeaturedProduct_Model_Observer {
    
    public function createFeaturedProductTab(Varien_Event_Observer $observer)
    {
          
      $tabs = $observer->getEvent()->getTabs();  
      $tabs->addTab('featuredproduct', array(
            'label'     => Mage::helper('featuredproduct')->__('Featured Products'),
            'content'   => $tabs->getLayout()->createBlock(
                'featuredproduct/adminhtml_category_tab_featuredproduct',
                'featuredproduct.grid'
            )->toHtml(),
        ));
    }  
    
    public function prepareFeaturedProductForSave(Varien_Event_Observer $observer){
        $request = $observer->getEvent()->getRequest();
        $category = $observer->getEvent()->getCategory();
        $data = $request->getPost('featured_products');
        if (isset($data)){
            $products = array();
            parse_str($data, $products);
            $category->setPostedFeaturedProducts($products);
        }    
    } 
}