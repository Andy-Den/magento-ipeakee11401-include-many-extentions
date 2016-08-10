<?php
/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
 /***************************************
 *         MAGENTO EDITION USAGE NOTICE *
 *****************************************/
 /* This package designed for Magento COMMUNITY edition
 * BelVG does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BelVG does not provide extension support in case of
 * incorrect edition usage.
 /***************************************
 *         DISCLAIMER   *
 *****************************************/
 /* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 *****************************************************
 * @category   Belvg
 * @package    Belvg_Twitterconnect
 * @copyright  Copyright (c) 2010 - 2011 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */?>
<?php

$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('belvg_twitter_settings')} (
  `user_name` varchar(111) character set utf8 NOT NULL,
  `title` varchar(111) character set utf8 NOT NULL,
  `subject` varchar(111) character set utf8 NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `shell_bg` varchar(111) character set utf8 NOT NULL,
  `shell_color` varchar(111) character set utf8 NOT NULL,
  `tweets_bg` varchar(111) character set utf8 NOT NULL,
  `tweets_color` varchar(111) character set utf8 NOT NULL,
  `tweets_link` varchar(111) character set utf8 NOT NULL,
  `position` int(11) NOT NULL,
  `pages` varchar(1111) character set utf8 NOT NULL,
  `status` int(11) NOT NULL,
  `store` int(11) NOT NULL,
  `twitter_id` int(11) NOT NULL auto_increment,
  `type` varchar(111) character set utf8 NOT NULL,
  `interval` int(11) NOT NULL,
  `timestamp` varchar(111) NOT NULL,
  `avatars` varchar(111) NOT NULL,
  `hashtags` varchar(111) NOT NULL,
  `scrollbar` varchar(111) NOT NULL,
  PRIMARY KEY  (`twitter_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `{$this->getTable('belvg_twitter_users')}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `twitter_id` int(11) NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `{$this->getTable('belvg_twitter_users')}`
  ADD CONSTRAINT `belvg_twitter_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `{$this->getTable('customer_entity')}` (`entity_id`) ON DELETE CASCADE;
 ");

$installer->endSetup(); 