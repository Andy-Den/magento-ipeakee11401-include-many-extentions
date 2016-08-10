<?php
class Godfreys_ProductNew_Block_Catalog_Product_New extends Mage_Catalog_Block_Product_New
{
    protected function _construct()
    {
        //parent::_construct();
        $this->addColumnCountLayoutDepend('empty', 6)
            ->addColumnCountLayoutDepend('one_column', 5)
            ->addColumnCountLayoutDepend('two_columns_left', 4)
            ->addColumnCountLayoutDepend('two_columns_right', 4)
            ->addColumnCountLayoutDepend('three_columns', 3);

        $this->addData(array('cache_lifetime' => 0));
        $this->addCacheTag(Mage_Catalog_Model_Product::CACHE_TAG);
    }


    public function getCacheKeyInfo()
    {
        return array(
            'CATALOG_PRODUCT_NEW',
            Mage::app()->getStore()->getId(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            Mage::getSingleton('customer/session')->getCustomerGroupId(),
            'template' => $this->getTemplate(),
            $this->getProductsCount(),
            $this->getData('sku')
        );
    }

}
			