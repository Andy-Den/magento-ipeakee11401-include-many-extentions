<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Adminhtml_M2epro_WizardController extends Ess_M2ePro_Controller_Adminhtml_MainController
{
    //#############################################

    protected function _initAction()
    {
        $this->loadLayout()
             ->_setActiveMenu('m2epro/wizard')
             ->_title(Mage::helper('M2ePro')->__('M2E Pro'))
             ->_title(Mage::helper('M2ePro')->__('Wizard'));

        $this->getLayout()->getBlock('head')
             ->addJs('M2ePro/WizardHandler.js');

        return $this;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('m2epro');
    }

    //#############################################

    public function indexAction()
    {
        if (Mage::getSingleton('M2ePro/Wizard')->isInstallationWelcome()) {
            $this->_redirect('*/*/welcome');
            return;
        }

        if (Mage::getSingleton('M2ePro/Wizard')->isInstallationActive()) {
            $this->_redirect('*/*/installation');
            return;
        }

        if (Mage::getSingleton('M2ePro/Wizard')->isInstallationFinished()) {
            $this->_redirect('*/*/congratulation');
            return;
        }

        $this->_redirect('*/adminhtml_about/index');
    }

    //---------------------------

    public function welcomeAction()
    {
        if (!Mage::getSingleton('M2ePro/Wizard')->isInstallationWelcome()) {
            $this->_redirect('*/*/index');
            return;
        }

        $this->_initAction()
             ->_addContent($this->getLayout()->createBlock('M2ePro/adminhtml_wizard_welcome'))
             ->renderLayout();
    }

    public function installationAction()
    {
        if (!Mage::getSingleton('M2ePro/Wizard')->isInstallationActive()) {
            $this->_redirect('*/*/index');
            return;
        }

        $this->_initAction()
             ->_addContent($this->getLayout()->createBlock('M2ePro/adminhtml_wizard_installation'))
             ->renderLayout();
    }

    public function congratulationAction()
    {
        if (!Mage::getSingleton('M2ePro/Wizard')->isInstallationFinished()) {
            $this->_redirect('*/*/index');
            return;
        }

        $this->_initAction()
             ->_addContent($this->getLayout()->createBlock('M2ePro/adminhtml_wizard_congratulation'))
             ->renderLayout();
    }

    //#############################################

    public function skipAction()
    {
        //$wizardMode = $this->getRequest()->getParam('mode');
        $wizardMode = Ess_M2ePro_Model_Wizard::MODE_INSTALLATION;

        Mage::getSingleton('M2ePro/Wizard')->setStatus(Ess_M2ePro_Model_Wizard::STATUS_SKIP, Ess_M2ePro_Model_Wizard::MODE_INSTALLATION);
        Mage::getSingleton('M2ePro/Wizard')->clearMenuCache();

        $this->_redirect('*/*/congratulation');
    }

    public function completeAction()
    {
        //$wizardMode = $this->getRequest()->getParam('mode');
        $wizardMode = Ess_M2ePro_Model_Wizard::MODE_INSTALLATION;

        Mage::getSingleton('M2ePro/Wizard')->setStatus(Ess_M2ePro_Model_Wizard::STATUS_COMPLETE, Ess_M2ePro_Model_Wizard::MODE_INSTALLATION);
        Mage::getSingleton('M2ePro/Wizard')->clearMenuCache();

        $this->_redirect('*/*/congratulation');
    }

    //#############################################

    public function setStatusAction()
    {
        //$wizardMode = $this->getRequest()->getParam('mode');
        $wizardMode = Ess_M2ePro_Model_Wizard::MODE_INSTALLATION;

        $status = $this->getRequest()->getParam('status');
        $status && Mage::getSingleton('M2ePro/Wizard')->setStatus($status, $wizardMode);

        if ($wizardMode == Ess_M2ePro_Model_Wizard::MODE_INSTALLATION &&
            Mage::getSingleton('M2ePro/Wizard')->isInstallationFinished()) {
            Mage::getSingleton('M2ePro/Wizard')->clearMenuCache();
        }
    }

    public function getHiddenStepsAction()
    {
        $hiddenSteps = array();
        !Mage::helper('M2ePro/Component_Ebay')->isEnabled() && $hiddenSteps[] = Ess_M2ePro_Model_Wizard::STATUS_ACCOUNTS_EBAY;
        //!Mage::helper('M2ePro/Component_Amazon')->isEnabled() && $hiddenSteps[] = Ess_M2ePro_Model_Wizard::STATUS_ACCOUNTS_AMAZON;

        exit(json_encode($hiddenSteps));
    }

    //#############################################
}