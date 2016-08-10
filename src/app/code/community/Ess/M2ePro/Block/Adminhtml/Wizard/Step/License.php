<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Block_Adminhtml_Wizard_Step_License extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();

        // Initialization block
        //------------------------------
        $this->setId('wizardStepLicense');
        //------------------------------

        $this->setTemplate('M2ePro/wizard/step/license.phtml');
    }

    protected function _beforeToHtml()
    {
        //-------------------------------
        $buttonBlock = $this->getLayout()
                            ->createBlock('adminhtml/widget_button')
                            ->setData( array(
                                'label'   => Mage::helper('M2ePro')->__('Proceed'),
                                'onclick' => 'WizardHandlerObj.processStep(\''.$this->getUrl('*/adminhtml_license/index').'\','.Ess_M2ePro_Model_Wizard::STATUS_LICENSE.');',
                                'class' => 'process_license_button'
                            ) );
        $this->setChild('process_license_button',$buttonBlock);
        //-------------------------------

        return parent::_beforeToHtml();
    }
}