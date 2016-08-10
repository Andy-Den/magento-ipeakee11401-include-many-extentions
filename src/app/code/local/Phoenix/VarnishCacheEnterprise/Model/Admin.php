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

class Phoenix_VarnishCacheEnterprise_Model_Admin
{
    const XML_PATH_VARNISH_SERVERS      = 'system/varnishcache/servers';
    const XML_PATH_VARNISH_ADMIN_PORT   = 'system/varnishcache/admin_port';
    const XML_PATH_VARNISH_ADMIN_SECRET = 'system/varnishcache/admin_secret';

    protected $_servers = array();

    /**
     * Returns varnish servers
     *
     * @return multitype:
     */
    protected function _getConfigServers()
    {
        return explode(';', Mage::getStoreConfig(self::XML_PATH_VARNISH_SERVERS));
    }

    /**
     * Returns admin port
     *
     * @return string
     */
    protected function _getConfigPort()
    {
        return Mage::getStoreConfig(self::XML_PATH_VARNISH_ADMIN_PORT);
    }

    /**
     * Returns secret string
     *
     * @return string
     */
    protected function _getConfigSecret()
    {
        return Mage::getStoreConfig(self::XML_PATH_VARNISH_ADMIN_SECRET);
    }

    /**
     * Returns varnish admin connections
     *
     * @return array
     */
    public function getServers()
    {
        if (!$this->_servers) {
            $port = $this->_getConfigPort();
            $secret = $this->_getConfigSecret();
            foreach ($this->_getConfigServers() as $host) {
                $this->_servers[] = Mage::getModel('varnishcacheenterprise/admin_server')
                    ->setHost($host)
                    ->setPort($port)
                    ->setSecret($secret);
            }
        }
        return $this->_servers;
    }

    /**
     * Resets
     *
     * @return Phoenix_VarnishCache_Model_Admin
     */
    public function resetServers()
    {
        foreach ($this->getServers() as $server) {
            unset($server);
        }
        $this->_servers = array();
        $this->getServers();
        return $this;
    }
}
