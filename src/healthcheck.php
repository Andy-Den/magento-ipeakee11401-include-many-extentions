<?php

require('app/Mage.php');

// Initialise Magento
Mage::app();

// Get a random value for tests
$rand = rand();

// Test database works
$product_id = Mage::getModel('catalog/product')->getCollection()->getAllIds(1);

// Test cache works
$cache = Mage::app()->getCacheInstance();
$cache->save($rand, 'healthcheck', array(), 5);
assert($cache->load('healthcheck') == $rand);
$cache->remove('healthcheck');

// Test sessions work
$session = Mage::getSingleton('core/session');
$session->setData('healthcheck', $rand);
assert($session->getData('healthcheck') == $rand);
$session->clear();

echo "OK\r\n";
