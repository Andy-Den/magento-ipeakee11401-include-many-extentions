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

class Phoenix_VarnishCacheEnterprise_Model_Crawler
    extends Mage_Core_Model_Abstract
{
    const XML_PATH_CRAWLER_ENABLED           = 'system/varnishcache_crawler/enabled';
    const XML_PATH_CRAWLER_DESIGN_EXCEPTIONS = 'system/varnishcache_crawler/design_exceptions';
    const XML_PATH_CRAWLER_MULTICURRENCY     = 'system/varnishcache_crawler/multicurrency';
    const XML_PATH_CRAWLER_THREADS_NUM       = 'system/varnishcache_crawler/threads_num';

    protected $_adapter;

    /* (non-PHPdoc)
     * @see Varien_Object::_construct()
     */
    protected function _construct()
    {
        $this->_init('varnishcacheenterprise/crawler');
    }

    /**
     * Crawl stores
     *
     * @return Phoenix_VarnishCacheEnterprise_Model_Crawler
     */
    public function run()
    {
        foreach ($this->getStores() as $storeId => $store) {
            if (!Mage::getStoreConfig(self::XML_PATH_CRAWLER_ENABLED, $storeId)) {
                continue;
            }
            foreach ($this->getCurrencies($store) as $currencyCode) {
                foreach ($this->getUserAgents($store) as $userAgent) {
                    $this->_run($store, $currencyCode, $userAgent);
                }
            }
        }
        return $this;
    }

    /**
     * Crawl store
     *
     * @param Mage_Core_Model_Store $store
     * @param string $currencyCode
     * @param string $userAgent
     * @return Phoenix_VarnishCacheEnterprise_Model_Crawler
     */
    protected function _run(Mage_Core_Model_Store $store, $currencyCode, $userAgent)
    {
        $storeId        = $store->getId();
        $baseUrl        = $store->getBaseUrl();

        $defaultStoreId = $store->getWebsite()->getDefaultStore()->getId();
        $defaultBaseUrl = $store->getWebsite()->getDefaultStore()->getBaseUrl();

        $options = array();
        if (($baseUrl == $defaultBaseUrl) && ($storeId != $defaultStoreId)) {
            $options[CURLOPT_COOKIE] = sprintf('store=%s;', $store->getCode());
        }
        if ($currencyCode != $store->getDefaultCurrencyCode()) {
            $options[CURLOPT_COOKIE] = sprintf('currency=%s;', $currency->getCode());
        }
        $options[CURLOPT_USERAGENT]  = $userAgent;

        $threadsNum = intval(Mage::getStoreConfig(self::XML_PATH_CRAWLER_THREADS_NUM, $storeId));
        if (!$threadsNum) {
            $threadsNum = 1;
        }
        foreach (array_chunk($this->_getUrls($store), $threadsNum) as $urls) {
            $this->_getAdapter()
                ->multiRequest($urls, $options);
            Mage::helper('varnishcache')->debug(
            	array(
                   'urls'    => $urls,
                   'options' => $options
                )
            );
        }

        return $this;
    }

    /**
     * Returns available stores
     *
     * @return array
     */
    public function getStores()
    {
        return Mage::app()->getStores();
    }

    /**
     * Returns available currencies
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getCurrencies(Mage_Core_Model_Store $store)
    {
        if (Mage::getStoreConfig(self::XML_PATH_CRAWLER_MULTICURRENCY, $store->getId())) {
            $currencies = $store->getAvailableCurrencyCodes(true);
        } else {
            $currencies = array($store->getDefaultCurrencyCode());
        }
        return $currencies;
    }

    /**
     * Returns User Agent according to design exceptions
     *
     * @param Mage_Core_Model_Store $store
     * @return array
     */
    public function getUserAgents(Mage_Core_Model_Store $store)
    {
        return array('VarnishCrawlerEnterprise');
    }

    /**
     * Returns urls to crawl
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    protected function _getUrls(Mage_Core_Model_Store $store)
    {
        $url = array();
        $stmt = $this->_getResource()
            ->getUrlStmt($store->getId());
        while ($row = $stmt->fetch()) {
            $urls[] = $store->getBaseUrl() . $row['request_path'];
        }
        return $urls;
    }

    /**
     * Returns curl adapter
     *
     * @return Varien_Http_Adapter_Curl
     */
    protected function _getAdapter()
    {
        if (!$this->_adapter) {
            $this->_adapter = new Varien_Http_Adapter_Curl();
        }
        return $this->_adapter;
    }

    /**
     * Parses urls from text/html
     *
     * @param string $html
     * @return array
     */
    public function parseUrls($html)
    {
        $urls = array();
        preg_match_all("/\s+href\s*=\s*[\"\']?([^\s\"\']+)[\"\'\s]+/ims", $html, $urls);
        return $urls[1];
    }
}
