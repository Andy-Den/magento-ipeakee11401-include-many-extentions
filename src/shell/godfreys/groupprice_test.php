<?php
require_once dirname(dirname(dirname(__FILE__))) . '/app/Mage.php';

$client = new SoapClient('http://devgodfreysau.balancenet.com.au/index.php/api/index/index/?wsdl=1');
$sessionId = $client->login('balance', 'balance08');

$data = array(
		'productId' => '2107',
		
		'productData' => array(
			'group_price'=> array(
			array(
				'website_id' => '0',
				'cust_group' => '3', //0- NOT LOGGED IN, 1- Gerneral, 2- Wholesale, 3- Retailer
				'price' => 0.90,
				'delete' => false,
			),
			array(
				'website_id' => '0',
				'cust_group' => '1', //0- NOT LOGGED IN, 1- Gerneral, 2- Wholesale, 3- Retailer
				'price' => 0.95,
				'delete' => false,
			),
			array(
				'website_id' => '13',
				'cust_group' => '0',
				'price' => '',
				'delete' => true,
			)
		)
		)
);

print_r($client->call($sessionId, 'catalog_product.update', $data));
