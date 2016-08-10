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

class Phoenix_VarnishCacheEnterprise_Adminhtml_VarnishCacheController
    extends Mage_Adminhtml_Controller_Action
{
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

    public function exportVclAction()
    {
        try {
            return $this->_prepareDownloadResponse(
                sprintf('default_%s.vcl', Mage::helper('varnishcacheenterprise')->getVersion()),
                Mage::getSingleton('varnishcacheenterprise/vcl')->export()
            );
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirectReferer();
        }
    }
}
