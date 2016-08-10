<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Connector_Xfabric_Amazon_Account_Delete_EntityResponser extends Ess_M2ePro_Model_Connector_Xfabric_Amazon_Responser
{
    // ########################################

    protected function unsetLocks($isFailed = false, $message = NULL)
    {
        try {
            $this->getAccount()->deleteObjectLocks(NULL,$this->messageHash);
            $this->getAccount()->deleteObjectLocks('server_synchronize',$this->messageHash);
            $this->getAccount()->deleteObjectLocks('deleting_from_server',$this->messageHash);
        } catch(Exception $e) {}
    }

    // ########################################

    protected function validateSucceededResponseData($response)
    {
        if (!isset($response['xProfileId'])) {
            return false;
        }
        return true;
    }

    protected function validateFailedResponseData($response)
    {
        if (!$this->validateErrorData($response)) {
            return false;
        }

        return true;
    }

    //-----------------------------------------

    protected function processSucceededResponseData($response)
    {
        try {
            /** @var $amazonAccount Ess_M2ePro_Model_Amazon_Account */
            $amazonAccount = $this->getAccount()->getChildObject();
            $amazonAccount->deleteMarketplaceItem($this->params['marketplace_id']);
        } catch(Exception $e) {}

        return $response;
    }

    protected function processFailedResponseData($response)
    {
        return false;
    }

    // ########################################

    /**
     * @return Ess_M2ePro_Model_Account
     */
    protected function getAccount()
    {
        return $this->getObjectByParam('Account','account_id');
    }

    /**
     * @return Ess_M2ePro_Model_Marketplace
     */
    protected function getMarketplace()
    {
        return $this->getObjectByParam('Marketplace','marketplace_id');
    }

    // ########################################
}