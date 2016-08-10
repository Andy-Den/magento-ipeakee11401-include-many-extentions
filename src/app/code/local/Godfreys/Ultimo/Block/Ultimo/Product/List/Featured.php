<?php
class Godfreys_Ultimo_Block_Ultimo_Product_List_Featured extends Infortis_Ultimo_Block_Product_List_Featured
{
    protected function _construct()
    {
        parent::_construct();

        $this->addData(array(
            'cache_lifetime'    => 0,
            'cache_tags'        => array(Mage_Catalog_Model_Product::CACHE_TAG),
        ));
    }

    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection))
        {
            $categoryID = $this->getCategoryId();
            if($categoryID)
            {
                $category = new Mage_Catalog_Model_Category();
                $category->load($categoryID);
                $collection = $category->getProductCollection();
            }
            else
            {
                $collection = Mage::getResourceModel('catalog/product_collection');
            }

            // Godfreys custom attributes
            $collection->addAttributeToSelect(array(
                'shoutout',
                'online_only',
            ));

            Mage::getModel('catalog/layer')->prepareProductCollection($collection);

            if ($this->getIsRandom())
            {
                $collection->getSelect()->order('rand()');
            }
            $collection->addStoreFilter();
            $collection->addOrder('position', 'asc');
            $productCount = $this->getProductCount() ? $this->getProductCount() : 8;
            $collection->setPage(1, $productCount)
                ->load();

            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }
}

