<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Connector_Xfabric_Amazon_VirtualRequester extends Ess_M2ePro_Model_Connector_Xfabric_Amazon_Requester
{
    private $cache = array();

    // ########################################

    protected function getTopicPath()
    {
        if (isset($this->cache['topic_path'])) {
            return $this->cache['topic_path'];
        }

        $this->cache['topic_path'] = $this->params['__topic_path__'];
        unset($this->params['__topic_path__']);
        return $this->cache['topic_path'];
    }

    // ########################################

    protected function getResponserModel()
    {
        if (isset($this->cache['responser_model'])) {
            return $this->cache['responser_model'];
        }

        $this->cache['responser_model'] = $this->params['__responser_model__'];
        unset($this->params['__responser_model__']);
        return $this->cache['responser_model'];
    }

    protected function getResponserParams()
    {
        if (isset($this->cache['responser_params'])) {
            return $this->cache['responser_params'];
        }

        $this->cache['responser_params'] = $this->params['__responser_params__'];
        unset($this->params['__responser_params__']);
        return $this->cache['responser_params'];
    }

    // ########################################

    protected function setLocks($messageHash) {}

    // ########################################

    protected function getRequestData()
    {
        if (isset($this->cache['request_data'])) {
            return $this->cache['request_data'];
        }

        $this->cache['request_data'] = $this->params['__request_data__'];
        unset($this->params['__request_data__']);
        return $this->cache['request_data'];
    }

    // ########################################
}