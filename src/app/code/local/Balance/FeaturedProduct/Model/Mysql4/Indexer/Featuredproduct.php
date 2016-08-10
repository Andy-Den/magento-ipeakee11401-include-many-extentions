<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Resource model for category product indexer
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Balance_FeaturedProduct_Model_Mysql4_Indexer_Featuredproduct extends Mage_Catalog_Model_Resource_Category_Indexer_Product
{
   
    protected function _construct()
    {        
        $this->_init('featuredproduct/featuredproduct_index', 'category_id');
        $this->_categoryProductTable = $this->getTable('featuredproduct/featuredproduct');
        $this->_categoryTable        = $this->getTable('catalog/category');
        $this->_productWebsiteTable  = $this->getTable('catalog/product_website');
        $this->_storeTable           = $this->getTable('core/store');
        $this->_groupTable           = $this->getTable('core/store_group');
        
    }

    /**
     * Process product save.
     * Method is responsible for index support
     * when product was saved and assigned categories was changed.
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_Catalog_Model_Resource_Category_Indexer_Product
     */
    public function catalogProductSave(Mage_Index_Model_Event $event)
    {
        $productId = $event->getEntityPk();
        $data      = $event->getNewData();
       
        /**
         * Select relations to categories
         */
        $select = $this->_getWriteAdapter()->select()
            ->from(array('cp' => $this->_categoryProductTable), 'category_id')
            ->joinInner(array('ce' => $this->_categoryTable), 'ce.entity_id=cp.category_id')
            ->where('cp.product_id=:product_id');

        /**
         * Get information about product categories
         */
        $categoryIds = $this->_getWriteAdapter()->fetchCol($select,array('product_id' => $productId));
        
        /**
         * Delete previous index data
         */
        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            array('product_id = ?' => $productId)
        );      
        $this->_refreshDirectRelations($categoryIds, $productId);       
        return $this;
    }

    /**
     * Process Catalog Product mass action
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_Catalog_Model_Resource_Category_Indexer_Product
     */
    public function catalogProductMassAction(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();

        /**
         * check is product ids were updated
         */
        if (!isset($data['product_ids'])) {
            return $this;
        }
        $productIds     = $data['product_ids'];
        $categoryIds    = array();
       
        /**
         * Select relations to categories
         */
        $adapter = $this->_getWriteAdapter();
        $select  = $adapter->select()
            ->distinct(true)
            ->from(array('cp' => $this->_categoryProductTable), array('category_id'))
            ->joinInner(array('ce' => $this->_categoryTable), 'ce.entity_id=cp.category_id')
            ->where('cp.product_id IN(?)', $productIds);
        $categoryIds  = $adapter->fetchCol($select);        
        /**
         * Delete previous index data
         */
        $this->_getWriteAdapter()->delete(
            $this->getMainTable(), array('product_id IN(?)' => $productIds)
        );        
        $this->_refreshDirectRelations($categoryIds, $productIds);        
        return $this;
    }
    
    /**
     * Process category index after category save
     *
     * @param Mage_Index_Model_Event $event
     */
    public function catalogCategorySave(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
      
        /**
         * Check if we have reindex category move results
         */
        if (isset($data['affected_category_ids'])) {
            $categoryIds = $data['affected_category_ids'];        
        } 
        else if (isset($data['featuredproducts_was_changed'])) {
            $categoryIds = array($event->getEntityPk());            
        } else {
            return;
        }
     
               
        if ($categoryIds) {
           /**
            * delete category ids
           */    
           $this->_getWriteAdapter()->delete(
               $this->getMainTable(),
               $this->_getWriteAdapter()->quoteInto('category_id IN(?)', $categoryIds)
           );
            
            $this->_refreshDirectRelations($categoryIds);
        }      
    }

  

    /**
     * Rebuild index for direct associations categories and products
     *
     * @param null|array $categoryIds
     * @param null|array $productIds
     * @return Mage_Catalog_Model_Resource_Category_Indexer_Product
     */
    protected function _refreshDirectRelations($categoryIds = null, $productIds = null)
    {
        if (!$categoryIds && !$productIds) {
            return $this;
        }

        $visibilityInfo = $this->_getVisibilityAttributeInfo();
        $statusInfo     = $this->_getStatusAttributeInfo();
        $adapter = $this->_getWriteAdapter();
        /**
         * Insert direct relations
         * product_ids (enabled filter) X category_ids X store_ids
         * Validate store root category
         */
        
        $select = $adapter->select()
            ->from(array('cp' => $this->_categoryProductTable),
                array('category_id', 'product_id', 'position'))
            ->joinInner(array('pw'  => $this->_productWebsiteTable), 'pw.product_id=cp.product_id', array())
            ->joinInner(array('g'   => $this->_groupTable), 'g.website_id=pw.website_id', array())
            ->joinInner(array('s'   => $this->_storeTable), 's.group_id=g.group_id', array('store_id'))            
            ->joinLeft(
                array('dv'=>$visibilityInfo['table']),
                $adapter->quoteInto(
                    "dv.entity_id=cp.product_id AND dv.attribute_id=? AND dv.store_id=0",
                    $visibilityInfo['id']),
                array()
            )
            ->joinLeft(
                array('sv'=>$visibilityInfo['table']),
                $adapter->quoteInto(
                    "sv.entity_id=cp.product_id AND sv.attribute_id=? AND sv.store_id=s.store_id",
                    $visibilityInfo['id']),
                array('visibility' => $adapter->getCheckSql('sv.value_id IS NOT NULL',
                    $adapter->quoteIdentifier('sv.value'),
                    $adapter->quoteIdentifier('dv.value')
                ))
            )
            ->joinLeft(
                array('ds'=>$statusInfo['table']),
                "ds.entity_id=cp.product_id AND ds.attribute_id={$statusInfo['id']} AND ds.store_id=0",
                array())
            ->joinLeft(
                array('ss'=>$statusInfo['table']),
                "ss.entity_id=cp.product_id AND ss.attribute_id={$statusInfo['id']} AND ss.store_id=s.store_id",
                array())
            ->where(
                $adapter->getCheckSql('ss.value_id IS NOT NULL',
                    $adapter->quoteIdentifier('ss.value'),
                    $adapter->quoteIdentifier('ds.value')
                ) . ' = ?',
                Mage_Catalog_Model_Product_Status::STATUS_ENABLED
            );
        if ($categoryIds) {
            $select->where('cp.category_id IN (?)', $categoryIds);
        }
        if ($productIds) {
            $select->where('cp.product_id IN(?)', $productIds);
        }
        $sql = $select->insertFromSelect(
            $this->getMainTable(),
            array('category_id', 'product_id', 'position', 'store_id', 'visibility'),
            true
        );
        $adapter->query($sql);
        return $this;
    }


    /**
     * Rebuild all index data
     *
     * @return Mage_Catalog_Model_Resource_Category_Indexer_Product
     */
    public function reindexAll()
    {
        $this->useIdxTable(true);
        $this->beginTransaction();
        try {
            $this->clearTemporaryIndexTable();
            $idxTable = $this->getIdxTable();
            $idxAdapter = $this->_getIndexAdapter();
            $stores = $this->_getStoresInfo();
            /**
             * Build index for each store
             */
            foreach ($stores as $storeData) {
                $storeId    = $storeData['store_id'];
                $websiteId  = $storeData['website_id'];
                /**
                 * Prepare visibility for all enabled store products
                 */
                $enabledTable = $this->_prepareEnabledProductsVisibility($websiteId, $storeId);
                
                /**
                 * Add relations between not anchor categories and products
                 */
                $select = $idxAdapter->select();
                /** @var $select Varien_Db_Select */
                $select->from(
                    array('cp' => $this->_categoryProductTable),
                    array('category_id', 'product_id', 'position',
                        'store_id' => new Zend_Db_Expr($storeId))
                )
                ->joinInner(array('pv' => $enabledTable), 'pv.product_id=cp.product_id', array('visibility'));
               
                $query = $select->insertFromSelect(
                    $idxTable,
                    array('category_id', 'product_id', 'position', 'store_id', 'visibility'),
                    false
                );
                $idxAdapter->query($query);
                                             
            }

            $this->syncData();
            /**
             * Clean up temporary tables
             */
            $this->clearTemporaryIndexTable();
            $idxAdapter->delete($enabledTable);           
            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
        return $this;
    }


  /**
     * Copy data from source table of read adapter to destination table of index adapter
     *
     * @param string $sourceTable
     * @param string $destTable
     * @param bool $readToIndex data migration direction (true - read=>index, false - index=>read)
     * @return Mage_Index_Model_Resource_Abstract
     */
    public function insertFromTable($sourceTable, $destTable, $readToIndex = true)
    {        
        $columns =  array('category_id', 'product_id', 'position', 'store_id', 'visibility');        
        $select = $this->_getIndexAdapter()->select()->from($sourceTable, $columns);
        Mage::getResourceHelper('index')->insertData($this, $select, $destTable, $columns, $readToIndex);
        return $this;
    }


}
