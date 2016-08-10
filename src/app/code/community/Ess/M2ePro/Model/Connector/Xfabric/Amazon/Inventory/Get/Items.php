<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Connector_Xfabric_Amazon_Inventory_Get_Items extends Ess_M2ePro_Model_Connector_Xfabric_Amazon_Requester
{
    // ########################################

    protected function getTopicPath()
    {
        return array('listing','search');
    }

    // ########################################

    protected function getResponserModel()
    {
        return 'Amazon_Inventory_Get_ItemsResponser';
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
        $skusArray = !empty($this->params['skus']) ? $this->params['skus'] :  null;

        $startTime = !empty($this->params['since_date']) ? strtotime($this->params['since_date']) : null;

        $output = array(
            'filter' => array(
                'skus' => $skusArray,
                'startTime' => $startTime,
                'endTime' => null
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