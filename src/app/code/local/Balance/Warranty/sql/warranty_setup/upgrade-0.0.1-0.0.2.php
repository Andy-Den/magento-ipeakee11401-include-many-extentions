<?php

$installer = $this;
$installer->startSetup();
$installer->run("

CREATE TABLE IF NOT EXISTS `warranty_registration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(10) NOT NULL,
  `make` varchar(100) NOT NULL,
  `model` varchar(100) NOT NULL,
  `term` int(1) NOT NULL COMMENT 'Years',
  `serial` varchar(100) DEFAULT NULL,
  `date_of_purchase` date NOT NULL,
  `price` double NOT NULL,
  `purchase_reason_price` int(1) DEFAULT NULL,
  `purchase_reason_features` int(1) DEFAULT NULL,
  `purchase_reason_brand` int(1) DEFAULT NULL,
  `purchase_reason_other` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

");
$installer->endSetup();