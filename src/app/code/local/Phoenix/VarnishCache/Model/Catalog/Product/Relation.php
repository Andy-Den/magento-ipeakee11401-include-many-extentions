<?php
/**
 * PageCache powered by Varnish
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to license that is bundled with
 * this package in the file LICENSE.txt.
 *
 * @category   Phoenix
 * @package    Phoenix_VarnishCache
 * @copyright  Copyright (c) 2011 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 */
class Phoenix_VarnishCache_Model_Catalog_Product_Relation extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('catalog/product_relation');
    }
}
