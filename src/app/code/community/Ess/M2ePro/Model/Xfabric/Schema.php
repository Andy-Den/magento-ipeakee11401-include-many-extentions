<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Xfabric_Schema extends Ess_M2ePro_Model_Abstract
{
    //####################################

    public function _construct()
    {
        parent::_construct();
        $this->_init('M2ePro/Xfabric_Schema');
    }

    //####################################

    public function getFile()
    {
        return $this->getData('file');
    }

    public function getVersion()
    {
        return $this->getData('version');
    }

    public function getBody()
    {
        return $this->getData('body');
    }

    public function getObject()
    {
        return $this->getData('object');
    }

    //------------------------------------

    public function getDecodedBody()
    {
        return @json_decode($this->getBody(),true);
    }

    public function getDecodedObject()
    {
        return @unserialize($this->getObject());
    }

    //####################################
}