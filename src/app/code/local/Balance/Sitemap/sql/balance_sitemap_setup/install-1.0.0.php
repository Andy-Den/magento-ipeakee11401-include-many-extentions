<?php
$installer = $this;
$installer->startSetup();

//clear old records and files
Mage::helper('balance_sitemap')->clearOldSitemaps($this);

$table = $installer->getConnection()
    ->newTable($installer->getTable('balance_sitemap/robots'))
    ->addColumn('robots_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Robots ID')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        ), 'Robots Title')
    ->addColumn('content', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
        ), 'Robots Content')
    ->addColumn('creation_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Robots Creation Time')
    ->addColumn('update_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Robots Modification Time')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '1',
        ), 'Is Robots Active')
     ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Store ID')
    ->addIndex($installer->getIdxName('balance_sitemap/robots', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('balance_sitemap/robots', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)   
    ->setComment('Stiemap Robots Table');
$installer->getConnection()->createTable($table);
$installer->endSetup();