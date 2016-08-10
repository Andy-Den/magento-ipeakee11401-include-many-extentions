<?php
/**
 * Innoexts
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the InnoExts Commercial License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://innoexts.com/commercial-license-agreement
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@innoexts.com so we can send you a copy immediately.
 * 
 * @category    Innoexts
 * @package     Innoexts_Warehouse
 * @copyright   Copyright (c) 2011 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

/**
 * Default product type price indexer resource
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Resource_Eav_Mysql4_Catalog_Product_Indexer_Price_Default 
    extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Default
{
    /**
     * Get warehouse helper
     *
     * @return  Innoexts_Warehouse_Helper_Data
     */
    protected function getWarehouseHelper()
    {
        return Mage::helper('warehouse');
    }
    /**
     * Get warehouse config
     * 
     * @return Innoexts_Warehouse_Model_Config
     */
    protected function getWarehouseConfig()
    {
        return $this->getWarehouseHelper()->getConfig();
    }
    /**
     * Get version helper
     * 
     * @return Innoexts_InnoCore_Helper_Version
     */
    protected function getVersionHelper()
    {
        return Mage::helper('innocore')->getVersionHelper();
    }
    /**
     * Prepare products default final price in temporary index table
     *
     * @param int|array $entityIds  the entity ids limitation
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Default
     */
    protected function _prepareFinalPriceData($entityIds = null)
    {
        $this->_prepareDefaultFinalPriceTable();
        $write  = $this->_getWriteAdapter();
        $select = $write->select()
            ->from(array('e' => $this->getTable('catalog/product')), array('entity_id'))
            ->join(array('cg' => $this->getTable('customer/customer_group')), '', array('customer_group_id'))
            ->join(array('cw' => $this->getTable('core/website')), '', array('website_id'))
            ->join(array('cwd' => $this->_getWebsiteDateTable()), 'cw.website_id = cwd.website_id', array())
            ->join(array('csg' => $this->getTable('core/store_group')), 
                'csg.website_id = cw.website_id AND cw.default_group_id = csg.group_id', array())
            ->join(array('cs' => $this->getTable('core/store')),
                'csg.default_store_id = cs.store_id AND cs.store_id != 0', array())
            ->join(array('pw' => $this->getTable('catalog/product_website')),
                'pw.product_id = e.entity_id AND pw.website_id = cw.website_id', array())
            ->joinLeft(array('tp' => $this->_getTierPriceIndexTable()), '(tp.entity_id = e.entity_id) AND '.
            	'(tp.website_id = cw.website_id) AND (tp.customer_group_id = cg.customer_group_id)', array())
           	->joinLeft(
            			array('gp' => $this->_getGroupPriceIndexTable()),
            			'gp.entity_id = e.entity_id AND gp.website_id = cw.website_id'
            			. ' AND gp.customer_group_id = cg.customer_group_id',
            			array())
            ->where('e.type_id=?', $this->getTypeId());
        $statusCond = $write->quoteInto('=?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        $this->_addAttributeToSelect($select, 'status', 'e.entity_id', 'cs.store_id', $statusCond, true);
        if ($this->getVersionHelper()->isGe1600()) {
            if (Mage::helper('core')->isModuleEnabled('Mage_Tax')) {
                $taxClassId = $this->_addAttributeToSelect($select, 'tax_class_id', 'e.entity_id', 'cs.store_id');
            } else {
                $taxClassId = new Zend_Db_Expr('0');
            }
        } else {
            $taxClassId = $this->_addAttributeToSelect($select, 'tax_class_id', 'e.entity_id', 'cs.store_id');
        }
        $select->columns(array('tax_class_id' => $taxClassId));
        $price          = $this->_addAttributeToSelect($select, 'price', 'e.entity_id', 'cs.store_id');
        $specialPrice   = $this->_addAttributeToSelect($select, 'special_price', 'e.entity_id', 'cs.store_id');
        $specialFrom    = $this->_addAttributeToSelect($select, 'special_from_date', 'e.entity_id', 'cs.store_id');
        $specialTo      = $this->_addAttributeToSelect($select, 'special_to_date', 'e.entity_id', 'cs.store_id');
        if ($this->getVersionHelper()->isGe1600()) {
            $currentDate    = $write->getDatePartSql('cwd.website_date');
            $groupPrice     = $write->getCheckSql('gp.price IS NULL', "{$price}", 'gp.price');
            $specialFromDate    = $write->getDatePartSql($specialFrom);
            $specialToDate      = $write->getDatePartSql($specialTo);
            $specialFromUse     = $write->getCheckSql("{$specialFromDate} <= {$currentDate}", '1', '0');
            $specialToUse       = $write->getCheckSql("{$specialToDate} >= {$currentDate}", '1', '0');
            $specialFromHas     = $write->getCheckSql("{$specialFrom} IS NULL", '1', "{$specialFromUse}");
            $specialToHas       = $write->getCheckSql("{$specialTo} IS NULL", '1', "{$specialToUse}");
            $finalPrice         = $write->getCheckSql("{$specialFromHas} > 0 AND {$specialToHas} > 0"
                . " AND {$specialPrice} < {$price}", $specialPrice, $price);
        } else {
            $curentDate     = new Zend_Db_Expr('cwd.date');
            $finalPrice     = new Zend_Db_Expr("IF(IF({$specialFrom} IS NULL, 1, "
                . "IF(DATE({$specialFrom}) <= {$curentDate}, 1, 0)) > 0 AND IF({$specialTo} IS NULL, 1, "
                . "IF(DATE({$specialTo}) >= {$curentDate}, 1, 0)) > 0 AND {$specialPrice} < {$price}, "
                . "{$specialPrice}, {$price})");
            $groupPrice     = $write->getCheckSql('gp.price IS NULL', "{$price}", 'gp.price');
        }
        $select->columns(array(
            'orig_price'    => $price, 
            'price'         => $finalPrice, 
            'min_price'     => $finalPrice, 
            'max_price'     => $finalPrice, 
            'tier_price'    => new Zend_Db_Expr('tp.min_price'), 
            'base_tier'     => new Zend_Db_Expr('tp.min_price'), 
        ));
        if (!is_null($entityIds)) {
            $select->where('e.entity_id IN(?)', $entityIds);
        }
        $eventData = array(
            'select'        => $select,
            'entity_field'  => new Zend_Db_Expr('e.entity_id'),
            'website_field' => new Zend_Db_Expr('cw.website_id'),
            'store_field'   => new Zend_Db_Expr('cs.store_id'), 
        );
        
        if ($this->getWarehouseConfig()->isMultipleMode()) {
            $select->columns(array('stock_id' => new Zend_Db_Expr($this->getWarehouseHelper()->getDefaultStockId())));
        } else {
            $select->join(array('cis' => $this->getTable('cataloginventory/stock')), '', array('stock_id'));
            $eventData['stock_field'] = new Zend_Db_Expr('cis.stock_id');
        }
        
        //JING: column position does matter in 'INSERT SELECT' 
        $select->columns(array(
        	'group_price'      => $groupPrice,
       		'base_group_price' => $groupPrice,
   		));

        Mage::dispatchEvent('prepare_catalog_product_index_select', $eventData);
        $query = $select->insertFromSelect($this->_getDefaultFinalPriceTable());
        $write->query($query);
        $select = $write->select()->join(array('wd' => $this->_getWebsiteDateTable()), 'i.website_id = wd.website_id', array());
        
        $parameters = array(
            'index_table'       => array('i' => $this->_getDefaultFinalPriceTable()), 
            'select'            => $select, 
            'entity_id'         => 'i.entity_id', 
            'customer_group_id' => 'i.customer_group_id', 
            'website_id'        => 'i.website_id', 
            'stock_id'          => 'i.stock_id', 
            'update_fields'     => array('price', 'min_price', 'max_price'), 
        );
        if ($this->getVersionHelper()->isGe1600()) {
            $parameters['website_date'] = 'wd.website_date';
        } else {
            $parameters['website_date'] = 'wd.date';
        }
        Mage::dispatchEvent('prepare_catalog_product_price_index_table', $parameters);
        return $this;
    }
    /**
     * Apply custom option minimal and maximal price to temporary final price index table
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Default
     */
    protected function _applyCustomOption()
    {
        $write      = $this->_getWriteAdapter();
        $coaTable   = $this->_getCustomOptionAggregateTable();
        $copTable   = $this->_getCustomOptionPriceTable();
        $this->_prepareCustomOptionAggregateTable();
        $this->_prepareCustomOptionPriceTable();
        $select = $write->select()
            ->from(array('i' => $this->_getDefaultFinalPriceTable()), array('entity_id', 'customer_group_id', 'website_id'))
            ->join(array('cw' => $this->getTable('core/website')), 'cw.website_id = i.website_id', array())
            ->join(array('csg' => $this->getTable('core/store_group')), 'csg.group_id = cw.default_group_id', array())
            ->join(array('cs' => $this->getTable('core/store')), 'cs.store_id = csg.default_store_id', array())
            ->join(array('o' => $this->getTable('catalog/product_option')), 'o.product_id = i.entity_id', array('option_id'))
            ->join(array('ot' => $this->getTable('catalog/product_option_type_value')), 'ot.option_id = o.option_id', array())
            ->join(array('otpd' => $this->getTable('catalog/product_option_type_price')), 
                'otpd.option_type_id = ot.option_type_id AND otpd.store_id = 0',array())
            ->joinLeft(
                array('otps' => $this->getTable('catalog/product_option_type_price')), 
                'otps.option_type_id = otpd.option_type_id AND otpd.store_id = cs.store_id', array())
            ->joinLeft(
                		array('gp' => $this->_getGroupPriceIndexTable()),
                		'gp.entity_id = i.entity_id AND gp.website_id = cw.website_id'
                		. ' AND gp.customer_group_id = i.customer_group_id',
                		array())
                
            ->group(array('i.entity_id', 'i.customer_group_id', 'i.website_id', 'o.option_id', 'i.stock_id'));
        if ($this->getVersionHelper()->isGe1600()) {
        	
            $optPriceType   = $write->getCheckSql('otps.option_type_price_id > 0', 'otps.price_type', 'otpd.price_type');
            $optPriceValue  = $write->getCheckSql('otps.option_type_price_id > 0', 'otps.price', 'otpd.price');
            $minPriceRound  = new Zend_Db_Expr("ROUND(i.price * ({$optPriceValue} / 100), 4)");
            $minPriceExpr   = $write->getCheckSql("{$optPriceType} = 'fixed'", $optPriceValue, $minPriceRound);
            $minPriceMin    = new Zend_Db_Expr("MIN({$minPriceExpr})");
            $minPrice       = $write->getCheckSql("MIN(o.is_require) = 1", $minPriceMin, '0');
            $tierPriceRound = new Zend_Db_Expr("ROUND(i.base_tier * ({$optPriceValue} / 100), 4)");
            $tierPriceExpr  = $write->getCheckSql("{$optPriceType} = 'fixed'", $optPriceValue, $tierPriceRound);
            $tierPriceMin   = new Zend_Db_Expr("MIN($tierPriceExpr)");
            $tierPriceValue = $write->getCheckSql("MIN(o.is_require) > 0", $tierPriceMin, 0);
            $tierPrice      = $write->getCheckSql("MIN(i.base_tier) IS NOT NULL", $tierPriceValue, "NULL");
            $maxPriceRound  = new Zend_Db_Expr("ROUND(i.price * ({$optPriceValue} / 100), 4)");
            $maxPriceExpr   = $write->getCheckSql("{$optPriceType} = 'fixed'", $optPriceValue, $maxPriceRound);
            $maxPrice       = $write->getCheckSql("(MIN(o.type)='radio' OR MIN(o.type)='drop_down')",
                				"MAX($maxPriceExpr)", "SUM($maxPriceExpr)");
        } else {
            $minPrice = new Zend_Db_Expr("IF(o.is_require, MIN(IF(IF(otps.option_type_price_id>0, otps.price_type, "
                . "otpd.price_type)='fixed', IF(otps.option_type_price_id>0, otps.price, otpd.price), "
                . "ROUND(i.price * (IF(otps.option_type_price_id>0, otps.price, otpd.price) / 100), 4))), 0)");
            $tierPrice = new Zend_Db_Expr("IF(i.base_tier IS NOT NULL, IF(o.is_require, "
                . "MIN(IF(IF(otps.option_type_price_id>0, otps.price_type, otpd.price_type)='fixed', "
                . "IF(otps.option_type_price_id>0, otps.price, otpd.price), "
                . "ROUND(i.base_tier * (IF(otps.option_type_price_id>0, otps.price, otpd.price) / 100), 4))), 0), NULL)");
            $maxPrice = new Zend_Db_Expr("IF((o.type='radio' OR o.type='drop_down'), "
                . "MAX(IF(IF(otps.option_type_price_id>0, otps.price_type, otpd.price_type)='fixed', "
                . "IF(otps.option_type_price_id>0, otps.price, otpd.price), "
                . "ROUND(i.price * (IF(otps.option_type_price_id>0, otps.price, otpd.price) / 100), 4))), "
                . "SUM(IF(IF(otps.option_type_price_id>0, otps.price_type, otpd.price_type)='fixed', "
                . "IF(otps.option_type_price_id>0, otps.price, otpd.price), "
                . "ROUND(i.price * (IF(otps.option_type_price_id>0, otps.price, otpd.price) / 100), 4))))");
        }
        $groupPrice     = $write->getCheckSql('gp.price IS NULL', "{$maxPrice}", 'gp.price');
        $select->columns(array(
            'min_price'   => $minPrice, 
            'max_price'   => $maxPrice, 
            'tier_price'  => $tierPrice, 
            'stock_id'    => 'i.stock_id',
        	'group_price' => $groupPrice, 
        ));
        $query = $select->insertFromSelect($coaTable);
        $write->query($query);
        $select = $write->select()
            ->from(array('i' => $this->_getDefaultFinalPriceTable()), array('entity_id', 'customer_group_id', 'website_id'))
            ->join(array('cw' => $this->getTable('core/website')), 'cw.website_id = i.website_id', array())
            ->join(array('csg' => $this->getTable('core/store_group')), 'csg.group_id = cw.default_group_id', array())
            ->join(array('cs' => $this->getTable('core/store')), 'cs.store_id = csg.default_store_id', array())
            ->join(array('o' => $this->getTable('catalog/product_option')), 'o.product_id = i.entity_id', array('option_id'))
            ->join(array('opd' => $this->getTable('catalog/product_option_price')), 'opd.option_id = o.option_id AND opd.store_id = 0', array())
            ->joinLeft(array('ops' => $this->getTable('catalog/product_option_price')), 
                'ops.option_id = opd.option_id AND ops.store_id = cs.store_id', array())
            ->joinLeft(
                		array('gp' => $this->_getGroupPriceIndexTable()),
                		'gp.entity_id = i.entity_id AND gp.website_id = cw.website_id'
                		. ' AND gp.customer_group_id = i.customer_group_id',
                		array())
        ;
        if ($this->getVersionHelper()->isGe1600()) {
            $optPriceType   = $write->getCheckSql('ops.option_price_id > 0', 'ops.price_type', 'opd.price_type');
            $optPriceValue  = $write->getCheckSql('ops.option_price_id > 0', 'ops.price', 'opd.price');
            $minPriceRound  = new Zend_Db_Expr("ROUND(i.price * ({$optPriceValue} / 100), 4)");
            $priceExpr      = $write->getCheckSql("{$optPriceType} = 'fixed'", $optPriceValue, $minPriceRound);
            $minPrice       = $write->getCheckSql("{$priceExpr} > 0 AND o.is_require > 1", $priceExpr, 0);
            $maxPrice       = $priceExpr;
            $tierPriceRound = new Zend_Db_Expr("ROUND(i.base_tier * ({$optPriceValue} / 100), 4)");
            $tierPriceExpr  = $write->getCheckSql("{$optPriceType} = 'fixed'", $optPriceValue, $tierPriceRound);
            $tierPriceValue = $write->getCheckSql("{$tierPriceExpr} > 0 AND o.is_require > 0", $tierPriceExpr, 0);
            $tierPrice      = $write->getCheckSql("i.base_tier IS NOT NULL", $tierPriceValue, "NULL");
        } else {
            $minPrice = new Zend_Db_Expr("IF((@price:=IF(IF(ops.option_price_id>0, ops.price_type, opd.price_type)='fixed',"
                . " IF(ops.option_price_id>0, ops.price, opd.price), ROUND(i.price * (IF(ops.option_price_id>0, "
                . "ops.price, opd.price) / 100), 4))) AND o.is_require, @price,0)");
            $maxPrice = new Zend_Db_Expr("@price");
            $tierPrice = new Zend_Db_Expr("IF(i.base_tier IS NOT NULL, IF((@tier_price:=IF(IF(ops.option_price_id>0, "
                . "ops.price_type, opd.price_type)='fixed', IF(ops.option_price_id>0, ops.price, opd.price), "
                . "ROUND(i.base_tier * (IF(ops.option_price_id>0, ops.price, opd.price) / 100), 4))) AND o.is_require, "
                . "@tier_price, 0), NULL)");
        }
        $groupPrice     = $write->getCheckSql('gp.price IS NULL', "{$maxPrice}", 'gp.price');
        $select->columns(array(
            'min_price' => $minPrice, 
            'max_price' => $maxPrice, 
            'tier_price' => $tierPrice, 
            'stock_id' => 'i.stock_id',
        	'group_price' => $groupPrice,
        ));
        $query = $select->insertFromSelect($coaTable);
        $write->query($query);
        $select = $write->select()
            ->from(array($coaTable), array('entity_id', 'customer_group_id', 'website_id',
                'min_price' => 'SUM(min_price)', 'max_price' => 'SUM(max_price)', 'tier_price' => 'SUM(tier_price)', 'stock_id', 'group_price' => 'MIN(group_price)'))
            ->group(array('entity_id', 'customer_group_id', 'website_id', 'stock_id'));
        $query = $select->insertFromSelect($copTable);
        $write->query($query);
        $table  = array('i' => $this->_getDefaultFinalPriceTable());
        $select = $write->select()
            ->join(array('io' => $copTable), '(i.entity_id = io.entity_id) AND (i.customer_group_id = io.customer_group_id) AND '.
                '(i.website_id = io.website_id) AND (i.stock_id = io.stock_id)', array());
        if ($this->getVersionHelper()->isGe1600()) {
            $tierPrice = $write->getCheckSql('i.tier_price IS NOT NULL', 'i.tier_price + io.tier_price', 'NULL');
        } else {
            $tierPrice = new Zend_Db_Expr('IF(i.tier_price IS NOT NULL, i.tier_price + io.tier_price, NULL)');
        }
        $select->columns(array(
            'min_price'  => new Zend_Db_Expr('i.min_price + io.min_price'),
            'max_price'  => new Zend_Db_Expr('i.max_price + io.max_price'),
            'tier_price' => $tierPrice,
        ));
        $query = $select->crossUpdateFromSelect($table);
        $write->query($query);
        if ($this->useIdxTable()) {
            $write->truncate($coaTable);
            $write->truncate($copTable);
        } else {
            $write->delete($coaTable);
            $write->delete($copTable);
        }
        return $this;
    }
    /**
     * Mode Final Prices index to primary temporary index table
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Default
     */
    protected function _movePriceDataToIndexTable()
    {
        $columns = array(
            'entity_id'         => 'entity_id', 
            'customer_group_id' => 'customer_group_id', 
            'website_id'        => 'website_id', 
            'tax_class_id'      => 'tax_class_id', 
            'price'             => 'orig_price', 
            'final_price'       => 'price', 
            'min_price'         => 'min_price', 
            'max_price'         => 'max_price', 
            'tier_price'        => 'tier_price', 
            'stock_id'			=> 'stock_id',
        	'group_price'		=> 'group_price', 
        );
        $write  = $this->_getWriteAdapter();
        $table  = $this->_getDefaultFinalPriceTable();
        $select = $write->select()->from($table, $columns);
        $query = $select->insertFromSelect($this->getIdxTable());
        $write->query($query);
        if ($this->useIdxTable()) {
            $write->truncate($table);
        } else {
            $write->delete($table);
        }
        return $this;
    }
    /*
ALTER TABLE `catalog_product_index_price` DROP FOREIGN KEY `FK_CATALOG_PRODUCT_INDEX_PRICE_STOCK`;

ALTER TABLE `catalog_product_index_price` ADD CONSTRAINT `FK_CAT_PRD_IDX_PRICE_CSTR_GROUP_ID_CSTR_GROUP_CSTR_GROUP_ID` FOREIGN KEY ('customer_group_id') REFERENCES `customer_group` (`customer_group_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `catalog_product_index_price` ADD CONSTRAINT `FK_CAT_PRD_IDX_PRICE_ENTT_ID_CAT_PRD_ENTT_ENTT_ID` FOREIGN KEY ('entity_id') REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `catalog_product_index_price` ADD CONSTRAINT `FK_CAT_PRD_IDX_PRICE_WS_ID_CORE_WS_WS_ID` FOREIGN KEY ('website_id') REFERENCES `core_website` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE;
 
     */
}