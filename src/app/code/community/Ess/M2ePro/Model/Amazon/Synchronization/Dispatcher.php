<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Amazon_Synchronization_Dispatcher extends Ess_M2ePro_Model_Synchronization_Dispatcher_Abstract
{
    //####################################

    public function process()
    {
        // Check global mode
        //----------------------------------
        if (!(bool)Mage::helper('M2ePro/Module')->getConfig()->getGroupValue('/amazon/synchronization/settings/','mode')) {
            return false;
        }
        //----------------------------------

        // Before dispatch actions
        //---------------------------
        if (!$this->beforeDispatch()) {
            return false;
        }
        //---------------------------

        try {

            // DEFAULTS SYNCH
            //---------------------------
            $tempTask = $this->checkTask(Ess_M2ePro_Model_Synchronization_Tasks::DEFAULTS);
            $tempGlobalMode = (bool)(int)Mage::helper('M2ePro/Module')->getConfig()->getGroupValue('/synchronization/settings/defaults/','mode');
            $tempLocalMode = (bool)(int)Mage::helper('M2ePro/Module')->getConfig()->getGroupValue('/amazon/synchronization/settings/defaults/','mode');
            if ($tempTask && $tempGlobalMode && $tempLocalMode) {
                $tempSynch = new Ess_M2ePro_Model_Amazon_Synchronization_Tasks_Defaults();
                $tempSynch->process();
            }
            //---------------------------

        } catch (Exception $exception) {
            $this->catchException($exception);
            return false;
        }

        try {

            // ORDERS SYNCH
            //---------------------------
            $tempTask = $this->checkTask(Ess_M2ePro_Model_Synchronization_Tasks::ORDERS);
            $tempGlobalMode = (bool)(int)Mage::helper('M2ePro/Module')->getConfig()->getGroupValue('/synchronization/settings/orders/','mode');
            $tempLocalMode = (bool)(int)Mage::helper('M2ePro/Module')->getConfig()->getGroupValue('/amazon/synchronization/settings/orders/','mode');
            if ($tempTask && $tempGlobalMode && $tempLocalMode) {
                $tempSynch = new Ess_M2ePro_Model_Amazon_Synchronization_Tasks_Orders();
                $tempSynch->process();
            }
            //---------------------------

        } catch (Exception $exception) {
            $this->catchException($exception);
            return false;
        }

        try {

            // TEMPLATES SYNCH
            //---------------------------
            $tempTask = $this->checkTask(Ess_M2ePro_Model_Synchronization_Tasks::TEMPLATES);
            $tempGlobalMode = (bool)(int)Mage::helper('M2ePro/Module')->getConfig()->getGroupValue('/synchronization/settings/templates/','mode');
            $tempLocalMode = (bool)(int)Mage::helper('M2ePro/Module')->getConfig()->getGroupValue('/amazon/synchronization/settings/templates/','mode');
            if ($tempTask && $tempGlobalMode && $tempLocalMode) {
                $tempSynch = new Ess_M2ePro_Model_Amazon_Synchronization_Tasks_Templates();
                $tempSynch->process();
            }
            //---------------------------

        } catch (Exception $exception) {
            $this->catchException($exception);
            return false;
        }

        try {

            // OTHER LISTINGS SYNCH
            //---------------------------
            $tempTask = $this->checkTask(Ess_M2ePro_Model_Synchronization_Tasks::OTHER_LISTINGS);
            $tempGlobalMode = (bool)(int)Mage::helper('M2ePro/Module')->getConfig()->getGroupValue('/synchronization/settings/other_listings/','mode');
            $tempLocalMode = (bool)(int)Mage::helper('M2ePro/Module')->getConfig()->getGroupValue('/amazon/synchronization/settings/other_listings/','mode');
            if ($tempTask && $tempGlobalMode && $tempLocalMode) {
                $tempSynch = new Ess_M2ePro_Model_Amazon_Synchronization_Tasks_OtherListings();
                $tempSynch->process();
            }
            //---------------------------

        } catch (Exception $exception) {
            $this->catchException($exception);
            return false;
        }

        try {

            // MARKETPLACES SYNCH
            //---------------------------
            $tempTask = $this->checkTask(Ess_M2ePro_Model_Synchronization_Tasks::MARKETPLACES);
            $tempGlobalMode = (bool)(int)Mage::helper('M2ePro/Module')->getConfig()->getGroupValue('/synchronization/settings/marketplaces/','mode');
            $tempLocalMode = (bool)(int)Mage::helper('M2ePro/Module')->getConfig()->getGroupValue('/amazon/synchronization/settings/marketplaces/','mode');
            if ($tempTask && $tempGlobalMode && $tempLocalMode) {
                $tempSynch = new Ess_M2ePro_Model_Amazon_Synchronization_Tasks_Marketplaces();
                $tempSynch->process();
            }
            //---------------------------

        } catch (Exception $exception) {
            $this->catchException($exception);
            return false;
        }

        return true;
    }

    //####################################

    private function beforeDispatch()
    {
        Mage::helper('M2ePro')->getGlobalValue('synchLogs')->setComponentMode(Ess_M2ePro_Helper_Component_Amazon::NICK);
        return true;
    }

    //####################################
}