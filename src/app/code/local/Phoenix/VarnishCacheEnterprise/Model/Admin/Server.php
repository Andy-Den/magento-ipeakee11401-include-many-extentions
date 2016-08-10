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

class Phoenix_VarnishCacheEnterprise_Model_Admin_Server extends Varien_Object
{
    protected $_socket;

    protected $_host = '127.0.0.1';
    protected $_port = 6081;
    protected $_blocking = 1;
    protected $_timeout = 5;
    protected $_secret;

    public function __destruct()
    {
        if ($this->_socket) {
            fclose($this->_socket);
        }
    }

    public function getHost()
    {
        return $this->_host;
    }

    public function setHost($host)
    {
        $this->_port = $host;
        return $this;
    }

    public function getPort()
    {
        return $this->_port;
    }

    public function setPort($port)
    {
        $this->_port = $port;
        return $this;
    }

    public function getBlocking()
    {
        return $this->_blocking;
    }

    public function setBlocking($blocking)
    {
        $this->_blocking = $blocking;
        return $this;
    }

    public function getTimeout()
    {
        return $this->_timeout;
    }

    public function setTimeout($timeout)
    {
        $this->_timeout = $timeout;
        return $this;
    }

    public function getSecret()
    {
        return $this->_secret;
    }

    public function setSecret($secret)
    {
        $this->_secret = $secret;
        return $this;
    }

    /**
     * Execute varnish admin command
     *
     * @return Varien_Object
     */
    public function command()
    {
        $args = func_get_args();
        $cmd = implode(' ', $args);
        $this->_write($cmd);
        $this->_write("\n");
        $response = $this->_read();
        if ($response->getCode() !== 200) {
            self::throwException(
                sprintf(
                    'Command failed:%s\nResponse code:%d\nResponse message:%s',
                    $cmd,
                    $response->getCode(),
                    implode("\n > ", explode("\n", trim($response->getBody())))
                )
            );
        }
        return $response;
    }

    /**
     * Returns active vcl from ram
     *
     * @return string
     */
    public function readActiveVcl()
    {
        $response = $this->command('vcl.list');
        foreach (explode("\n", $response->getBody()) as $line) {
            if (strpos($line, 'active') === 0) {
                $line = explode(" ", $line);
                $name = array_pop($line);
                return $this->command('vcl.show', $name)
                    ->getBody();
            }
        }
        self::throwException('No active vcl found.');
    }

    /**
     * Commands varnish to use vcl with given name
     *
     * @param string $vclName
     * @param bool $last
     * @return Phoenix_VarnishCacheEnterprise_Model_Admin_Server
     */
    public function useVcl($vclName = null, $last = true)
    {
        if ($vclName === null && $last === true) {
            $vclName = $this->getLastVcl();
        }
        if ($vclName === null) {
            self::throwException('Empty vcl name.');
        }
        $this->command('vcl.use ', $vclName);
        return $this;
    }

    /**
     * Returns varnish admin connection
     *
     * @return resource
     */
    protected function _getSocket()
    {
        if (!$this->_socket) {
            $this->_socket = fsockopen(
                $this->getHost(),
                $this->getPort(),
                $errno,
                $errstr,
                $this->getTimeout()
            );
            if (!is_resource($this->_socket)) {
                self::throwException(
                    sprintf(
                        'Could not connect. Errno:%s; Errstr:%s',
                        $errno,
                        $errstr
                    )
                );
            }
            stream_set_blocking($this->_socket, $this->getBlocking());
            stream_set_timeout($this->_socket, $this->getTimeout());
            $response = $this->_read();
            if ($response->getCode() === 107) {
                if (!$this->getSecret()) {
                    self::throwException('Authentication required.');
                }
                try {
                    $challenge = substr($response->getBody(), 0, 32);
                    $response = $this->command(
                        'auth',
                        hash('sha256', $challenge . "\n" . $this->getSecret() . $challenge . "\n")
                    );
                } catch (Exception $e){
                    self::throwException(
                        sprintf(
                            'Authentication failed: %s',
                            $e->getMessage()
                        )
                    );
                }
            }
            if ($response->getCode() !== 200) {
                self::throwException('Bad response.');
            }
        }
        return $this->_socket;
    }

    /**
     * Read
     *
     * @return Varien_Object
     */
    protected function _read()
    {
        $response = new Varien_Object();
        $socket   = $this->_getSocket();
        while (!feof($socket)) {
            $line = fgets($socket, 1024);
            if (!$line) {
                $meta = stream_get_meta_data($socket);
                if ($meta['timed_out']) {
                    self::throwException('Read timeout.');
                }
            }
            if (preg_match('/^(\d{3}) (\d+)/', $line, $matches)) {
                list($head, $code, $length) = $matches;
                $code = intval($code);
                if (isset($code)) {
                    $response->setCode($code);
                } else {
                    self::throwException('Empty response code.');
                }
                break;
            }
        }
        $body = '';
        while (!feof($this->_socket) && strlen($body) < $length) {
            $body .= fgets($this->_socket, 1024);
        }
        $response->setBody($body);
        return $response;
    }

    /**
     * Writes data
     *
     * @param string $data
     * @return Phoenix_VarnishCacheEnterprise_Model_Admin_Server
     */
    protected function _write($data)
    {
        $bytes = fputs($this->_getSocket(), $data);
        if ($bytes !== strlen($data)) {
            self::throwException('Failed to write.');
        }
        return $this;
    }

    /**
     * Throws exceptions
     *
     * @param mixed $msg
     * @throws Exception
     */
    public function throwException($msg)
    {
        $msg = sprintf(
            '[%s:%s] %s',
            $this->getHost(),
            $this->getPort(),
            $msg
        );
        Mage::helper('varnishcache')->debug($msg);
        throw new Exception($msg);
    }
}
