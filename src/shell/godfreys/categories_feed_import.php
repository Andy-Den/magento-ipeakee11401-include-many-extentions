<?php
require_once dirname(dirname(dirname(__FILE__))) . '/app/Mage.php';

$app = Mage::app('admin');

$stores = $app->getStores();

$exportDir = $app->getConfig()->getBaseDir('tmp');

$feeds = Mage::getModel('datafeed/datafeed')->getCollection()->addFieldToFilter('feed_status', 1);

$res = Mage::getSingleton('core/resource');
$conn = $res->getConnection('core_write');

$sql = "UPDATE ". $conn->getTableName('datafeed'). " SET `datafeed_categories`=? , `datafeed_category_filter`=1 WHERE feed_id=?";
//echo $sql ."\n";

foreach($feeds as $feed) {
	$storeId = $feed->getStoreId();
	
	if (!isset($stores[$storeId])) {
		echo "Cannot found store ".$storeId . "for feed ".$feed->getFeedName() . "\n";
		continue;
	}

//	$filterType = $feed->getDatafeedCategoryFilter();
//	$filterType = ($filterType == 1 ? true : false);
	$filterType = true;
	
	$store = $stores[$storeId];
	$storeCode = $store->getCode();
	$storeCode = str_replace(' ', '_', $storeCode);
	
	$filename = $storeCode . '.csv' ;

	$filepath = $exportDir . DS . $filename;
	
	if (!file_exists($filepath)) {
		echo "File {$filepath} not found for store ".$storeId. " and feed ".$feed->getFeedName(). ' ' . $feed->getId(). "\n";
		continue;
	}
	
	$fh = @fopen($filepath, 'r');
	if (false === $fh) {
		echo "Failed to open ". $filepath . "\n";
		continue;
	}
	$feed_categories = array();
	$arr = fgetcsv($fh);
	while($arr !== false) {
		$arr = fgetcsv($fh);
print_r($arr);
		$feed_categories[] = array(
			"line" => $arr[0],
			"checked" => ($filterType ? ($arr[1]== 1 ? true : false ) : ($arr[1] == 1 ? false : true)),
			"mapping" => $arr[3],
		);				
	}
	
	$conn->query($sql, array(json_encode($feed_categories), $feed->getId()) );
	 
}
