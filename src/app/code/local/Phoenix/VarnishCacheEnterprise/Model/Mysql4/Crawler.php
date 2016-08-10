<?php
/**
 * PageCache powered by Varnish
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the PageCache powered by Varnish License
 * that is bundled with this package in the file LICENSE_VARNISH_CACHE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.phoenix-media.eu/license/license_varnish_cache.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to support@phoenix-media.eu so we can send you a copy immediately.
 *
 * @category   Phoenix
 * @package    Phoenix_VarnishCacheEnterprise
 * @copyright  Copyright (c) 2011 PHOENIX MEDIA GmbH & Co. KG (http://www.phoenix-media.eu)
 * @license    http://www.phoenix-media.eu/license/license_varnish_cache.txt
 */

class Phoenix_VarnishCacheEnterprise_Model_Mysql4_Crawler
    extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
         $this->_init('core/url_rewrite', 'url_rewrite_id');
    }

    /**
     * Returns core_url_rewrite query statement
     *
     * @param int $storeId
     * @return Zend_Db_Statement
     */
    public function getUrlStmt($storeId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(
                $this->getTable('core/url_rewrite'),
                array('store_id', 'request_path')
            )
            ->where('store_id=?', $storeId)
            ->where('is_system=1');

        return $this->_getReadAdapter()->query($select);
    }
}
