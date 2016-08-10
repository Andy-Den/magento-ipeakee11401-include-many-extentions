<?php
class AHT_Aproduct_Block_Featured extends Mage_Core_Block_Template
{
	protected $_limit;
	protected $_p;
    protected $_collectionProducts;
    protected $_title ="Featured items";
	public function _construct()
    {
        $this->_limit 	= $this->_getLimit();
		$this->_p 		= $this->_getPage();
        parent::_construct();		
	}
	
    protected function _getLimit(){
		Mage::getSingleton('catalog/session')->unsLimitPage();
		return (isset($_REQUEST['limit'])) ? intval($_REQUEST['limit']) : Mage::getStoreConfig('catalog/frontend/grid_per_page');
	}
	 
	protected function _getPage(){
	  return (isset($_REQUEST['p'])) ? intval($_REQUEST['p']) : 1;
	}
    
	protected function _prepareLayout()
    {
		
        // add Home breadcrumb
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {

            $breadcrumbs->addCrumb('home', array(
                'label' => $this->__('Home'),
                'title' => $this->__('Go to Home Page'),
                'link'  => Mage::getBaseUrl()
            ))->addCrumb('search', array(
                'label' => $this->__('Featured items'),
                'title' => $this->__('Featured items')
            ));
        }
		
        $this->getLayout()->getBlock('head')->setTitle($this->__('Featured items'));
        return parent::_prepareLayout();
    }
	
    protected function _getIsFeaturedProductCollection()
    {
        $collection = Mage::getResourceModel('catalog/product_collection');

        Mage::getModel('catalog/layer')->prepareProductCollection($collection);
        $collection->addStoreFilter();
        $collection->addAttributeToFilter('is_featured', 1)
            ->setPageSize($this->_limit)
            ->setCurPage($this->_p)
            ->load();
        return $collection;
    }
    
	public function setListCollection() {
        $this->getChild('product_list_products')->setCollection($this->_getIsFeaturedProductCollection());
    }    
	public function getTitle(){
		return $this->_title;
	}
    
}