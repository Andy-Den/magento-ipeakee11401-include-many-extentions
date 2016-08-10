<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Xfabric_Request extends Ess_M2ePro_Model_Abstract
{
    const MAX_LIFE_TIME_INTERVAL = 21600;

    //####################################

    public function _construct()
    {
        parent::_construct();
        $this->_init('M2ePro/Xfabric_Request');
    }

    //####################################

    public function getMessageHash()
    {
        return $this->getData('message_hash');
    }

    public function getMessageGuid()
    {
        return $this->getData('message_guid');
    }

    public function getTopicPath()
    {
        return $this->getData('topic_path');
    }

    public function getRequestBody()
    {
        return $this->getData('request_body');
    }

    public function getResponserModel()
    {
        return $this->getData('responser_model');
    }

    public function getResponserParams()
    {
        return $this->getData('responser_params');
    }

    //------------------------------------

    public function getDecodedTopicPath()
    {
        return @json_decode($this->getTopicPath(),true);
    }

    public function getDecodedRequestBody()
    {
        return @json_decode($this->getRequestBody(),true);
    }

    public function getDecodedResponserParams()
    {
        return @json_decode($this->getResponserParams(),true);
    }

    //####################################
}