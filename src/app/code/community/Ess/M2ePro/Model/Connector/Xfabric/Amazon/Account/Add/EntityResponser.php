<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Connector_Xfabric_Amazon_Account_Add_EntityResponser extends Ess_M2ePro_Model_Connector_Xfabric_Amazon_Responser
{
    private $embeddedMessagePayload = null;

    // ########################################

    protected function unsetLocks($isFailed = false, $message = NULL)
    {
        $this->getAccount()->deleteObjectLocks(NULL,$this->messageHash);
        $this->getAccount()->deleteObjectLocks('server_synchronize',$this->messageHash);
        $this->getAccount()->deleteObjectLocks('adding_to_server',$this->messageHash);
    }

    // ########################################

    protected function validateSucceededResponseData($response)
    {
        if (!isset($response['p'])) {
            return false;
        }

        $response = $response['p'];

        if (!isset($response['xId']) || !isset($response['embeddedMessage']['payload'])) {
            return false;
        }

        $this->embeddedMessagePayload = $this->decodeEmbeddedMessage($response['embeddedMessage']);
        if (!isset($this->embeddedMessagePayload['info'])) {
            $this->embeddedMessagePayload['info'] = array();
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
        $response = $response['p'];

        $response['embeddedMessage']['payload'] = $this->embeddedMessagePayload;

        /** @var $amazonAccount Ess_M2ePro_Model_Amazon_Account */
        $amazonAccount = $this->getAccount()->getChildObject();
        $amazonAccount->addMarketplaceItem($this->params['marketplace_id'],
                                           $response['xId'],
                                           $this->params['user_merchant_id'],
                                           $this->params['user_marketplace_id'],
                                           $this->params['related_store_id'],
                                           $response['embeddedMessage']['payload']['info']);
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