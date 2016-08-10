<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Block_Adminhtml_Wizard_Step_General extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();

        // Initialization block
        //------------------------------
        $this->setId('wizardStepGeneral');
        //------------------------------

        $this->setTemplate('M2ePro/wizard/step/general.phtml');
    }

    protected function _beforeToHtml()
    {
        // Set data for form
        //----------------------------
        $this->status = Mage::getSingleton('M2ePro/Wizard')->getStatus($this->getMode());

        $hiddenSteps = array();
        !Mage::helper('M2ePro/Component_Ebay')->isEnabled() && $hiddenSteps[] = Ess_M2ePro_Model_Wizard::STATUS_ACCOUNTS_EBAY;
        !Mage::helper('M2ePro/Component_Amazon')->isEnabled() && $hiddenSteps[] = Ess_M2ePro_Model_Wizard::STATUS_ACCOUNTS_AMAZON;

        $this->hiddenSteps = json_encode($hiddenSteps);
        //----------------------------

        return parent::_beforeToHtml();
    }

    public function getMode()
    {
        $mode = $this->getData('mode');
        $mode = Ess_M2ePro_Model_Wizard::MODE_INSTALLATION;

        return $mode;
    }
}