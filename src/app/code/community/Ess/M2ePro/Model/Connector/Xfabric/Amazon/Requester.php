<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

abstract class Ess_M2ePro_Model_Connector_Xfabric_Amazon_Requester extends Ess_M2ePro_Model_Connector_Xfabric_Requester
{
    /**
     * @var Ess_M2ePro_Model_Marketplace|null
     */
    protected $marketplace = NULL;

    /**
     * @var Ess_M2ePro_Model_Account|null
     */
    protected $account = NULL;

    // ########################################

    public function __construct(array $params = array(),
                                Ess_M2ePro_Model_Marketplace $marketplace = NULL,
                                Ess_M2ePro_Model_Account $account = NULL)
    {
        $this->marketplace = $marketplace;
        $this->account = $account;
        parent::__construct($params);
    }

    // ########################################

    public function process()
    {
        if (!is_null($this->account) && !is_null($this->marketplace)) {

            /** @var $amazonAccount Ess_M2ePro_Model_Amazon_Account */
            $amazonAccount = $this->account->getChildObject();
            $marketplaceDataTemp = $amazonAccount->getMarketplaceItem($this->marketplace->getId());

            if (!is_null($marketplaceDataTemp)) {
                if ((bool)$this->useGlobalPModeForBody()) {
                    $this->requestExtraData['p'][$this->getAccountIdKey()] = $marketplaceDataTemp['server_hash'];
                } else {
                    $this->requestExtraData[$this->getAccountIdKey()] = $marketplaceDataTemp['server_hash'];
                }
            }
        }

        parent::process();
    }

    // ########################################

    protected function getDestinationId()
    {
        return (string)Mage::helper('M2ePro/Component_Amazon')->getXfabricDestinationId();
    }

    //----------------------------------------

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