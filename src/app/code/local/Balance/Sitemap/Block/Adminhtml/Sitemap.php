<?php

class Balance_Sitemap_Block_Adminhtml_Sitemap extends Mage_Adminhtml_Block_Sitemap {
     /**
     * Block constructor
     */
    public function __construct()
    {       
        parent::__construct();        
        $this->removeButton('add');        
    }
}
