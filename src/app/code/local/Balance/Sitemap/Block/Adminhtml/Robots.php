<?php
class Balance_Sitemap_Block_Adminhtml_Robots extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_robots';
        $this->_blockGroup = 'balance_sitemap';
        $this->_headerText = Mage::helper('balance_sitemap')->__('Robots.txt');
        $this->_addButtonLabel = Mage::helper('balance_sitemap')->__('Add New Robots.txt');
        parent::__construct();
    }

}
