<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Xfabric_Cron
{
    // ########################################

    public function process()
    {
        //$this->clearOldXfabricRequests();
        //Mage::helper('M2ePro/Module')->xfabricUpdateTenantDataAtServer(false);
    }

    // ########################################

    private function clearOldXfabricRequests()
    {
        $currentDateTime = Mage::helper('M2ePro')->getCurrentGmtDate(true);
        $maxLifeTimeInterval = Ess_M2ePro_Model_Xfabric_Request::MAX_LIFE_TIME_INTERVAL;

        $minCreateTimeStamp = $currentDateTime - $maxLifeTimeInterval;
        $minCreateDateTime = Mage::helper('M2ePro')->getDate($minCreateTimeStamp);

        /** @var $collection Mage_Core_Model_Mysql4_Collection_Abstract */
        $collection = Mage::getModel('M2ePro/Xfabric_Request')->getCollection();
        $collection->getSelect()->where('create_date < \''.$minCreateDateTime.'\'');

        $xfabricRequests = $collection->getItems();

        foreach ($xfabricRequests as $xfabricRequest) {

            /** @var $xfabricRequest Ess_M2ePro_Model_Xfabric_Request */
            $modelName = $xfabricRequest->getResponserModel();
            $className = Mage::getConfig()->getModelClassName($modelName);

            /** @var $responserObject Ess_M2ePro_Model_Connector_Xfabric_Responser */
            $responserObject = new $className($xfabricRequest);
            $responserObject->processMessageFailed('Request wait timeout exceeded.');
        }
    }

    // ########################################
}