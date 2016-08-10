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

class Phoenix_VarnishCacheEnterprise_Model_Vcl
{
    /**
     * Export VCL
     *
     * @return Phoenix_VarnishCacheEnterprise_Model_Vcl
     */
    public function export()
    {
        try {
            $newDesignExceptionSub = $this->_buildDesignExceptionSub();
            $admin = Mage::getSingleton('varnishcacheenterprise/admin');
            foreach ($admin->getServers() as $server) {
                $vcl = $server->readActiveVcl();
                $oldDesignExceptionSub = $this->_parseDesignExceptionSub('design_exception', $vcl);
                if ($oldDesignExceptionSub) {
                    $vcl = str_ireplace($oldDesignExceptionSub, $newDesignExceptionSub, $vcl);
                } else {
                    $vcl .= "\n" . $newDesignExceptionSub;
                }
                return $vcl;
            }
        } catch (Exception $e) {
            $msg = 'Failed to prepare vcl: '.$e->getMessage();
            Mage::helper('varnishcache')->debug($msg);
            Mage::throwException($msg);
        }
        return $this;
    }

    /**
     * Generates design exceptions sub for current config
     *
     * @return string
     */
    public function generateDesignExceptionSub()
    {
        return $this->_buildDesignExceptionSub();
    }

    /**
     * Parse sub from given vcl string by given sub name
     *
     * @param string $name
     * @param string $vcl
     * @return string
     */
    protected function _parseDesignExceptionSub($name, $vcl)
    {
        $sub = '';
        $lb = 0;
        $rb = 0;
        $inside = false;
        foreach (explode("\n", $vcl) as $line) {
            if ($inside && strpos(ltrim($line), '#') === 0) {
                $sub .= $line . "\n";
                continue;
            }
            //if (preg_match('/sub\sdesign_exception/', $line)) {
            if (preg_match('/sub\s' . $name . '\W*{/', $line)) {
                $inside = true;
            }
            if ($inside) {
                foreach (str_split($line) as $pos => $char) {
                    if ($char == '{') {
                        $lb++;
                    }
                    if ($char == '}') {
                        $rb++;
                        if ($lb && $rb >= $lb) {
                            $sub .= substr($line, 0, $pos + 1);
                            break 2;
                        }
                    }
                }
                $sub .= $line . "\n";
            }
        }
        return $sub;
    }

    /**
     * Prepare design exceptions vcl sub
     *
     * @return string
     */
    protected function _buildDesignExceptionSub()
    {
        $stores = Mage::app()->getStores();
        $configsPaths = array(
            'package' => 'design/package/ua_regexp',
            'templates' => 'design/theme/template_ua_regexp',
            'skin' => 'design/theme/skin_ua_regexp',
            'layout' => 'design/theme/layout_ua_regexp',
            'theme' => 'design/theme/default_ua_regexp'
        );
        $vclSub = array();
        $vclSub[] = "sub design_exception {";
        foreach ($stores as $storeId => $store) {
            $urls = array();
            if ($store->getId() == $store->getGroup()->getDefaultStoreId()) {
                $urls[] = rtrim($store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_DIRECT_LINK), '/');
            }
            $urls[] = rtrim($store->getBaseUrl(), '/');
            foreach ($urls as $url) {
                foreach ($configsPaths as $configType => $configPath) {
                    $config = Mage::getStoreConfig($configPath, $storeId);
                    if ($config) {
                        foreach (unserialize($config) as $exception) {
                            extract($exception);
                            extract(parse_url($url));
                            if (!isset($path)) {
                                $path = '/';
                            }
                            $vclSub[] = sprintf('    if (req.http.host == "%s" && req.url ~ "^%s" && req.http.User-Agent ~ "%s") {', $host, $path, $regexp);
                            if ($this->_getVersion() == Phoenix_VarnishCacheEnterprise_Model_Source_Version::VERSION_2_1) {
                                $vclSub[] = sprintf('        set req.hash += "%s";', sprintf('%s_%s', $configType, $value));
                            } else if ($this->_getVersion() == Phoenix_VarnishCacheEnterprise_Model_Source_Version::VERSION_3_0) {
                                $vclSub[] = sprintf('        hash_data("%s");', sprintf('%s_%s', $configType, $value));
                            }
                            $vclSub[] = '    }';
                        }
                    }
                }
            }
        }
        $vclSub[] = "}";
        return implode("\n", $vclSub);
    }

    /**
     * Returns varnish version
     *
     * @return string
     */
    protected function _getVersion()
    {
        return Mage::helper('varnishcacheenterprise')->getVersion();
    }
}
