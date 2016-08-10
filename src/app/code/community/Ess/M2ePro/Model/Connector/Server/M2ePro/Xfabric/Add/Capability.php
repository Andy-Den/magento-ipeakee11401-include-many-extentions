<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Connector_Server_M2ePro_Xfabric_Add_Capability extends Ess_M2ePro_Model_Connector_Server_M2ePro_Abstract
{
    // ########################################

    protected function getCommand()
    {
        return array('xfabric','add','capability');
    }

    // ########################################

    protected function getRequestData()
    {
        return array();
    }

    //----------------------------------------

    protected function validateResponseData($response)
    {
        return true;
    }

    protected function prepareResponseData($response)
    {
        return $response;
    }

    // ########################################
}