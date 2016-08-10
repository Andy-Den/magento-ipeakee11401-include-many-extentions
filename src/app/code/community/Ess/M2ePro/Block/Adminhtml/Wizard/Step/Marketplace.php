<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Block_Adminhtml_Wizard_Step_Marketplace extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();

        // Initialization block
        //------------------------------
        $this->setId('wizardStepMarketplace');
        //------------------------------

        $this->setTemplate('M2ePro/wizard/step/marketplace.phtml');
    }

    protected function _beforeToHtml()
    {
        //-------------------------------
        $buttonBlock = $this->getLayout()
                            ->createBlock('adminhtml/widget_button')
                            ->setData( array(
                                'label'   => Mage::helper('M2ePro')->__('Proceed'),
                                'onclick' => 'WizardHandlerObj.processStep(\''.$this->getUrl('*/adminhtml_marketplace/index').'\','.Ess_M2ePro_Model_Wizard::STATUS_MARKETPLACES.');',
                                'class' => 'process_marketplaces_button'
                            ) );
        $this->setChild('process_marketplaces_button',$buttonBlock);
        //-------------------------------

        return parent::_beforeToHtml();
    }
}