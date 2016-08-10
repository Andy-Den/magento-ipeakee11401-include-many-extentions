<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Connector_Xfabric_Amazon_Orders_Get_Items extends Ess_M2ePro_Model_Connector_Xfabric_Amazon_Requester
{
    // ########################################

    protected function getTopicPath()
    {
        return array('order','search');
    }

    // ########################################

    protected function getResponserModel()
    {
        return 'Amazon_Orders_Get_ItemsResponser';
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

    }

    // ########################################

    protected function getRequestData()
    {
        $output = array(
            'filter' => array(
                'startTime' => strtotime($this->params['from_date']),
                'endTime' => NULL,
                'status' => !empty($this->params['status']) ? $this->params['status'] : NULL
            )
        );

        return $output;
    }

    protected function getAvroSchemaVersion()
    {
        return '1.0.0';
    }

    // ########################################

    protected function getAccountIdKey()
    {
        return 'xProfileId';
    }

    protected function useGlobalPModeForBody()
    {
        return false;
    }

    // ########################################
}