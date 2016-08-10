<?php
require_once dirname(dirname(dirname(__FILE__))) . '/app/Mage.php';

$app = Mage::app('admin');

$stores = $app->getStores();

$header = '"path",include,"name","mapping"'. "\n";

foreach($stores as $store) {
	$storeId = $store->getId();
	$storeCode = $store->getCode();
	$storeCode = str_replace(' ', '_', $storeCode);
	
	$filename = $storeCode . '.csv' ;
	
	$filepath = $app->getConfig()->getBaseDir('export') . DS . $filename;
	
	$fh = @fopen($filepath, 'w');
	
	if (false === $fh) {
		echo "Can not open file " . $filepath."\n";
		break;
	}
	
	$root_id = $store->getRootCategoryId();
	$pattern = '1/'.$root_id;
	$pattern_length = strlen($pattern);
		
	$listOfCategories = Mage::getModel('catalog/category')->getCollection()
		->setStoreId($storeId)
		->addAttributeToSelect('name')
		//->addAttributeToSelect('is_active')
		->addAttributeToSort('path', 'ASC');
	
	$categories = array();
	foreach ($listOfCategories as $category) {
		if (substr($category->getPath(), 0, $pattern_length) !== $pattern) continue;
		
		if (substr($category->getPath(), 0, 1) != '/' && $category->getLevel()>0) {
			$categories[$category->getId()]['name'] = $category->getName();
			$categories[$category->getId()]['path'] = $category->getPath();
			$categories[$category->getId()]['depth'] = count(explode('/', $category->getPath()))-1;
			$categories[$category->getId()]['id'] = $category->getId();
		}
	}
	
	fwrite($fh, $header);
	
	$current_depth = array();
	$a = 0;
	$current_depth[$a] = 0;
	$a++;
	foreach ($categories as $category) {
		$current_depth[$a] = $category['depth'];
		if ($current_depth[$a] - $current_depth[$a - 1] > 1) continue;
		
		if ($current_depth[$a] > $current_depth[$a - 1]){
			
		}
		elseif ($current_depth[$a] < $current_depth[$a - 1]) {
			$ll = $current_depth[$a - 1] - $current_depth[$a];
			for ($l = 0; $l < $ll; $l++) {

			}
		}
		fwrite($fh, '"' . $category['path'] . '",1,"'.$category['name'].'",' . "\n");
		$a++;
	}
	
}
