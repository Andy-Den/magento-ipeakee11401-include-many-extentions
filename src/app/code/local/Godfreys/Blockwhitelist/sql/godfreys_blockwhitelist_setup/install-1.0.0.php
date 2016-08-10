<?php
/**
 *  This setup adds block types to the permission_block table to allow blocks to display in the frontend
 */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->insertMultiple(
    $installer->getTable('admin/permission_block'),
    array(
        array('block_name' => 'cms/block', 'is_allowed' => 1),
        array('block_name' => 'aproduct/aproduct', 'is_allowed' => 1),
        array('block_name' => 'accessory/search_form_mini', 'is_allowed' => 1),
        array('block_name' => 'partsfinder/form', 'is_allowed' => 1),
        array('block_name' => 'brands/brands', 'is_allowed' => 1),
        array('block_name' => 'newsletter/subscribe', 'is_allowed' => 1),
        array('block_name' => 'catalog/product_list', 'is_allowed' => 1),
        array('block_name' => 'catalog/product_compare_list', 'is_allowed' => 1),
        array('block_name' => 'awislider/block', 'is_allowed' => 1),
        array('block_name' => 'ultimo/product_list_featured', 'is_allowed' => 1),
        array('block_name' => 'page/html', 'is_allowed' => 1),
        array('block_name' => 'ebizmarts_abandonedcart/email_order_items', 'is_allowed' => 1),
        array('block_name' => 'ebizmarts_autoresponder/email_backtostock_item', 'is_allowed' => 1),
        array('block_name' => 'ebizmarts_autoresponder/email_related_items', 'is_allowed' => 1),
        array('block_name' => 'ebizmarts_autoresponder/email_review_items', 'is_allowed' => 1),
        array('block_name' => 'ebizmarts_autoresponder/email_wishlist_items', 'is_allowed' => 1),
    )
);