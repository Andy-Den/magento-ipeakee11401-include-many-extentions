<?php
class Balance_Datafeed_Block_Adminhtml_Datafeed_Edit_Tab_Cron extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
       
        $form = new Varien_Data_Form();
        $model = Mage::getModel('datafeed/datafeed');

        $model->load($this->getRequest()->getParam('id'));

        $this->setForm($form);
        $fieldset = $form->addFieldset('datafeed_form', array('legend' => $this->__('Cron')));


        $this->setTemplate('datafeed/cron.phtml');


        if (Mage::registry('datafeed_data'))
            $form->setValues(Mage::registry('datafeed_data')->getData());

        return parent::_prepareForm();
    }

}