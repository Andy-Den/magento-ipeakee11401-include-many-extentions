<?php
$installer = $this;
$installer->startSetup();
/**
 * Create table 'featuredproduct/featuredproduct'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('featuredproduct/featuredproduct'))
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Category ID')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Product ID')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Position')
    ->addIndex($installer->getIdxName('featuredproduct/featuredproduct', array('product_id')),
        array('product_id'))
    ->addForeignKey($installer->getFkName('featuredproduct/featuredproduct', 'category_id', 'catalog/category', 'entity_id'),
        'category_id', $installer->getTable('catalog/category'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('featuredproduct/featuredproduct', 'product_id', 'catalog/product', 'entity_id'),
        'product_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Featured Product To Category Linkage Table');
$installer->getConnection()->createTable($table);


/**
 * Create table 'featuredproduct/featuredproduct_index
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('featuredproduct/featuredproduct_index'))
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    	'default'   => '0',
    ), 'Category ID')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    	'primary'   => true,
    	'default'   => '0',
    ), 'Product ID')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    	'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Position')
   ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Store ID')     
   ->addColumn('visibility', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
     ), 'Visibility')
     ->addIndex(
     		$installer->getIdxName(
     				'featuredproduct/featuredproduct_index',
     				array('product_id','store_id', 'category_id', 'visibility')
     		),
     		array('product_id','store_id', 'category_id', 'visibility'))
     ->addIndex(
     		$installer->getIdxName(
     			'featuredproduct/featuredproduct_index',
     				array('store_id','category_id','visibility','position')
     		),
     		array('store_id','category_id','visibility','position'))
    ->addForeignKey($installer->getFkName('featuredproduct/featuredproduct_index', 'category_id', 'catalog/category', 'entity_id'),
        'category_id', $installer->getTable('catalog/category'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('featuredproduct/featuredproduct_index', 'product_id', 'catalog/product', 'entity_id'),
        'product_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('featuredproduct/featuredproduct_index', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)    
    ->setComment('Featured Product Index Table');

$installer->getConnection()->createTable($table);

$installer->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'featuredproduct_displaymethod', array(
        'type'    => 'int',        
        'label'   => 'Featured Product Display Method',
        'input'   => 'select',
        'source'  => 'featuredproduct/attribute_source_displaymethod',
        'sort_order' => 180,
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'group'  => 'Display Settings',
        'visible'       => 1,
        'required' => false
    
));

$installer->endSetup();