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

class Phoenix_VarnishCacheEnterprise_Model_Observer
{
    /**
     * Shows notice to update Varnish VCL file
     *
     * @param Varien_Event_Observer $observer
     * @return Phoenix_VarnishCacheEnterprise_Model_Observer
     */
    public function showVclUpdateMessage(Varien_Event_Observer $observer)
    {
        Mage::getSingleton('core/session')->addNotice(
            Mage::helper('varnishcacheenterprise')->__('New VCL file can be exported from Varnish system configuration section.')
        );

        return $this;
    }
}
