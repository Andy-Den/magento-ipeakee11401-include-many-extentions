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
 * @package    Phoenix_VarnishCache
 * @copyright  Copyright (c) 2011 PHOENIX MEDIA GmbH & Co. KG (http://www.phoenix-media.eu)
 * @license    http://www.phoenix-media.eu/license/license_varnish_cache.txt
 */

class Phoenix_VarnishCache_Model_Control_Catalog_Category
    extends Phoenix_VarnishCache_Model_Control_Abstract
{
    protected $_helperName = 'varnishcache/control_catalog_category';

    /**
     * Purge Category
     *
     * @param Mage_Catalog_Model_Category $category
     * @return Phoenix_VarnishCache_Model_Control_Catalog_Category
     */
    public function purge(Mage_Catalog_Model_Category $category)
    {
        if ($this->_canPurge()) {
            $this->_purgeById($category->getId());
            if ($categoryName = $category->getName()) {
                $this->_getSession()->addSuccess(
                	Mage::helper('varnishcache')->__('Varnish cache for "%s" has been purged.', $categoryName)
                );
            }
        }
        return $this;
    }

    /**
     * Purge Category by id
     *
     * @param int $id
     * @return Phoenix_VarnishCache_Model_Control_Catalog_Category
     */
    public function purgeById($id)
    {
        if ($this->_canPurge()) {
            $this->_purgeById($id);
        }
        return $this;
    }

    /**
     * Purge Category by id
     *
     * @param int $id
     * @return Phoenix_VarnishCache_Model_Control_Catalog_Category
     */
    protected function _purgeById($id)
    {
        $collection = $this->_getUrlRewriteCollection()
            ->filterAllByCategoryId($id);
        foreach ($collection as $urlRewriteRule) {
            $urlRegexp = '/' . $urlRewriteRule->getRequestPath();
            $this->_getCacheControl()
                ->clean($this->_getStoreDomainList(), $urlRegexp);
        }
        return $this;
    }
}
