<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Balance_FeaturedProduct_Model_Mysql4_Category extends Mage_Catalog_Model_Resource_Category{
    
    /**
     * Catalog products table name
     *
     * @var string
     */
    protected $_featuredProductTable;
    
     /**
     * Class constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->_featuredProductTable = $this->getTable('featuredproduct/featuredproduct');
    }
    
    /**
     * Process category data after save category object
     * save related products ids and update path value
     *
     * @param Varien_Object $object
     * @return Mage_Catalog_Model_Resource_Category
     */
    
    protected function _afterSave(Varien_Object $object)
    {
        parent::_afterSave($object);
        $this->_saveFeaturedProducts($object);
    }
    
    /* Save category products relation
     *
     * @param Mage_Catalog_Model_Category $category
     * @return Mage_Catalog_Model_Resource_Category
     */
    protected function _saveFeaturedProducts($category)
    {
        $category->setIsChangedFeaturedProductList(false);
        $id = $category->getId();
        /**
         * new category-product relationships
         */
        $products = $category->getPostedFeaturedProducts();

        /**
         * Example re-save category
         */
        if ($products === null) {
            return $this;
        }

        /**
         * old category-product relationships
         */
        $oldProducts = $category->getFeaturedProductsPosition();

        $insert = array_diff_key($products, $oldProducts);
        $delete = array_diff_key($oldProducts, $products);

        /**
         * Find product ids which are presented in both arrays
         * and saved before (check $oldProducts array)
         */
        $update = array_intersect_key($products, $oldProducts);
        $update = array_diff_assoc($update, $oldProducts);

        $adapter = $this->_getWriteAdapter();

        /**
         * Delete products from category
         */
        if (!empty($delete)) {
            $cond = array(
                'product_id IN(?)' => array_keys($delete),
                'category_id=?' => $id
            );
            $adapter->delete($this->_featuredProductTable, $cond);
        }

        /**
         * Add products to category
         */
        if (!empty($insert)) {
            $data = array();
            foreach ($insert as $productId => $position) {
                $data[] = array(
                    'category_id' => (int)$id,
                    'product_id'  => (int)$productId,
                    'position'    => (int)$position
                );
            }
            $adapter->insertMultiple($this->_featuredProductTable, $data);
        }

        /**
         * Update product positions in category
         */
        if (!empty($update)) {
            foreach ($update as $productId => $position) {
                $where = array(
                    'category_id = ?'=> (int)$id,
                    'product_id = ?' => (int)$productId
                );
                $bind  = array('position' => (int)$position);
                $adapter->update($this->_featuredProductTable, $bind, $where);
            }
        }
     

        if (!empty($insert) || !empty($update) || !empty($delete)) {
            $category->setIsChangedFeaturedProductList(true);

            /**
             * Setting affected products to category for third party engine index refresh
             */
            $productIds = array_keys($insert + $delete + $update);
            $category->setAffectedFeaturedProductIds($productIds);
        }
        return $this;
    }
    
     /**
     * Get positions of associated to category products
     *
     * @param Mage_Catalog_Model_Category $category
     * @return array
     */
    public function getFeaturedProductsPosition($category)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from($this->_featuredProductTable, array('product_id', 'position'))
            ->where('category_id = :category_id');
        $bind = array('category_id' => (int)$category->getId());

        return $this->_getWriteAdapter()->fetchPairs($select, $bind);
    }
    
}
