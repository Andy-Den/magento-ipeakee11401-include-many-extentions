<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Connector_Xfabric_Amazon_Account_Delete_Entity extends Ess_M2ePro_Model_Connector_Xfabric_Amazon_Requester
{
    // ########################################

    protected function getTopicPath()
    {
        return array('marketplace','profile','delete');
    }

    // ########################################

    protected function getResponserModel()
    {
        return 'Amazon_Account_Delete_EntityResponser';
    }

    protected function getResponserParams()
    {
        return array(
            'account_id' => $this->account->getId(),
            'marketplace_id' => $this->marketplace->getId()
        );
    }

    // ########################################

    protected function setLocks($messageHash)
    {
        $this->account->addObjectLock(NULL,$messageHash);
        $this->account->addObjectLock('server_synchronize',$messageHash);
        $this->account->addObjectLock('deleting_from_server',$messageHash);
    }

    // ########################################

    protected function getRequestData()
    {
        return array();
    }

    // ########################################
}