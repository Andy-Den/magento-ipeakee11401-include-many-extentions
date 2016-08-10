<?php
class AHT_Aproduct_Block_Aproduct extends Mage_Catalog_Block_Product_List
{
	
    
    protected $_productsCount = null;
    
    protected $_defaultToolbarBlock = 'catalog/product_list_toolbar';

    const DEFAULT_PRODUCTS_COUNT = 5;
    
    public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
    /**
     * Initialize block's cache
     */
    protected function _construct()
    {
        parent::_construct();
    }

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return array(
           'CATALOG_PRODUCT_NEW',
           Mage::app()->getStore()->getId(),
           Mage::getDesign()->getPackageName(),
           Mage::getDesign()->getTheme('template'),
           Mage::getSingleton('customer/session')->getCustomerGroupId(),
           'template' => $this->getTemplate(),
           $this->getProductsCount()
        );
    }
    
    public function getBestSellerProducts($category = false, $limit)
    {
        
        $visibility = array(
                          Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
                          Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
                      );
                    
                                  
        $storeId    = Mage::app()->getStore()->getId();
        $this->_productCollection = Mage::getResourceModel('reports/product_collection')
             ->addOrderedQty()
             ->addAttributeToSelect('*')
             ->setStoreId($storeId)
             ->addStoreFilter($storeId)
             ->setOrder('ordered_qty', 'desc')
             ->addAttributeToFilter('status', 1)
             ->addAttributeToFilter('visibility', 4)
             ->setPageSize($limit);
         
         if($category) {
            $this->_productCollection = $this->_productCollection->addCategoryFilter($category);
        } 
        return $this->_productCollection;
    }
    
    public function getMostViewedProducts()
    {
        /**
         * Number of products to display
         * You may change it to your desired value
         */
        $productCount = 5;
     
        /**
         * Get Store ID
         */
        $storeId    = Mage::app()->getStore()->getId();      
        
        /**
         * Get most viewed product collection
         */
        $collection = Mage::getResourceModel('reports/product_collection')
            ->addAttributeToSelect('*')
            ->setStoreId($storeId)
            ->addStoreFilter($storeId)
            ->addViewsCount()
            ->setPageSize($productCount);
     
        Mage::getSingleton('catalog/product_status')
                ->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')
                ->addVisibleInCatalogFilterToCollection($collection);
     
        return $collection;
    }
    
    protected function _getIsFeaturedProductCollection($limit)
    {
        $categoryID = $this->getCategoryId();
        if($categoryID)
        {
          $category = new Mage_Catalog_Model_Category();
          $category->load($categoryID); // this is category id
          $collection = $category->getProductCollection();
        } else{
          $collection = Mage::getResourceModel('catalog/product_collection');
        }

        Mage::getModel('catalog/layer')->prepareProductCollection($collection);
        $collection->addStoreFilter()
                   ->addAttributeToSelect('*');
        $collection->addAttributeToFilter('is_featured', 1);
        $collection->setPage(1, $limit)->load();
        return $collection;
    }
    
    protected function _getSpecialWiget()
    {
        $todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());

        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->addAttributeToFilter('special_from_date', array('date' => true, 'to' => $todayDate))
            ->addAttributeToFilter('special_to_date', array('or'=> array(
                0 => array('date' => true, 'from' => $todayDate),
                1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left')
            ->addAttributeToSort('special_from_date', 'desc')
            ->setPageSize($this->getProductsCount())
            ->setCurPage(1)
        ;             
        return $collection;
    }    
    
    protected function _getSpecial()
    {
        $todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        $collection = Mage::getResourceModel('catalog/product_collection');
        Mage::getModel('catalog/layer')->prepareProductCollection($collection);
        $collection->addStoreFilter()
            //->addCategoryFilter($category)
            ->addAttributeToFilter('special_from_date', array('date' => true, 'to' => $todayDate))
            ->addAttributeToFilter('special_to_date', array('or'=> array(
                0 => array('date' => true, 'from' => $todayDate),
                1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left')
            ->addAttributeToSort('special_from_date', 'desc');
            $collection->setPageSize(4)
            ->setCurPage(1)
            ->load();
         
        return $collection;
    }
    
    protected function _getIsHotProductCollection()
    {
        $categoryID = $this->getCategoryId();
        if($categoryID)
        {
          $category = new Mage_Catalog_Model_Category();
          $category->load($categoryID); // this is category id
          $collection = $category->getProductCollection();
        } else{
          $collection = Mage::getResourceModel('catalog/product_collection');
        }
        Mage::getModel('catalog/layer')->prepareProductCollection($collection);
        $collection->addStoreFilter();
        $collection->addAttributeToFilter('is_hot', 1);
        $collection->setPage(1, 4)->load();
        return $collection;
    }
    
    protected function _getIsSaleProductCollection()
    {
	
        $categoryID = $this->getCategoryId();
        if($categoryID)
        {
          $category = new Mage_Catalog_Model_Category();
          $category->load($categoryID); // this is category id
          $collection = $category->getProductCollection();
        } else{
          $collection = Mage::getResourceModel('catalog/product_collection');
        }
        Mage::getModel('catalog/layer')->prepareProductCollection($collection);
        $collection->addStoreFilter();
        $collection->addAttributeToFilter('is_sale', 1);
        $collection->setPage(1, 4)->load();
        return $collection;
    }
    
    protected function _getInCategoryHomeProductCollection($category)
    {
        if($category) {
            $collection = $category->getProductCollection();
        } else {
            $collection = Mage::getResourceModel('catalog/product_collection');
        }
        
        Mage::getModel('catalog/layer')->prepareProductCollection($collection);
        $collection->addStoreFilter();
        $collection->addAttributeToFilter('in_category_home', 1);
        $collection->setPage(1, 4)->load();
        return $collection;
    }
    
    protected function _getIsNewProductCollection()
    {
        $todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());

        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->addAttributeToFilter('news_from_date', array('or'=> array(
                0 => array('date' => true, 'to' => $todayDate),
                1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left')
            ->addAttributeToFilter('news_to_date', array('or'=> array(
                0 => array('date' => true, 'from' => $todayDate),
                1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left')
            ->addAttributeToFilter(
                array(
                    array('attribute' => 'news_from_date', 'is'=>new Zend_Db_Expr('not null')),
                    array('attribute' => 'news_to_date', 'is'=>new Zend_Db_Expr('not null'))
                    )
              )
            ->addAttributeToSort('news_from_date', 'desc')
            ->setPageSize($this->getProductsCount())
            ->setCurPage(1)
        ;
        return $collection;
    }
    /*
    public function setProductsCount($count)
    {
        $this->_productsCount = $count;
        return $this;
    }

    /**
     * Get how much products should be displayed at once.
     *
     * @return int
     */
    public function getProductsCount()
    {
        if (null === $this->_productsCount) {
            $this->_productsCount = self::DEFAULT_PRODUCTS_COUNT;
        }
        return $this->_productsCount;
    }
	
	protected function _getIsClearanceProductCollection($limit)
    {
        $categoryID = $this->getCategoryId();
        if($categoryID)
        {
          $category = new Mage_Catalog_Model_Category();
          $category->load($categoryID); // this is category id
          $collection = $category->getProductCollection();
        } else{
          $collection = Mage::getResourceModel('catalog/product_collection');
        }

        Mage::getModel('catalog/layer')->prepareProductCollection($collection);
        $collection->addStoreFilter()
                   ->addAttributeToSelect('*');
        $collection->addAttributeToFilter('is_clearance', 1);
        $collection->setPage(1, $limit)->load();
        return $collection;
    }
		
}
