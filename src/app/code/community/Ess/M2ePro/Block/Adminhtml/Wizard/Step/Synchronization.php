<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Block_Adminhtml_Wizard_Step_Synchronization extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();

        // Initialization block
        //------------------------------
        $this->setId('wizardStepSynchronization');
        //------------------------------

        $this->setTemplate('M2ePro/wizard/step/synchronization.phtml');
    }

    protected function _beforeToHtml()
    {
        //-------------------------------
        $buttonBlock = $this->getLayout()
                            ->createBlock('adminhtml/widget_button')
                            ->setData( array(
                                'label'   => Mage::helper('M2ePro')->__('Proceed'),
                                'onclick' => 'WizardHandlerObj.processStep(\''.$this->getUrl('*/adminhtml_synchronization/index').'\','.Ess_M2ePro_Model_Wizard::STATUS_SYNCHRONIZATION.', WizardHandlerObj.callBackAfterEndConfiguration);',
                                'class' => 'process_synchronization_button'
                            ) );
        $this->setChild('process_synchronization_button',$buttonBlock);

        $buttonBlock = $this->getLayout()
                            ->createBlock('adminhtml/widget_button')
                            ->setData( array(
                                'label'   => Mage::helper('M2ePro')->__('Skip'),
                                'onclick' => 'WizardHandlerObj.skipStep('.Ess_M2ePro_Model_Wizard::STATUS_SYNCHRONIZATION.', WizardHandlerObj.callBackAfterEndConfiguration);',
                                'class' => 'skip_synchronization_button'
                            ) );
        $this->setChild('skip_synchronization_button',$buttonBlock);
        //-------------------------------

        return parent::_beforeToHtml();
    }
}