<?php
/**
 * Review the registration before submission
 *
 * @category   Balance
 * @package    Balance_Warranty
 * @author     Carey Sizer <carey@balanceinternet.com.au>
 */
class Balance_Warranty_Block_Registration_Confirmation extends Balance_Warranty_Block_Registration_Abstract
{
    protected function _construct()
    {
        $this->getRegistration()->setStepData('confirmation', array(
            'label'     => Mage::helper('warranty')->__('Confirmation'),
            'is_show'   => $this->isShow()
        ));
        parent::_construct();
    }
    
    public function getPreviousStep()
    {
        return 'contact';
    }
}