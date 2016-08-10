<?php
require_once dirname(dirname(dirname(__FILE__))) . '/app/Mage.php';

$client = new SoapClient('http://magento:balance13@vacspareau.internal.balancenet.com.au/index.php/api/index/index/?wsdl=1', array('login' => 'magento', 'password' => 'balance13'));
//$client = new SoapClient('vacspareau.wsdl', array('login' => 'magento', 'password' => 'balance13'));
$sessionId = $client->login('balance', 'balance08');

/*
$data['accessoryId'] = '1114';
$data['brand'] = 'Vax';
$data['model'] = '4000 Power';
$data['productName'] = 'VaxPower4000';

echo $client->call($sessionId, 'partsfinder_accessory.upsert', $data);


$data['accessoryId'] = '1114';
$data['brand'] = 'Vax';
$data['model'] = '121';
$data['productName'] = 'Vax121';

echo $client->call($sessionId, 'partsfinder_accessory.upsert', $data);


$data['accessoryId'] = '1114';
$data['brand'] = 'Vax';
$data['model'] = '4000 Power';

echo $client->call($sessionId, 'partsfinder_accessory.remove', $data);
*/

$data['accessoryId'] = '1114';

print_r($client->call($sessionId, 'partsfinder_accessory.list', $data));