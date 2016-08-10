<?php
/**
 * Webtex
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.webtexsoftware.com/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@webtexsoftware.com and we will send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to http://www.webtexsoftware.com for more information, 
 * or contact us through this email: info@webtexsoftware.com.
 *
 * @category   MagExt
 * @package    MagExt_Tips
 * @copyright  Copyright (c) 2011 Webtex Solutions, LLC (http://www.webtexsoftware.com/)
 * @license    http://www.webtexsoftware.com/LICENSE.txt End-User License Agreement
 */

$this->startSetup()->run("

ALTER TABLE {$this->getTable('eav_attribute')}
    ADD `mgxtips` TEXT NOT NULL ;
    
ALTER TABLE {$this->getTable('catalog_product_option')}
    ADD `mgxtips` TEXT NOT NULL ;
    
DROP TABLE IF EXISTS {$this->getTable('mgxtips')};    
CREATE TABLE  {$this->getTable('mgxtips')} (
`mgxtips_label_id` INT( 9 ) NOT NULL auto_increment,
`attribute_id` INT( 9 ) NOT NULL ,
`store_id` INT( 9 ) NOT NULL ,
`value` TEXT NOT NULL,
PRIMARY KEY  (`mgxtips_label_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

")->endSetup();
