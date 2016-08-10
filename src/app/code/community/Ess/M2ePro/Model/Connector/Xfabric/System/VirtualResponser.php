<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Connector_Xfabric_System_VirtualResponser extends Ess_M2ePro_Model_Connector_Xfabric_System_Responser
{
    // ########################################

    protected function unsetLocks($isFailed = false, $message = NULL) {}

    // ########################################

    protected function validateSucceededResponseData($response)
    {
        return true;
    }

    protected function validateFailedResponseData($response)
    {
        return true;
    }

    //-----------------------------------------

    protected function processSucceededResponseData($response)
    {
        return $response;
    }

    protected function processFailedResponseData($response)
    {
        return $response;
    }

    // ########################################
}