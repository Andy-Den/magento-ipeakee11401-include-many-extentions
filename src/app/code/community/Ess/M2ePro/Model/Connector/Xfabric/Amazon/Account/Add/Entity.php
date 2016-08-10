<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Connector_Xfabric_Amazon_Account_Add_Entity extends Ess_M2ePro_Model_Connector_Xfabric_Amazon_Requester
{
    // ########################################

    protected function getTopicPath()
    {
        return array('marketplace','profile','create');
    }

    // ########################################

    protected function getResponserModel()
    {
        return 'Amazon_Account_Add_EntityResponser';
    }

    protected function getResponserParams()
    {
        return array(
            'account_id' => $this->account->getId(),
            'marketplace_id' => $this->params['marketplace_id'],
            'user_merchant_id' => $this->params['user_merchant_id'],
            'user_marketplace_id' => $this->params['user_marketplace_id'],
            'related_store_id' => $this->params['related_store_id']
        );
    }

    // ########################################

    protected function setLocks($messageHash)
    {
        $this->account->addObjectLock(NULL,$messageHash);
        $this->account->addObjectLock('server_synchronize',$messageHash);
        $this->account->addObjectLock('adding_to_server',$messageHash);
    }

    // ########################################

    protected function getRequestData()
    {
        /** @var $marketplaceObject Ess_M2ePro_Model_Marketplace */
        $marketplaceObject = Mage::helper('M2ePro/Component_Amazon')->getObject('Marketplace',$this->params['marketplace_id']);

        $output = array(
            'xId' => null,
            'payment' => null,
            'shipping' => null,
            'returnPolicy' => null,
            'xAccountId' => 'verified',
            'name' => $this->account->getTitle(),
            'siteCode' => $marketplaceObject->getCode(),
        );

        $output['embeddedMessage'] = array();
        $output['embeddedMessage']['payload'] = array();
        $output['embeddedMessage']['payload']['merchantId'] = $this->params['user_merchant_id'];
        $output['embeddedMessage']['payload']['marketplaceId'] = $this->params['user_marketplace_id'];
        $output['embeddedMessage']['payload']['info'] = array();

        if ($this->contentType == parent::CONTENT_TYPE_AVRO) {

            $output['embeddedMessage']['schemaVersion'] = '1.0.0';
            $postfix = 'public/xfabric/avro/schema/1.0.0/profile/specific.avpr';
            $output['embeddedMessage']['schemaUri'] = Mage::helper('M2ePro/Module')->getServerScriptsPath().$postfix;

            $output['embeddedMessage']['payload'] = $this->encodeEmbeddedMessage($output['embeddedMessage']);
        }

        return array('p'=>$output);
    }

    protected function getAvroSchemaVersion()
    {
        return '1.0.1';
    }

    // ########################################

    protected function getAccountIdKey()
    {
        return 'xId';
    }

    protected function useGlobalPModeForBody()
    {
        return true;
    }

    // ########################################
}