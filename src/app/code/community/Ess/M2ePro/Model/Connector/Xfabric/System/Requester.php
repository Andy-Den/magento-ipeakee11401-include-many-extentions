<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

abstract class Ess_M2ePro_Model_Connector_Xfabric_System_Requester extends Ess_M2ePro_Model_Connector_Xfabric_Requester
{
    protected function getDestinationId()
    {
        return '';
    }

    protected function getAuthorizationBearer()
    {
        return $this->capabilityToken;
    }
}