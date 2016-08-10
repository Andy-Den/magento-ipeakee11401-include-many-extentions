<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Helper_Ess extends Mage_Core_Helper_Abstract
{
    // ########################################

    /**
     * @return Ess_M2ePro_Model_Config_Ess
     */
    public function getConfig()
    {
        return Mage::getSingleton('M2ePro/Config_Ess');
    }

    // ########################################

    public function getModules()
    {
        return $this->getConfig()->getAllGroupValues('/modules/');
    }

    // ########################################

    public function getServerAdminKey()
    {
        return (string)$this->getConfig()->getGroupValue('/server/','admin_key');
    }

    public function getServerBaseUrl()
    {
        return (string)$this->getConfig()->getGroupValue('/server/','baseurl');
    }

    // ########################################

    public function getXfabricEndpoint()
    {
        return (string)$this->getConfig()->getGroupValue('/xfabric/','endpoint');
    }

    //-----------------------------------------

    public function updateXfabricWorkingEndpoint()
    {
        $params = array(
            'endpoint' => Mage::helper('M2ePro/Module')->getXfabricModuleEndpoint()
        );

        if (Mage::helper('M2ePro/Server')->isDeveloper()) {
            $topic = array('xfabric','capability','endpoint');
        } else {
            $topic = array('system','capability','endpoint','update');
        }

        try {
            Mage::getModel('M2ePro/Connector_Xfabric_System_Dispatcher')
                    ->processVirtual($topic,'System_VirtualResponser',array(),$params);
        } catch (Exception $exception) {}

        return true;
    }

    // ########################################
}