<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Connector_Server_M2ePro_Xfabric_Update_TenantData extends Ess_M2ePro_Model_Connector_Server_M2ePro_Abstract
{
    // ########################################

    protected function getCommand()
    {
        return array('xfabric','update','tenantData');
    }

    // ########################################

    protected function getRequestData()
    {
        return array(
            'tenant_id' => $this->params['tenant_id'],
            parent::API_VERSION_KEY => parent::API_VERSION,
            parent::REQUEST_INFO_KEY => $this->getRequestInfo()
        );
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